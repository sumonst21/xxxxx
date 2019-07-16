<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Linkage extends Base
{
    protected $beforeActionList = [
        //'first',
        //'second' =>  ['except'=>'hello'],
        'get_type_list'  =>  ['only'=>'add,edit,index'],
    ];
	protected function get_type_list(){
		$Linkage	=	model("Linkage");
		$list = $Linkage->query('select DISTINCT `type` from z_linkage');
        //P($list);
        //exit;
        $this->assign('TypeList',$list);
	}
    function cache(){
        $Linkage = model('Linkage');
        $types = $Linkage->query('select DISTINCT `type` from z_linkage');
        $typeList=array();
        foreach ($types as $k => $v) {
            $typeList[$v['type']]=$Linkage->where('type="'.$v['type'].'" and status=1')->order('sort asc')->column('name,title');
        }
        cache('LinkageList',$typeList);
        $this->success('缓存成功');
    }
}
