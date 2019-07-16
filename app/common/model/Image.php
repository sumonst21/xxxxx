<?php
namespace app\common\model;
use app\common\model\Base;
class Image extends Base
{
    protected $auto = [];
	protected function _filter($data){
		if(isset($data['keywords'])){
			if($data['keywords']){
				$data['title'] = array('like','%'.$data['keywords'].'%');
			}
		}
        unset($data['keywords']);
		return $data;
	}
}
