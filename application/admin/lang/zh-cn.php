<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 核心中文语言包
return [
    /*
     * 字母说明
     * g------------ 全局
     * p------------ 页面
     * m------------ 弹窗提示
     * n------------ 节点
     * l------------ 标签
     * t------------ 选项卡
     * c------------ 面包屑
     * _notice------ 说明
     * error-------- 错误提示
     * u------------ 用户
     * v------------ 验证
     * f------------ form 表单
     * */


    'g_error_no_unspecified_lang_key :name' => '数组 {:name} 未指定 index',

    'g_error_no_unspecified_lang_key :name :index' => '数组 {:name} 不存在键 {:index}',
    'g_error_no_unspecified_lang_key :name[:key] :index' => '数组 {:name}[{:key}] 不存在键 {:index}',

    'g_save'       => '保存',
    'g_close'      => '关闭',
    'g_open'       => '开启',
    'g_photo'               => '图片',
    'g_name'                => '名称',
    'g_en'                  => '英文名',
    'g_status'              => '状态',
    'g_status_normal'       => '正常',
    'g_status_close'        => '关闭',
    'g_sort'                => '排序',
    'g_edit'                => '编辑',
    'g_del'                 => '删除',
    'g_select'              => '请选择',
    'g_auto'                => '自定义',
    'g_goods_copy'          => '复制',
    'g_copy_success'        => '复制成功',
    'g_copy_error'          => '复制失败',
    'g_pay'                 =>  '支付',
    'g_pay_wechat'          =>  '微信',
    'g_pay_alipay'          =>  '支付宝',

    //文件上传
    'g_upload_file'         => '文件上传',
    'g_upload_image'        => '图片上传',
    'g_upload_success'      => '上传成功',
    'g_upload_error :error' => '上传失败: {:error}',

    //数据保存
    'g_no_find'                 => '请求操作异常',
    'g_data_save_error'         => '保存失败:数据未发生变化',
    'g_data_save_error :error'  => '保存异常: {:error}',
    'g_data_save_success'       => '保存成功',
    'g_data_del_success'        => '删除成功',
    'g_data_del_error'          => '删除成功',


    //应用状态
    'g_state_close_app_0'       => '否',
    'g_state_close_app_1'       => '是',
    //注册状态
    'g_state_close_reg_0'       => '否',
    'g_state_close_reg_1'       => '是',
    //评论是否审核
    'g_state_auth_comment_0'      => '关闭',
    'g_state_auth_comment_1'      => '开启',

    //商品评论条件
    'p_l_goods_comment_condition_0'   => '所有用户',
    'p_l_goods_comment_condition_1'   => '仅登录用户',
    'p_l_goods_comment_condition_2'   => '有过一次以上购买行为用户',
    'p_l_goods_comment_condition_3'   => '仅购买过该商品用户',

    //自动收货
    'p_l_automatic_receipt_1'         => '开启',
    'p_l_automatic_receipt_0'         => '关闭',

    //是否开启页面区分地区商品
    'p_l_open_location_state_1'       => '开启',
    'p_l_open_location_state_0'       => '关闭',

    //开启发票
    'p_l_state_publish_1'       => '能',
    'p_l_state_publish_0'       => '不能',

    //能否使用积分
    'p_l_state_integral_1'      => '使用',
    'p_l_state_integral_0'      => '不使用',

    //是否使用余额
    'p_l_state_money_1'      => '使用',
    'p_l_state_money_0'      => '不使用',

    'p_l_goods_reduce_stock_1' => '下订单时',
    'p_l_goods_reduce_stock_2' => '发货时',

    //客户付款时是否给商家发短信
    'p_l_state_order_pay_1'     => '发短信',
    'p_l_state_order_pay_0'     => '不发短信',

    //商家发货时是否给客户发短信
    'p_l_state_order_send_1'     => '发短信',
    'p_l_state_order_send_0'     => '不发短信',

    //商品属性价格模式
    'p_l_goods_price_mode_1'    => '单一模式',
    'p_l_goods_price_mode_2'    => '货品模式',

    //商品SKU价格模式
    'p_l_goods_price_sku_mode_1'=> 'SKU价格（属性货品价格）',
    'p_l_goods_price_sku_mode_2'=> 'SKU价格（商品价格 + 属性货品价格）',

    //优惠券
    'p_l_open_coupon_1'      =>'开启',
    'p_l_open_coupon_0'      =>'关闭',

    //全局信息
    'global_info' =>[
        'platform'          => '平台',
        'shop'              => '商城',
        'logout'            => '退出登录',
        'clear_cache'       => '清空缓存',
    ],

    //面包屑
    'c_config'          =>  '系统设置',
    'c_config_shop'     =>  '商城设置',
    /*platform start*/
    /*文章管理开始*/
    'c_article'             =>  '文章管理',
    'c_article_list'        =>  '文章列表',
    'c_article_cate'        =>  '文章分类',
    'c_article_cate_opt'    =>  '文章分类操作',

    /*文章管理结束*/
    /*platform end*/

    /*shop start*/
    /*商品管理开始*/
    'c_goods'               => '商品管理',
    'c_goods_index'         => '商品列表',
    'c_goods_opt'           => '商品操作',

    'c_goods_brand'         => '品牌管理',
    'c_goods_brand_opt'     => '品牌操作',

    'c_goods_cate'          => '商品分类',
    'c_goods_cate_opt'      => '商品分类操作',

    'c_goods_model'         => '商品模型',
    'c_goods_model_opt'     => '商品模型操作',
    'c_goods_model_attr'    => '{:name}模型属性列表',
    'c_goods_model_attr_opt'=> '商品模型属性操作',
    /*商品管理结束*/
    /*shop end*/

    /*订单管理开始*/
    'c_order'               =>  '订单管理',
    'c_order_index'         =>  '订单列表',
    'c_order_detail'        =>  '订单详情',

    'f_order_is_auth_no'    =>  '未审核',
    'f_order_is_auth_yes'   =>  '已审核',
    'f_order_is_auth_refund'=>  '审核被拒',

    'f_order_is_pay_no'     =>  '未支付',
    'f_order_is_pay_yes'    =>  '已支付',

    'f_order_is_send_no'    =>  '未发货',
    'f_order_is_send_yes'   =>  '已发货',



    /*订单管理结束*/

    //全局table
    'g_td_number'       => '序号',
    'g_td_opt'          => '操作',
    'g_td_create_time'  => '创建时间',
    'g_td_update_time'  => '更新时间',
    'g_td_delete_time'  => '删除时间',
    'g_td_name'         => '名称',
    'g_td_detail'       =>  '详情',

    //订单状态按钮
    'g_b_order_all'          =>  '全部订单',
    'g_b_order_wait_pay'     =>  '等待付款',
    'g_b_order_wait_auth'    =>  '等待审核',
    'g_b_order_wait_send'    =>  '代发货',

    //增加操作
    'g_add' =>[
        'article'            => '添加文章',
        'article_cate'       => '添加分类',
    ],

    //错误
//    'error' => [
//        'g_no_find'                             => '请求操作异常',
//
//        'article_cate_name_req'               => '文章名称必须',
//        'article_cate_name :rule'              => '文章名称最多不能超过{:rule}个字符',
//    ],
    //文章分类
    'e_article_cate_name_req'                   => '文章分类名必须',
    'e_article_cate_name_max'                   => '文章分类名最多不能超过 :rule 个字符',
    'e_article_cate_name_unique'                => '文章分类名称已存在',
    //文章
    'e_article_cid_req'                         => '文章分类必须选择',
    'e_article_cid_gt'                          => '文章分类必须选择_',
    'e_article_title_req'                       => '文章标题必须输入',
    'e_article_title_max'                       => '文章标题最多不能超过 :rule 个字符',
    'e_article_author_req'                      => '文章作者名必须输入',
    'e_article_author_max'                      => '文章作者名最多不能超过 :rule 个字符',
    'e_article_origin_req'                      => '文章来源必须输入',
    'e_article_origin_max'                      => '文章来源最多不能超过 :rule 个字符',
    'e_article_intro_req'                       => '文章简介必须输入',
    'e_article_intro_max'                       => '文章简介最多不能超过 :rule 个字符',
    'e_article_content_req'                     => '文章内容必须输入',
    'e_article_content_gt'                      => '文章内容最多不能低于 :rule 个字符',


    //店铺
    //商品品牌
    'e_goods_brand_name_req'                    =>  '品牌名必须输入',
    'e_goods_brand_name_max'                    =>  '品牌名最多不能超过 :rule 个字符',
    //商品分类
    'e_goods_cate_name_req'                     =>  '分类名必须输入',
    'e_goods_cate_name_max'                     =>  '分类名最多不能超过 :rule 个字符',
    //分类模型
    'e_goods_model_name_req'                    =>  '模型名必须输入',
    'e_goods_model_name_max'                    =>  '模型名最多不能超过 :rule 个字符',
    'e_goods_model_name_unique'                 =>  '模型名已重复',
    'e_goods_model_attr_req'                    =>  '模型属性名必须输入',
    'e_goods_model_attr_max'                    =>  '模型属性名必须输入:1',
    //商品模型属性
    'e_goods_model_attr_name_req'               =>  '名称必须输入',
    'e_goods_model_attr_name_max'               =>  '名称最多不能超过 :rule 个字符',
    'e_goods_model_attr_attr_req'               =>  '请选择所属商品模型',
    'e_goods_model_attr_attr_gt'                =>  '请选择所属商品模型:1',

    //添加商品
    'e_goods_cid_req'                           =>  '请选择商品分类',
    'e_goods_cid_gt'                            =>  '请选择商品分类-',
    'e_goods_name_req'                          =>  '请输入商品名称',
    'e_goods_name_max'                          =>  '商品名称最多不能超过:rule 个字符',
    'e_goods_cover_img_req'                     =>  '商品封面图必须上传',
    'e_goods_mid_req'                           =>  '请选择商品模型',
    'e_goods_sku_req'                           =>  '商品必须设置sku属性',

    //模块信息
    'platform' => [
        //平台设置块
        'n_setting'       => '设置',
        'p_t_platform' =>'平台信息',
        'p_l_shop_name'              =>  '商店名称',
        'p_l_shop_title'             =>  '商店标题',
        'p_l_shop_intro'             =>  '网站描述',
        'p_l_shop_c_s'               =>  '客服QQ号码',
        'p_l_shop_c_s_notice'    =>  'QQ客服名称和号码请用“|”隔开（如：客服1|123456），如果您有多个客服的QQ号码，请换行.',
        'p_l_shop_tel'               =>  '客服电话',
        'g_state_close_app'          =>  '暂时关闭网站',
        'p_l_shop_logo'              =>  '网站logo',
        'p_l_shop_n_u'               =>  '用户中心公告',
        'p_l_shop_n_s'               =>  '商店公告',
        'g_state_close_reg'          =>  '是否关闭注册',
        'p_l_shop_e_x_g'             =>  '发货日期起可退换货时间',

        'p_t_base'     =>'基本信息',
        'p_l_language'               =>  '系统语言',
        'p_l_icp_cert'               =>  'ICP证书或ICP备案证书号',
        'p_l_icp_cert_file'          =>  'ICP 备案证书文件',
        'g_state_auth_comment'       =>  '用户评论是否需要审核',
        'p_l_default_good_img'       =>  '商品默认图片',
        'p_l_code_statistics'        =>  '统计代码',
        'p_l_u_reg_integral'         =>  '会员注册赠送积分',
        'p_l_file_max'               =>  '附件上传大小',
        'p_l_goods_comment_condition'=>  '商品评论的条件',

        'p_t_show'     =>'显示设置',
        'p_l_automatic_receipt'      => '是否开启自动确认收货',
        'p_l_open_location_state'    => '是否开启页面区分地区商品',
        'p_l_index_search_keyword'   => '首页搜索的关键字',
        'p_l_index_search_keyword_notice' => '首页显示的搜索关键字,请用半角逗号(,)分隔多个关键字',

        'p_t_shopping' =>'购物流程',
        'p_l_state_publish'          => '能否开发票',
        'p_l_state_integral'         => '是否使用积分',
        'p_l_state_money'            => '是否使用余额',
        'p_l_publish_select'         => '发票内容',
        'p_l_publish_select_notice'         => '客户要求开发票时可以选择的内容。例如：办公用品。每一行代表一个选项.',
        'p_l_goods_reduce_stock'     => '减库存的时机',
        'p_l_order_wait_pay_time'    => '订单等待支付时长',
        'p_l_order_wait_pay_time_notice'         => '超过订单支付时间,直接删除该订单',

        'p_t_goods'    =>'商品显示设置',

        'p_t_sms'      =>'短信设置',
        'p_l_state_order_pay'        =>  '客户付款时是否给商家发短信',
        'p_l_state_order_send'       =>  '商家发货时是否给客户发短信',

        'p_t_extra'    =>'扩展信息',
        'p_l_goods_price_mode'       =>  '商品属性价格模式',
        'p_l_goods_price_sku_mode'   =>  '商品SKU价格模式',
        'p_l_open_coupon'            =>  '是否启用优惠券',
        'p_l_qq_map_key'             =>  '腾讯地图密钥',
        'p_l_express_100_key'        =>  '快递100密钥',

        'n_n_shop_setting'  => '商店设置',
        'n_n_location'      => '地区&配送',
        'n_n_mail'          => '邮件服务器设置',
        'n_n_api'           => '接口对接',
        'n_n_verify'        => '验证码设置',
        'n_n_u_retrieval'=> '用户检索',

        //平台广告
        'n_ad'            => '广告',
        'n_article'       => '文章',
        'n_n_article_cate'                  =>  '文章分类',
        'n_n_article_cate_add'              =>  '文章分类增加/编辑',
        'p_f_article_cate'              =>  '选择文章分类',
        'p_f_article_cate_name'         =>  '名称',

        'n_n_article_list'                  =>  '文章列表',
        'p_article_add'                 =>  '添加文章',
        'p_td_article_title'            =>  '文章标题',
        'p_td_article_cate'             =>  '文章分类',
        'p_td_article_status'           =>  '文章状态',
        'p_td_article_date'             =>  '日期',

        'p_f_article_title'             =>  '文章标题',
        'p_f_article_photo'             =>  '文章封面图',
        'p_f_article_author'            =>  '作者',
        'p_f_article_origin'            =>  '来源',
        'p_f_article_release_time'      =>  '发布时间',
        'p_f_article_intro'             =>  '简介',
        'p_f_article_content'           =>  '详细内容',

        'n_member'                              => '会员',
        'n_n_u_list'                         => '会员列表',
        'n_n_u_site_info'                    => '站内信',
        'n_n_u_address'                      => '收货地址列表',

        'n_power'         => '权限',
        'n_n_manager_list'                  =>  '管理员列表',
        'n_n_manager_logs'                  =>  '管理员日志',
        'n_n_manager_role'                  =>  '角色管理',

        'n_template'      => '模板',
        'n_n_temp_mail'                     =>  '邮件模版',

    ],
    'shop'   => [
        'n_goods'         => '商品',
        'n_n_goods'                     =>  '商品管理',
        'p_t_goods_copy'                =>  '是否复制该商品',
        'p_td_goods_name'               =>  '商品名称',
        'p_td_goods_label'              =>  '商品标签',
        'p_td_goods_sku'                =>  'sku/库存',
        'p_td_goods_status'             =>  '状态',

        'p_td_goods_code'               =>  '商品编码',
        'p_td_goods_bar_code'           =>  '商品条码',
        'p_td_goods_price'              =>  '商品价格',
        'p_td_goods_stock'              =>  '库存',

        'p_goods_add'                   =>  '添加商品',
        'p_goods_attr'                  =>  '商品属性',

        'p_f_goods_cate'                =>  '商品分类',
        'p_f_goods_cate_select'         =>  '请选择商品分类',
        'p_f_goods_brand'               =>  '商品品牌',
        'p_f_goods_brand_select'        =>  '请选择品牌',
        'p_f_goods_name'                =>  '商品名称',
        'p_f_goods_subtitle'            =>  '商品副标题',
        'p_f_goods_cover_img'           =>  '商品封面图',
        'p_f_goods_intro'               =>  '商品简介',
        'p_f_goods_content'             =>  '商品详情',
        'p_f_goods_model'               =>  '商品模型',
        'p_f_goods_model_select'        =>  '请选择商品模型',
        'p_f_goods_sku'                 =>  '商品规格',
        'p_f_goods_spu'                 =>  '商品属性',

        'n_n_goods_cate'                    =>  '类目管理',
        'p_td_goods_top_cate_name'      =>  '顶级分类',
        'p_td_goods_one_cate_name'      =>  '一级子类',
        'p_td_goods_two_cate_name'      =>  '二级子类',
        'p_td_goods_cate_status'        =>  '状态',

        'p_goods_cate_add'              =>  '添加分类',

        'p_f_goods_cate_pid'            =>  '上级栏目',
        'p_f_goods_cate_name'           =>  '分类名',

        'n_n_goods_brand'                   =>  '品牌管理',
        'p_td_goods_brand_name'         =>  '名称',
        'p_td_goods_brand_en'           =>  '英文名',
        'p_td_goods_brand_letter'       =>  '品牌首字母',
        'p_td_goods_brand_logo'         =>  '品牌logo',
        'p_td_goods_brand_intro'        =>  '品牌描述',
        'p_td_goods_brand_status'       =>  '状态',

        'p_goods_brand_add'             =>  '添加品牌',
        'p_f_goods_brand_logo'          =>  '品牌logo',
        'p_f_goods_brand_letter'        =>  '品牌首字母',
        'p_f_goods_brand_letter_notice'   =>  '用于解决某些生僻字无法正确生成品牌首字母的情况.',
        'p_f_goods_brand_intro'         =>  '品牌描述',

        'n_n_goods_model'                   =>  '商品模型',
        'p_td_goods_model_name'         =>  '名称',
        'p_td_goods_model_attr'         =>  '属性分组',
        'p_td_goods_model_count'        =>  '属性数量',

        'p_td_goods_model_attr_name'    =>  '属性名称',
        'p_td_goods_model_attr_cate'    =>  '属性分类',
        'p_td_goods_model_attr_type'    =>  '属性类型',
        'p_td_goods_model_attr_value'   =>  '属性值',

        'p_goods_model_attr_add'        =>  '属性添加',
        'p_f_goods_model_attr_mid'      =>  '所属商品模型',
        'p_f_goods_model_attr_cate'     =>  '所属分类',
        'p_f_goods_model_attr_cate1'=>  'sku属性',
        'p_f_goods_model_attr_cate0'=>  'spu属性',
        'p_f_goods_model_attr_key'      =>  '所属分组',
        'p_f_goods_model_attr_type'     =>  '所属类型',
        'p_f_goods_model_attr_type1'=>  '枚举类型',
        'p_f_goods_model_attr_type0'=>  '自定义类型',
        'p_f_goods_model_attr_enum'     =>  '枚举值',
        'p_f_goods_model_attr_enum_notice'=>  '每行一个属性组。排序也将按照自然顺序排序.',

        'p_goods_model_add'             =>  '模型添加',
        'p_f_goods_model_attr'          =>  '属性分组',
        'p_f_goods_model_attr_notice'   =>  '每行一个商品属性组。排序也将按照自然顺序排序.',


        'n_sales'         => '促销',
        'n_order'         => '订单',
            'n_n_order'                 =>  '订单列表',

                'p_td_order_no'        =>  '订单编号',
                'p_td_order_user_name' =>  '客户名称',
                'p_td_order_receive_name'=>  '收货人',
                'p_td_order_pay_info'   =>  '支付信息',
                'p_td_order_status'     =>  '订单状态',
                'p_td_order_create_time'=>  '订单创建时间',
                'p_td_order_pay_way'    =>  '支付方式',
                'p_td_order_total_money'=>  '应付金额',
                'p_td_order_pay_money'  =>  '实付金额',
                'p_td_order_dis_money'  =>  '优惠金额',

                'p_f_order_base_info'   =>  '基本信息',
                'p_f_order_no'          =>  '订单号',
                'p_f_order_user_name'   =>  '客户名称',
                'p_f_order_create_name' =>  '下单时间',
                'p_f_order_status'      =>  '订单状态',
                'p_f_order_pay_way'     =>  '支付方式',
                'p_f_order_pay_time'    =>  '支付时间',
                'p_f_order_remark'      =>  '订单备注',
                'p_f_order_flow_no'     =>  '交易流水',

                'p_f_order_receive_user'=>  '收货人信息',
                'p_f_order_rec_name'    =>  '收货人',
                'p_f_order_rec_phone'   =>  '手机号码',
                'p_f_order_rec_addr'    =>  '地址',
                'p_f_order_rec_zip_code'=>  '邮编',

                'p_f_order_invoice_info'=>  '发票信息',
                'p_f_order_invoice_type'=>  '发票类型',
                'p_f_order_invoice_rise'=>  '发票抬头',
                'p_f_order_invoice_no'  =>  '纳税人识别号',
                'p_f_order_invoice_mark'=>  '发票内容',

                'p_f_order_goods_info'  =>  '商品信息',
                'p_f_order_goods_name'  =>  '商品名称',
                'p_f_order_goods_no'    =>  '商品编号',
                'p_f_order_goods_sku'   =>  'sku属性',
                'p_f_order_goods_price' =>  '价格',
                'p_f_order_goods_num'   =>  '数量',
                'p_f_order_goods_total' =>  '小计',

                'p_f_order_flow'        =>  '订单流水',
                'p_f_order_opt_name'    =>  '操作者',
                'p_f_order_opt_time'    =>  '操作时间',
                'p_f_order_opt_state'   =>  '订单状态',
                'p_f_order_opt_pay_state'=>  '支付状态',
                'p_f_order_opt_send_state'=>  '发货状态',
                'p_f_order_flow_mark'   =>  '备注',

                'p_f_order_cost'        =>  '费用信息',
                'p_f_order_goods_total_money'  =>  '商品总金额',
                'p_f_order_total_money' =>  '订单总金额',
                'p_f_order_freight_money'=>  '配送费用',
                'p_f_order_should_pay_money'=>  '已付款金额',
                'p_f_order_dis_money'   =>  '优惠金额',
                'p_f_order_pay_money'   =>  '支付金额',

            'n_n_order_refund'          =>  '退款订单',
            'n_n_refund_goods'          =>  '退货单',
        'n_cargo_shop'    => '铺货',
    ]
];
