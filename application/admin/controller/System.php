<?php
namespace app\admin\controller;

class System extends Platform
{
    public function index()
    {
        return view('',[

        ]);
    }

    //系统设置
    public function setting()
    {
        $model = model('SysConfig');
        $where = [
            ['status','=',1] ,
            ['pid','=',0],
            ['type','=','platform'],
        ];
        $list = $model->with('linkData')->where($where)->order('sort','asc')->select();
        return view('',[
            'list' => $list
        ]);
    }

    //系统设置保存操作
    public function settingAction()
    {
        $input_data = $this->request->param('key',[]);

        $save_data=[];
        foreach ($input_data as $key => $vo) {
            $save_data[] = [
                'id'=>$key,
                'value' => $vo
            ];
        }
        $model = model('SysConfig');
        $result = $this->_dataSaveAll($model,$save_data);
        unset($result['coll']);
        return $this->_dataSaveAll($model,$save_data);
    }
}