<?php
namespace app\common\event;

use app\common\model\Merchant;

class BindMch
{
    public function beforeInsert($model,Merchant $merchant_info)
    {
        //判断商户id是否存在
        if($merchant_info->getKey()) {
            $fields = $model->getMchRelationKey();
            $model->$fields = $merchant_info->getKey();
        }
    }
}