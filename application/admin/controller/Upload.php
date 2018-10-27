<?php
namespace app\admin\controller;


class Upload extends Common
{
    public function upload($route='normal', $type='images')
    {
        $upload_root_path = \Env::get('root_path').'public'.DIRECTORY_SEPARATOR;
        $save_path = 'uploads'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$route.DIRECTORY_SEPARATOR;
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( $upload_root_path.$save_path);
        if($info){
            return ['code'=>1, 'msg'=>lang('g_upload_success'),'data'=>$save_path.$info->getSaveName()];

        }else{
            // 上传失败获取错误信息
            $error= $file->getError();
            return ['code'=>0, 'msg'=>lang('g_upload_error :error',['error'=>$error])];
        }
    }

    //layui 富文本上传信息
    public function layuiUpload($route='normal', $type='images')
    {
        $upload_root_path = \Env::get('root_path').'public'.DIRECTORY_SEPARATOR;
        $save_path = 'uploads'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$route.DIRECTORY_SEPARATOR;
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( $upload_root_path.$save_path);
        if($info){
            return ['code'=>0,
                'msg'=>lang('g_upload_success'),
                'data'=>[
                    'src' => $this->request->domain().DIRECTORY_SEPARATOR. $save_path.$info->getSaveName()
                ]
            ];

        }else{
            // 上传失败获取错误信息
            $error= $file->getError();
            return ['code'=>1, 'msg'=>lang('g_upload_error :error',['error'=>$error])];
        }
    }
}