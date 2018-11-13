<?php
namespace app\admin\controller;

class Goods extends Shop
{
    //商品列表
    public function index()
    {
        $list = model('Goods')->with(['linkAttr','linkPrice'])->paginate()->each(function($item, $key){
//            $attr = array_column($item['link_attr'],null,$);
            $attr = [];
            foreach($item['link_attr'] as $vo) {
                $attr[$vo['id']] = $vo['val'];
            }

            foreach($item['link_price'] as &$pr) {
                $attr_ext = [];
                foreach ($pr['attr_info'] as $index){
                    $attr_ext[] = [
                        'name'  => isset($attr[$index])?$attr[$index]:'',
                        'value' => $index
                    ];
                }
                unset($pr['attr_info']);
                $pr['attr_info_ext'] = $attr_ext;
            }
        });

        return view('index',[
            'list'=>$list,
            'page'  => $list->render()
        ]);
    }

    //商品-操作
    public function add()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\Goods();

        //表单提交
        if($this->request->isAjax()){
            $input_data = $this->request->param();
            $validate = new \app\common\validate\Goods();
            $validate->scene(self::VALIDATE_SCENE);

            return $model->actionAdd($input_data,$validate);
        }
        //商品信息
        $model = $model->where('id','=',$id)->find();
        //商品分类
        $cate_model = model('goodsCate');
        $cate_list = $cate_model
            ->with(['linkData'=>function($query){
                $query->with(['linkData'=>function($query){
                    $query->where('status','=',1);
                }])->where('status','=',1);
            }])
            ->where([['status','=',1],['pid','=',0]])
            ->select();
        //品牌
        $brand_model = model('goodsBrand');
        $brand_list = $brand_model->where('status','=',1)->select();
        //模型
        $attr_model = new \app\common\model\GoodsModel();
        $attr_list = $attr_model->select();

        return view('add',[
            'model' => $model,
            'brand_list'    =>  $brand_list,
            'cate_list'    =>  $cate_list,
            'attr_list'    =>  $attr_list,
        ]);
    }

    //商品删除
    public function goodsDel()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\Goods();
        return $model->actionDel($id);
    }

    //品牌管理
    public function brand()
    {
        $model = new \app\common\model\GoodsBrand();
        $list = $model->order('sort','asc')->paginate();
        return view('brand',[
            'list'  =>  $list,
            'page'  => $list->render()
        ]);
    }

    //品牌添加
    public function brandAdd()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsBrand();

        //表单提交
        if($this->request->isAjax()){
            $input_data = $this->request->param();
            $validate = new \app\common\validate\GoodsBrand();
            $validate->scene(self::VALIDATE_SCENE);

            return $model->actionAdd($input_data, $validate);
        }

        $model = $model->where('id','=',$id)->find();


        return view('brandAdd',[
            'model' => $model,
        ]);
    }

    //品牌删除
    public function brandDel()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsBrand();
        return $model->actionDel($id);
    }

    //分类
    public function cate()
    {
        $list = model('GoodsCate')
            ->with(['linkData.linkData'])
            ->where('pid','=','0')
            ->order('sort','asc')
            ->select();

        foreach ($list as &$vo) {
            $count = 1;
            foreach ($vo['link_data'] as &$ch) {
                $ch['count'] = count($ch['link_data'])+1;
                $count +=$ch['count'];
            }
            $vo['count'] = $count;
        }

        return view('cate',[
            'list' => $list,
        ]);
    }

    //分类--添加
    public function cateAdd()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsCate();

        //表单提交
        if($this->request->isAjax()){
            $input_data = $this->request->param();
            $validate = new \app\common\validate\GoodsCate();
            $validate->scene(self::VALIDATE_SCENE);

            return $model->actionAdd($input_data, $validate);
        }
        //获取分类
        $cate = $model
            ->with(['linkData'=>function($query){
                return $query->where('status','=',1);
            }])
            ->where([
                ['pid','=',0],
                ['status','=',1]
            ])
            ->select();

        $model = $model->where('id','=',$id)->find();


        return view('cateAdd',[
            'cate'  => $cate,
            'model' => $model,
        ]);
    }

    //分类删除
    public function cateDel()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsCate();
        return $model->actionDel($id);
    }

    //商品模型
    public function model()
    {
        $model = new \app\common\model\GoodsModel();
        $list = $model->paginate();
        return view('model',[
            'list' => $list,
            'page' => $list->render()
        ]);
    }


    //分类--添加
    public function modelAdd()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsModel();

        //表单提交
        if($this->request->isAjax()){
            $input_data = $this->request->param();
            $validate = new \app\common\validate\GoodsModel();
            $validate->scene(self::VALIDATE_SCENE);

            return $model->actionAdd($input_data, $validate);
        }


        $model = $model->where('id','=',$id)->find();


        return view('modelAdd',[
            'model' => $model,
        ]);
    }

    //商品属性列表
    public function modelAttr()
    {
        $id = $this->request->param('id',0,'intval');
        $model = model('GoodsModel')->where('id','=',$id)->find();
        $attr_model = new \app\common\model\GoodsModelAttr();

        $list = $attr_model->where('mid','=',$id)->order('cate','desc')->order('sort','asc')->select();

        return view('modelAttr',[
            'model' => $model,
            'list' => $list,
        ]);
    }

    //属性添加
    public function modelAttrAdd()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsModelAttr();

        //表单提交
        if($this->request->isAjax()){
            $input_data = $this->request->param();
            $validate = new \app\common\validate\GoodsModelAttr();
            $validate->scene(self::VALIDATE_SCENE);

            return $model->actionAdd($input_data, $validate);
        }
        //所有分类
        $cate = model('GoodsModel')->select();

        $model = $model->where('id','=',$id)->find();
        //属性分组
        $key_list = [];
        if($model['mid']){
            foreach($cate as $vo) {
                if($vo['id'] == $model['mid']) {
                    $key_list = explode(PHP_EOL, $vo['attr']);
                    break;
                }
            }
        }else{
            $key_list = isset($cate[0]) ? explode(PHP_EOL, $cate[0]['attr']): [];
        }
//        dump($key_list);exit;

        return view('modelAttrAdd',[
            'key_list' => $key_list,
            'cate' => $cate,
            'model' => $model,
        ]);
    }
    //模型分组
    public function getModelType()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsModel();
        $model = $model->where('id','=',$id)->find();
        $attr = explode(PHP_EOL,$model['attr']);

        $data = [];
        foreach($attr as $key=>$em) {
            $data[]= [
                'value' => $key,
                'name'  => $em,
            ];
        }

        return ['code'=>1,'msg'=>'获取成功','data'=>$data];
    }

    //获取商品模型数据
    public function getModelAttr()
    {
        $id = $this->request->param('id',0,'intval'); //模型id
        $gid = $this->request->param('gid',0,'intval');//商品id
        $goods_model = new \app\common\model\Goods();
        list($sku,$spu,$stock) = $goods_model->goodsAttr($id,$gid);

        return ['code'=>1,'msg'=>'获取成功','data'=>['sku'=>$sku,'spu'=>$spu,'stock'=>$stock]];
    }

    //获取商品模型数据
    public function modelAttrDel()
    {
        $id = $this->request->param('id',0,'intval');
        $model = new \app\common\model\GoodsModelAttr();
        return $model->actionDel($id);
    }

    //商品复制
    public function goodsCopy()
    {
        $id = $this->request->param('id');
        $model = new \app\common\model\Goods();
        list($state,$msg,$model) = $model->goodsCopy($id);
        return ['code'=>(int)$state,'msg'=>$msg];
    }
}