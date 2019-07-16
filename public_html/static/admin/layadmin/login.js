layui.define(['form','jquery','layer'], function(exports) {
    var $ = layui.jquery,form= layui.form,jQuery=$,layer=layui.layer;
    form.on('submit(login)', function(data){
    	layer.msg('数据提交中...',{time:500000});
        var errorfn=errorfn || function(d){
			layer.msg(d.msg,{icon:2});
        }
        var successfn=successfn || function(d){
			layer.msg(d.msg, {icon:1},function() {
				if ((d.url).length>0){location.href= d.url;}
			});
        }
        $.ajax({
            url: $(data.form).attr('action'),
            type:'POST',
            dataType: 'json',
            data:data.field || {},
            success:function(d){
                if(d.code==1){successfn(d);}else{errorfn(d);}
            },
            error:function(d){
				layer.msg('服务器繁忙,请稍后再试!',{icon:2});
            }
        })

		console.log(data) //被执行事件的元素DOM对象，一般为button对象
		return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
	});
    exports('login',{});
});