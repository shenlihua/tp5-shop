<?php
namespace app\common\model;

class SysNode extends BaseModel
{
    protected $name = 'sys_node';


    public function linkData()
    {
        return $this->hasMany('SysNode','pid')->where('status','=',1)->order('sort','asc');
    }
}