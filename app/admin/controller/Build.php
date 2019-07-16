<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Build extends Base
{
	function preview(){
		return view('',['vo'=>$_POST]);
	}
	function flow(){
		return view();
	}
}
