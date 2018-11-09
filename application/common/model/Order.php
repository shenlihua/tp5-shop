<?php

namespace app\common\model;

use app\common\model\traits\Pay;
use think\model\concern\SoftDelete;
use think\Validate;

class Order extends BaseModel
{

    use SoftDelete,Pay;

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

    //设置订单编号
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
        $no = date('YmdHis').rand(1000,9999).sprintf('%06d',$number);
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


    //创建订单
    public function actionAdd($input_data, Validate $validate = null)
    {
        if($validate && !$validate->check($input_data)){
            abort(40000,$validate->getError());
        }


        //获取商品信息
        $goods_model =new Goods();
        $goods_info = $input_data['goods_info'];
        if(!is_array($goods_info)) {
            $goods_info = json_decode($goods_info,true);
        }

        $goods_ids = array_column($goods_info,'gid');       //所有商品id
        $goods_attr_id = array_column($goods_info,'attr_id'); //选择的属性
        $goods_num = array_column($goods_info, 'num'); //购买数量
        $comb_goods_num = array_combine($goods_ids,$goods_num); //商品数量信息

        $goods_info = $goods_model->with([
            'linkOnePrice'=>function($query) use($goods_attr_id){
            $query->whereIn('id',$goods_attr_id);
        }
        ,'linkAttr'])->whereIn('id',$goods_ids)->select();


        empty($goods_info) && abort(40001,'创建订单异常');


        //处理订单信息
        list($total_num,$total_money,$pay_money,$dis_money,$freight_money) = $goods_model->handlePayInfo($goods_info,$comb_goods_num);
        //订单信息
        $order_data = [
            'pay_id' => $input_data['pay_id'],
            'total_num' => $total_num,
            'total_money' => $total_money,
            'pay_money' => $pay_money,
            'freight_money' => $freight_money,
            'dis_money' => $dis_money,
            'remark'    => empty($input_data['remark'])?'':trim($input_data['remark']),
        ];
        //商品信息
        $order_goods = [];
//        dump($goods_info);exit;
        foreach($goods_info as $vo) {
            $order_goods[] = [
                'gid'       => $vo['id'],
                'g_name'    => $vo['name'],
                'g_price'   => $vo['link_one_price']['price'],
                'g_number'  => empty($comb_goods_num[$vo['id']])?1:$comb_goods_num[$vo['id']],
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
        $fields = 'id,oid,gid,g_name,g_price,g_number,g_img,g_attr,is_send';
        return $this->hasMany('OrderGoods','oid')->field($fields);
    }

    //发票
    public function linkInvoice()
    {
        return $this->hasOne('OrderInvoice','oid');
    }
}