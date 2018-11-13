<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsCart extends BaseModel
{
    protected $name = 's_goods_cart';

    protected $autoWriteTimestamp = false;

    //关联商品属性
    public function linkStock()
    {
        return $this->belongsTo('GoodsAttrPrice','attr_id');
    }
    //关联商品
    public function linkGoods()
    {
        return $this->belongsTo('Goods','gid');
    }
}