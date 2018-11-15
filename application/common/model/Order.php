<?php

namespace app\common\model;

use app\common\model\traits\Pay;
use app\common\model\traits\TMch;
use think\model\concern\SoftDelete;
use think\Validate;

class Order extends BaseModel
{

    use SoftDelete,Pay,TMch;


    protected $name = 's_order';
    //新增入库操作
    protected $insert = ['no','receive_addr'];

    //订单状态
    public static $btn_order_status = ['g_b_order_all','g_b_order_wait_pay','g_b_order_wait_auth','g_b_order_wait_send'];
    //订单审核状态
    public static $fields_is_auth =['f_order_is_auth_no','f_order_is_auth_yes','f_order_is_auth_refund'];
    //订单支付状态
    public static $fields_is_pay =['f_order_is_pay_no','f_order_is_pay_yes'];
    //订单支付发货
    public static $fields_is_send =['f_order_is_send_no','f_order_is_send_yes'];


    //设置订单编号--最短 22
    public function setNoAttr($value)
    {
        if($value) {
            return $value;
        }

        $cache_name = 'order_no'.date('Y-m-d');
        $number = cache($cache_name);
        if(!$number) {
            $number = 0;
            cache($cache_name,$number,86400);
        }
        $number = $number+1;
        //再次缓存
        cache($cache_name,$number);
        $no = date('YmdHis').rand(100,999).sprintf('%05d',$number);
        return $no;
    }

    //设置收货地址
    public function setReceiveAddrAttr($value,$data)
    {
        if(empty($data['addr_id'])) {
            return;
        }

        $this->data([
            'rec_name'  => 'rec_name',
            'rec_phone'  => 'rec_name',
            'province'  => 'rec_phone',
            'city'  => 'city',
            'area'  => 'area',
            'addr'  => 'addr',
        ]);

        return;
    }


    /*
     * 创建订单
     * @param $goods_id array 商品id
     * @param $attr_id array 对应商品属性
     * @param $number array 购买数量
     * */
    public function createOrder(array $goods_id,array $attr_id,array $number)
    {
        $comb_goods_num = [];
        foreach ($goods_id as $key=>$vo) {
            if(!empty($attr_id[$key]) && !empty($number[$key])){
                $str_key = $vo.'_'.$attr_id[$key];
                $comb_goods_num[$str_key] = $number[$key];
            }
        }

        //获取商品信息
        $goods_model =new Goods();

        $goods_info = $goods_model->withJoin([
            'linkOnePrice'=>function($query) use($attr_id){
            $query->whereIn('linkOnePrice.id',$attr_id);
        }
        ,'linkAttr'],'right')->whereIn('goods.id',$goods_id)->select();

        empty($goods_info) && abort(40001,'创建订单异常');


        //处理订单信息
        list($total_num,$total_money,$pay_money,$dis_money,$freight_money) = $goods_model->handlePayInfo($goods_info,$comb_goods_num);

        //订单信息
        $order_data = [
            'total_num' => $total_num,
            'total_money' => $total_money,
            'pay_money' => $pay_money,
            'freight_money' => $freight_money,
            'dis_money' => $dis_money,

        ];
        //商品信息
        $order_goods = [];
//        dump($goods_info);exit;
        foreach($goods_info as $vo) {
            $order_goods[] = [
                'gid'       => $vo['id'],
                'g_name'    => $vo['name'],
                'g_price'   => $vo['link_one_price']['price'],
                'total_price'   => $vo['total_price'],
                'g_number'  => $vo['number'],
                'g_img'     => $vo['cover_img'],
                'g_attr'    => implode(PHP_EOL,$vo['link_one_price']['attr_info_name']),
                'info'      => $vo,
            ];
        }
        try{
            $this->startTrans(); //开启事务
            //保存订单的
            $this->save($order_data);
            //保存商品信息
            $this->linkGoods()->saveAll($order_goods);
            //保存发票信息


            $this->commit();//提交事务
        }catch (\Exception $e) {
            $this->rollback();//关闭事务
            abort(40002,'创建订单异常'.$e->getMessage());
        }




    }

    /*
     * 创建--商户订单
     * @param $mch_id int 商户id
     * @param $goods_id array 商品id
     * @param $attr_id array 对应商品属性
     * @param $number array 购买数量
     * */
    public function createMerchantOrder($mch_id,array $goods_id,array $attr_id,array $number)
    {
        $comb_goods_num = [];
        foreach ($goods_id as $key=>$vo) {
            if(!empty($attr_id[$key]) && !empty($number[$key])){
                $str_key = $vo.'_'.$attr_id[$key];
                $comb_goods_num[$str_key] = $number[$key];
            }
        }

        //获取商品信息
        $goods_model =new Goods();

        $goods_info = $goods_model->withJoin([
            'linkOnePrice'=>function($query) use($attr_id){
            $query->whereIn('linkOnePrice.id',$attr_id);
        }
        ,'linkAttr'],'right')->whereIn('goods.id',$goods_id)->select();

        empty($goods_info) && abort(40001,'创建订单异常');


        //处理订单信息
        list($total_num,$total_money,$pay_money,$dis_money,$freight_money) = $goods_model->handlePayInfo($goods_info,$comb_goods_num);
        //处理商品属性信息
        $goods_model->handleFullGoodsInfo($goods_info);
        //订单信息
        $order_data = [
            'mch_id'       => $mch_id,
            'total_num' => $total_num,
            'total_money' => $total_money,
            'pay_money' => $pay_money,
            'freight_money' => $freight_money,
            'dis_money' => $dis_money,

        ];
        //商品信息
        $order_goods = [];
//        dump($goods_info);exit;
        foreach($goods_info as $vo) {
            $order_goods[] = [
                'mch_id'       => $mch_id,
                'gid'       => $vo['id'],
                'attr_id'       => $vo['link_one_price']['id'],
                'g_name'    => $vo['name'],
                'g_price'   => $vo['link_one_price']['price'],
                'total_price'   => $vo['total_price'],
                'g_number'  => $vo['number'],
                'g_img'     => $vo['cover_img'],
                'g_attr'    => implode(PHP_EOL,$vo['link_one_price']['attr_info_name']),
                'info'      => $vo,
            ];
        }
        try{
            $this->startTrans(); //开启事务
            //保存订单的
            $this->save($order_data);
            //保存商品信息
            $this->linkGoods()->saveAll($order_goods);
            //保存发票信息


            $this->commit();//提交事务
        }catch (\Exception $e) {
            $this->rollback();//关闭事务
            abort(40002,'创建订单异常'.$e->getMessage());
        }
    }

    //商品
    public function linkGoods()
    {
        $fields = 'id,oid,gid,g_name,g_price,g_number,g_img,g_attr,is_send,total_price';
        return $this->hasMany('OrderGoods','oid')->field($fields);
    }

    //发票
    public function linkInvoice()
    {
        return $this->hasOne('OrderInvoice','oid');
    }
}