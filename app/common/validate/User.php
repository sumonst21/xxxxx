<?php
namespace app\common\validate;
use think\Validate;
class User extends Validate
{
    protected $rule = [
        'account|帐号'      =>  'require|alphaDash|max:32|min:5|unique:user',
        'password|密码'       =>  'require|max:32|min:6',
        //'nickname|昵称'       =>  'require|max:32|min:6',
        'nickname|密码'       =>  'require|max:32|min:6',
    ];
}
