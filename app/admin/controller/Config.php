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
        $list=$model->column('name,value');
        Cache('Config',array_change_key_case($list,CASE_UPPER));
        $this->success('配置更新成功！');
        exit;
    }
	// 缓存配置文件
	public function cache() {
		$model=model("Config");
		$list=$model->column('name,value');
        Cache('Config',array_change_key_case($list,CASE_UPPER));
        $this->success('配置缓存成功！');
	}
}
