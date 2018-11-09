<?php
namespace app\common\server\pay;

use think\Model;

include_once __DIR__.'/../../../../vendor/alipay/AopSdk.php';

class AliPay  implements IPay
{
    protected static $PID ='';
    //交易状态
    public static $trade_status = array(
        'WAIT_BUYER_PAY'    => '交易创建，等待买家付款',
        'TRADE_CLOSED'    => '未付款交易超时关闭，或支付完成后全额退款',
        'TRADE_SUCCESS'    => '交易支付成功',
        'TRADE_FINISHED'    => '交易结束，不可退款',
    );



    //通知地址
    public $notify_url;

    //超时时间
    private $timeout_express;

    protected $aop;

    public function __construct($config = [])
    {
        $aop = new \AopClient();
        $aop->gatewayUrl = $config['gateway_url'];
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['merchant_private_key'];
        $aop->alipayrsaPublicKey = $config['alipay_public_key'];
        self::$PID = $config['pid'];
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $this->aop = $aop;
//        dump($aop);exit;
    }


    //支付信息
    public function pay(Model $model, $pay_way)
    {
        //全用大写
        $pay_way = strtoupper($pay_way);
//        $this->model = $model;
        //获取订单支付信息
        $pay_info = $model->getPayData();

//        dump($this->model->pay_info);exit;
        $bizcontent = array(
            'body' => $pay_info['body'],
            'subject' => 'APP支付',
            'out_trade_no' => $pay_info['no'],
            'timeout_express' => $this->timeout_express,
            'total_amount' => $pay_info['money'],
        );


        if($pay_way=='APP') {
           return $this->_appPay($bizcontent);
        }
//        elseif ($pay_way == 'MICROPAY') {
//            $auth_code = isset($this->model->pay_info['auth_code'])?$this->model->pay_info['auth_code']:'';
//            $scene = isset($this->model->pay_info['scene']) && !empty($this->model->pay_info['scene'])?$this->model->pay_info['scene']:'bar_code';
//            empty($auth_code) && E('参数异常:请检测 auth_code');
//            $input = $this->_microPay($bizcontent,$this->model->pay_info['auth_code'],$scene);
//        }
        elseif ($pay_way == 'NATIVE') {
            $input = $this->_nativePay($bizcontent);
        } else {
            abort(40070,'支付方式不存在');
        }

        $result = $this->aop->execute($input);
//        dump($result);exit;
        $api_method_name = $input->getApiMethodName();

        $name = str_replace('.','_',$api_method_name).'_response';
        $result = (array)$result->$name;//返回处理信息

        if($result['code']==10000){
            return $result;
        }elseif ($result['code']==10003){
            $result = $this->_waitPay($result['out_trade_no'],$result['trade_no']);
            return $result;
        } else {
            abort(40071,$result['sub_msg']);

        }
//        dump($result);exit;
    }

    //app支付
    private function _appPay(&$bizcontent)
    {
        $bizcontent['product_code'] = 'QUICK_MSECURITY_PAY';
        //转json，防止中文转义
        $bizcontent = json_encode($bizcontent,JSON_UNESCAPED_UNICODE);

        $request = new \AlipayTradeAppPayRequest();

        $request->setNotifyUrl($this->notify_url);

        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $this->aop->sdkExecute($request);
//            \Think\Log::record($response);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
//            echo htmlspecialchars($response);exit;
        return $response;//就是orderString 可以直接给客户端请求，无需再做处理。
    }

    //条码支付
    private function _microPay(&$bizcontent, $auth_code,$scene='bar_code')
    {
        $bizcontent['product_code'] = 'FACE_TO_FACE_PAYMENT';   //销售产品码
        $bizcontent['auth_code'] = $auth_code;//付款码 用户付款码，25~30开头的长度为16~24位的数字，
        //支付场景  条码支付，取值：bar_code  声波支付，取值：wave_code
        $bizcontent['scene'] = $scene;
//        dump($bizcontent);exit;
        $input = new \AlipayTradePayRequest();
        $input->setBizContent(json_encode($bizcontent,JSON_UNESCAPED_UNICODE));
        return $input;
    }

    //支付二维码
    private function _nativePay(&$bizcontent)
    {
        $input = new \AlipayTradePrecreateRequest ();
        $input->setNotifyUrl($this->notify_url);
        $input->setBizContent(json_encode($bizcontent,JSON_UNESCAPED_UNICODE));
        return $input;
    }


    //回调通知
    public function notify()
    {
        $flag = $this->aop->rsaCheckV1($_POST, NULL, "RSA2");

        //验证成功
        if($flag){
            if($_POST['trade_status'] == 'TRADE_SUCCESS') { //交易完成
                return $_POST;
            }else {
                \Think\Log::Write('支付宝订单交易失败,相关数据:'.json_encode($_POST));
            }
        }else{
            \Think\Log::Write('支付宝签验失败!相关数据:'.json_encode($_POST));
        }
    }

    //设置回调地址
    public function setNotifyUrl($url)
    {
        $this->notify_url = $url;
    }

    /*
     * 设置订单有效期该笔订单允许的最晚付款时间，逾期将关闭交易。
     * 取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     *  该参数数值不接受小数点， 如 1.5h，可转换为 90m。注：若为空，则默认为15d。
     * */
    public function setOrderExpress($second)
    {
        $this->timeout_express = intval($second/60).'m';
    }

    //接收通知后关闭回调支付宝信息
    public function responseInfo()
    {
        return 'success';
    }




    /*
     * 等待付款
     * $param $out_trade_no 商户订单号
     * */
    private function _waitPay($out_trade_no, $trade_no='')
    {
        $request = new \AlipayTradeQueryRequest(); //查询订单

        $query_biz_content = "{" .
            "\"out_trade_no\":\"{$out_trade_no}\"," .
            "\"trade_no\":\"{$trade_no}\"" .
            "  }";
//        dump($query_biz_content);exit;
        $request->setBizContent($query_biz_content);
        //③、确认支付是否成功
        $queryTimes = 3;
        while($queryTimes > 0)
        {
            $queryResult = $this->aop->execute ($request);
            $api_method_name = $request->getApiMethodName();
            $name = str_replace('.','_',$api_method_name).'_response';
            $result = (array)$queryResult->$name;//返回处理信息
//            dump($result);
            //如果需要等待1s后继续
            //交易状态：WAIT_BUYER_PAY（交易创建，等待买家付款）、TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）、TRADE_SUCCESS（交易支付成功）、TRADE_FINISHED（交易结束，不可退款）
            if($result['trade_status'] == 'WAIT_BUYER_PAY'){
                $queryTimes--; //支付定时
                sleep(5);
                continue;
            } else if($result['trade_status'] == 'TRADE_SUCCESS'){//支付成功
                return $result;
            } else {//订单交易失败
                E('订单交易失败，请重新下单',$result['code']);
//                trace('订单交易失败:'.json_encode($result));
//                return false;
            }
        }
        //④、次确认失败，则撤销订单
        $cancel = new \AlipayTradeCancelRequest(); //取消订单
        $cancel->setBizContent($query_biz_content);

        $cancel_response = $this->aop->execute($cancel);
        $api_method_name = $cancel->getApiMethodName();
        $name = str_replace('.','_',$api_method_name).'_response';
        $result = (array)$cancel_response->$name;//返回处理信息
        if($result['code']!='10000')
        {
            E("撤销单失败！",$result['code']);
        }
        E("订单支付超时！",$result['code']);
    }


    /*
     * 退款功能
     * */
    public function handleRefund()
    {

        $request = new \AlipayTradeRefundRequest ();
        $biz_content = array(
            'out_trade_no' => $this->model->back_info['order_no'],
            'refund_amount' => $this->model->back_info['refund_fee'],
            'refund_reason' => isset($this->model->back_info['refund_reason'])
            && !empty($this->model->back_info['refund_reason']) ? $this->model->back_info['refund_reason'] : '申请退款',
//            'out_request_no' => isset($attach['out_request_no'])?$attach['out_request_no']:'',//request()->post('out_request_no'),//标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
        );
        $request->setBizContent(json_encode($biz_content,JSON_UNESCAPED_UNICODE));

        $result = $this->aop->execute ( $request);

        $name = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $result = (array)$result->$name;
        if($result['code']==10000)
        {
            return array(true,'',$result);
        } else {
            return array(false,$result['msg'].'['.$result['code'].']',$result);
        }


    }


    /*
     * 授权
     * */
    public function auth()
    {
        $param = array(
            'apiname' => 'com.alipay.account.auth',
            'app_id' => $this->aop->appId,
            'app_name' => 'mc',
            'auth_type' => 'AUTHACCOUNT',
            'biz_type' => 'openservice',
            'method' => 'alipay.open.auth.sdk.code.get',
            'pid' => self::$PID,
            'product_id' => 'APP_FAST_LOGIN',
            'scope' => 'kuaijie',
            'sign_type' => 'RSA2',
            'target_id' => $this->aop->appId,
        );
        $sign = $this->aop->rsaSign($param,'RSA2');
        $sign = urlencode($sign);
        $info_str = '';
        foreach ($param as $key=>$vo) {
            $info_str .= "$key" . "=" . "$vo&";
        }
        return $info_str.'sign='.$sign;
    }

    /*
     * 获取用户信息
     * */
    public function authUserInfo($auth_code)
    {
        //根据返回的auth_code换取access_token
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($auth_code);
        $result = $this->aop->execute($request);
//        \Think\Log::write('回调2222333:'.$result);
//        \Think\Log::write('回调2222333:'.json_encode($result));
        $access_token = $result->alipay_system_oauth_token_response->access_token;
//        dump($access_token);exit;
//        \Think\Log::write('回调:'.$access_token);
        $request = new \AlipayUserInfoShareRequest ();
        $result = $this->aop->execute ( $request , $access_token );
//        dump($result);exit;
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
//        \Think\Log::write('回调2222:'.$responseNode);
//        \Think\Log::write('回调2222333:'.json_encode($responseNode));
        $resultCode = $result->$responseNode->code;
//        \Think\Log::write('回调22223212333:'.$resultCode);
        if(!empty($resultCode)&&$resultCode == 10000){
            return $result->$responseNode;
        } else {
            return false;
        }
    }

}