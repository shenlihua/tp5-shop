<?php
namespace app\common\validate;

use think\Validate;

class GoodsCate extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:20',
    ];

    protected $message  =   [
        'name.require' => '{%e_goods_cate_name_req}',
        'name.max'     => '{%e_goods_cate_name_max}',
    ];


}
