<?php
// +----------------------------------------------------------------------
// | 用户
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\home\controller;
use app\home\controller\Base;
class Git extends Base
{
    public function index()
    {
    	Config('app_trace',0);
    	//P($_SERVER);
		/*$data = Input('post.');
        P($data);*/
        $data = json_decode(file_get_contents('php://input'),true);
        //P($data);
		$secret = "n1e5a6s6m7";
		$path = "/www/web/xxx";
		// Headers deliveried from GitHub
		$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
		if ($signature) {
		    /*$hash = "sha1=" . hash_hmac('sha1', $HTTP_RAW_POST_DATA, $secret);
		    if (strcmp($signature, $hash) == 0) {*/
		        echo shell_exec("cd {$path} && git pull");
		        exit();
		    /*}else{
	    		echo 'signature error';		    	
		    }*/
		}else{
	    	echo 'no signature';
	    }
		return 'ok';
    }

}
