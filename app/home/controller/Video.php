<?php
namespace app\home\controller;
use app\home\controller\Base;
use think\Session;
class Video extends Base
{
	public function index(){
    	return $this->category();
    }
    public function ac(){
        $this->display();
    }
    public function category(){
    	/*$cid=intval($_REQUEST['cid']);
		$pagetitle=GTC($cid,'Cate');
    	$pagetitle=empty($pagetitle) ? '文章列表' : $pagetitle;
        $this->setseoinfo($pagetitle,$pagetitle,$pagetitle);
		*/
        return view('index');
    }
    public function view($id=0){
        return view('view',['id'=>$id]);
        //M('Video')->where('id='.$id)->setInc('hit');
    }
    public function search($qw=''){
        $qw= empty($qw) ? input('qw') : $qw;
        $this->setseoinfo('搜索结果:'.$qw,$qw,$qw);
        return view('index');
    }
    function setbad(){
        M('Video')->setInc('bad');
    }
    function setgood(){
        M('Video')->setInc('good');
		$this->success('OK');
    }
	function dashang(){
		$param['order_no'] = substr(md5(time().print_r($_SERVER,1)), 0, 24); //订单号
		$param['channel'] = 'alipay'; //表单提交的方式支持  alipay  wxpay   chinapay_b2c  chinapay  需要哪个就替换哪个
		$param['amount'] = 0.01; //金额
		$param['subject'] = "打赏作者";//商品名
		$param['metadata'] = "";//备注
		$param['return_url'] = 'http://www.sssui.com/Pay/return.html';
		$param['notify_url'] = 'http://www.sssui.com/Pay/notify.html';//支付成功后天工支付网关通知
		$param['client_ip'] = get_client_ip();//'127.0.0.1';
		$param['client_id'] = $TEE_CLIENT_ID;

	}
    function getcomment(){
        $pid=I('get.pid',0,'intval');
        $p=I('get.p',1,'intval');
        $module=I('get.module','Article','trim');
        $pagesize=I('get.pagesize',20,'intval');

        $Comment=model('Comment');
        $count = $Comment->where('module="Article" and commentid=0 and pid='.$pid)->count('id');
        $ret=array(
            'total'=>$count,
            'page'=>$p,
            'total_page'=>ceil($count/$pagesize),
            'list'=>array()
        );
        $field='id,pid,commentid,url,content,nickname,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as create_time';
        if ($count > 0) {
            $firstRow= $pagesize*($p-1);
            $CommentList = $Comment->Field($field)->Where('module="Article" and commentid=0 and pid='.$pid)->Limit($firstRow.','.$pagesize)->Order('id desc')->Select();
            foreach ($CommentList as $key => $value) {
                $CommentList[$key]['_child']=$Comment->Field($field)->Where('module="Article" and commentid='.$value['id'].' and pid='.$pid)->Order('id desc')->Select();
            }
            $ret['list']=$CommentList;
        }
        echo json_encode($ret);
    }
    function preview(){
        $this->assign('vo',$_POST);
        $this->display('view');
    }
    function gettags(){
        $kw=input('kw');
        $Tag=model('Tag');
        if($kw){
            $list=$Tag->where('module="Video" and name like "%'.$kw.'%"')->limit(10)->order('name asc')->getField('name',true);
        }else{
            $list=$Tag->where('module="Video"')->limit(10)->order('name asc')->getField('name',true);
        }
        echo json_encode($list,JSON_UNESCAPED_UNICODE);
    }
    function getalbum(){
        $Attach = model('Attach');
        $p=input('get.p',1,'intval');
        $pagesize=input('get.pagesize',15,'intval');
        $map=array(
            'extension'=>array('in','jpg,png,gif,bmp')
        );
        $count = $Attach->where($map)->count('id');
        $ret=array(
            'total'=>$count,
            'page'=>$p,
            'total_page'=>ceil($count/$pagesize),
            'list'=>array()
        );
        $field='id,CONCAT(savepath,savename) as src';
        if ($count > 0) {
            $firstRow= $pagesize*($p-1);
            $ret['list'] = $Attach->Field($field)->Where($map)->Limit($firstRow.','.$pagesize)->Order('id desc')->Select();
        }
        echo json_encode($ret);
    }
    function catchToQiniu(){
        $ret= catchToQiniu($_POST['srcs'],$_POST['path']);
        echo json_encode($ret,true);
    }
	function autosave(){
		$_POST['id']=-1;
        $ret=parent::update();
		$this->success('ok');
	}
	/*按标签显示文章*/
    function tag(){
        return view('index',['tag'=>input('tag')]);
    }
    function m3u8(){
        Config('app_trace',0);
        $url = Input('url');
        if(strpos($url,'.m3u8')!==0){
            $urlinfo = explode('/',$url);
            $urlPrefix = 'http://'.$urlinfo['2'];
            define('urlPrefix',$urlPrefix);
            $body =   get($url);
            $arr = explode("\n",$body);
            foreach($arr as $key => $val){
                if($val[0]!='#' && !empty(trim($val))){
                    if(substr($val,0,4)=='http'){
                        $arr[$key] = '/Video/m3u8.html?url='.$val;
                    }else{
                        if($val[0]=='/'){
                            $arr[$key] = '/Video/m3u8.html?url='.urlPrefix.'/'.$val;
                        }else{
                            $arr[$key] = '/Video/m3u8.html?url='.pathinfo($url,PATHINFO_DIRNAME).'/'.$val;//http://www.bbs.com/Video/m3u8.html?url=http://eet.com-baidu-com.com/2017/88601/hls/index.m3u8
                        }
                    }
                }
            }
            //array_pop($arr);
            return implode("\n",$arr);
        }else{
            return readfile($url);
            /*$stream = php_stream_open_wrapper($url, "rb", REPORT_ERRORS, NULL);
            if ($stream) {
                while(!php_stream_eof($stream)) {
                    //char buf[1024];
                    streamWrapper::stream_set_option(STREAM_OPTION_WRITE_BUFFER );
                    if (php_stream_gets($stream, buf, sizeof(buf))) {
                        printf(buf);
                    } else {
                        break;
                    }
                }
                php_stream_close($stream);
            }*/
        }

        /*P($arr);
        exit;
        $body = preg_replace_callback('/(https|http|ftp|rtsp|mms)?:\/\/)[^\s]+/mi',function($matches){
            return '/Video/m3u8.html?url='.urlPrefix.'/'.$matches[0];
        },$body);
        $body = preg_replace_callback('/([\/A-Za-z0-9]+\.m3u8)/mi',function($matches){
            return '/Video/m3u8.html?url='.urlPrefix.'/'.$matches[0];
        },$body);
        $body = preg_replace_callback('/([:\/A-Za-z0-9]+\.ts)/mi',function($matches){
            return '/Video/m3u8.html?url='.urlPrefix.$matches[0];
        },$body);
        $body = preg_replace_callback('/([\/A-Za-z0-9]+\.ts)/mi',function($matches){
            return '/Video/m3u8.html?url='.urlPrefix.'/'.$matches[0];
        },$body);
        //$body = preg_replace(/([\/A-Za-z0-9]+)\.m3u8/ig,'/Video/m3u8.html?url=$1.m3u8',$body);
        return $body;
        //return view('Video:index',['tag'=>input('tag')]);*/
    }
}
