<?php
namespace app\common\validate;
use think\Validate;
class Income extends Validate
{
    protected $rule = [
        'res_id|源ID'      =>  'require|integer',
        'mod|模型'       =>  'require',
        //'nickname|昵称'       =>  'require|max:32|min:6',
        'date|日期'       =>  'require|integer',
        'money|金额'       =>  'require|float',
    ];
}
