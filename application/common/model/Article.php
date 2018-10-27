<?php
namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class Article extends BaseModel
{
    use SoftDelete;
    protected $name = 'article';
    public static $fields_status = ['','正常','关闭'];

    public function setReleaseTimeAttr($value){
        return $value?strtotime($value):$value;
    }


    public function getReleaseTimeAttr($value){
        return $value?date('Y-m-d H:i:s',$value):$value;
    }

    public function linkCate()
    {
        return $this->belongsTo('ArticleCate','cid');
    }
}