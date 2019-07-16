layui.define(['jquery','element'],function(exports) {
	var page = 1,
		pagesize =15,
		element = layui.element,
		$ = layui.jquery;
	window.$ = window.jQuery = $;
		load = function(){
			$.getJSON('/Attach/getList.html', {page:page,pagesize:pagesize}, function(json, textStatus) {
				$(json).each(function(k, v) {
					$('#FmList').append('<div class="item" data-id="'+v.id+'"><img src="'+v.url+'" alt="" /><i class="layui-icon preview"></i><i class="layui-icon selector"></a></div>');
				});
			});
		}
	load();
	$(document).on('click','.item',function(){
		var t=$(this).attr('type'),
			id=$(this).attr('data-id'),
			src=$(this).find('img:eq(0)').attr('src'),
			title=$(this).find('img:eq(0)').attr('title') || "";
		if(id){
			if($(this).hasClass('selected')){
				__SelectFiles['f'+id]=null;
				$(this).removeClass('selected');
				selected-=1;
			}else{
				if(maxselect>selected){
					__SelectFiles['f'+id]={id:id,src:src,title:title};
					$(this).addClass('selected');
					selected+=1
				}else{
					if(maxselect == 1){
						$('.item').removeClass('selected');
						__SelectFiles['f'+id]={id:id,src:src,title:title};
						$(this).addClass('selected');
						selected=1;
					}else{
						alert('最多选择'+maxselect+'个文件');
					}
				}
			}
		}
	})
	//文件上传
	var mimeType={
		csv : 'text/csv',
		xls : 'application/vnd.ms-excel',
		doc : 'application/msword',
		ppt : 'application/vnd.ms-powerpoint',
		jpg : 'image/jpeg',
		png : 'image/png',
		gif : 'image/gif',
		bmp : 'image/bmp',
		zip : 'application/zip',
		rar : 'application/octet-stream',
		docx : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		xlsx : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		pptx : 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
	} 
	var accept={title:[],extensions:[],mimeTypes:[]};
	$.each(fileext.split(','),function(k,v){
		accept['title'].push(v);
		accept['extensions'].push(v);
		accept['mimeTypes'].push(mimeType[v]);
	});
	accept.title = accept.title.join(',');
	accept.extensions = accept.extensions.join(',');
	accept.mimeTypes = accept.mimeTypes.join(',');
	fnaccept = accept;
	element.on('tab(FileManage)', function(data){
	  console.log(this); //当前Tab标题所在的原始DOM元素
	  console.log(data.index); //得到当前Tab的所在下标
	  console.log(data.elem); //得到当前的Tab大容器
	});
	$.getScript('https://cdn.staticfile.org/webuploader/0.1.5/webuploader.html5only.min.js',function(){
		//info(accept);
		var uploader = WebUploader.create({
		    swf: 'https://cdn.staticfile.org/webuploader/0.1.5/Uploader.swf',
		    server: ADMIN_PATH +'/Attach/upload.html',
		    accept:fnaccept,
		    pick: '#picker',
		    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
		    resize: false
		});
		// 当有文件被添加进队列的时候
		uploader.on( 'fileQueued', function( file ) {
		    var $li = $(
	            '<div id="' + file.id + '" class="file">' +
	            '	<div class="thumb">'+
	            '		<p><img src="" alt="" /><img src="" alt="" class="hidden" /></p>'+
	            '	</div>'+
	            '	<div class="title">' + file.name + '</div>' +
	            '	<div class="progress"><span></span></div>'+
				'	<div class="mask">'+
				'		<div class="overlay"></div>'+
				'		<i class="fa fa-check"></i>' +
	            '	</div>'+
	            '</div>'
	        ),
	        $img = $li.find('img');
		    $('#queueList').append( $li );
		    uploader.makeThumb( file, function( error, src ) {
		        if ( error ) {
		            $img.replaceWith('<span>不能预览</span>');
		            return;
		        }
		        $img.attr( 'src', src );
		    }, 120, 120 );
		});
		// 文件上传过程中创建进度条实时显示。
		uploader.on( 'uploadProgress', function( file, percentage ) {
		    var $li = $( '#'+file.id ),
		        $percent = $li.find('.progress span');
		    // 避免重复创建
		    if ( !$percent.length ) {
		        $percent = $('<p class="progress"><span></span></p>').appendTo($li).find('span');
		    }
		    $percent.css( 'width', percentage * 100 + '%' );
		});
		uploader.on( 'uploadSuccess', function(file,data) {
			//console.info('wwww',data.file);
			$('#StartUpload').removeClass('disabled');
			var data=data['file'];
			$('#'+file.id ).attr('data-id',data.id);
			$('#'+file.id+' img').attr('src',__ROOT__ +data.url);
			$('#'+file.id+' .title').text(data.title);
		    $('#'+file.id ).find('p.state').text('已上传');
		});

		uploader.on( 'uploadError', function( file ) {
			$('#StartUpload').removeClass('disabled');
		    $( '#'+file.id ).find('p.state').text('上传出错');
		});

		uploader.on( 'uploadComplete', function( file ) {
			$('#StartUpload').removeClass('disabled');
		    $( '#'+file.id ).find('.progress').fadeOut();
		});
	    $('#StartUpload').click(function() {
	        if ($(this).hasClass('disabled')) {
	            return false;
	        }
	        uploader.upload();
	        $('#StartUpload').addClass('disabled');
	    });
	});	
    exports('FM',{});
});