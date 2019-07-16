<?php
namespace app\common\model;
use app\common\model\Base;
class Build extends Base
{
    protected $auto = [];
    protected $insert = ['create_time','status'=>1];
    protected $update = ['update_time'];

    public function setCreateTimeAttr($value,$data)
    {
        return time();
    }

    public function setUpdateTimeAttr($value,$data)
    {
        return time();
    }
	protected function getFieldsAttr($v)
    {
        return json_decode($v,true);
    }
	protected function getFlowsAttr($v)
    {
        return json_decode($v,true);
    }
	protected function setFieldsAttr($v)
    {
        return json_encode($v);
    }
	protected function setFlowsAttr($v)
    {
		if(empty($v)){
			$v = [
				"class"=> "go.GraphLinksModel",
				"nodes"=> [ 
					["key"=>"1", "id"=>"1", "text"=>"开始", "category"=>"start", "loc"=>"-4 -105"],
					["key"=>"4", "id"=>"2", "text"=>"结束", "category"=>"end", "loc"=>"-4 170"]
				],
				"links"=> [
					["from"=>"1","to"=>"2","points"=>[-4,-85,-4,-75,-4,33,-4,33,-4,141,-4,151]]
				]
			];
		}
        return json_encode($v);
    }
	protected function _filter($data){
		if(isset($data['keywords']) && $data['keywords']){
			$data['title'] = array('like','%'.$data['keywords'].'%');
		}
        unset($data['keywords']);
		return $data;
	}
}
