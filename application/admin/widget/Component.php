<?php
namespace app\admin\widget;

use think\Controller;

class Component extends Controller
{
    public function menu($slider_bar,$module='')
    {
        $node_model = model('SysNode');
        $where = [
            ['module','=',$module],
            ['status','=',1],
            ['pid','=',0]
        ];
        $fields = ['id','name','url','pid','module','icon'];
        $list = $node_model->field($fields)->with(['linkData'=>function($query)use($fields){
            $query->field($fields);
        }])->where($where)->order('sort','asc')->select();

        $this->assign('slider_bar',$slider_bar);
        $this->assign('list',$list);
        return $this->fetch('common/menu');
//        return ;
    }
}