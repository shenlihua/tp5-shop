<?php
namespace app\common\server\pay;

/*
 * 获取订单支付信息
 * */

use app\common\model\traits\Pay;
use think\Model;

class ThirdServer
{
    private $third_server;
    //第三方配置信息
    protected $third_config=[];





    public static $pay_class =[
        [],
        //微信支付
        [
            'key' => 'wechat',
            'name' => '微信',
            'lang' => 'g_pay_wechat',
            'class'=>'\app\common\server\pay\Wechat',
            'config'=>[
                //键的值仅供测试使用
                'appid'=>'wx426b3015555a46be',
                'mchid'=>'1900009851',
                'key'=>'8934e7d15453e97507ef794cf7b0519d',
                'appsecret'=>'7813490da6f1265e4901ffb80afaa36f',
            ]
        ],
        //支付宝
        [
            'key'=>'alipay',
            'name' => '支付宝',
            'lang' => 'g_pay_alipay',
            'class'=>'\app\common\server\pay\AliPay',
            'config'=>[
                'gateway_url'   =>  'https://openapi.alipay.com/gateway.do',
                'app_id' =>  '2018081561077542',
                'merchant_private_key'   =>  'MIIEpAIBAAKCAQEA2px8Pe9XsFlCfSKyZ3CvFYzhNSp57rQcWRsb7nBEMyE7p/Rn6U5vhfbuhcZYHefBygOmrAOBjqZR/CiWrX60Jrt4uLJV8GnUz8qYCzSiUecXPiWNvgWYJDBDq9/J+VjCwDZGHXduAuvYO9M2sizO+Mu6+D5d/dYJRUz56+0jWzhU7DUn0D0dm5vNOrrF2S/SW+PM4cnUiQpL8XzYv2wtemOeie2p4kfjFasKkT1XlzHuFipGhAwLLAg9okkK+U9H3CIFUcQdy4a6KqJaUHCusf9lRXmLMXrX5jQ4JP2t02LDMdVP3du06qzXDByE0F8LEMvOXg0SkS7cS/m2s+azswIDAQABAoIBAQC6Fb5EUjnIaRKJxkEqf+bpZzREt1V3dWU0DDloa7QB+eX3/7l8BUI1PI6o3L8hJaf1t8nmXk2oCbd792jbYiEjXy1Ruqwgq1V1UzS9RR5urVFBm2HeQJDTLSpNYX9hesa8KvbgGktQvUUhF5dVb4L2kSUkLqJ/cfQOQ4nx47fW6redUm7v4+ugxy58usRBptfFvNzEL6Cf2uEa1wdgXSDlhncOeRKg3OprWKR66v1IxUpXzYI9aHO3YncgYiSrZ/u0Ei05CY5MnlukpQt79LChycvAZLrKciAFP9BUhigAPg1bYY1gJNxBfoezlFhxhhxvKizRTSoSYUmzbXBil+LRAoGBAPQb1d9B2bpGtnvMgA4A2OD/GSPEUWXDS04OSUpoNr0FV0Rmnz/w8d1hqtPaHM4IQUuqWhlY6//8Qt6FZmliJkr4H+zVJIFXfqsoVzRvf0JLXyRJ2HJX3qbZIryDWopMTY462w0ZiCvXeE+n2pcBlljDETewJdTmGTcS3g9hEak9AoGBAOVCrt+MzkIO9Muil8cf2VhyQYMyP1n37JCgIzyWIVyCtTuGdHOIsPzv325TkT3Lr6GZa0PQvNygsgWpLFyjhRCfeiVuaZfH59MU5zos+OQ+fSsxky/j8w7+n3xdNFJxg7OrLpu2LECH8eHnZD6/npad/LAo3oiufhSogSc3xj+vAoGAMC/ieyzblwrocO3AhYMjTN7ujoHtc4ImtmTZkOebjpqkTGtZmlys0f+6ohFXSFCW2yHooUJNa/3XEWgR/FGUr43ld3Hnv+SOq6jN9hrgayzHvjkZkhDIcfVBByeC0vQXBzGVFjPpDoZ9SHQhEVgN3r27A1wxS6KUbau6zBxkSR0CgYEA2gces67/AMekeny4h0B8vKZ8Mz74DKHBMpF4sUe9lnMf0+o8mRtn5kRhYlTLDc/FyZNT9XxwAAo7AzMt4zQHehWQXkSw2w3cJFv+ws6+LKJY+1j8N1E7eAkit20UM0uNCrNDcgY/82bV3BbouNwW//pjs0xKxgXcFd1iZl+ehDUCgYBoV4jNOQ2yqA5R0jrlKmse+R0hezAlklasR4BAlQ6Vj3QGmgnI44e92TLRhjPaAL+lCEvtuClPpgqoqkNpVYH1MpF8sKV6Urd4Seetpcli7BrWyk2KIn6fe7jAGUH9S89Nlmezky92i5d2Lx2QBrVrUW2z3NDJIHLDct1BySU+OA==',
                'alipay_public_key'   =>  'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAg3UPtirxaXFfOTpyajoU+lBROUdnZdcz5TPiwsUDpgAjHjJ1i/ivVnlr479jG9rqlQ9J2bs+9L8nuV0HV91tuoP5h0HUCXq5UqdvNSZ13oaS3XoHYK8f/bwz63EYu2qogTI45OHkqBc7SCy59cbJK4+DmgyQIxpo2YOykjuKKvC6HBA08AS8HWz2LQdAyMK4VxBk1tmALRQwkZruULmXb5zZY5vq0O3DGRAGYibF6hvBeLtyOqolvWKghkQcDatzCugAwKVYi/fiuzPqknZxXVgZJJ6E4QZinlNB3751HmLP4xzUqZuvwfSN/w73LyiQNSYUEh3693ardnoJjxqu3wIDAQAB',
                'pid'   =>  '',
            ]
        ]
    ];


    /*
     * 支付信息
     * @param $opt_obj Model 模型对象
     * @param $pay_mode string 支付模式 APP-app支付 NATIVE--二维码
     * */
    public function payInfo(Model $opt_obj,$pay_mode)
    {
        //获取第三方模式
        $third_id = $opt_obj->getPayId();
        //创建服务对象
        $this->_createServer($third_id);
        //设置回调通知地址
        $obj_class_name = class_basename($opt_obj);
        $notify_url = url('Index/Order/notify',['order_id'=>$opt_obj->getPrimaryKeyValue(),'model'=>$obj_class_name],false,true);
//        dump($notify_url);exit;
        //设置通知地址
        $this->third_server->setNotifyUrl($notify_url);
        //设置订单有效时长
        $this->third_server->setOrderExpress(1800);
        //支付操作

        $result = $this->third_server->pay($opt_obj,$pay_mode);

        return $result;
    }


    /*
     * 处理回调
     * @param $opt_obj Model 模型对象
     * @param $pay_mode string 支付模式 APP-app支付 NATIVE--二维码
     * */
    public function payNotify(Model $opt_obj)
    {
        //获取第三方模式
        $third_id = $opt_obj->getPayId();
        //创建服务对象
        $this->_createServer($third_id);
        //支付操作
//        try{
            //验证支付是否完成
            $state = $opt_obj->checkOrderStatus();
            if(!$state) {

                $pay_info = $this->third_server->notify();
                //处理通知数据
                $opt_obj->handleOrderComplete($pay_info);
            }
            //回调响应
            return $this->third_server->responseInfo();
//        } catch (\Exception $e){
//            trace(40060,'通知回调异常:'.$e->getMessage());
//
//        }
    }

    /*
     * 获取配置信息
     * */
    public function getConfig()
    {
        return model('ThirdConfig')->getContent($this->third_config['key']);
    }

    private function _createServer($third_id)
    {
        if(empty($third_id) || empty(self::$pay_class[$third_id])){
            abort(40040, lang('e_class_no_exist'));
        }

        $this->third_config = self::$pay_class[$third_id];
        //获取配置信息
        $config = $this->getConfig();
        //合并配置
        if(!empty($config)) {
            $this->third_config['config']=array_merge($this->third_config['config'], $config);
        }

        $class = $this->third_config['class'];
        $this->third_server = new $class($this->third_config['config']);
    }
}



