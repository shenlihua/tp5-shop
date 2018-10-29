<?php
namespace app\admin\controller;

use think\model\concern\SoftDelete;

class Article extends Platform
{
    //文章列表
    public function index()
    {
        $model = new \app\common\model\Article();
        $list = $model->with('linkCate')->order('release_time','desc')->paginate();
        return view('index',[
            'list'  => $list,
            'page'  => $list->render()
        ]);
    }

    //文章--添加
    public function add()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\Article();

        //表单提交
        if($this->request->isAjax()){
            $input_data = $this->request->param();
            $validate = new \app\common\validate\Article();
            $validate->scene(self::VALIDATE_SCENE);

            return $model->actionAdd($input_data, $validate);
        }
        $cate = model('ArticleCate')->with(['linkData'=>function($sql){
            $sql->where('status','=',1);
        }])->where('pid','=','0')->order('sort','asc')->select();

        $model = $model->where('id','=',$id)->find();


        return view('add',[
            'cate' => $cate,
            'model' => $model,
        ]);
    }

    //分类--列表
    public function cate()
    {
        $list = model('ArticleCate')
            ->with('linkData')
            ->where('pid','=','0')
            ->order('sort','asc')
            ->select();
        return view('cate',[
            'list' => $list,
        ]);
    }
    //文章分类添加
    public function cateAdd()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\ArticleCate();

        //表单提交
        if($this->request->isAjax()){
            $input_data = $this->request->param();
            $validate = new \app\common\validate\ArticleCate();
            $validate->scene(self::VALIDATE_SCENE);

            return $model->actionAdd($input_data, $validate);
        }
        //顶级分类
        $top_list = $model->where('pid','=','0')->order('sort','asc')->select();

        $model = $model->where('id','=',$id)->find();


        return view('cateAdd',[
            'top_list' => $top_list,
            'model' => $model,
        ]);
    }

    //分类---删除
    public function cateDel()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\ArticleCate();
        return $model->actionDel($id);
    }
}