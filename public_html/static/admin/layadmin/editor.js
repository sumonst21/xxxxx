layui.define(['form','jquery','layer'], function(exports) {
    var $ = layui.jquery,form= layui.form,jQuery=$,layer=layui.layer;
    window.$ = window.jQuery = $;
    var plugins=[
        'align',
        'char_counter',
        'code_beautifier',
        'code_view',
        'colors',
        'draggable',
        'emoticons',
        'code_view',
        'entities',
        'file',
        'font_family',
        'font_size',
        'forms',
        'fullscreen',
        'help',
        'image',
        'image_manager',
        'inline_style',
        'line_breaker',
        'link',
        'lists',
        'paragraph_format',
        'paragraph_style',
        'print',
        'quote',
        'save',
        'table'
    ];
    var $js = '/js.php?../FE/js/froala_editor.pkgd.min.js,../FE/js/languages/zh_cn.js';
    var $css = '/css.php?FE/css/froala_editor.min.css,FE/css/froala_style.min.css';
    for (var i = 0; i < plugins.length; i++) {
        $js+=',../FE/js/plugins/'+plugins[i]+'.min.js';
        $css+=',FE/css/plugins/'+plugins[i]+'.css';
    }
    layui.link(ADMIN_PATH+'/static/fonts/font-awesome/min.css?v=1');
    //layui.link(ADMIN_PATH+'/static/FE/css/froala_editor.pkgd.min.css?v=1');
    //layui.link(ADMIN_PATH+'/static/FE/css/froala_style.min.css');
    //layui.link('../../Js/codemirror/codemirror.min.css');
    layui.link($css);
    $.getScript($js,function(){
        $('#editor').froalaEditor({
            inlineMode: false,
            language:'zh_cn',
            heightMin:400,
            spellcheck:false,
            autosave: true,
            autosaveInterval: 180,
            saveURL: '/Article/autosave.html',
            saveParams: {
                title:function (){return $('[name=title]').val();},
                tags:function (){return $('#tags_ipt').val();},
                cid:function (){return $('[name=cid]').val();}
            },
            saveParam:'content',
            plainPaste: true,
            enableScript: false,
            heightMax:600,
            toolbarButtons:[
            'fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'fontFamily', 'fontSize', 'color', 'inlineStyle', 'paragraphStyle', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote',  'insertLink', 'insertImage', 'insertVideo', 'embedly', 'insertFile', 'insertTable', '|', 'emoticons', 'specialCharacters', 'insertHR', 'selectAll', 'clearFormatting', '|', 'print', 'spellChecker', 'help', 'html', '|', 'undo', 'redo'
            ],
            alwaysBlank: true,
            imageManagerLoadURL: "/Attach/getList.html?type=image&p=",
            imageManagerDeleteURL: "/Attach/delete.html",
            imageUploadURL: "http://up.qiniu.com",
            imageUploadToQiniu: {
                getTokenUrl: "/Attach/getToken",
                domain: "http://static.sssui.com/",
                saveUrl: "/Attach/save.html?key=",
                params: {
                    //crc32: "",
                    accept: "application/json"
                }
            },
            fileUploadURL: "http://up.qiniu.com",
            fileUploadToQiniu: {
                getTokenUrl: "/Attach/getToken",
                domain: "http://static.sssui.com/",
                saveUrl: "/Attach/save.html?key=",
                params: {
                    //crc32: "",
                    accept: "application/json"
                }
            },
        }).on('editable.afterRemoveImage', function (e, editor, $img) {
            // Set the image source to the image delete params.        
            editor.options.imageDeleteParams = {src: $img.attr('src')};
            // Make the delete request
            editor.deleteImage($img);
        });
    })
    //'../../Js/codemirror/codemirror.min',
    //'../../Js/codemirror/codemirror.min.css'
    //require('editor');
    $('form .submit').click(function(){
        if($('[name=title]').val()==""){return '';}
        var a1=$('<div id="a1" style="display:none;">'+$('#editor').froalaEditor('html.get')+'</div>');
        a1.find('img').removeAttr('data-key').removeAttr('data-hash');
        var d = dialog({}).showModal();
        d.content('<div class="ui-dialog-loading"><i class="fa fa-spinner"></i>&nbsp;&nbsp;Loading...</div>');
        SSSUI.catchImg(a1,d,function(){
            d.content('<div class="ui-dialog-loading"><i class="fa fa-spinner"></i>&nbsp;&nbsp;正在保存内容到服务器...</div>');
            var img =a1.find('img').eq(0).attr('src');
            $('#cover').val(img||'/static/cover.jpg');
            $('#content').val(a1.html());
            $.post('/Article/edit.html',$('form').serialize(),function(res){
                if(res.status==1){
                    d.content(res.info);
                    setTimeout(function () {
                        d.close().remove();
                        window.location=res.url;
                    }, 2000);
                }else{       
                    d.content(res.info);
                    setTimeout(function (){d.close().remove();}, 1000);
                }
            },'JSON');
            
        });
        return false;
    });
    $(document).keydown(function(e) {
        if((e.altKey && e.keyCode==83) || (e.ctrlKey && e.keyCode==13)){
            $('form .submit').trigger('click');
        }
    });
    exports('editor',{});
});