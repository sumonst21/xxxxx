<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use QL\QueryList;
class Caiji extends Base
{
	function index(){
		Config('app_trace',false);
		return view();
	}
	function work(){
		Config('app_trace',false);
		return view();
	}
	function test(){
		$vo = model('Caiji')->getView(['id'=>Input('id')]);
		P($vo);
		echo '列表网址：';
		$urls = $this->getListUrl($vo['list']);
		P($urls);
		$ql = QueryList::get($urls[0]);
		$html = $ql->find($vo['area'])->html();
		$rules = [
			'title' => ['article .entry-title a','text'],
			'href' => ['article .entry-title a','href']
		];
		$rt = $ql->html($html)->rules($rules)->query()->getData();
		
		P($rt->all());
		
	}
	function getListItem($id=0,$url=""){
		$id = Input('id');
		$url = Input('url');
		$vo = model('Caiji')->getView(['id'=>Input('id')]);
		$ql = QueryList::get($url);
		$html = $ql->find($vo['area'])->html();
		$rules = [
			'title' => ['article .entry-title a','text'],
			'href' => ['article .entry-title a','href']
		];
		$rt = $ql->html($html)->rules($rules)->query()->getData();
		return $rt->all();
	}
	function getListUrl($str=''){
		if(!$str){
			$vo = model('Caiji')->getView(['id'=>Input('id')]);
			$str = $vo['list'];
		}
		$strs = explode("\r\n", trim($str));
		$urls = [];
		foreach ($strs as $skey => $svalue) {
			$strPattern = "/(?<=\{)[^\}]+/";
			$arrMatches = [];
			preg_match_all($strPattern, $svalue, $arrMatches);
			if($arrMatches){
				$loop = [];
				$loopStr = '';
				$noloop = [];
				$noloopStr = '';
				foreach($arrMatches[0] as $key => $value){
					$_tmp = explode(',',$value);
					if($_tmp[0]=='loop'){
						$loop = $_tmp;
						$loopStr = '{'.$value.'}';
					}elseif($_tmp[0]=='noloop'){
						$noloop = $_tmp;
						$noloopStr = '{'.$value.'}';
					}
				}
				if($loop){
					for($i=max($loop[1],$loop[4]);$i<=$loop[2];$i+=$loop[3]){
						$_tmp1 = str_replace($loopStr,$i,$svalue);
						if($noloop){
							/*for($i=max($noloop[1],$noloop[4]);$i<=$noloop[2];$i+=$noloop[3]){
								$urls[] = str_replace($noloopStr,$i,$_tmp1);;
							}*/
							$urls[] = $_tmp1;
						}else{
							$urls[] = $_tmp1;
						}
					}
				}
			}else{
				$urls[] = $svalue;
			}
		}
		return $urls;
	}
}
