<?php
namespace app\home\controller;

class Xs
{
	function add(){
		Config('app_trace',false);
		$model  =   model('Article');	
		$data = Input('post.');	
		$count = $model->where('title',$data['title'])->count('id');
		if($count >0) {
			return '[err]标题已经存在[/err]';
		}else {
			$data['cid'] = str_replace(array('凌辱强暴','同志小说','校园师生','群P交换','职业制服','近亲乱伦','都市激情'),array('1','3','159','160','163','164','167'),$data['cid']);
			$validate = new \app\common\validate\Article;
			if (!$validate->check($data)) {
				return $validate->getError();
			}
			$result = $model->save($data);
			if($result) {
				return '[ok]';
			}else {
				return $model->getError();
			}
		}
	}
   function getSiteList(){
        $list =[];
        foreach(glob(CONF_PATH.'/rules/*.php') as $afile){ 
            if(is_dir($afile)){
            } else {
                $site = include($afile);
                $list[basename($afile,'.php')]=$site['cate'];
            } 
        } 
        echo json_encode(['code'=>0,'data'=>$list,'msg'=>'']);
        exit;
    }
    function getArtList (){
    	$site = trim($_GET['site']);
    	$cid = trim($_GET['cid']);
    	$page = $_GET['page'] ? $_GET['page'] : '1';
    	if(file_exists(CONF_PATH.'/rules/'.$site.'.php')){
    		$site = include(CONF_PATH.'/rules/'.$site.'.php');
    		$url = $site['host'].$site['list']['url']($site['cate'][$cid],$page);
            if($site['charset']!='UTF-8'){
                $ql = \QL\QueryList::get($url)->removeHead();
                $html = $ql::getHtml();
                $html = iconv('GBK', 'UTF-8', $html); 
                $data = $ql::html($html)->rules($site['list']['regex'])->query()->getData();
            }else{
                $ql = \QL\QueryList::get($url);
                $html = $ql::getHtml();
                $data = $ql::html($html)->rules($site['list']['regex'])->query()->getData();
            }
            echo json_encode(['code'=>1,'data'=>$data->all(),'msg'=>'']);
            exit;
    	}else{
    		echo json_encode(['code'=>0,'data'=>[],'msg'=>'没找到站点']);
    	}
    }
    function getArt(){
        $site = trim($_GET['site']);
        $url = trim($_GET['url']);
        if(file_exists(CONF_PATH.'/rules/'.$site.'.php')){
            $site = include(CONF_PATH.'/rules/'.$site.'.php');
            $url = $site['host'].$site['view']['url']($url);
            if($site['charset']!='UTF-8'){
                $ql = \QL\QueryList::get($url)->removeHead();
                $html = $ql::getHtml();
                $html = iconv('GBK', 'UTF-8', $html); 
                $data = $ql::html($html)->rules($site['view']['regex'])->range($site['view']['range'])->query()->getData();
            }else{
                $data = \QL\QueryList::get($url)->rules($site['view']['regex'])->range($site['view']['range'])->query()->getData();
            }
            echo json_encode(['code'=>1,'data'=>$data->all()[0],'msg'=>'']);
            exit;
        }else{
            echo json_encode(['code'=>0,'data'=>[],'msg'=>'没找到站点']);
        }
    }
}
