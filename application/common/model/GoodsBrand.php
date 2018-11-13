<?php
namespace app\common\model;

use app\common\model\traits\TMch;
use think\model\concern\SoftDelete;

class GoodsBrand extends BaseModel
{
    use SoftDelete,TMch;

    protected $name = 's_goods_brand';
}