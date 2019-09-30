<?php
namespace app\index\controller;

use org\Http;
use think\Cache;
use think\Controller;
use think\Db;
use think\Session;
use app\common\model\Sms as SmsModel;

class Douyin extends Controller
{
    protected $site_config;
    public function _initialize()
    {
        parent::_initialize();
        $this->site_config = Cache::get('site_config');
        $this->auth();
    }
    function getVideoList(){
        $forum_fields = '`f`.id,`f`.tid,`f`.uid,`f`.title,`f`.choice,`f`.carousel,`f`.zan, `f`.view, `f`.time, `f`.add_time, `f`.reply, `f`.keywords,/* `f`.description, */`f`.remarks, `f`.jiacu, `f`.yanse, `f`.memo, `f`.attach, `f`.coverpic, `f`.collect, `f`.attach/*, `f`.content*/';
        $forum        = Db::name('forum');
        $user        = Db::name('user');
        $forumcate        = Db::name('forumcate');
        $tptcs = $forum->alias('f')
                    ->field($forum_fields.',false as flag')
                    ->where('f.open',1)
                    ->where('f.tid',3)
                    //->where('f.id',12242)
                    ->order('f.settop desc,f.time desc')
                    ->paginate($this->site_config['b_home_main']);
        $ret = [];
        foreach($tptcs as $k=>$v){
            $ret[$k] =$v;
            $ret[$k]['forum'] = $forumcate->where('id',$v['tid'])->column('name')[0];
            $ret[$k]['content'] = htmlspecialchars_decode( $forum->where('id',$v['id'])->column('content')[0]);
            $ret[$k]['user'] = $user->where('id',$v['uid'])->column('id,userhead,attestation,username')[strval($v['uid'])];
        }
        $this->response('success',$ret,1);
    }

    //我发布的文章 ?item=forum
    public function getMyItem()
    {
        $uid = session('userid');

        $tableArr = ['article', 'forum'];
        $table    = in_array(input('item'), $tableArr) ? input('item') : '';
        if (!$uid || !$table) {
            return json(array('code' => 0, 'msg' => '请求非法'));
        }

        $limit = is_number(input('limit')) ? input('limit') : 1;
        $page  = is_number(input('page')) ? input('page') : 10;
        $pre   = ($page - 1) * $limit;

        $model = Db::name($table);
        $count = $model->where("uid = {$uid}")->count();

        $field = 'm.*,c.template,c.name as catename';
        $order = 'updatetime DESC';
        if ($table == 'forum') {
            $field = 'm.*,c.name as catename';
            $order = 'time DESC';
        }
        $tptc = $model->alias('m')->join($table . 'cate c', 'c.id=m.tid', 'LEFT')->where("uid = {$uid}")->field($field)->order($order)->limit($pre, $limit)->select();
        foreach ($tptc as $k => $v) {
            $tptc[$k]['title'] = strip_tags($v['title']);
        }
        return json(array('code' => 0, 'msg' => '', 'count' => $count, 'data' => $tptc));
    }
 
    /**
     * 我的收藏
     * 如getMyCollect?item=forum&ctype=1
     * @ctype 0:关注的人 1:forum 2:comment 3:article 4:artcomment
     * */
    public function getMyCollect()
    {
        $uid      = session('userid');
        $type     = is_number(input('ctype')) ? input('ctype') : '';
        $tableArr = ['article', 'forum'];
        $table    = in_array(input('item'), $tableArr) ? input('item') : '';
        if ($type==="" || !$uid || !$table) {
            return json(array('code' => 0, 'msg' => '请求非法'));
        }

        $limit = is_number(input('limit')) ? input('limit') : 10;
        $page  = is_number(input('page')) ? input('page') : 1;
        $pre   = ($page - 1) * $limit;
        $model = Db::name('collect');
        $count = $model->where("uid = {$uid}")->count();
        $field = 'c.*,t.uid as zuid,u.username,t.id as fid,t.title,a.template';
        if ($table == 'forum') {
            $field = 'c.*,t.uid as zuid,u.username,t.id as fid,t.title';
        }

        $tptc = $model->alias('c')->join($table . ' t', 'c.sid=t.id', 'LEFT')->join($table . 'cate a', 'a.id=t.tid')->join('user u', 'u.id=t.uid')->field($field)->where("c.uid = {$uid} and c.type = {$type}")->order('id DESC')->limit($pre, $limit)->select();

        foreach ($tptc as $k => $v) {
            $tptc[$k]['title'] = strip_tags($v['title']);
        }
        return json(array('code' => 0, 'msg' => '', 'count' => $count, 'data' => $tptc));
    }
    /**
     * 点赞或关注
     * 例如 zan_collect?id=28082&zan_collect=collect&type=1
     * @type 1:forum 2:comment 3:article 4:artcomment
     * @zan_collect zan:赞  collect:收藏
     *  */
    public function zan_collect()
    {
        $data = $this->request->param();
        $id   = $data['id'];
        $uid  = session('userid');
        if (!session('userid') || !session('username')) {

            return json(array('code' => 0, 'msg' => '登录后才能操作'));
        } else {

            //状态:
            // 0 用户 1 帖子 2 评论
            $zan_collect = $data['zan_collect'];

            $msgsubject                         = '';
            $zan_collect == 'zan' ? $msgsubject = '点赞' : $msgsubject = '收藏';
            $tablename                          = '';
            $type                               = $data['type'];
            switch ($type) {
                case 1:
                    $tablename = 'forum';
                    break;
                case 2:
                    $tablename = 'comment';
                    break;
                case 3:
                    $tablename = 'article';
                    break;
                    case 4:
                    $tablename = 'artcomment';
                    break;
                default:
                    $msgsubject = '关注';
                    $tablename  = 'user';
                    break;
            }
            $zuid = $id;
            if ($type != '0') {
                $zuid = Db::name($tablename)->where('id', $id)->value('uid');
            }
            if ($zuid == $uid) {
                return json(array('code' => 0, 'res' => '减', 'msg' => '不可以孤芳自赏哦'));
            }

            $insertdata['type'] = $type;
            $insertdata['uid']  = $uid;
            $insertdata['sid']  = $id;

            $n = Db::name($zan_collect)->where($insertdata)->find();
            if (empty($n)) {
                $insertdata['time'] = time();
                if (Db::name($zan_collect)->insert($insertdata)) {

                    Db::name($tablename)->where('id', $id)->setInc($zan_collect);

                    return json(array('code' => 200, 'res' => '加', 'msg' => $msgsubject . '成功'));

                } else {
                    return json(array('code' => 0, 'res' => '加', 'msg' => $msgsubject . '失败'));

                }
            } else {
                if (Db::name($zan_collect)->where('id', $n['id'])->delete()) {
                    Db::name($tablename)->where('id', $id)->setDec($zan_collect);
                    return json(array('code' => 200, 'res' => '减', 'msg' => $msgsubject . '成功'));

                } else {
                    return json(array('code' => 0, 'res' => '减', 'msg' => $msgsubject . '失败'));
                }
            }

        }
    }
    
    protected function allowCross(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Request-Method:POST,GET,OPTIONS');
        header('Access-Control-Allow-Headers:content-type,ly');
    }
    function auth(){
        $this->allowCross();
        if(request()->isOptions()){
            exit('pass');
        }else{
            $signArr = Input('param.');
            //P($signArr);
            ksort($signArr);
            $str="";
            foreach ($signArr as $k => $value) {
                if($k!='_ajax' && $k!='sign'){
                    $str.=$k.$value;
                }
            }
            $Encrypt = new \addons\encrypt\encrypt();
            $sign = $Encrypt->sha($str);
            if(array_key_exists('sign',$signArr) && $sign == $signArr['sign']){
                $uid= Input('access_token');
                $this->uid = $Encrypt->UserId($uid,'decode');
            }else{
                //P($sign);
                //P($signArr['sign']);
                $this->response('Sign not Valid',array('signStr'=>$str),1);
                //exit;
            }
        }
    }
	function response($msg='', $data = array(), $code=0){
		$_callback = Input('callback','','trim');
		if($_callback){
			echo $_callback.'(';
		}else{
			header("content-type:application/json;chartset=uft-8");
		}
		$debug = Input('debug','','trim');
		$data = array(
			'code' => $code,
			'msg' => $msg,
			'data' => $data,
		);
        if ($debug !== 'debug') {
            echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
		if($_callback){
			echo ');';
		}
        exit;
	}

}
