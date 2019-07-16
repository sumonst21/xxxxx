layui.define(['laytpl', 'layer', 'element','jquery','laypage','form','table'], function(exports){
	window.$ = window.jQuery = layui.jquery;
	var form = layui.form,
		table = layui.table,
		element = layui.element,
		laypage = layui.laypage;
	form.verify({
        money: function(v, item){
        	if(v=='待核'){

        	}else{
	            //v：表单的值、item：表单的DOM对象
	            var reg = /^[0-9]+(.[0-9]{1,2})?$/; 
	            var r = v.match(reg);
	            if(r==null) {
	                return '请输入有效金额'; 
	            }
	            if(v.length==1 && v=="0"){
	            }else{
	                /*if(v.substring(0,1)=='0' && v.length==1){
	                    return '请输入有效金额';
	                }*/
	                var a = parseFloat(v);
	                if(a+"" != v){
	                    return '请输入有效金额';	                	
	                }
	                if(isNaN(a)){
	                    return '请输入有效金额';
	                }
	            }
            }
        },
        identity:function(idCard,item) { 
            idCard = idCard.toUpperCase();
            var regIdCard=/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}X)$)$/;
            //如果通过该验证，说明身份证格式正确，但准确性还需计算
            if(regIdCard.test(idCard)){
                if(idCard.length==18){
                    var idCardWi=new Array( 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ); //将前17位加权因子保存在数组里
                    var idCardY=new Array( 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ); //这是除以11后，可能产生的11位余数、验证码，也保存成数组
                    var idCardWiSum=0; //用来保存前17位各自乖以加权因子后的总和
                    for(var i=0;i<17;i++){
                        idCardWiSum+=idCard.substring(i,i+1)*idCardWi[i];
                    }

                    var idCardMod=idCardWiSum%11;//计算出校验码所在数组的位置
                    var idCardLast=idCard.substring(17);//得到最后一位身份证号码

                    //如果等于2，则说明校验码是10，身份证号码最后一位应该是X
                    if(idCardMod==2){
                        if(idCardLast=="X"){
                        }else{
                            return "身份证号码错误！";
                        }
                    }else{
                        //用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
                        if(idCardLast==idCardY[idCardMod]){
                        }else{
                            return "身份证号码错误！";
                        }
                    }
                } 
            }else{
                return "身份证格式不正确!";
            }
        }
        //我们既支持上述函数式的方式，也支持下述数组的形式
        //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
        ,realname: [
            /^[\u4E00-\u9FA5\uf900-\ufa2d]{2,4}$/
            ,'姓名必须是2到4个中文字符'
        ] 
    });
	function info (a){
		console.info(a);
	}
	window.Ajax={
	    send:function(url,data,type,successfn,errorfn){
	        layer.msg('数据提交中...',{time:500000});
	        errorfn=errorfn || function(d){
				layer.msg(d.msg,{icon:2});
	        }
	        successfn=successfn || function(d){
				layer.msg(d.msg, {icon:1},function() {
					if ((d.url).length>0){location.href= d.url;}
				});
	        }
	        $.ajax({
	            url: url,
	            type:type,
	            dataType: 'json',
	            data:data || {},
	            success:function(d){
	                if(d.code==1){successfn(d);}else{errorfn(d);}
	            },
	            error:function(d){
					layer.msg('服务器繁忙,请稍后再试!',{icon:2});
	            }
	        })
	    },
	    get:function(url,data,success,error){
	        Ajax.send(url,data,'GET',success,error);
	    },
	    post:function(url,data,success,error){
	        Ajax.send(url,data,'POST',success,error);
	    },
	    sendForm:function(f,success,error){
	        Ajax.send($(f).attr('action'),$(f).serialize(),($(f).attr('method') || 'GET'),success,error);
	    }
	}
	$(function(){
		if(ACTION_NAME=='edit' || ACTION_NAME=='add'){
			form.on('submit(formSubmit)',function(data){
				Ajax.sendForm(data.form);
				return false;
			});
			//SyncFormData();
		}
	});
	function getChecked(){
		var deleteNum= [];//定义要传递的数组
		$('table input.checkbox-ids:checked').each(function(index, el) {
			deleteNum.push(el.value);
		});
		return deleteNum;
	}
	window.getChecked = getChecked ;
    form.on('checkbox(allChoose)', function(data) {
        var child = $(data.elem).parents('table').find('tbody input.checkbox-ids');
        child.each(function(index, item) {
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });    

    /* 监听状态设置开关 */
    form.on('switch(switchStatus)', function(data) {
        var that = $(this), status = 0;
        if (!that.attr('data-href')) {
            layer.msg('请设置data-href参数');
            return false;
        }
        if (this.checked) {
            status = 1;
        }
        Ajax.get(that.attr('data-href'), {val:status}, function(res) {
            layer.msg(res.msg);
            if (res.code == 0) {
                that.trigger('click');
                form.render('checkbox');
            }
        });
    });
	$(document).on('click','a.popen',function(){
		layer.open({
			type: 2,
			shade: false,
			area: ['80%','90%'],
			maxmin :true,
			//maxHeight:document.body.clientHeight,
			title:'编辑',
			content: $(this).attr('data-href'),
			zIndex: layer.zIndex, //重点1
			btn: $(this).attr('data-button') ? ['提交', '重置'] : [],
			yes: function(index, layero){
				var f = layero.find('iframe').get(0);
				Ajax.sendForm($(f.contentDocument).find('form'));
				//info( body button'));
			}
			,btn2: function(index, layero){
				var f = layero.find('iframe').get(0);
				$(f.contentDocument).find('form').get(0).reset();
			  	return false;
			},
			success: function(layero,aindex){
				var f = layero.find('iframe').get(0);
				$(f.contentDocument).find('button[type="submit"]').hide();
				//layer.iframeAuto(aindex);
				layer.setTop(layero); //重点2
			}
		});    
		return false;
	}).on('click','button.ajax,a.ajax',function(){
		var txt = $(this).text(),
			href = $(this).attr('href') ? $(this).attr('href') : $(this).attr('data-href'),
			isDel = txt.indexOf('删除')!=-1,
			query={},	
			code = function(href,data) {
				Ajax.post(href, data, function(res) {
					layer.msg(res.msg, {}, function(){
						if (res.code != 0) {
							if(res.url){
								location.href= res.url;
							}else{
								location.reload();
							}
						} 
					});
				});
			};
		if (!href) {
			layer.msg('请设置data-href参数');
			return false;
		}
		if(isDel){
			query['ids'] = getChecked();
		}
		if ($(this).hasClass('confirm')) {
			var tips = $(this).attr('tips') ? $(this).attr('tips') : '您确定要执行此操作吗？';
			layer.confirm(tips, {title:false, closeBtn:0}, function(index){
				code(href,query);
				layer.close(index);
			});
		} else {
		   code(href,query); 
		}
		return false;
	}).on('click','table tr',function(e){
		if(e.target.nodeName=='TD'){
			var ckb = $(this).find('.checkbox-ids').get(0);
			ckb.checked = !ckb.checked;
			form.render('checkbox');
		};
	});

	exports('lib/global', {
		Ajax:Ajax
	});
});
