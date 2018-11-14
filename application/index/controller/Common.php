<?php
namespace app\index\controller;

use think\Controller;

class Common extends Controller
{
    protected $user_id = 1;

    public function initialize()
    {
        //绑定用户信息
        bind('container_user_id',function(){
            return $this->user_id;
        });
    }
}