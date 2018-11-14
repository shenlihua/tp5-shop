<?php
namespace app\common\model\traits;

//支付信息扩展
trait Pay{
    protected $order_info;

    protected $primary_keys='';

    protected $pay_id;

    protected $order_no;

    protected $pay_money;

    protected $body;


    //获取订单信息
    public function getOrderInfo()
    {
        return $this->order_info;
    }

    /*
     * 设置订单的商品信息
     * @param $id string|array 订单主键id
     * */
    public function setOrderInfo($id)
    {
        if(empty($id)){
            return null;
        }

        $this->order_info = $this->whereIn($this->getPk(),$id)->select();

    }

    /*
     * 处理订单支付信息--合单问题处理
     * */
    public function handleOrderPayInfo()
    {
        $order_info = isset($this->order_info[0])?$this->order_info[0] : [];
        empty($order_info) && abort(200,'订单信息异常');
        $this->order_no = $order_info[$this->getFieldOrderNo()];
        $this->pay_id = $order_info[$this->getFieldPayId()];
        $this->primary_keys = $order_info[$this->getPk()];
        $this->pay_money = $order_info[$this->getFieldOrderMoney()];
        $this->setOrderBody();
        //订单总数量
        $order_count = $this->order_info->count();

        if($order_count > 1) {//多笔订单
            $order_info = $this->order_info->toArray();
            $keys = array_column($order_info,$this->getPk());
            $this->primary_keys = implode('_',$keys);
            $this->order_no .= sprintf('%02d',$order_count);
            $pay_moneys = array_column($order_info,$this->getFieldOrderMoney());
            $this->pay_money = array_sum($pay_moneys);
        }
    }



    //获取主键的值
    public function getPrimaryKeyValue()
    {
        return $this->primary_keys;
    }

    //设置
    public function setPrimaryKeyValue($keys)
    {
        $this->primary_keys = $keys;
    }

    //获取支付模式
    public function getFieldPayId()
    {
        return 'pay_id';
    }

    //获取支付模式
    public function getPayId()
    {
        return $this->pay_id;
    }
    //设置支付方式
    public function setPayId($pay_id)
    {
        $this->pay_id = $pay_id;
    }

    //获取订单号字段
    public function getFieldOrderNo()
    {
        return 'no';
    }

    //订单号
    public function getOrderNo()
    {
        return $this->order_no;
    }
    //订单号-设置
    public function setOrderNo($order_no)
    {
        $this->order_no = $order_no;
    }

    //订单body
    public function getOrderBody()
    {
        return $this->body;
    }
    //设置body
    public function setOrderBody($body='我是body')
    {
        $this->body = $body;
    }

    //订单支付金额字段
    public function getFieldOrderMoney()
    {
        return 'pay_money';
    }
    //订单body
    public function getOrderMoney()
    {
        return $this->pay_money;
    }

    //订单支付金额
    public function setOrderMoney($pay_money)
    {
        $this->pay_money=$pay_money;
    }


    //订单支付信息
    public function getPayData()
    {
        return [
            'body'          => $this->getOrderBody(),
            'no'            => $this->getOrderNo(),
            'money'         => $this->getOrderMoney(),
            'tag'           =>  'goods',
        ];
    }

    //验证订单是否已完成支付
    public function checkOrderStatus()
    {
        return false;
    }


    //处理订单完成支付流程
    public function handleOrderComplete(array $pay_info)
    {
        $order_info = $this->order_info->toArray();
        $ids = array_column($order_info,$this->getPk());
        $ids = implode(',',$ids);

        return $this->save([
            'pay_status'    =>  1,
            'pay_time'      =>  time(),
            'pay_info'      => $pay_info,
        ],[
            ['pay_status','=',0],
            [$this->getPk(),'in', $ids]

        ]);
    }

}