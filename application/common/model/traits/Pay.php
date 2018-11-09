<?php
namespace app\common\model\traits;

//支付信息扩展
trait Pay{

    //获取订单信息
    public function getOrderInfo($id)
    {
        return empty($id) ? null : $this->find($id);
    }

    //获取主键的值
    public function primaryKeyValue()
    {
        return $this->getKey();
    }

    //获取支付模式
    public function getPayId()
    {
        return $this->pay_id;
    }
    //订单号
    protected function getOrderNo()
    {
        return $this->no;
    }

    //订单body
    protected function getOrderBody()
    {
        return '我是body';
    }

    //订单body
    protected function getOrderMoney()
    {
        return $this->pay_money;
    }


    //订单支付信息
    public function getPayData()
    {
        return [
            'body'          => $this->getOrderBody(),
            'no'            => $this->getOrderNo(),
            'money'         => $this->getOrderMoney(),
            'start_time'    => time(),
            'expire_time'   => time()+600,
            'tag'           =>  'goods',
        ];
    }

    //验证订单是否已完成支付
    public function checkOrderStatus()
    {
        return $this->pay_status==1 ? true : false;
    }


    //处理订单完成支付流程
    public function handleOrderComplete(array $pay_info)
    {
        return $this->save([
            'pay_status'    =>  1,
            'pay_time'      =>  time(),
            'pay_info'      => $pay_info,
        ],[
            ['id','=',$this->id],
            ['pay_status','=',0],
            ['update_time','=',strtotime($this->update_time)]
        ]);
    }

}