<?php
namespace app\index\controller;

class Order extends Common
{

    //创建订单
    public function create()
    {
        $input_data = $this->request->post();
        $validate = new \app\common\validate\OrderCreate();
        $order_model = new \app\common\model\Order();
//        dump($input_data);exit;
        try{

            $order_model->actionAdd($input_data,$validate);
        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->redirect('Order/pay',['order_id'=>$order_model->id]);

    }

    //订单支付
    public function pay()
    {
        $order_id = $this->request->param('order_id',0,'intval');
        $order_model = new \app\common\model\Order();

        $order_model = $order_model->getOrderInfo($order_id);
        if(empty($order_model)) {
            $this->error('订单信息异常');
        }

        $pay_server = new \app\common\server\pay\ThirdServer();
        try{
            $pay_mode = 'NATIVE';
            $result = $pay_server->payInfo($order_model, 'NATIVE');
        }catch (\Exception $e) {
            return $this->display($pay_mode.'支付信息异常'.$e->getMessage());
        }
        return view('pay',[
            'order_model'   => $order_model,
            'code_url'      => base64_encode($result['code_url'])
        ]);
    }


    //订单回调
    public function notify()
    {
        $order_id = $this->request->param('order_id',0,'intval');
        $model = $this->request->param('model','','trim');
        $class = '\\app\\common\\model\\'.$model;
        $order_model = new $class();

        $order_model = $order_model->getOrderInfo($order_id);
        if(empty($order_model)) {
            return '订单信息异常';
        }

        $pay_server = new \app\common\server\pay\ThirdServer();

        return $pay_server->payNotify($order_model);


    }

}