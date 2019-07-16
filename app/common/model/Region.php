<?php
namespace app\common\model;
use app\common\model\Base;
class Region extends Base
{
    protected $resultSetType = 'collection';
	protected function _filter($data){
		if(!$data['pid']){
			$data['pid'] = 0;
		}
		if($data['keywords']){
			$data['title'] = array('like','%'.$data['keywords'].'%');
		}
		unset($data['keywords']);
		return $data;
	}
}
