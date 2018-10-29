<?php
namespace app\common\validate;

use think\Validate;

class GoodsModel extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:20|unique:s_goods_model',
        'attr' =>  'require',
    ];

    protected $message  =   [
        'name.require' => '{%e_goods_model_name_req}',
        'name.max'     => '{%e_goods_model_name_max}',
        'name.unique'  => '{%e_goods_model_name_unique}',
        'attr.require'=> '{%e_goods_model_attr_req}',
    ];


}
