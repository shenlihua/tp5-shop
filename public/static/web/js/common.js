(function ($) {
    $.common = {
        do_main: ''//域名
        //商品计数器
        ,numberListeners:[]
        , numberInc: function (obj,limit_num,step) { //增加
            var number = parseInt(obj.val());
            number = number>0?number:1;

            step = parseInt(step)//步进值
            step = step?step:1;

            number +=step
            if(number>limit_num){
                return;
            }

            $(obj).val(number)
            this.numberListeners.forEach(function(listener){
                listener(obj)
            })
        }
        //计数器减少
        , numberDec: function (obj,step) {//减少
            var number = parseInt(obj.val());
            number = number>0?number:1;

            step = parseInt(step)//步进值
            step = step?step:1;
            number -=step
            if(number<1){
                return;
            }
            $(obj).val(number)
            this.numberListeners.forEach(function(listener){
                listener(obj)
            })
        }
        //购物车商品信息
        ,cartGoodsInfo:function(){

        }
        //获取域名
        , set_do_main: function () {
            if (this.do_main.length <= 0) {
                var url = document.location.toString();
                var arrUrl = url.split("//");

                var start = arrUrl[1].indexOf("/");
                var relUrl = arrUrl[1].substr(0, start);//stop省略，截取从start开始到结尾的所有字符
                this.do_main = arrUrl[0] + '//' + relUrl
            }
            return this.do_main;
        }
    }
    //直接处理域名
    $.common.set_do_main();
})(jQuery)