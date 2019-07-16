<?php
namespace app\common\model;
use think\Model;
class Base extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //parent::initialize();
    }
    function getUserId(){
        return session(Config('USER_AUTH_KEY'));
    }
	function arr2where($data){
		$map =[];
		foreach($data as $k=>$v){
			$map[]=[$k,is_array($v) ? $v[0] : '=',is_array($v) ? $v[1] : $v];
		}
		return $map;
	}
    function getList($data=array()){
        $_field = empty($data['_field']) ? '*' : $data['_field'];unset($data['_field']);
        $limit = empty($data['limit']) ? '' : $data['limit'];unset($data['limit']);
        $order = empty($data['order']) ? '' : $data['order'];unset($data['order']);
        $pagesize = empty($data['pagesize']) ? Config('PAGE_SIZE') : $data['pagesize'] ;unset($data['pagesize']);
        $_data = [];
        foreach ($data as $key => $value) {
        	if(!is_null($value) && $value!==""){
        		$_data[$key]= $value;
        	}
        }
        $data = $_data;
        if(method_exists($this, '_filter')){
            $data=$this->_filter($data);
        }
		$map = $this->arr2where($data);
		$pagehtml = '';
        if($limit){
            if($limit=='all'){
                $list=$this->Field($_field)->Where($map)->Order($order)->Select();
            }else{
                $list=$this->Field($_field)->Where($map)->Limit($limit)->Order($order)->Select();
            }
        }else{
            $list = $this->Field($_field)->where($map)->Order($order)->paginate($pagesize,false,[
                'query' =>  Input()
            ]);
            $pagehtml = $list->render();
        }
        //P($this->getlastsql());
        $var=  array(
            'list'      =>  $list,
            'page'      =>  $pagehtml,
            'pages'     =>  array(
                'total'     =>  0,
                'page'      =>  0,
                'pagesize'  =>  $pagesize
            )
        );
        return $var;
    }
    function getView($data=array()){
    	$_data = [];
        foreach ($data as $key => $value) {
        	if($value){
        		$_data[$key]= $value;
        	}
        }
        $data = $_data;
		$map = $this->arr2where($data);
        $var=$this->Where($map)->select();
        if($var){
            return $var[0];
        }else{
            return array();
        }
    }
}
