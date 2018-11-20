<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class ThirdConfig extends BaseModel
{

    protected $name = 'third_config';

    protected $json = ['content'];

    public static $config_fields = [
        'wechat'    =>  [
            'lang_name' => 'config_wechat',
            'fields'    => [
                ['name'=>'mch_id' , 'ipt_type'=>'text'],
                ['name'=>'appid' ,  'ipt_type'=>'text'],
                ['name'=>'scerect' ,  'ipt_type'=>'text'],
            ]
        ],
        'alipay'    =>  [
            'lang_name' => 'config_alipay',
            'fields'    => [
                ['name'=>'mch_id' , 'ipt_type'=>'text'],
                ['name'=>'appid' ,  'ipt_type'=>'text'],
            ]
        ]
    ];

    public function getContent($type)
    {
        $content = $this->cache(true)->where('type','=',$type)->value('content');
        return $content;
    }

    public function setContent($type,$content)
    {
        return $this->where([['type','=',$type]])->setField('content',$content);
    }
}