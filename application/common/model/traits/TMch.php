<?php
namespace app\common\model\traits;
//多商户
use app\common\model\Merchant;

trait TMch
{

    protected function base($query)
    {
        //获取商户信息
        $mch_info = app('model\MerchantInfo');
        if($mch_info->getData()) {
            //绑定商户查询条件
            $query->where('mch_id',$mch_info['id']);
        }

    }
}