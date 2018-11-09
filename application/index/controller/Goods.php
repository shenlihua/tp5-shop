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
        return view('cart',[

        ]);
    }

    /*
     * 预览订单
     * */
    public function order()
    {
        $gid = $this->request->param('gid',0,'intval');
        $attr_id =$this->request->param('attr_id',0,'intval');
        $goods_model = new \app\common\model\Goods();

        $goods = $goods_model->with(['linkOnePrice'=>function($query)use($attr_id){
            $query->where('id','=',$attr_id);
        }])->where('id','=',$gid)->select();
        //获取商品数据
        list($number,$total_money,$pay_money,$dis_money,$freight_money) = $goods_model->handlePayInfo($goods);


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
