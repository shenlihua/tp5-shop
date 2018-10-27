<?php
namespace temp_view;

use think\App;
use think\view\driver\Think;
//前端应用模板解析
class TempView extends Think
{
    public function __construct(App $app, array $config = [])
    {
        $template = 'default';
        $config['view_path'] = $app->getRootPath() .'template/'.$template.'/';
        parent::__construct($app, $config);
    }
}