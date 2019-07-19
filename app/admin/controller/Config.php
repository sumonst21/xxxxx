<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Config extends Base
{
    public function index()
    {
        return view('',[
        	'category'	=>	input('category') ? input('category') : 'site'
    	]);
    }
    public function update(){        
        $model = model(request()->controller());
        $data = input('post.');
        foreach ($data as $key => $value) {            
            $model->where('name', $key)->update(['value' => $value]);
        }
        $config = model("Config");
        $groups = $config->distinct(true)->column('category');
        $list = array();
        foreach ($groups as $value){
            $_tmp = $config->where('category',$value)->column('name,value');
            $list[$value] = array_change_key_case($_tmp,CASE_LOWER);
        }
        $list=array_change_key_case($list,CASE_LOWER);
        // 所有配置参数统一为大写
        Cache('Config',$list);
        foreach ($list as $key=>$value){
            Config($value,$key);
        }
        exit;
    }
	// 缓存配置文件
	public function cache() {
        $config = model("Config");
        $groups = $config->distinct(true)->column('category');
        $list = array();
        foreach ($groups as $value){
            $_tmp = $config->where('category',$value)->column('name,value');
            $list[$value] = array_change_key_case($_tmp,CASE_LOWER);
        }
        $list=array_change_key_case($list,CASE_LOWER);
        // 所有配置参数统一为大写
        Cache('Config',$list);
        foreach ($list as $key=>$value){
            Config($value,$key);
        }
        $this->success('配置缓存成功！');
	}
}
