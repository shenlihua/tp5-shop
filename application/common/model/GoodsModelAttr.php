<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsModelAttr extends BaseModel
{
    use SoftDelete;

    public static $fields_cate = ['spu','sku'];
    public static $fields_type = ['自定义属性','枚举值'];

    protected $name = 's_goods_model_attr';

//    public function setKyeAttr($value,$data)
//    {
//
//    }
}