<?php
namespace app\common\model;

use app\common\model\traits\TMch;
use think\model\concern\SoftDelete;

class GoodsCate extends BaseModel
{
    use SoftDelete,TMch;

    protected $insert=[];

    protected $name = 's_goods_cate';



    public function linkData()
    {
        return $this->hasMany('GoodsCate','pid');
    }

}