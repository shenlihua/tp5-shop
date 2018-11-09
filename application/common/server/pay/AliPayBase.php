<?php
namespace app\common\server\pay;

class AliPayBase
{
    protected $aop;





//    const NAME = 'alipay';

//    protected  static $PID = '';

//    public function __construct()
//    {
//        $conf_info = D('SystemCache')->getContent('alipay_conf');
//        $conf_info = json_decode($conf_info,true);
////        dump($conf_info);
//        vendor('alipay.AopSdk');
//        $aop = new \AopClient();
//        $aop->gatewayUrl = isset($conf_info['gateway_url'])?$conf_info['gateway_url']:"";
//        $aop->appId = isset($conf_info['app_id'])?$conf_info['app_id']:"";
//        $aop->rsaPrivateKey = isset($conf_info['merchant_private_key'])?$conf_info['merchant_private_key']:"";
//        $aop->format = "json";
//        $aop->charset = "UTF-8";
//        $aop->signType = "RSA2";
//        $aop->alipayrsaPublicKey = isset($conf_info['alipay_public_key'])?$conf_info['alipay_public_key']:"";
//        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
//        $this->aop = $aop;
//        self::$PID=isset($conf_info['pid'])?$conf_info['pid']:"";
////        dump($aop);exit;
//    }

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
        vendor("Alipay.AlipaySystemOauthTokenRequest");//调用sdk里面的AlipaySystemOauthTokenRequest类
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