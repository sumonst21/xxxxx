<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Role extends Base
{
    function _filterData($data){
        if(empty($data['rules'])){
            unset($data['rules']);
        }
        if(is_array($data['rules'])){
            $data['rules'] = implode(',',$data['rules']);
        }
        return $data;
    }
    
    public function add(){
        return view('edit',['RuleList'=>$this->getRole()]);
    }
    public function edit(){
        return view('edit',['RuleList'=>$this->getRole()]);
    }
    protected function getRole(){
        $Rule = model('Rule');
        $list = $Rule->where('status=1')->column('id,title,pid');
        //P($list);
        //exit;
        //$list = $list->toArray();
        //echo $Rule->getlastsql();
        return list_to_tree($list,'id','pid','children');
    }
    function cache(){
        $i = new Index();
        $i->getMenu(true);
        $this->success('缓存成功！');
    }
}
