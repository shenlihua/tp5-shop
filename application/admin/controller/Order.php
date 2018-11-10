<?php
namespace app\admin\controller;

use app\common\server\pay\ThirdServer;

class Order extends Shop
{
    public function index()
    {
        $order_state = $this->request->param('order_state',0,'intval');
        $order_model = new \app\common\model\Order();
        $list = $order_model->order('id', 'desc')->paginate();
        return view('index',[
            'list'  => $list,
            'page'  => $list->render(),
            'pay_way' => ThirdServer::$pay_class,
            'btn_order_status' => $order_model::$btn_order_status,
            'order_state'   => $order_state
        ]);
    }

    //详情
    public function detail()
    {
        $id = $this->request->param('id',0,'intval');
        $order_model = new \app\common\model\Order();
        $order_model = $order_model->with(['linkGoods','linkInvoice'])->where('id','=',$id)->find();
//        dump($order_model);exit;
        return view('detail',[
            'order_model' =>$order_model
        ]);
    }
}

