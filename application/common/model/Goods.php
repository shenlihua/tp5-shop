<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class Goods extends BaseModel
{
    use SoftDelete;

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

        if ($validate && !$validate->check($validate_goods_info)) {
            return ['code' => 0, 'msg' => $validate->getError()];
        }

        if (!empty($input_data['id'])) {
            //商品数据
            $goods_info = $this->where('id', '=', $input_data['id'])->find();
        }



        //商品属性信息--sku
        $attr_model = model('GoodsAttr');
        $goods_attr_sku = empty($input_data['choose_sku']) ? [] : $input_data['choose_sku'];
        $goods_attr = [];
        foreach ($goods_attr_sku as $key=>$vo) {
            foreach ($vo as $attr_index => $attr){
                $goods_attr[] = [
                    'aid'   => $key,
                    'val'   =>  $attr,
                    'index' => $attr_index
                ];
            }
        }
        //商品属性信息--spu
        $goods_attr_spu = empty($input_data['spu']) ? [] : $input_data['spu'];
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
//        dump($goods_sku_other);
//        exit;
        try {
            $this->startTrans();//开启事务
            //保存商品信息
            $this->save($goods_info);
            $goods_id = $this->id;//商品id

            foreach ($goods_attr as &$attr) {
                $attr['gid'] = $goods_id;
            }
            $attr_save_info = $attr_model->saveAll($goods_attr);
            $attr_key = [];
            foreach ($attr_save_info as $key=>$asi) {
                if(isset($asi['index'])){
                    $attr_key[$asi['aid'].'_'.$asi['index']]= $asi['id'];
                }
            }

            foreach ($goods_sku_other as &$gso) {
                $gso['gid'] = $goods_id;
                foreach($gso['attr'] as &$g_attr) {
                    $g_attr = isset($attr_key[$g_attr]) ? $attr_key[$g_attr]:'';
                }
                $gso['attr_info'] = implode('|',$gso['attr']);
            }
            //保存商品价格相关信息
            model('GoodsAttrPrice')->saveAll($goods_sku_other);

            $this->commit();
            return ['code'=>1, 'msg'=>lang('g_data_save_success')];
        } catch (\Exception $e) {
            $this->rollback();
        }
        return ['code'=>0,'msg'=>lang('g_data_save_error :error', ['error'=>$e->getMessage()])];

    }

    public function linkPrice()
    {
        return $this->hasMany('GoodsAttrPrice','gid');
    }
}