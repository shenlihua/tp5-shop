<?php

namespace app\common\model;

use app\common\model\traits\TMch;
use think\model\Collection;
use think\model\concern\SoftDelete;
use think\Validate;

class Goods extends BaseModel
{
    use SoftDelete,TMch;



    protected $name = 's_goods';


    //添加商品
    public function actionAdd($input_data, Validate $validate = null)
    {
        //商品基本数据
        $goods_info = empty($input_data['goods']) ? [] : $input_data['goods'];
        //商品sku
        $goods_sku = empty($input_data['sku']) ? [] : $input_data['sku'];
        //需要验证的数据
        $validate_goods_info = array_merge($goods_info, ['sku'=>$goods_sku]);
        //属性信息
        $attr_key = [];

        if ($validate && !$validate->check($validate_goods_info)) {
            return ['code' => 0, 'msg' => $validate->getError()];
        }


        if (!empty($goods_info['id'])) {
            //商品数据
            $goods_model = $this->where('id', '=', $goods_info['id'])->find();
            return $this->actionEdit($goods_model,$input_data);
        }else{

            return $this->actionCreate($input_data);
        }
    }

    //商品新增
    public function actionCreate($input_data)
    {

        //商品基本数据
        $goods_info = empty($input_data['goods']) ? [] : $input_data['goods'];
        //商品sku
        $goods_sku = empty($input_data['sku']) ? [] : $input_data['sku'];
        //属性信息
        $attr_key = [];

        //商品属性信息--sku
        $attr_model = model('GoodsAttr');
        $goods_attr_sku = empty($input_data['choose_sku']) ? [] : $input_data['choose_sku'];
        $goods_attr = [];
        foreach ($goods_attr_sku as $key=>$vo) {
            foreach ($vo as $attr_index => $attr){
                //新增状态
                $goods_attr[] = [
                    'aid'   => $key,
                    'val'   =>  $attr,
                    'index' => $attr_index
                ];
            }
        }
//        dump($attr_key);
//        dump($goods_attr);exit;
        //商品属性信息--spu
        $goods_attr_spu = empty($input_data['spu']) ? [] : array_filter($input_data['spu']);
        foreach ($goods_attr_spu as $key=>$vo) {
            $goods_attr[] = [
                'aid'   => $key,
                'val'  =>  $vo,
            ];
        }
//        dump($goods_attr);exit;
//        dump($input_data);
        //商品价格相关信息
        $goods_sku_other = [];
        //编码
        $code = empty($input_data['code'])?[]:$input_data['code'];
        //条形编码
        $bar_code = empty($input_data['bar_code'])?[]:$input_data['bar_code'];
        //价格
        $price = empty($input_data['price'])?[]:$input_data['price'];
        //库存
        $stock = empty($input_data['stock'])?[]:$input_data['stock'];
        //属性信息
        $attr = empty($input_data['attr'])?[]:$input_data['attr'];

        foreach ($goods_sku as $vo) {
            $goods_sku_other[] = [
                'code' => empty($code[$vo])?'':$code[$vo],  //编码
                'bar_code' => empty($bar_code[$vo])?'':$bar_code[$vo],  //条形编码
                'price' => empty($price[$vo])?'':$price[$vo],  //价格
                'stock' => empty($stock[$vo])?'':$stock[$vo],  //库存
                'attr'  => empty($attr[$vo])?[]:array_keys($attr[$vo]),//属性
            ];

        }

        try {
            $this->startTrans();//开启事务

            //保存商品信息
            $this->save($goods_info);

            $goods_id = $this->id;//商品id

            foreach ($goods_attr as &$g_attr) {
                $g_attr['gid'] = $goods_id;
            }

            $attr_save_info = $attr_model->saveAll($goods_attr);

            foreach ($attr_save_info as $key=>$asi) {
                if(isset($asi['index'])){
                    $attr_key[$asi['aid'].'_'.$asi['index']]= $asi['id'];
                }
            }

            //保存商品价格相关信息
            $stock_model = model('GoodsAttrPrice');


            foreach ($goods_sku_other as &$gso) {

                $gso['gid'] = $goods_id;
                foreach($gso['attr'] as &$g_attr) {
                    $g_attr = isset($attr_key[$g_attr]) ? $attr_key[$g_attr]:'';
                }
                $gso['attr_info'] = implode('|',$gso['attr']);
            }

            $stock_model->saveAll($goods_sku_other);

            $this->commit();
            return ['code'=>1, 'msg'=>lang('g_data_save_success')];
        } catch (\Exception $e) {
            $this->rollback();
        }
        return ['code'=>0,'msg'=>lang('g_data_save_error :error', ['error'=>$e->getMessage()])];

    }


    //编辑商品
    public function actionEdit(self $goods_model,$input_data)
    {
        //商品基本数据
        $goods_info = empty($input_data['goods']) ? [] : $input_data['goods'];
        //商品sku
        $goods_sku = empty($input_data['sku']) ? [] : $input_data['sku'];
        //属性信息
        $attr_key = [];


        //商品属性信息--sku
        $attr_model = model('GoodsAttr');
        $goods_attr_sku = empty($input_data['choose_sku']) ? [] : $input_data['choose_sku'];
        $goods_attr = [];
        foreach ($goods_attr_sku as $key=>$vo) {
            foreach ($vo as $attr_index => $attr){
                //编辑状态
                $attr_info = $attr_model->where([
                    ['gid','=',$goods_model['id']],
                    ['aid','=',$key],
                    ['val','=',$attr],
                ])->find();
                if($attr_info) { //存在属性记录属性
                    $attr_key[$key.'_'.$attr_info['id']] = $attr_info['id'];
                }else{
                    $goods_attr[] = [
                        'aid'   => $key,
                        'val'   =>  $attr,
                        'index' => $attr_index
                    ];
                }

            }
        }
        //商品属性信息--spu
        $goods_attr_spu = empty($input_data['spu']) ? [] : $input_data['spu'];
        foreach ($goods_attr_spu as $key=>$vo) {
            //编辑状态
            $spu_attr_info = $attr_model->where([
                ['gid','=',$goods_model['id']],
                ['aid','=',$key]
            ])->find();
            if($spu_attr_info) {
                //更新数据
                $spu_attr_info->val = $vo;
                $spu_attr_info->save();
            }else{
                //添加数据
                $goods_attr[] = [
                    'aid'   => $key,
                    'val'  =>  $vo,
                ];
            }

        }
//        dump($goods_attr);exit;
//        dump($input_data);
        //商品价格相关信息
        $goods_sku_other = [];
        //编码
        $code = empty($input_data['code'])?[]:$input_data['code'];
        //条形编码
        $bar_code = empty($input_data['bar_code'])?[]:$input_data['bar_code'];
        //价格
        $price = empty($input_data['price'])?[]:$input_data['price'];
        //库存
        $stock = empty($input_data['stock'])?[]:$input_data['stock'];
        //属性信息
        $attr = empty($input_data['attr'])?[]:$input_data['attr'];
        //编辑的属性信息
        $goods_stock_index = empty($input_data['goods_stock_index']) ? [] : $input_data['goods_stock_index'];


        try {
            $this->startTrans();//开启事务



            //保存商品信息
            $goods_save_where= $goods_model?['id','=',$goods_model['id']]:[];
            $this->save($goods_info,$goods_save_where);

            $goods_id = $this->id;//商品id

            foreach ($goods_attr as &$g_attr) {
                $g_attr['gid'] = $goods_id;
            }

            $attr_save_info = $attr_model->saveAll($goods_attr);
//            dump($attr_save_info);exit;
            foreach ($attr_save_info as $key=>$asi) {
                if(isset($asi['index'])){
                    $attr_key[$asi['aid'].'_'.$asi['index']]= $asi['id'];
                }
            }

            //保存商品价格相关信息
            $stock_model = model('GoodsAttrPrice');
            $update_stock_index = [];//需要更新的数据
            foreach ($goods_sku as $vo) {
                $stock_info = [
                    'code' => empty($code[$vo])?'':$code[$vo],  //编码
                    'bar_code' => empty($bar_code[$vo])?'':$bar_code[$vo],  //条形编码
                    'price' => empty($price[$vo])?'':$price[$vo],  //价格
                    'stock' => empty($stock[$vo])?'':$stock[$vo],  //库存
                    'attr'  => empty($attr[$vo])?[]:array_keys($attr[$vo]),//属性
                ];
                if(!empty($goods_stock_index[$vo])) {
                    //更新信息
//                    $new_stock_model = new GoodsAttrPrice();
                    $update_stock_index[] = $goods_stock_index[$vo];
                    (new GoodsAttrPrice())->save($stock_info,[['id','=',$goods_stock_index[$vo]]]);
                }else{
                    $goods_sku_other[] = $stock_info;
                }
            }

            foreach ($goods_sku_other as &$gso) {

                $gso['gid'] = $goods_id;
                foreach($gso['attr'] as &$g_attr) {
                    $g_attr = isset($attr_key[$g_attr]) ? $attr_key[$g_attr]:'';
                }
                $gso['attr_info'] = implode('|',$gso['attr']);
            }
            //删除旧输入
            $stock_model->where('gid','=',$goods_model['id'])->whereNotIn('id',$update_stock_index)->delete();
            //入库新数据
            $stock_model->saveAll($goods_sku_other);

            $this->commit();
            return ['code'=>1, 'msg'=>lang('g_data_save_success')];
        } catch (\Exception $e) {
            $this->rollback();
        }
        return ['code'=>0,'msg'=>lang('g_data_save_error :error', ['error'=>$e->getMessage()])];
    }
    /*
     * 获取商品属性信息
     * @param $mid int 模型id
     * @param $gid int 商品id
     * return array
     * */
    public function goodsAttr($mid,$gid=0)
    {
        //商品模型
        $model = new GoodsModel();
        $model = $model->where('id','=',$mid)->find();
        //获取商品属性信息
        $goods_attr_model = new GoodsAttr();
        $goods_attr_model = $goods_attr_model->where('gid','=',$gid)->select()->toArray();
        $goods_attr_info =[];
        foreach ($goods_attr_model as $vo) {
            $goods_attr_info[$vo['aid']][$vo['val']] = $vo;
        }
//        dump($goods_attr_info);exit;
        //商品库存信息
        $goods_stock_model = new GoodsAttrPrice();
        $goods_stock_model = $goods_stock_model->where('gid','=',$gid)->select()->toArray();
        $goods_stock_info = []; //库存信息
        $exit_stock_index = []; //已存在的库存信息索引
        foreach ($goods_stock_model as $vo) {
            $exit_stock_index = array_merge($exit_stock_index,$vo['attr_info']);
            $attr_info = implode('|',$vo['attr_info']);
            unset($vo['attr_info'],$vo['gid']);
            $goods_stock_info[$attr_info] = $vo;

        }

        $attr = explode(PHP_EOL,$model['attr']);
        //dump($model);exit;
        $attr_model = new GoodsModelAttr();
        $list = $attr_model->where('mid','=',$mid)->order('sort','asc')->select();

        $sku = $spu = [];
        foreach($list as $vo) {
            $enum = explode(PHP_EOL,$vo['enum']);
            $child = [];

            if($vo['cate']==1) {
                if($vo['type']==1){ //枚举类型
                    foreach($enum as $key=>$em) {
                        $state = 0;
                        $exit_attr_info = isset($goods_attr_info[$vo['id']])?$goods_attr_info[$vo['id']]:[];

                        if(isset($exit_attr_info[$em])) { //判断属性是否存在
                            $state =1;
                            $id = $exit_attr_info[$em]['id'];
                        }else{
                            $id = $key;
                        }
                        $child[]= [
                            'attr' => $em,
                            'state' => $state,
                            'id'    => $id,
                            'pid'    => $vo['id'],
                        ];
                    }
                }elseif(isset($goods_attr_info[$vo['id']])){
                    foreach ($goods_attr_info[$vo['id']] as $key=>$ai) {
                        $child[]= [
                            'attr' => $ai['val'],
                            'state' => in_array($ai['id'],$exit_stock_index)?1:0,
                            'id'    => $ai['id'],
                            'pid'    => $vo['id'],
                        ];
                    }
                }
                $sku[] = [
                    'group_name' => $vo['name'],
                    'id'         => $vo['id'],
                    'type'       => $vo['type']==1 ? 'enum' : 'auto',
                    'child'      => $child
                ];

            }else{

                if($vo['type']==1){ //枚举类型
                    foreach($enum as $key=>$em) {
                        $child[]= [
                            'value' => $key,
                            'name'  => $em,
                        ];
                    }
                }
                //属性信息
                $attr_info = isset($goods_attr_info[$vo['id']])?array_values($goods_attr_info[$vo['id']]):[];
                $child = [
                    'attr'   => $vo['name'],
                    'id'     => $vo['id'],
                    'pid'    => $vo['id'],
                    'state'  => 1,
                    'type'   => $vo['type']==1 ? 'enum' : 'auto',
                    'data'   => $child,
                    'value' => isset($attr_info[0])?$attr_info[0]['val']:'',
                ];
                if(array_key_exists($vo['key'],$spu)){
                    $spu[$vo['key']]['child'][] = $child;
                }else{
                    $spu[$vo['key']] = [
                        'group_name' => $attr[$vo['key']],
                        'id'         => $vo['id'],
                        'child'      => [$child]
                    ];
                }
            }
        }

        return [$sku, array_values($spu),$goods_stock_info];
    }

    /*
     * 商品复制
     * */
    public function goodsCopy($id)
    {
        try{
            $goods_info = $this->with(['linkAttr'=>function($query){
                $query->field('aid,gid,val,id as old_id');
            },'linkPrice'])->where('id','=',$id)->find()->toArray();

            $goods_attr =$goods_attr_new_key = $goods_price = [];
//        $goods_attr = $goods_info;
//
            unset($goods_info['id'],$goods_info['create_time'],$goods_info['update_time']);
//            dump($goods_info);exit;
            $this->startTrans();
            $goods_model = $this->newInstance();
            $goods_model->save($goods_info);
//            dump($goods_model);exit;
//            $new_goods_id = $goods_model->id;
            //入库属性
            $attr_data = $goods_model->linkAttr()->saveAll($goods_info['link_attr']);
            foreach($attr_data as $vo) {
                $goods_attr_new_key[$vo['old_id']] = $vo['id'];
            }

            foreach($goods_info['link_price'] as $vo) {
                $attr_info =[];
                foreach ($vo['attr_info'] as $attr){
                    $attr_info[] = isset($goods_attr_new_key[$attr])?$goods_attr_new_key[$attr]:'';
                }
                $vo['attr_info'] = implode('|',$attr_info);
                unset($vo['id']);
                $goods_price[] = $vo;
            }
//            dump($goods_price);exit;
            //入库价格
            $goods_model->linkPrice()->saveAll($goods_price);
            $this->commit();
            return [true, lang('g_copy_success'),$goods_model];
        }catch (\Exception $e) {
            $this->rollback();
            return [false,lang('g_copy_error').':'.$e->getMessage()];
        }
    }


    //查询商品价格信息
    public function handleOrderData()
    {
        //属性信息
        $attr_info = $this->link_one_price['attr_info'];
        $attr_model = model('GoodsAttr');
        return $this->link_one_price['attr_info_name'] = $attr_model->whereIn('id',$attr_info)->column('val');
    }

    /*
     * 商品价格信息处理
     * @param $goods_model collection
     * @param $goods_num array
     * return array
     * */
    public function handlePayInfo(Collection $goods_model,array $goods_num=[])
    {
        //商品-支付信息
        $number = $total_money = $pay_money = $dis_money = $freight_money = 0;

        if ( $goods_model->count()>0 ) {
            foreach ($goods_model as $key=>$item) {
                $link_num_key = $item['id'].'_'.$item['link_one_price']['id'];  //商品数量
                $item['number'] = empty($goods_num[$link_num_key])?1:$goods_num[$link_num_key]; //购买数量
                $number +=$item['number']; //总数量
                $item['total_price'] = $item['number']*$item['link_one_price']['price']; //总价格
                $total_money += $item['total_price'];

            }
        }

        $pay_money = $total_money;

        return array(
            $number,
            $total_money,
            $pay_money,
            $dis_money,
            $freight_money
        );
    }

    /*
     * 商品信息--完整信息
     * */
    public function handleFullGoodsInfo(\think\Collection $goods_model)
    {
        $attr_ids = [];
        $goods_model->each(function($value,$index) use(&$attr_ids){
            $attr_ids = array_merge($attr_ids,$value['link_one_price']['attr_info']);
        });
        $attr_ids = array_unique($attr_ids);
        //查询属性信息
        $attr = model('GoodsAttr')->whereIn('id',$attr_ids)->column('val','id');

        $goods_model->each(function($value,$index)use($attr){
            $attr_info_name = [];
            foreach ($value['link_one_price']['attr_info'] as $ai) {
                $attr_info_name[] = isset($attr[$ai]) ? $attr[$ai]: '';
            }
            $value['link_one_price']['attr_info_name'] = $attr_info_name;
        });
    }

    /*
     * 按商家划分数据
     * */
    public function handleMerchantGoodsInfo(\think\Collection $goods)
    {
        $list = [];
        foreach($goods as $vo) {
            $merchant_info = $vo['link_merchant'];  //商户信息
            $primary_id = empty($merchant_info['id'])?0:$merchant_info['id'];     //商户id
            if(array_key_exists($primary_id,$list)){
                $list[$primary_id]['list'][] = $vo;
            }else{
                $list[$primary_id]['merchant_info'] = $merchant_info;
                $list[$primary_id]['list'][] = $vo;
            }

        }
        return array_values($list);
    }



    //关联商品价格属性--一个属性
    public function linkOnePrice()
    {
        return $this->hasOne('GoodsAttrPrice','gid');
    }
    //关联商品价格属性
    public function linkPrice()
    {
        return $this->hasMany('GoodsAttrPrice','gid');
    }

    //关联商品属性
    public function linkAttr()
    {
        return $this->hasMany('GoodsAttr','gid');
    }

    //关联购物车
    public function linkCart()
    {
        return $this->hasMany('GoodsCart','gid');
    }

    //关联商户
    public function linkMerchant()
    {
        return $this->belongsTo('Merchant','mch_id');
    }

    //关联商户
    public function linkGoodsCart()
    {
        return $this->hasOne('GoodsCart','gid');
    }

}