<?php
namespace app\index\controller;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;

class Index extends Common
{
    public function index()
    {
        $goods_list = model('goods')->with(['linkOnePrice'=>function($query){
            $query->group('gid');
        }])->limit(8)->select();
//        dump($goods_list);exit;
        return view('index',[
            'goods_list' => $goods_list
        ]);
    }

    //生成图片
    public function qrcode()
    {
        $content = $this->request->param('content','','base64_decode');
        $qrCode = new \Endroid\QrCode\QrCode($content);
        $qrCode->setSize(300);

        $path = \Env::get('vendor_path');
        //资源路径
        $asset_path = $path.'/endroid/qr-code/assets/';
        // Set advanced options
        $qrCode
            ->setWriterByName('png')
            ->setMargin(10)
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
//            ->setLabel('Scan the code', 16, $asset_path.'noto_sans.otf', LabelAlignment::CENTER)
//            ->setLogoPath($asset_path.'symfony.png')
            ->setLogoWidth(150)
            ->setValidateResult(false)
        ;

        return response()->content($qrCode->writeString())->header(['Content-Type'=>$qrCode->getContentType()]);
    }
}
