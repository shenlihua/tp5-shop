<?php
namespace app\common\validate;

use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'cid'        =>  'require|gt:0',
        'name'       =>  'require|max:150',
        'cover_img'  =>  'require',
        'mid'       =>  'require',
        'sku'        =>  'require|checkSkuInfo'
    ];

    protected $message  =   [
        'cid.require'   => '{%e_goods_cid_req}',
        'cid.gt'        => '{%e_goods_cid_gt}',

        'name.require'  => '{%e_goods_name_req}',
        'name.max'      => '{%e_goods_name_max}',
        'cover_img.require'     => '{%e_goods_cover_img_req}',
        'e_goods_mid.require'   => '{%e_goods_mid_req}',
        'sku.require'           => '{%e_goods_sku_req}',
    ];

    protected $scene = [
        'admin_add' => ['cid','name','cover_img','e_goods_mid','sku'],
    ];

    public function checkSkuInfo($value,$rule,$data,$field,$intro)
    {
//        dump($data);exit;
        return true;
    }
}
