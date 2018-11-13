<?php
namespace app\common\model;

use app\common\model\traits\TMch;
use think\model\concern\SoftDelete;

class GoodsModel extends BaseModel
{
    use SoftDelete,TMch;

    protected $name = 's_goods_model';

    public function setAttrAttr($value,$data)
    {
        return trim($value);
    }
}