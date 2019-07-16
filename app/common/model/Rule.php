<?php
namespace app\common\model;
use app\common\model\Base;
class Rule extends Base
{
    protected $insert = ['sort'=>0];
	protected function _filter($data){
		if(array_key_exists('keywords', $data) && $data['keywords']){
			$data['name|title'] = array('like','%'.$data['keywords'].'%');
		}
		unset($data['keywords']);
		if(array_key_exists('pid', $data)){
			if($data['pid']>0){

			}else{
				$data['pid'] = 0;
			}
		}
		return $data;
	}
}
