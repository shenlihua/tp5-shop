<?php
namespace app\common\validate;

use think\Validate;

class GoodsBrand extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:20',
    ];

    protected $message  =   [
        'name.require' => '{%e_goods_brand_name_req}',
        'name.max'     => '{%e_goods_brand_name_max}',
    ];
}
