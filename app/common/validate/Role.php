<?php
namespace app\common\validate;
use think\Validate;
class Role extends Validate
{
    protected $rule = [
        'title|标题'      =>  'require|max:32|min:2',
    ];
}
