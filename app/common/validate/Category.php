<?php
// +----------------------------------------------------------------------
// | 全局分类模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\common\validate;
use think\Validate;
class Category extends Validate {
    protected $rule = [
        'title|标题'      =>  'require|max:32|min:2',
        'name|标识'       =>  'alphaDash|max:32|min:4',
    ];
}
?>