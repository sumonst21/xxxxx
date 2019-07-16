<?php
namespace app\admin\controller;
use think\Controller;

class Base extends Controller
{
    public function initialize(){
        $this->_initialize();
    }

    public function _initialize(){
        $this->readConfig();

        $key = Config('USER_AUTH_KEY');
        $authPath = strtolower(Request()->module().'/'.Request()->controller().'/'.Request()->action());
        //exit($authPath);
        if($authPath=='admin/member/login'){

        }else{
            if(Session('?'.$key)!=null) {
                $uid = Session($key);
                $this->assign('userinfo',model('User')->getView(['id'=>$uid]));
            }else{
                $this->redirect(__APP__.'/Member/login.html');
            }
        }
    }
    protected function readConfig(){
        $list=Cache('Config');
        if (!$list) {
            $config = model("Config");
            $list = $config->column('name,value');
			$list=array_change_key_case($list,CASE_UPPER);
            // 所有配置参数统一为大写
            Cache('Config',$list);
        }
        Config($list,'app');
    }
    public function index()
    {
        return view('index');
    }
    public function edit()
    {
		if(Request()->isPost()){
			$model = model(request()->controller());
			$pk = $model->getPk();
			$id = input('post.'.$pk);
			$data = input('post.');
			if(method_exists($this, '_filterData')){
				$data =  $this->_filterData($data);
			}
			$result = $this->validate($data,'app\\common\\validate\\'.request()->controller());
			if (true !== $result) {
				$this->error($result);
			}
            $result = $model->allowField(true)->save($data,[$pk=>$id]);		
			if($result){
				$this->success('保存成功！');
			}else{
				$msg = $model->getError();
				if($msg){
					$this->error($msg);            
				}else{
					$this->error('保存失败，请稍后重试！'.$model->getlastsql());
				}
			}
		}else{
			return view('edit');
		}
    }
	public function del(){
        $model = model(request()->controller());
        $ids = input('ids',input('id'));
        if(is_string($ids)){
            $ids=[$ids];
        }
        $model->whereIn('id',$ids)->delete();
        $this->success('操作成功！'.$model->getlastsql());
	}
    public function add()
    {
		if(Request()->isPost()){
			$model = model(request()->controller());
			$pk = $model->getPk();
			$id = input('post.'.$pk);
			$data = input('post.');
			if(method_exists($this, '_filterData')){
				$data =  $this->_filterData($data);
			}
			$result = $this->validate($data,'app\\common\\validate\\'.request()->controller());
			if (true !== $result) {
				$this->error($result);
			}
			$result = $model->allowField(true)->save($data);			
			if($result){
				$this->success('保存成功！');
			}else{
				$msg = $model->getError();
				if($msg){
					$this->error($msg);            
				}else{
					$this->error('保存失败，请稍后重试！'.$model->getlastsql());
				}
			}
		}else{
			return view('edit');
		}
    }
    protected function save($data=[],$model_name='',$action=''){
        $model_name = $model_name ? $model_name : request()->controller();
        $model = model($model_name);
        $pk = $model->getPk();
        $data = $data ? $data : input('post.');
        $id = $data[$pk];
        if(method_exists($this, '_filterData')){
            $data =  $this->_filterData($data);
        }
        $result = $this->validate($data,'app\\common\\validate\\'.$model_name);
        if (true !== $result) {
            $this->error($result);
        }
        if($action=='save'){
            if($id){
                $result = $model->allowField(true)->save($data,[$pk=>$id]);
                if(method_exists($this, '_triggerUpdate')){
                    $this->_triggerUpdate($model);
                }
            }else{
                $this->error('保存失败，请稍后重试！id=null');
            }
        }else{
            $result = $model->allowField(true)->save($data);
            if(method_exists($this, '_triggerInsert')){
                $this->_triggerInsert($model);
            }
        }
        if($result){
            $this->success('保存成功！');
        }else{
            $msg = $model->getError();
            if($msg){
                $this->error($msg);            
            }else{
                $this->error('保存失败，请稍后重试！'.$model->getlastsql());
            }
        }
    }
    public function attr(){
        $field = input('field');
        $id = input('ids');
        $val = input('val');
        $model = model(request()->controller());
        $model->allowField([$field])->save([$field=>$val],['id'=>$id]);
        //exit($model->getlastsql());
        $this->success('操作成功！');
        //P($val);
        //exit;
    }
}
