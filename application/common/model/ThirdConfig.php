<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class ThirdConfig extends BaseModel
{

    protected $name = 'third_config';

    protected $json = ['content'];

    public function getContent($type)
    {
        $content = $this->cache(true)->where('type','=',$type)->value('content');
        return $content;
    }

    public function setContent($type,$data)
    {
        return $this->save($data,[['type','=',$type]]);
    }
}