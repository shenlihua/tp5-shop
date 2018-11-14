<?php
namespace app\common\validate;

use think\Validate;

//创建订单
class OrderCreate extends Validate
{
    protected $rule = [
        'addr_id'  =>  'require|gt:0',
        'pay_id'  =>  'require|gt:0',
        'goods_info'  =>  'require|checkGoodsInfo',
    ];

    protected $message  =   [
        'addr_id.require'   => '请选择收货地址',
        'addr_id.gt'        => '收货地址异常',
        'pay_id.require'    => '请选择支付方式',
        'pay_id.gt'         => '支付方式异常',
        'goods_info'         => '订单商品信息异常',
    ];


    public function checkGoodsInfo($value,$rule,$data,$field,$intro)
    {
        if(!is_array($value)) {
            $value = json_decode($value,true);
        }

        if(!count($value)) {
            return '商品信息异常:struct';
        }

        foreach ($value as $vo) {
            if(!isset($vo['mch_id'])){
                return '商户信息异常:mch_id';
            }
            foreach ($vo['sku_info'] as $sku){
                if(empty($sku['gid'])) {
                    return '商品信息异常:gid';
                }elseif(empty($sku['attr_id'])){
                    return '商品信息异常:attr_id';
                }elseif(empty($sku['num'])){
                    return '商品信息异常:num';
                }
            }

        }


        return true;
    }

}
