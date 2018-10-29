<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsCate extends BaseModel
{
    use SoftDelete;

    protected $name = 's_goods_cate';


    public function linkData()
    {
        return $this->hasMany('GoodsCate','pid');
    }
}