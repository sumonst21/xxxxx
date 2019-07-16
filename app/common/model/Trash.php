<?php
namespace app\common\model;
use app\common\model\Base;
class Trash extends Base
{
    protected $insert = ['create_time'];
    public function setCreateTimeAttr($value,$data)
    {
        return time();
    }

	protected function _filter($data){
		if($data['keywords']){
			$data['data'] = array('like','%'.$data['keywords'].'%');
		}
		unset($data['keywords']);
		return $data;
	}
}
