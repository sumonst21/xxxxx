<?php
namespace app\common\validate;
use think\Validate;
class Caiji extends Validate
{
    protected $rule = [
        'title|标题'      =>  'require|alphaDash|max:32|min:5|unique:Caiji'
    ];
}
