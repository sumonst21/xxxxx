<?php
function P($msg){
	echo '<pre>';
	print_r($msg);
	echo '</pre>';
}
$root = './static/';
if($_SERVER['QUERY_STRING']){
	$js=explode(',',explode('&',$_SERVER['QUERY_STRING'])[0]);
	$data=[];
	foreach($js as $k=>$v){
		if(file_exists($root.$v)){
			$v=file_get_contents($root.$v);
			$data[] = $v;
		}
	}
	$str = implode(PHP_EOL,$data);
	header("Content-type:text/css; Charset: utf-8");
	echo $str;
}