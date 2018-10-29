<?php
namespace app\common\validate;

use think\Validate;

class GoodsModelAttr extends Validate
{
    protected $rule = [
        'mid'   =>  'require|gt:0',
        'name'  =>  'require|max:20',
    ];

    protected $message  =   [
        'name.require'  => '{%e_goods_model_attr_name_req}',
        'name.max'      => '{%e_goods_model_attr_name_max}',
        'mid.require'   => '{%e_goods_model_attr_attr_req}',
        'mid.gt'        => '{%e_goods_model_attr_attr_gt}',
    ];


}
