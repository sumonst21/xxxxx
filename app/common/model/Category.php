<?php
// +----------------------------------------------------------------------
// | 全局分类模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;

//use think\Model;
//class Category extends Model {
use app\common\model\Base;

class Category extends Base {
    protected $insert = ['status' => 1,'create_time'];  
    protected $update = ['update_time'];  
	protected function setCreateTimeAttr()
    {
        return time();
    }
	protected function setUpdateTimeAttr()
    {
        return time();
    }
    protected function _filter($data){
        if(array_key_exists('keywords',$data) && $data['keywords']){
            $data['title'] = array('like','%'.$data['keywords'].'%');
        }
        unset($data['keywords']);
        return $data;
    }
	public function getCategory($module=null){
		$module= is_null($module) ? str_replace('Category', '', request()->controller()) : $module;
		$cateList=$this->where('module="'.$module.'" and status=1')->getField('id,title');
		return  $cateList;
	}
	function CountChild($data=[]){
		$where = 'a.status=1';
		if($data['module']){
			$where.=" and a.module = '".$data['module']."'";
		}
        $list=$this->query("SELECT
			a.id,a.title,(SELECT count(b.id) FROM z_article AS b WHERE b.cid = a.id) as count
		FROM z_category AS a WHERE ".$where);
        $var=  [
            'list'      =>  $list,
            'page'      =>  '',
            'pages'     =>  array(
                'total'     =>  0,
                'page'      =>  '',
                'pagesize'  =>  20
            )
        ];
        return $var;
	}
}
?>