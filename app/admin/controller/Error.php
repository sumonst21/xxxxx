<?php
namespace app\admin\controller;
class Error extends Base
{
	function _empty($str){
        $RootPath = \Env::get('root_path').'/public_html';
        if(Request()->controller() == 'Uploads'){
            parse_str ($_SERVER['QUERY_STRING'],$s);
            $filepath = iconv('UTF-8','GB2312//IGNORE',$RootPath.$s['s']);
            if(file_exists($filepath)){
                config('app_trace',false);
                $fp=fopen($filepath,"r");
                $filesize=filesize($filepath);
                header("Content-type:application/octet-stream");
                header("Accept-Ranges:bytes");
                header("Accept-Length:".$filesize);
                header("Content-Disposition: attachment; filename=".pathinfo($s['s'])['basename']);
                //echo file_get_contents($filepath);
                $buffer=1024;
                $buffer_count=0;
                while(!feof($fp)&&$filesize-$buffer_count>0){
                    $data=fread($fp,$buffer);
                    $buffer_count+=$buffer;
                    echo $data;
                }
                fclose($fp);
            }else{
                $this->error('此文件已被删除，请联系管理员！');                
            }
        }
	}
}
