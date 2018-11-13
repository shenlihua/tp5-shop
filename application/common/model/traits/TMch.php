<?php
namespace app\common\model\traits;
//多商户

trait TMch
{


    protected $mch_table_relation_key = 'mch_id';

    protected function base($query)
    {
        //获取商户信息
        $mch_info = app('app\common\model\Merchant');
        if($mch_info->getKey()) {
            //绑定商户查询条件
            $query->where($this->mch_table_relation_key,$mch_info->getKey());
        }
    }

    //定义事件
    protected static function init()
    {
        self::observe(\app\common\event\BindMch::class);
    }

    //获取商户数据库关联的键
    public function getMchRelationKey()
    {
        return $this->mch_table_relation_key;
    }
}