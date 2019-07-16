define(function(require, exports, module) {
    exports.dlg=null;
    exports.init=function(opt){
        opt=$.extend(true,{
            type:'img',
            currSelect:'FmUpload',
            max:1,
            callback:function(src){console.info(src);exports.dlg.close();}
        }, opt);
        exports.dlg=dialog({id:'FileManageDialog',padding:0}).showModal()
        exports.dlg
            .title('选择素材')
            .width(1060)
            .content('<iframe id="FileManageFrame" name="FileManageFrame" src="'+__URL__+'/fileManage.html?type='+opt.type+'&currSelect='+opt.currSelect+'&maxselect='+opt.max+'" frameborder="0" width="100%" height="500"></iframe>')
            .button([
                {
                    value: '确定',
                    callback: function () {
                        var fs=FileManageFrame.getSelectFile();
                        opt.callback(fs);
                        return false;
                    },
                    autofocus: true
                },
                {value: '取消',callback:true}
            ]);
    }
    exports.close=function(){
        exports.dlg.close();
    }
    exports.initBtn=function(id,type){
		type = type ? type : 'img';
		$('#'+id).click(function(){
			exports.init({type:type,callback:function(o){
                    o=o[0];
    				$('#'+id).siblings(':text').val(o.src);
    				$('#'+id).siblings('.thumbUrl').attr('src',o.src);
    				exports.close();
    			}
            })
		})
	}
});
