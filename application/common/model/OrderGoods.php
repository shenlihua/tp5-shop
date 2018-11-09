<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class OrderGoods extends BaseModel
{

    protected $name = 's_order_goods';

    protected $json = ['info'];

}