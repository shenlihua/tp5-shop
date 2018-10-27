<?php

namespace app\common\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'cid' => 'require|gt:0',
        'title' => 'require|max:150',
        'author' => 'require|max:50',
        'origin' => 'require|max:50',
        'intro' => 'require|max:255',
        'content' => 'require|gt:0',
    ];

    protected $message = [
        'cid.require'       => '{%e_article_cid_req}',
        'cid.gt'            => '{%e_article_cid_gt}',
        'title.require'     => '{%e_article_title_req}',
        'title.max'         => '{%e_article_title_max}',
        'author.require'    => '{%e_article_author_req}',
        'author.max'        => '{%e_article_author_max}',
        'origin.require'    => '{%e_article_origin_req}',
        'origin.max'        => '{%e_article_origin_max}',
        'intro.require'     => '{%e_article_intro_req}',
        'intro.max'         => '{%e_article_intro_max}',
        'content.require'   => '{%e_article_content_req}',
        'content.gt'       => '{%e_article_content_gt}',

    ];
}
