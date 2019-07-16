var Ajax={}
Ajax.send = function(url,data,type='get',sfn,efn){
	sfn = sfn || function(d){
        if(d.url) {window.location.href=d.url;}else{$.toast(d.msg);}
	},
	efn =function(d){$.toast(d.msg);};
	var opt= {
		url:url,
		data:data,
		type:type,
		dataType:'json',
		success:function(d){
			if(d.code==1){sfn(d);}else{efn(d);}
		},
		error:function(d){$.toast('服务器繁忙,请稍后再试');}
	};
	$.ajax(opt);
}
Ajax.sendForm = function(f,cb){
	f = $(f);
	Ajax.send(f.attr('action'),f.serialize(),'post',cb);
}
Ajax.get = function(url,cb,type){
	Ajax.send(url,{},'get',cb);
}