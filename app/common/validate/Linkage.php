<?php
namespace app\common\validate;
use think\Validate;
class Linkage extends Validate
{
    protected $rule = [
        'title|标题'      =>  'require|max:32|min:2',
        'name|标识'       =>  'require|alpha|max:32|min:1',
        'type|类型'		  =>  'require'
    ];
}
