<?php
namespace app\common\model;
use app\common\model\Base;
class Ad extends Base
{
    protected $auto = [];
    protected $insert = ['create_time'];
    protected $update = ['update_time'];

    public function setCreateTimeAttr($value,$data)
    {
        return time();
    }

    public function setUpdateTimeAttr($value,$data)
    {
        return time();
    }
	protected function _filter($data){
		if(isset($data['keywords']) && $data['keywords']){
			$data['title'] = array('like','%'.$data['keywords'].'%');
		}
        unset($data['keywords']);
		return $data;
	}
}
