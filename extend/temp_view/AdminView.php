<?php
namespace temp_view;

use think\App;
use think\view\driver\Think;
//后端
class AdminView extends Think
{
    public function __construct(App $app, array $config = [])
    {
        dump($config);
        dump($app);
//        $template = 'default';
//        $config['view_path'] = $app->getRootPath() .'template/'.$template.'/';
        parent::__construct($app, $config);
    }
}