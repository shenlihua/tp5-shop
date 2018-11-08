<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class GoodsAttr extends BaseModel
{

    protected $name = 's_goods_attr';

    public function linkModelAttr()
    {
        return $this->belongsTo('GoodsModelAttr','aid');
    }
}