<?php
namespace app\index\controller;

class Index extends Common
{
    public function index()
    {
        return view('');
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
