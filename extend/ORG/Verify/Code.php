<?php
namespace ORG\Verify;
class Code{
	static function send($type='normal'){
	    $captcha = new \think\captcha\Captcha();
	    return $captcha->entry($type);
	}
	static function valid($type,$val=''){
		$val = $val ?: input('code');
	    $captcha = new \think\captcha\Captcha();
	    return $captcha->check($val, $type);
	}
}