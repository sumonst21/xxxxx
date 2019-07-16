<?php

// +----------------------------------------------------------------------
// | 公开操作
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\admin\controller\Base;
use think\Session;
class Pub extends Base
{
    public function index(){
         redirect("/");
    }
    // 验证码显示
    public function verify()
    {
        //
        //Image::buildImageVerify(4,1, 'png', 290, 130, 'TestVerify');
        import("ORG.Util.Verify");
        $Verify=new Verify();
        $Verify->entry('Login');
    }
    public function logout(){
		$this->setseoinfo('退出登录','退出登录','退出登录');
        $jumpUrl='/';
        if(isset($_SESSION["uid"])) {
            unset($_SESSION["uid"]);
            unset($_SESSION["nickname"]);
            unset($_SESSION['account']);
            unset($_SESSION);
            session_destroy();
            Cookie('loginfo',null);
            $this->success('登出成功！',$jumpUrl);
        }else {
            $this->success('已经登出！',$jumpUrl);
        }
    }

    // 用户登录页面
    public function login() {
		if(Session::has("uid") && Session("uid")>0) {
                echo 'cansnow1';
			redirect("Index/index");
		}else{
            if(request()->isPost()){
                echo 'cansnow2';
                if(empty($_POST['account'])) {
                    $this->error('帐号错误！');
                }elseif (empty($_POST['password'])){
                    $this->error('密码必须！');
                }
                //生成认证条件
                $map            =   array();
                // 支持使用绑定帐号登录
                $map['account']	= $_POST['account'];
                $map["status"]	=	array('gt',0);
                $User	=	model('User');
                $vo = $User->where($map)->find();
                if(false === $vo) {
                    $this->error('帐号不存在或已禁用！');
                }else {
                    if($vo['password'] != md5($_POST['password'])) {
                        $this->error('密码错误！');
                    }
                    Cookie::set('loginfo',authcode($vo['id'].','.$vo['nickname'].','.$vo['account'],'ENCODE'));
                    Session::set(Config('USER_AUTH_KEY'),$vo['id']);
                    Session::set('nickname',$vo['nickname']);
                    if($vo['id']==261) {
                        Session::set(Config('ADMIN_AUTH_KEY'),true);
                    }
                    //保存登录信息
                    $time	=	time();
                    $data = array();
                    $data['id']	=	$vo['id'];
                    $data['last_login_time']	=	$time;
                    $data['login_count']	=	array('exp','(login_count+1)');
                    $data['last_login_ip']	=	request()->ip();
                    $User->save($data);
                    echo '登录成功！';
                    $this->success('登录成功！','');
                }
            }else{
                $this->setseoinfo('用户登录','用户登录','用户登录');
                return view('Public:login');
            }
		}
    }
    /*Login End*/
    public function reg(){
        //如果通过认证跳转到首页
        $this->setseoinfo('用户注册','用户注册','用户注册');
        return view('Public:reg');
    }
    // 插入数据
    public function doreg() {
        $_POST['account']=$_POST['email'];
        if($_SESSION['registerverify'] != md5($_POST['vcode'])) {
                $this->error('验证码错误！');
        }
        if($_POST['password'] != $_POST['repassword']) {
                $this->error('两次密码输入不一致！');
        }
        $password=$_POST['password'];
        $_POST['password']=md5($_POST['password']);
        //$_POST['create_time']=time();
        $User	 =	 D("User");
        if(!$User->create()) {
            $this->error($User->getError());
        }else{
            // 写入帐号数据
            if($result	 =	 $User->add()) {
                $this->addRole($result);
                //sendmsg(0,$result,'你好,'.$data['nickname'].',欢迎来到'.C("WEB_NAME")."!");
                $this->checkLogin($_POST['account'],$password,U("User/"));
                //$this->success('注册成功！');
            }else{
                $this->assign('jumpUrl','javascript:history.back();');
                $this->success('注册失败！');
            }
        }
    }
    //检查帐号
    public function checkAccount(){
        $account  =  $_REQUEST['account'];
        $User = D("User");
        $result  =  $User->getByAccount($account);
        if($result) {
            die('y');
        }else {
            die('该账号不存在!');
        }
    }
    function spage($id=0){
		return view('Public:spage',[
            'id'    =>  $id
        ]);
    }
}
