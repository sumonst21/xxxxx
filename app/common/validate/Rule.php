<?php
namespace app\common\validate;
use think\Validate;
class Rule extends Validate
{
	protected $regex = [ 'name' => '[A-Za-z\/]+'];
    protected $rule = [
        'title|标题'      =>  'require|max:32|min:2',
        'name|标识'       =>  'regex:name|max:32|min:4',
        //'name'       =>  'regex:name',
    ];
	protected $message = [
	    'name.regex' => '标识只能是字母和"/"',
	];
}
