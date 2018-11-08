<?php
namespace app\index\controller;

class Index extends Common
{
    public function index()
    {
        $goods_list = model('goods')->with(['linkOnePrice'=>function($query){
            $query->group('gid');
        }])->limit(8)->select();
//        dump($goods_list);exit;
        return view('index',[
            'goods_list' => $goods_list
        ]);
    }
}
