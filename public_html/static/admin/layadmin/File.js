layui.define(['laytpl', 'layer', 'element','jquery','laypage','form','table'], function(exports){
	window.$ = window.jQuery = layui.jquery;
	var form = layui.form,
		table = layui.table,
		element = layui.element,
		laypage = layui.laypage;
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
	var FM=function(){		
		var accept = $(this).attr('accept');
		var fileext = $(this).attr('ext');
		fileext = fileext || 'jpg,gif,bmp,png';
		if(accept){
		}
		//images（图片）、file（所有文件）、video（视频）、audio（音频）
		layer.tab({
		    tab: [{
		      	title: '上传', 
		      	content: [
					'<div id="FmUpload">',
					'    <div id="queueList">',
					'        <div id="dndArea" class="placeholder">',
					'            <div id="picker">点击选择图片</div>',
					'            <p>或将照片拖到这里，单次最多可选300张</p>',
					'        </div>',
					'    </div>',
					'    <div class="fmfooter">',
					'        <span class="progress hide">',
					'            <span class="text">0%</span>',
					'            <span class="percentage"></span>',
					'        </span>',
					'		 <span class="info hide"></span>',
					'        <button class="layui-btn layui-btn-primary layui-btn-sm hide" id="filePicker2">添加文件</button><button class="layui-btn layui-btn-sm hide" id="StartUpload">开始上传</button><button class="layui-btn layui-btn-sm hide" id="Sure1">确认</button>',
					'    </div>',
					'</div>'
		      		].join('')
		    }, {
		      	title: '文件库', 
		      	content: ['<div style="position:absolute;top:0;bottom:50px;left:0;right:0;overflow:hidden;overflow-y:scroll;"><div id="FmList"></div></div>',
					'    <div class="fmfooter" style="bottom:1px;">',
					'		<div id="page"></div>',
					'		<button class="layui-btn layui-btn-sm" id="Sure1">确认</button>',
					'    </div>'].join('')
		    }],
			area: ['80%','80%'],
			zIndex: layer.zIndex, //重点1
			success: function(layero, index){
				var load = function(page,pagesize){
					$.getJSON('/Attach/getList.html', {page:page,pagesize:pagesize}, function(json, textStatus) {
						$(json).each(function(k, v) {
							$('#FmList').append('<div class="fmfile uploaded" data-id="'+v.id+'"><div class="thumb"><p><img src="'+v.url+'" alt="" /><img src="'+v.url+'" alt="" class="hidden"/></p></div><div class="mask"><div class="overlay"></div><i class="fa fa-check"></i><i class="layui-icon preview"></i><i class="layui-icon selector"></div></div>');
						});
					});
				}
				$(document).on('click','.fmfile.uploaded',function(){
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
									$('.fmfile.uploaded').removeClass('selected');
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
				load(1,10);
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
						dnd :'#queueList',
						disableGlobalDnd : true,
					    swf: 'https://cdn.staticfile.org/webuploader/0.1.5/Uploader.swf',
					    server: ADMIN_PATH +'/Attach/upload.html',
					    pick: '#picker',
					    formData :{_ajax:1},
					    compress:{
						    width: 1500,
						    //height: 1600,
						    // 图片质量，只有type为`image/jpeg`的时候才有效。
						    quality: 90,
						    // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
						    allowMagnify: false,
						    // 是否允许裁剪。
						    crop: false,
						    // 是否保留头部meta信息。
						    preserveHeaders: true,
						    // 如果发现压缩后文件大小比原来还大，则使用原来图片
						    // 此属性可能会影响图片自动纠正功能
						    noCompressIfLarger: true,
						    // 单位字节，如果图片大小小于此值，不会采用压缩。
						    compressSize: 1024
						},
					    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
					    resize: false
					});
				    // 添加“添加文件”的按钮，
				    uploader.addButton({
				        id: '#filePicker2',
				        label: '继续添加'
				    });
        			var fileCount=0;
        			var fileSize =0;

					// 当有文件被添加进队列的时候
					uploader.on( 'fileQueued', function( file ) {
					    var $li = $(
				            '<div id="' + file.id + '" class="fmfile">' +
				            '	<div class="thumb">'+
				            '		<p><img src="" alt="" /><img src="" alt="" class="hidden" /></p>'+
				            '	</div>'+
				            '	<div class="title">' + file.name + '</div>' +
				            '	<div class="progress layui-progress" lay-filter="'+file.id+'"><div class="layui-progress-bar" lay-percent="10%"></div></div>'+
							'	<div class="mask">'+
							'		<div class="overlay"></div>'+
							'		<i class="fa fa-check"></i>' +
				            '	</div>'+
				            '</div>'
				        ),
				        $img = $li.find('img');
				        if(file.type.substring(0,5)=='image'){
						    uploader.makeThumb( file, function( error, src ) {
						        if ( error ) {
						            $li.find('.thumb p').html('<span>不能预览</span>');
						            return;
						        }
						        $img.attr( 'src', src );
						    }, 150, 150);				        	
				        }else{
						    $li.find('.thumb p').html('<i class="fm-ft-'+file.ext+'"><i class="layui-icon">&#xe621;</i></i>');
				        }
					    $('#queueList').append( $li );
        				fileCount++;
        				fileSize += file.size;
				        if ( fileCount > 0 ) {
				        	$('#dndArea').hide();
				        	$('#StartUpload').show();
				        	$('#filePicker2').show();
				        }
					});
					uploader.on('FileDequeued',function( file ) {
				        fileCount--;
				        fileSize -= file.size;
				        if ( !fileCount ) {
				        	$('#dndArea').show();
				        	$('#StartUpload').hide();
				        	$('#filePicker2').hide();
				        }
				        removeFile( file );
				        updateTotalProgress();

				    });
					// 文件上传过程中创建进度条实时显示。
					uploader.on( 'uploadProgress', function( file, percentage ) {
					    var p = $( '#'+file.id +' .progress');
					    p.show();
					    element.progress(p.attr('lay-filter'), percentage * 100 + '%');
					});
					uploader.on( 'uploadSuccess', function(file,data) {
						$('#StartUpload').removeClass('layui-btn-disabled');
						var data=$.parseJSON(data['_raw']);
						$('#'+file.id ).attr('data-id',data.id).addClass('uploaded');
						$('#'+file.id+' img').attr('src',data.savepath +data.savename);
						$('#'+file.id+' .title').text(data.name);
					    $('#'+file.id ).find('p.state').text('已上传');
					});

					uploader.on( 'uploadError', function( file ) {
						$('#StartUpload').removeClass('layui-btn-disabled');
					    $( '#'+file.id ).find('p.state').text('上传出错');
					});

					uploader.on( 'uploadComplete', function( file ) {
						$('#StartUpload').removeClass('layui-btn-disabled');
					    $( '#'+file.id ).find('.progress').fadeOut();
					});
				    $('#StartUpload').click(function() {
				        if ($(this).hasClass('layui-btn-disabled')) {
				            return false;
				        }
				        uploader.upload();
				        $('#StartUpload').addClass('layui-btn-disabled');
				    });
				});	
			  	//console.log(layero, index);
			}
		});
	}
	var singleupload = function(){
		
	}
	$(document).on('click','.fm button',function(){
		
	}).on('click','.fm button',function(){
		
	})
	exports('File', {
		upload:singleupload,
		fm = FM
	});
});