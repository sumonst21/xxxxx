<?php
// +----------------------------------------------------------------------
// | 文章模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

namespace app\common\validate;
use think\Validate;

class Download extends validate {

    protected $rule = [
        'url' =>  'require',
        'title'   =>  'require|min:2|max:50|unique:Download',
    ];
    protected $message  =   [
        'url.require'		=> '请上传文件！',
        'title.require'		    => '标题必须填写',
        'title.min'				=> '标题最最少2个字符',
        'title.max'				=> '标题最多不能超过25个字符',
        'title.unique'		    => '标题已经存在',    
    ];

}
?>