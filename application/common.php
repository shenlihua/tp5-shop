<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//共用语言
function c_lang($name, $key = '')
{


    $result = lang($name);

    if (is_array($result)) {
        if (strpos($key, '.')) {
            list($key, $index) = explode('.',$key);
        } else {
            empty($key) && abort('200', lang('g_error_no_unspecified_lang_key :name', ['name' => $name]));
        }

        if(!array_key_exists($key,$result)) {//不存在索引
//            dump($key);exit;
            abort('200', lang('g_error_no_unspecified_lang_key :name :index', ['name' => $name,'index'=>$key]));

        }elseif(isset($index)){
            !array_key_exists($index,$result[$key]) &&
            abort('200', lang('g_error_no_unspecified_lang_key :name[:key] :index', ['name' => $name,'key'=>$key,'index'=>$index]));

            $result = $result[$key][$index];
        }else{
            $result = $result[$key];
        }
    }
    return $result;
}

//检测语言是否有数据
function c_lang_check($name, $key = '')
{


    $result = lang($name);

    if (is_array($result)) {
        if (strpos($key, '.')) {
            list($key, $index) = explode('.',$key);
        } else {
            if(empty($key)){
                return false;
            }
        }

        if(!array_key_exists($key,$result)) {//不存在索引
            return false;
        }elseif(isset($index)){
            if(!array_key_exists($index,$result[$key])){
              return false;
            }
        }
    }

    return empty($result) ? false : true;
}


//获取图片
function get_image_location($img='')
{
    if(preg_match('/^https?/',$img)){
        return $img;
    }else{
        return DIRECTORY_SEPARATOR.$img;
    }
//    return $img;
}


//生成二维码
function generateQrCode()
{
    $qrCode = new \Endroid\QrCode\QrCode('Life is too short to be generating QR codes');

    header('Content-Type: '.$qrCode->getContentType());
    echo $qrCode->writeString();

}