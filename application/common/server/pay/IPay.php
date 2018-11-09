<?php
namespace app\common\server\pay;



use Think\Model;

interface IPay
{
    function __construct($config=[]);

    //设置回调通知地址
    function setNotifyUrl($url);
    //支付
    function pay(Model $model,$pay_style);
    //通知回调
    function notify();
    //返回通知结果
    public function responseInfo();
}