<?php
namespace ORG\Verify;
class Phone{
	static $tpyeArr=[
		'normal'=>'',
		'login'	=>'登陆',
		'reg'	=>'注册',
	];
	static function send($phone,$type='normal'){
        $code = build_count_rand(1);
        session($type.'SmsCode',md5($code[0]));
        \ORG\Sms::send('18926034082',['action'=>self::$tpyeArr[$type],'code'=>$code[0],'tpl'=>'415104']);
	}
	static function valid($type,$val=''){
		$code= session($type.'SmsCode');
		$val = $val ?: input('code');
		if($code == md5($val)){
			return true;
		}else{
			return false;
		}
	}
}