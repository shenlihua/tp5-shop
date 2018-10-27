<?php

namespace app\common\model;

class SysConfig extends BaseModel
{
    protected $name = 'sys_config';

    public function getStoreRangeAttr($value, $data)
    {
        if ($data['type'] == 'radio') {
            return explode(',', $value);

        } elseif ($data['type'] == 'select') {
            $data = explode(',', $value);
            $code = [];
            foreach ($data as $vo) {
                $arr = explode('|', $vo);
                $code[$arr[0]] = isset($arr[1]) ? $arr[1] : $arr[0];
            }
            return $code;

        } elseif ($data['type'] == 'file') { //上传
            $arr = explode(',', $value);
            return [
                'url' => url('Upload/upload'),
                'data'=> [
                    'route' => $arr[0],
                    'type' => isset($arr[1]) ? $arr[1] : 'images'
                ],
                'accept'=> isset($arr[1]) ? $arr[1] : 'images',
            ];
        }
        return $value;
    }

    public function linkData()
    {
        return $this->hasMany('SysConfig', 'pid')->where('status', '=', 1)->order('sort', 'asc');
    }
}