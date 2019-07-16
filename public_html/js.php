<?php
function P($msg){
	echo '<pre>';
	print_r($msg);
	echo '</pre>';
}
$root = './static/js/';
if($_SERVER['QUERY_STRING']){
	$js=explode(',',explode('&',$_SERVER['QUERY_STRING'])[0]);
	$data =[];
	foreach($js as $k=>$v){
		if(file_exists($root.$v)){
			$v=file_get_contents($root.$v);
			//$v = str_replace("\t", "", $v); //清除空格
			//$v = str_replace("\r\n", "", $v); 
			//$v = str_replace("\n", "", $v); 
			$data[] = $v;
		}
	}
	$str = implode(PHP_EOL,$data);


	// 删除单行注释
	//$str = preg_replace("/\/\/\s*[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/", "", $str); 
	// 删除多行注释
	//$str = preg_replace("/\/\*[^\/]*\*\//s", "", $str);
	header("Content-type:Application/x-javascript; Charset: utf-8");
	echo $str;
}