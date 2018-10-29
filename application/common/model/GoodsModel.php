<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsModel extends BaseModel
{
    use SoftDelete;

    protected $name = 's_goods_model';

    public function setAttrAttr($value,$data)
    {
        return trim($value);
    }
}