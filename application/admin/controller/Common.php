<?php
namespace app\admin\controller;

use think\Controller;
use think\Model;
use think\Validate;

class Common extends Controller
{
    const VALIDATE_SCENE = 'admin_add';

    //是否开启多商户状态
    protected $open_mch_state;
    protected $mch_id = 1;//商户信息

    protected $master;

    protected function initialize()
    {
        //多商户状态
        $this->open_mch_state = (bool)config('open_mch_state');
        //绑定商户信息
        bind('app\common\model\Merchant',
            $this->open_mch_state ?
                (new \app\common\model\Merchant())->where('id',$this->mch_id)->findOrEmpty() :
                'app\common\model\Merchant'
        );
        //模块
        $this->view->assign('sys_master',$this->master);

    }

    public function getMaster()
    {
        return $this->master;
    }

    /*
     * 共用数据保存
     * @param model Model 模型对象
     * @param input_data array 数组
     * @param $validate validate 验证
     * */
    protected function _dataSave(Model &$model, $input_data,Validate $validate=null)
    {
        if($validate && !$validate->check($input_data)){
            return ['code'=>0,'msg'=>$validate->getError()];
        }

        $where = [];
        $pk = $model->getPk();
        if(!empty($input_data[$pk])){
            $where[$pk] = $input_data[$pk];
            //更新数据保留数据原始数据
            $model = $model->where($where)->find();
        }
//        dump($opt);
//        dump($where);
//        dump($input_data);exit;
        // 过滤post数组中的非数据表字段数据
        try{
            $bool = $model->allowField(true)->save($input_data);
            return ['code'=>(int)$bool, 'msg'=>$bool ? lang('g_data_save_success') : lang('g_data_save_error')];
        } catch (\Exception $e) {
            return ['code'=>0,'msg'=>lang('g_data_save_error :error', ['error'=>$e->getMessage()])];
        }

    }

    /*
     * 共用数据批量保存
     * @param model Model 模型对象
     * @param input_data array 数组
     * @param $validate validate 验证
     * */
    protected function _dataSaveAll(Model $model, $input_data,Validate $validate=null)
    {
        if($validate && !$validate->check($input_data)){
            return ['code'=>0,'msg'=>$validate->getError()];
        }

        try{
            $coll = $model->allowField(true)->saveAll($input_data);
            return ['code'=>1,'msg'=>lang('g_data_save_success'),'coll'=>$coll];
        } catch (\Exception $e) {
            return ['code'=>0,'msg'=>lang('g_data_save_error :error',['error'=>$e->getMessage(),'coll'=>''])];
        }

    }
}