<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsCart extends BaseModel
{
    protected $name = 's_goods_cart';

    protected $autoWriteTimestamp = false;

    /*
     * 获取购物车信息
     * */
    public function cartInfo()
    {
        $attr_ids = [];
        $goods_model = new Goods();
        $goods_list = $goods_model
            ->withJoin([
                'linkGoodsCart',
                'linkOnePrice'=>function($query){ $query->where('linkOnePrice.id = linkGoodsCart.attr_id'); },
                'linkMerchant'
            ],'left')
            ->where('linkGoodsCart.uid','=',app('container_user_id'))
            ->select();
        if(empty($goods_list)) {
            return [];
        }
        $goods_model->handleFullGoodsInfo($goods_list);
//        $list = $goods_list->toArray();
        $cart_list = $goods_model->handleMerchantGoodsInfo($goods_list);
//        dump($goods_list);exit;
//        print_r($cart_list);exit;
        return $cart_list;
    }

    /*
     * 添加购物车
     * @param $gid int 商品id
     * @param $attr_id int sku id
     * @param $num int 数量
     * */
    public function addCart($gid,$attr_id,$num=1)
    {
        $where =  [
            ['gid','=',$gid],
            ['uid','=',app('container_user_id')],
            ['attr_id','=',$attr_id],
        ];
//        dump($where);
        //查看商品信息
        $goods_info = model('goods')->withJoin(['linkOnePrice'=>function($query)use($attr_id){
            $query->where('id',$attr_id);
        }],'right')->where('goods.id',$gid)->find();

        if(empty($goods_info)){
            return [false,'商品不存在'];
        }elseif($goods_info['status']!=1) {
            return [false,'商品已下架'];
        }elseif($goods_info['link_one_price']['stock']<=0 || $goods_info['link_one_price']['stock']<$num) {
            return [false,'商品库存不足'];
        }

        $cart_info = $this->where($where)->find();
        if($cart_info) {
            $this->where($where)->setInc('num',$num);
        }else{

            $save =  [
                'mch_id'=>$goods_info['mch_id'],
                'gid'=>$gid,
                'uid'=>app('container_user_id'),
                'attr_id'=>$attr_id,
                'num'=>$num,
            ];
            $this->save($save);
        }

        return [true,''];
    }



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
    //关联商品
    public function linkMerchant()
    {
        return $this->belongsTo('Merchant','mch_id');
    }
}