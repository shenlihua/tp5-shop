<?php
namespace app\admin\controller;


class ThirdConfig extends Platform
{
    //第三方接口
    public function index()
    {
        $model = new \app\common\model\ThirdConfig();
        $list = $model->where('status','=',1)->select();
        //合并数据
        $data = [];
        $config_fields =$model::$config_fields;
        foreach($list as $vo) {
            if(isset($config_fields[$vo['type']])){
                $content = $vo['content'];
                $config_fields_type = $config_fields[$vo['type']];
                $config_fields_type['type'] = $vo['type']; //绑定类型
                foreach ($config_fields_type['fields'] as &$field) {
                    $field['value'] = empty($content[$field['name']]) ? '': $content[$field['name']];
                }
                $data[]= $config_fields_type;
            }else{

            }

        }

        return view('index',[
            'data' => $data
        ]);
    }

    //第三方保存操作
    public function configAction()
    {
        $input_data = $this->request->param();
        $type = key($input_data);
        $content = array_values($input_data);
        $model = new \app\common\model\ThirdConfig();
        $model->setContent($type,$content);
        return ['code'=>1,'msg'=>'保存成功'];
    }
}