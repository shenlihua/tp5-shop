<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class GoodsAttrPrice extends BaseModel
{

    protected $name = 's_goods_attr_price';

    public function getAttrInfoAttr($value)
    {
        return $value?explode('|',$value):[];
    }

    public function linkAttr()
    {
        return $this->belongsTo('GoodsAttr','attr_info');
    }

}