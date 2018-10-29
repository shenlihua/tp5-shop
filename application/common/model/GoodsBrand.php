<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsBrand extends BaseModel
{
    use SoftDelete;

    protected $name = 's_goods_brand';
}