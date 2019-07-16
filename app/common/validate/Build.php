<?php
namespace app\common\validate;
use think\Validate;
class Build extends Validate
{
    protected $rule = [
        'title|标题'      =>  'require|max:32|min:2',
        'name|标识'       =>  'alphaDash|max:32|min:4',
    ];
}
