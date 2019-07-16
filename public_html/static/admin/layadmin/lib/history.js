layui.define(['laytpl', 'layer', 'element','jquery','table','laydate'], function(exports){
    var $ = layui.jquery,
        laydate = layui.laydate,
        layer = layui.layer,
        laytpl = layui.laytpl,
        element = layui.element,
        table = layui.table,
        imgs= {
            car : 'https://webapi.amap.com/images/car.png',
            start : '/static/admin/layadmin/history/img/start.png',
            end : '/static/admin/layadmin/history/img/marker_stop.png',
        };
    var history = {
        data : null,
        map : null,
        marker : null,
        lineArr : [],
        getData : function(callback){
            var _that = this;
            $.getJSON('/Gps/GetHistory', {deviceNumber: deviceNumber}, function(json, textStatus) {
                callback(json);
            });
        },
        /*getData : function(){

        },
        getData : function(){

        },
        getData : function(){

        },*/
        play : function(){
            var _that = this;
            var data = this.data.data;
            for(var i in data){
                _that.lineArr.push(new AMap.LngLat(data[i].GLng, data[i].GLat));                
            }
            this.map.panTo(_that.lineArr[0]);
            this.marker = new AMap.Marker({
                map: _that.map,
                position: [data[0].GLng, data[0].GLat],
                icon: imgs.car,
                offset: new AMap.Pixel(-26, -13),
                autoRotation: true
            });
            var startMarker = new AMap.Marker({
                map: _that.map,
                position: [data[0].GLng, data[0].GLat],
                icon: imgs.start,
                offset: new AMap.Pixel(-26, -13),
                autoRotation: true
            });
            var endMarker = new AMap.Marker({
                map: _that.map,
                position: [data[data.length-1].GLng, data[data.length-1].GLat],
                icon: imgs.end,
                offset: new AMap.Pixel(-26, -13),
                autoRotation: true
            });
            // 绘制轨迹
            var polyline = new AMap.Polyline({
                map: this.map,
                path: _that.lineArr,
                strokeColor: "green",  //线颜色
                // strokeOpacity: 1,     //线透明度
                strokeWeight: 6,      //线宽
                showDir: true,
                // strokeStyle: "solid"  //线样式
            });
            var passedPolyline = new AMap.Polyline({
                map: this.map,
                // path: _that.lineArr,
                strokeColor: "#FF9900",  //线颜色
                // strokeOpacity: 1,     //线透明度
                strokeWeight: 6,      //线宽
                showDir: true,
                // strokeStyle: "solid"  //线样式
            });


            this.marker.on('moving',function(e){
                passedPolyline.setPath(e.passedPath);
            })
            this.map.setFitView();
            _that.marker.moveAlong(_that.lineArr, 5000);
            /*
            AMap.event.addDomListener(document.getElementById('start'), 'click', function() {
                _that.marker.moveAlong(_that.lineArr, 500);
            }, false);
             AMap.event.addDomListener(document.getElementById('pause'), 'click', function() {
                _that.marker.pauseMove();
               }, false);
              AMap.event.addDomListener(document.getElementById('resume'), 'click', function() {
                _that.marker.resumeMove();
            }, false);
            AMap.event.addDomListener(document.getElementById('stop'), 'click', function() {
                _that.marker.stopMove();
            }, false);*/
            AMap.event.addDomListener(document.getElementById('speedBar'), 'change', function() {
                _that.marker.moveAlong(_that.lineArr, $(this).val());
                console.info();
                //_that.marker.stopMove();
            }, false);
        },
        init : function(container){
            var _that = this;
            Date.prototype.Format = function (fmt) {
                var o = {
                    "M+": this.getMonth() + 1, //月份
                    "d+": this.getDate(), //日
                    "h+": this.getHours(), //小时
                    "m+": this.getMinutes(), //分
                    "s+": this.getSeconds(), //秒
                    "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                    "S": this.getMilliseconds() //毫秒
                };
                if (/(y+)/.test(fmt))
                    fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
                for (var k in o)
                    if (new RegExp("(" + k + ")").test(fmt))
                        fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
                return fmt;
            }
            Date.prototype.addDays = function (d) {
                this.setDate(this.getDate() + d);
            };
            Date.prototype.subDays = function (d) {
                this.setDate(this.getDate() - d);
            };
            var today  = new Date();
            //执行一个laydate实例
            laydate.render({
                range: '至' ,
                elem: '#DateRange', //指定元素,
                max: today.Format('yyyy-MM-dd hh:mm:ss'),
                change:function(value,date,endDate){
                    console.info(value,date,endDate);
                }
            });
            laydate.render({
                elem: '#testDate'
                ,position: 'static',
                max: today.Format('yyyy-MM-dd hh:mm:ss'),
                showBottom :false
                ,change: function(value, date){ //监听日期被切换
                    var sd = new Date(value);
                    sd.subDays(1);
                    $('#DateRange').val(sd.Format('yyyy-MM-dd')+' 至 '+value);
                }
            });
            $('.toggleButton').click(function(){
                $(this).parents('.layui-nav').toggleClass('show');
            })

            _that.map = new AMap.Map(container, {
                resizeEnable: true,
                center: [116.397428, 39.90923],
                zoom: 17
            });
            /*
            var index = layer.load(2);
            this.getData(function(json){
                layer.close(index);
                _that.data = json;
                _that.play();
            })*/
        },
    };
    exports('lib/history',history);
});
