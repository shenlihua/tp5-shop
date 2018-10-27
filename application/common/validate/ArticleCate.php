<?php
namespace app\common\validate;

use think\Validate;

class ArticleCate extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:10|unique:article_cate',
    ];

    protected $message  =   [
        'name.require' => '{%e_article_cate_name_req}',
        'name.max'     => '{%e_article_cate_name_max}',
        'name.unique'     => '{%e_article_cate_name_unique}',
    ];
}
