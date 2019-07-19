<?php
// +----------------------------------------------------------------------
// | 用户
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\home\controller;
use app\home\controller\Base;
class User extends Base
{
    public function index(){
        return view('');
    }
    public function logout(){
		$this->setseoinfo('退出登录','退出登录','退出登录');
        $jumpUrl='/';
        if(Session("?".Config('USER_AUTH_KEY'))) {
            Session(null);
            $this->success('退出成功！',$jumpUrl);
        }else {
            $this->success('退出成功！',$jumpUrl);
        }
    }

    // 用户登录页面
    public function login() {
		if(Session("?".Config('USER_AUTH_KEY')) && Session(Config('USER_AUTH_KEY'))>0) {
			$this->redirect("home/Index/index");
		}else{
            if(request()->isPost()){
                if(empty($_POST['account'])) {
                    $this->error('帐号错误！');
                }elseif (empty($_POST['password'])){
                    $this->error('密码必须！');
                }
                $User	=	model('User');
                $vo = $User->where('account',Input('post.account'))->where('status','>',0)->find();
                if(!$vo) {
                    $this->error('帐号不存在或已禁用！');
                }else {
                    if($vo['password'] != Input('post.password','','trim,md5')) {
                        $this->error('密码错误！');
                    }
                    //Cookie::set('loginfo',authcode($vo['id'].','.$vo['nickname'].','.$vo['account'],'ENCODE'));
                    Session(Config('USER_AUTH_KEY'),$vo['id']);
                    Session('nickname',$vo['nickname']);
                    //保存登录信息
                    $data = array();
                    $data['last_login_time']	=	time();
                    $data['login_count']	=	\Db::raw('login_count+1');
                    $data['last_login_ip']	=	request()->ip();
                    $User->save($data,['id'=>$vo['id']]);
                    $this->success('登录成功！');
                }
            }else{
                $this->setseoinfo('用户登录','用户登录','用户登录');
                return view();
            }
		}
    }
    /*Login End*/
    public function reg(){
        if(request()->isPost()){
            $data=Input('post.');
            if(\ORG\Verify\Code::valid('registerverify',Input('post.vcode'))) {
                $this->error('验证码错误！');
            }
            if($data['password'] != $data['repassword']) {
                $this->error('两次密码输入不一致！');
            }
            $data['nickname']=$data['account'];
            $ret = parent::save('User',$data);
            if($ret !== false){
                //$this->addRole($ret);
                //$this->login();
                $this->success('注册成功！'.$ret,'Login');
            }else{
                $this->error('注册失败！');
            }
        }else{
            //如果通过认证跳转到首页
            $this->setseoinfo('用户注册','用户注册','用户注册');
            return view();
        }
    }
    //检查帐号
    public function checkAccount(){
        $account  =  input('account');
        $User = model("User");
        $result  =  $User->getByAccount($account);
        if($result) {
            die('y');
        }else {
            die('该账号不存在!');
        }
    }
}
