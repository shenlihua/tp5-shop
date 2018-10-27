<?php
namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class ArticleCate extends BaseModel
{
    use SoftDelete;
    protected $name = 'article_cate';


    public function linkData()
    {
        return $this->hasMany('ArticleCate','pid')->order('sort','asc');
    }
}