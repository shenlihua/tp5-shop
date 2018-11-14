<?php
namespace app\index\controller;

class Goods extends Common
{
    public function detail()
    {
        $id = $this->request->param('id',0,'intval');
        $goods_info = model('Goods')->with(['linkAttr.linkModelAttr','linkPrice'])->where('id','=',$id)->find();
        $goods_stock = $goods_attr =[];
        //商品库存
        $goods_info['link_price'] && $goods_stock = $goods_info['link_price'];
        //商品属性
        $goods_info['link_attr'] && $goods_attr = $goods_info['link_attr']->toArray();
        $goods_attr = array_column($goods_attr,null,'id');
//        dump($goods_attr);
        $attr_info = [];
        foreach ($goods_stock as $vo) {
            $attr_info = array_merge($attr_info,$vo['attr_info']);
        }
        $attr_info = array_unique($attr_info);

        $property = [];
        foreach ($attr_info as $ai) {
            if(isset($goods_attr[$ai])) {
                $a_id = $goods_attr[$ai]['aid'];//属性id
                if(array_key_exists($a_id,$property)){
                    $property[$a_id]['data'][] = [
                        'id'    => $ai,
                        'name'  => $goods_attr[$ai]['val']
                    ];
                }else{
                    $property[$a_id] = [
                        'name'  => $goods_attr[$ai]['link_model_attr']['name'],
                        'data'  => [[
                            'id'    => $ai,
                            'name'  => $goods_attr[$ai]['val']
                        ]]
                    ];
                }

            }
        }
        $property = array_values($property);
//        dump($property);exit;
        return view('detail',[
            'goods_info' => $goods_info,
            'property' => $property,
        ]);
    }

    /*
     * 购物车
     * */
    public function cart()
    {
        //sku属性id
        $model = model('GoodsCart');

        $cart_list = $model->cartInfo();

        return view('cart',[
            'cart_list' => $cart_list
        ]);
    }

    /*
     * 购物车添加
     * */
    public function addCart()
    {
        $goods_id = $this->request->param('gid',0,'intval');
        $attr_id = $this->request->param('attr_id',0,'intval');
        $goods_num = $this->request->param('num',1,'intval');

        empty($goods_id) && abort(200,'商品信息异常');
        empty($attr_id) && abort(200,'商品sku信息异常');
        //查询


        $model = model('GoodsCart');
        list($state,$msg) = $model->addCart($goods_id,$attr_id,$goods_num);

        return ['code'=>(int)$state,'msg'=>!$state?$msg:'添加成功'];

    }


    /*
     * 处理商品立即跳转
     * 业务逻辑判断跳转那个页面
     * */
    public function goodsRedirect()
    {
        $input_data = $this->request->param();
        $this->redirect('order',$input_data);
    }



    /*
     * 预览订单
     * */
    public function order()
    {
        if($this->request->has('goods')){

            $goods = $this->request->param('goods');
            $goods = explode(',',$goods);
            $gid = $attr_id = $number =$goods_num = [];
            foreach ($goods as $vo) {
                $arr = explode('_',$vo);
                if(!empty($arr[0]) && !empty($arr[1]) && !empty($arr[2])) {
                    $gid[] = $arr[0];
                    $attr_id[] = $arr[1];
                    $number[] = $arr[2];
                    //商品库存信息
                    $goods_num[$arr[0].'_'.$arr[1]] =$arr[2];
                }
            }


        } else {
            $gid = $this->request->param('gid',0,'intval');
            $attr_id =$this->request->param('attr_id',0,'intval');
            $number =$this->request->param('buy_num',1,'intval');
            //商品库存信息
            $goods_num = [$gid.'_'.$attr_id=>$number];
        }


        $goods_model = new \app\common\model\Goods();
//        dump($gid);
//        dump($attr_id);
//        dump($number);
//        dump($goods_num);

        $goods = $goods_model->withJoin([
            //关联库存信息
            'linkOnePrice'=>function($query)use($attr_id){
                $query->whereIn('linkOnePrice.id', $attr_id);
            },
            'linkMerchant'
        ],'left')->whereIn('Goods.id',$gid)->select();
//        print_r($goods);exit;
        //获取商品数据--支付
        list($number,$total_money,$pay_money,$dis_money,$freight_money) = $goods_model->handlePayInfo($goods,$goods_num);
        //拼接商品属性等信息
        $goods_model->handleFullGoodsInfo($goods);
        //按店铺拆分商品信息
        $goods = $goods_model->handleMerchantGoodsInfo($goods);
//        foreach ($goods as $key=>$vo){
//            dump($key);
//        }
//        dump($goods);exit;
        return view('order',[
            'number' => $number,
            'total_money' => $total_money,
            'pay_money'  => $pay_money,
            'dis_money'  => $dis_money,
            'freight_money'  => $freight_money, //运费
            'goods_list' => $goods
        ]);
    }

    /*
     * 订单支付
     * */
    public function pay()
    {
        return view('pay',[

        ]);
    }
}
