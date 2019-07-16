<?php
namespace app\common\model;
use app\common\model\Base;
class Linkage extends Base
{
    protected $insert = ['sort'=>0];
	protected function _filter($data){
		if($data['keywords']){
			$data['name|title'] = array('like','%'.$data['keywords'].'%');
		}
        unset($data['keywords']);
		return $data;
	}
}
