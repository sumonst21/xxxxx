<?php
namespace app\home\controller;
class Error extends Base
{
    public function view()
    {
        require(EXTEND_PATH.'\ORG\Imap.class.php');
        $Imap = new \ORG\Imap();

        $ret = $Imap->mailConnect("imap.exmail.qq.com",'993/ssl','liqiankun@zphjr.com','Lisiwei321');
        return view('',['list'=>$Imap->getList()]);
    }

	/**
	 * 
	 * 匹配提取信件头部信息
	 * @param String $str
	 */
	function _empty($str){
		$file = APP_PATH .Request()->module().DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'Index'.DIRECTORY_SEPARATOR.Request()->controller().'.html';
		//exit($file);
    	if(file_exists($file)){
    		return view($file);
    	}
	}
}
