<?php
namespace app\common\server\pay;

use think\Model;
//引入微信官方库
include_once __DIR__.'/../../../../vendor/wx/lib/WxPay.Api.php';
include_once __DIR__.'/../../../../vendor/wx/example/notify.php';

class Wechat implements IPay
{
    const NAME = 'wechat';

    protected $notify_url;
    //订单有效时长
    protected $valid_time;
    //微信配置信息
    protected $config;

    public function __construct($config=[])
    {
        $this->config = new WxPayConfig($config);
    }

    /*
     * 支付信息
     * @param $model Model 订单模型
     * @param $pay_way 支付模式  app--app支付  MICROPAY--条码支付   NATIVE-扫码支付
     * */
    public function pay(Model $model, $pay_way,$auth_code='')
    {
        //全用大写
        $pay_way = strtoupper($pay_way);
//        $this->model = $model;
        //获取订单支付信息
        $pay_info = $model->getPayData();

        if ( $pay_way == 'APP' ) {
            $input = new \WxPayUnifiedOrder();
            $input->SetTrade_type("APP");
            $input->SetBody(config('app_name').$pay_info['body']);
            $pay_mode = 'unifiedOrder';

        }
//        elseif ($pay_way == 'MICROPAY') {//条码支付--无法使用
//            $input = new \WxPayMicroPay();
//            $input->SetBody($pay_info['body']);
//            $input->SetAuth_code($auth_code);
//            $pay_mode = 'micropay';
//
//        }
        elseif($pay_way == 'NATIVE') { //扫描支付
            $input = new \WxPayUnifiedOrder();
            $input->SetTrade_type('NATIVE');
            $input->SetBody($pay_info['body']);

            $pay_mode = 'unifiedOrder';

        }else {
            abort(40041,'支付方式不存在');

        }
        //微信配置
//        $config = new WxPayConfig();
        $start_time = time();

        $input->SetNotify_url($this->notify_url);       //回调地址
        $input->SetProduct_id('wechat_pay');           //此参数为二维码中包含的商品ID，商户自行定义。

        $input->SetOut_trade_no($pay_info['no']);
        $input->SetTotal_fee($pay_info['money']*100);
        $input->SetTime_start(date("YmdHis",$start_time));
        $input->SetTime_expire(date("YmdHis", $start_time+$this->valid_time));
        $input->SetGoods_tag($pay_info['tag']);

        $result = \WxPayApi::$pay_mode($this->config, $input);
        if($result['result_code']=='SUCCESS' && $result['return_code']=='SUCCESS'){
            if($pay_way=='APP') {
                $result = $this->handleAppPayResult($result);
            }
            return $result;
        }elseif ($result['result_code']=='FAIL') {
            abort(40042,'微信支付内部异常!:'.$result['err_code_des']);
        }

        abort(40042,'微信支付内部异常!:'.$result['return_msg']);
    }


    //回调通知
    public function notify()
    {
        $data = file_get_contents('php://input');
        $notify = new HandleNotify();

        $sign_state = $notify->Handle($this->config,false);
        $data = $notify->FromXml($data);

        if($sign_state!==false){
            if( $data["return_code"] == "SUCCESS" && $data["result_code"] == "SUCCESS" ){
                return $data;
            }
        }

        abort(40056,"签名异常:" . json_encode($data));
    }


    //退款流程
    public function handleRefund()
    {
        $total_fee = $this->model->back_info['total_fee']*100;
        $refund_fee = $this->model->back_info['refund_fee']*100;
        $order_type = $this->model->back_info['order_type'];
        $order_no = $this->model->back_info['order_no'];
//        dump($total_fee);exit;
        $input = new \WxPayRefund();
        if($order_type == 'order') { //商户订单号
            $input->SetOut_trade_no($order_no);
        } else { //微信订单号
            $input->SetTransaction_id($order_no);
        }

        //关闭异步通知
//        $input->SetNotify_url($this->refund_notify_url);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no(\WxPayConfig::$MCHID.date("YmdHis"));
        $input->SetOp_user_id(\WxPayConfig::$MCHID);
//        dump($input);exit;
        $result = \WxPayApi::refund($input);
        if($result['result_code']=='SUCCESS') { //操作成功
            return array(true,'',$result);
        }else {//操作失败
            return array(false,$result['err_code_des'].'['.$result['err_code'].']',$result);
        }

//        dump($result);
//        exit();
    }

    //设置通知地址
    public function setNotifyUrl($url)
    {
        $this->notify_url = $url;
    }
    //设置订单有效期
    public function setOrderExpress($second)
    {
        $this->valid_time = $second;
    }

    //返回通知结果
    public function responseInfo()
    {
        //处理回调信息
        return '<xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>';
    }

    public function handleAppPayResult($app_data)
    {
        $result_data = array(
            'appid'  => $app_data['appid'],
            'partnerid' => $app_data['mch_id'],
            'prepayid' => $app_data['prepay_id'],
            'noncestr' => $app_data['nonce_str'],
            'timestamp' => time(),
            'package' => 'Sign=WXPay',
        );
        //排序
        ksort($result_data);
        //处理签名
        $result_str = $this->ToUrlParams($result_data);
        $result_temp = $result_str .'&key='.$this->config->GetKey();
        $result_data['sign'] = strtoupper(md5($result_temp));

        return $result_data;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($data)
    {
        $buff = "";
        foreach ($data as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}


class WxPayConfig extends \WxPayConfigInterface
{
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;

    }

    //=======【基本信息设置】=====================================
    /**
     * TODO: 修改这里配置为您自己申请的商户信息
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     */
    public function GetAppId()
    {
//        return 'wx426b3015555a46be';
        return $this->config['appid'];
    }
    public function GetMerchantId()
    {
//        return '1900009851';
        return $this->config['mchid'];
    }

    //=======【支付相关配置：支付成功回调地址/签名方式】===================================
    /**
     * TODO:支付回调url
     * 签名和验证签名方式， 支持md5和sha256方式
     **/
    public function GetNotifyUrl()
    {
        return "";
    }
    public function GetSignType()
    {
        return "HMAC-SHA256";
    }

    //=======【curl代理设置】===================================
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var unknown_type
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
    }


    //=======【上报信息配置】===================================
    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    public function GetReportLevenl()
    {
        return 1;
    }


    //=======【商户密钥信息-需要业务方继承】===================================
    /*
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）, 请妥善保管， 避免密钥泄露
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）， 请妥善保管， 避免密钥泄露
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * @var string
     */
    public function GetKey()
    {
//        return '8934e7d15453e97507ef794cf7b0519d';
        return $this->config['key'];
    }
    public function GetAppSecret()
    {
//        return '7813490da6f1265e4901ffb80afaa36f';
        return $this->config['appsecret'];
    }


    //=======【证书路径设置-需要业务方继承】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * 注意:
     * 1.证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载；
     * 2.建议将证书文件名改为复杂且不容易猜测的文件名；
     * 3.商户服务器要做好病毒和木马防护工作，不被非法侵入者窃取证书文件。
     * @var path
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        $sslCertPath = '../cert/apiclient_cert.pem';
        $sslKeyPath = '../cert/apiclient_key.pem';
    }
}



class MicroPay
{
    /**
     *
     * 提交刷卡支付，并且确认结果，接口比较慢
     * @param WxPayMicroPay $microPayInput
     * @throws WxpayException
     * @return 返回查询接口的结果
     */
    public function pay($microPayInput)
    {
        //①、提交被扫支付
        $config = new WxPayConfig();
        $result = \WxPayApi::micropay($config, $microPayInput, 5);
        //如果返回成功
        if(!array_key_exists("return_code", $result)
            || !array_key_exists("result_code", $result))
        {
            echo "接口调用失败,请确认是否输入是否有误！";
            abort(40051,"接口调用失败！");
        }

        //取订单号
        $out_trade_no = $microPayInput->GetOut_trade_no();

        //②、接口调用成功，明确返回调用失败
        if($result["return_code"] == "SUCCESS" &&
            $result["result_code"] == "FAIL" &&
            $result["err_code"] != "USERPAYING" &&
            $result["err_code"] != "SYSTEMERROR")
        {
            return false;
        }

        //③、确认支付是否成功
        $queryTimes = 10;
        while($queryTimes > 0)
        {
            $succResult = 0;
            $queryResult = $this->query($out_trade_no, $succResult);
            //如果需要等待1s后继续
            if($succResult == 2){
                sleep(2);
                continue;
            } else if($succResult == 1){//查询成功
                return $queryResult;
            } else {//订单交易失败
                break;
            }
        }

        //④、次确认失败，则撤销订单
        if(!$this->cancel($out_trade_no))
        {
            abort(40050,"撤销单失败！");
        }

        return false;
    }

    /**
     *
     * 查询订单情况
     * @param string $out_trade_no  商户订单号
     * @param int $succCode         查询订单结果
     * @return 0 订单不成功，1表示订单成功，2表示继续等待
     */
    public function query($out_trade_no, &$succCode)
    {
        $queryOrderInput = new \WxPayOrderQuery();
        $queryOrderInput->SetOut_trade_no($out_trade_no);
        $config = new \WxPayConfig();
        try{
            $result = \WxPayApi::orderQuery($config, $queryOrderInput);
        } catch(\Exception $e) {
            trace(json_encode($e));
        }
        if($result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            //支付成功
            if($result["trade_state"] == "SUCCESS"){
                $succCode = 1;
                return $result;
            }
            //用户支付中
            else if($result["trade_state"] == "USERPAYING"){
                $succCode = 2;
                return false;
            }
        }

        //如果返回错误码为“此交易订单号不存在”则直接认定失败
        if($result["err_code"] == "ORDERNOTEXIST")
        {
            $succCode = 0;
        } else{
            //如果是系统错误，则后续继续
            $succCode = 2;
        }
        return false;
    }

    /**
     *
     * 撤销订单，如果失败会重复调用10次
     * @param string $out_trade_no
     * @param 调用深度 $depth
     */
    public function cancel($out_trade_no, $depth = 0)
    {
        try {
            if($depth > 10){
                return false;
            }

            $clostOrder = new \WxPayReverse();
            $clostOrder->SetOut_trade_no($out_trade_no);

            $config = new \WxPayConfig();
            $result = \WxPayApi::reverse($config, $clostOrder);


            //接口调用失败
            if($result["return_code"] != "SUCCESS"){
                return false;
            }

            //如果结果为success且不需要重新调用撤销，则表示撤销成功
            if($result["result_code"] != "SUCCESS"
                && $result["recall"] == "N"){
                return true;
            } else if($result["recall"] == "Y") {
                return $this->cancel($out_trade_no, ++$depth);
            }
        } catch(\Exception $e) {
            trace(json_encode($e));
        }
        return false;
    }
}


class HandleNotify extends \PayNotifyCallBack
{
    /**
     *
     * 回包前的回调方法
     * 业务可以继承该方法，打印日志方便定位
     * @param string $xmlData 返回的xml参数
     *
     **/
    public function LogAfterProcess($xmlData)
    {
        trace("call back， return xml:" . $xmlData,'wechat_pay_log');
        return;
    }
}