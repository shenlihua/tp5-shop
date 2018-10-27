(function ($) {
    var href = window.location.href;
    var suffix = '.php';
    var url = '';
    var search_index = href.indexOf(suffix);
    if(search_index != -1) {
        url = href.substr(0,search_index)+suffix
    }

    //上传对象
    $.fn.upload_obj = {
        url: url+'/Upload/upload'
        , accept: 'images'
        , acceptMime: 'image/*'
        , multiple: false     //是否允许多人上传
        , number:0      //上传限制 0不限制
        , done:''
        , upload: function (upload, elem, data) {
            console.log($.fn.upload_obj);
            //执行实例
            var uploadInst = upload.render({
                elem: elem //绑定元素
                , url: $.fn.upload_obj.url
                , accept: $.fn.upload_obj.accept
                // , acceptMime: $.fn.upload_obj.acceptMime
                , multiple: $.fn.upload_obj.multiple
                , number: $.fn.upload_obj.number
                , data: data
                , done: function (res) {
                    if($.fn.upload_obj.done){
                        $.fn.upload_obj.done(res)
                    }else {
                        //上传完毕回调
                        // console.log(res)
                        //文件上传成功
                        layer.msg(res.msg)
                        if(res.code == 1) {
                            var item = this.item;
                            //第一个图添加隐藏图片地址
                            $(item).parent().find('input:eq(0)').val(res.data)

                            $(item).parent().find('img').attr('src','/'+res.data)
                        }

                    }
                }
                , error: function () {
                    //请求异常回调
                    layer.msg('upload error')
                }
            });
        }
        , upload_img: function (upload, elem, data) {
            console.log($.fn.upload_obj);
            //执行实例
            var uploadInst = upload.render({
                elem: elem //绑定元素
                , url: $.fn.upload_obj.url
                , accept: $.fn.upload_obj.accept
                , acceptMime: $.fn.upload_obj.acceptMime
                , multiple: $.fn.upload_obj.multiple
                , number: $.fn.upload_obj.number
                , data: data
                , done: function (res) {
                    if($.fn.upload_obj.done){
                        $.fn.upload_obj.done(res)
                    }else {
                        //上传完毕回调
                        // console.log(res)
                        //文件上传成功
                        layer.msg(res.msg)
                        if(res.code == 1) {
                            var item = this.item;
                            //第一个图添加隐藏图片地址
                            $(item).parent().find('input:eq(0)').val(res.data)

                            $(item).parent().find('img').attr('src','/'+res.data)
                        }

                    }
                }
                , error: function () {
                    //请求异常回调
                    layer.msg('upload error')
                }
            });
        }
    }
})(jQuery)
