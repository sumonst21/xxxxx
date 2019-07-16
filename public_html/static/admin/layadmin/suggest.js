layui.define(['element', 'form','laytpl'], function(exports) {
    var $ = layui.jquery,jQuery=$,laytpl=layui.laytpl;
	var suggest = function(obj,opt){
		var opt = $.extend(true,{source:'',template:'<dd data-date="{{d.date}}" data-address="{{d.address}}">{{d.name}}</dd>'}, opt);
		function buildList(json){			
        	var s= "";
        	for(var i in json){
        		s+= laytpl(opt.template).render(json[i]);
        	}
	        return s;
		}
		$(obj).wrap('<div class="layui-select-title"></div>');
		var layui_select_title = $(obj).parents('.layui-select-title');
		$(obj).after('<i class="layui-edge"></i>');
		//layui-form-selected
		//layui-unselect 
		layui_select_title.wrap('<div class="layui-form-select"></div>');
		var top = $(obj).parents('.layui-form-select');
		layui_select_title.after('<dl class="layui-anim layui-anim-upbit"></dl>');
		var dl = top.find('dl');
		var selected=function(item){
			$(obj).val(item.text());
			opt.callback && opt.callback.apply(this,item);
		}
		dl.on('click', 'dd', function(event) {
			selected($(this));
			event.preventDefault();
		});
		function a(){
			var v=$(obj).val();
			if(v){
		        $.getJSON(opt.source,{query:v}, function(json, textStatus) {
		        	oldval = v;
		        	dl.html(buildList(json));
		        	if(top.hasClass('layui-form-selected')){

		        	}else{
						dl.find('dd').removeClass('layui-this');
	        			top.addClass('layui-form-selected');
		        	}
		        });
	        }
		}
		var oldval= $(obj).val();
		$(obj).on('focus',function(){
			a();
		}).on('keydown',function(e){
			var cur = dl.find('.layui-this');
			var dds = dl.find('dd');
			i = dds.index(dl.find('.layui-this'));
			switch(e.keyCode){
				case 13:
					selected(cur);
        			top.removeClass('layui-form-selected');
					e.stopPropagation();
					e.preventDefault();
					break;
				case 38:
				case 40:
					if(e.keyCode==38){
						//UP
						var n=i-1;
						if(n<0){n=dds.length-1;}
					}else{
						//down
						var n=i+1;
						if(n>(dds.length-1)){n=0;}
					}
					dds.removeClass('layui-this');
					dds.eq(n).addClass('layui-this');
					e.stopPropagation();
					e.preventDefault();
					return false;
					break;
			}
		}).on('keyup',function(e){
			if(oldval != $(obj).val()){
				a();
			}
		});
	}
	/*$.suggest = function(a,b){
		console.log(this,a,b);
	}*/
	$.fn.suggest = function(a){
		return new suggest(this,a)
	}
    exports('suggest',suggest);
});