<?php
namespace app\common\model;

use think\Model;
use think\Validate;

class BaseModel extends Model
{

    public static $fields_status = ['','正常','关闭'];
    protected $autoWriteTimestamp = true;

    //添加动作
    public function actionAdd($input_data,Validate $validate=null)
    {
        if($validate && !$validate->check($input_data)){
            return ['code'=>0,'msg'=>$validate->getError()];
        }
        //
        $model = $this;

        $where = [];
        $pk = $model->getPk();

        if(!empty($input_data[$pk])){
            $where[$pk] = $input_data[$pk];
            //更新数据保留数据原始数据
            $model = $model->where($where)->find();
            if(empty($model))  return ['code'=>0,'msg'=>lang('g_no_find')];
        }
        // 过滤post数组中的非数据表字段数据
        try{
            $bool = $model->allowField(true)->save($input_data);
            return ['code'=>(int)$bool, 'msg'=>$bool ? lang('g_data_save_success') : lang('g_data_save_error')];
        } catch (\Exception $e) {
            return ['code'=>0,'msg'=>lang('g_data_save_error :error', ['error'=>$e->getMessage()])];
        }
    }

    //删除动作
    public function actionDel($where,$is_soft_delete=false)
    {
        $bool = self::destroy($where,$is_soft_delete);
        return ['code'=>(int)$bool, 'msg'=>$bool ? lang('g_data_del_success') : lang('g_data_del_error')];
    }

}