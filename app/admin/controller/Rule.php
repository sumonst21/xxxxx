<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Rule extends Base
{
    function cache(){
    	$i = new Index();
    	$i->getMenu(true);
    	$this->success('缓存成功！');
    }
    
    public function add(){
        return view('edit',[
            'vo'=>[
                'pid' => Input('get.pid'),
            ]
        ]);
    }
    function index (){
        /*$path= APP_PATH.'admin/controller/';
        if($handle = opendir($path)){
            while (false !== ($file = readdir($handle))){
                if($file !='.' && $file!='..'){
                    $ClassName = 'app\\admin\\controller\\'.str_replace('.php', '', $file);
                    echo $ClassName.'<br/>';
                    $a = new $ClassName();
                    p(get_class_vars($a));
                }
            }
            closedir($handle);
        }
        exit;*/
    	$rule = model('Rule');
    	$list = $rule->select();
        $list = $list->toArray();
    	return view('',['list'=>list_to_tree($list)]);
    }
    function getlist(){
    	P(list_to_tree($list));
    	P('cansnow');
    }
}
