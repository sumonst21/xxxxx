<?php
namespace app\index\controller;
use think\Session;
use app\common\controller\HomeBase;
use app\common\model\User as UserModel;
use app\index\model\Article as ArticleModel;
use app\common\model\Articlecate as ArticlecateModel;
use think\Cache;
use think\Db;


class Articles extends HomeBase
{
    protected $site_config;
    public function _initialize()
    {
        parent::_initialize();
        if (CBOPEN == 2) {
            $this->redirect(url('bbs/index/index'));
        }

        $this->site_config = Cache::get('site_config');
    }

    public function lists()
    {
        $a=input('cate_alias');
        $alias                = is_valid_character(input('cate_alias')) ? input('cate_alias') : '';
        $cateinfo['showall']  = 0;
        $cateinfo['catename'] = '全部';
        $cateinfo['list_template']='articlelist';
        $article              = Db::name('article');
        $cateid=0;
        if ($alias) {
           $cateid                 = Db::name('articlecate')->where('alias', $alias)->value('id');
          
           $childIds= $cateid . ls_child_id('articlecate', $cateid);
        }
       //查询下一级目录
       $next_cate=Db::name('articlecate')->where('tid',$cateid)->select();
       $next=1;
       if(!$next_cate){
       //查询兄弟目录
        $parentid=Db::name('articlecate')->where('id',$cateid)->value('tid');
         $next_cate=Db::name('articlecate')->where('tid',$parentid)->select();
         $next=0;
       }
        if ($alias) {
            $cateinfo['showall'] = 1;

            $info                 = Db::name('articlecate')->where('alias', $alias)->find();
            $catealias['tid']     = ['in',$childIds];
            $cateinfo['catename'] = $info['name'];
            $cateinfo['template'] = $info['template'];
            $cateinfo['description'] = @$info['description'];
            $cateinfo['keywords'] = @$info['keywords'];
            $cateinfo['list_template']=$info['list_template'];
            $article_list         = Db::name('article')->alias('a')->join('user u', 'u.id=a.uid')->where('a.open', 1)->where($catealias)->field('a.*,u.username')->order('a.id desc')->paginate($this->site_config['c_list_main']);
           
        } else {
            $article_list = Db::name('article')->alias('a')->join('articlecate c', 'c.id=a.tid')->join('user u', 'u.id=a.uid')->where('a.open', 1)->field('a.*,c.name,c.template,u.username')->order('a.id desc')->paginate($this->site_config['c_list_main']);
        }

        //阅读排行
        $artphb = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->field('a.*,c.name,c.template')->order('view desc')->limit($this->site_config['c_list_phb'])->select();
        $this->assign('artphb', $artphb);
        //文章推荐
        $artchoice = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->where("choice=1")->field('a.*,c.name,c.template')->order('id desc')->limit($this->site_config['c_list_choice'])->select();
        $this->assign('artchoice', $artchoice);

        //子栏目
        $this->assign('next', $next);
        $this->assign('nextcate', $next_cate);
        $this->assign('cateid', $cateid); 
        $this->assign('cateinfo', $cateinfo);
        $this->assign('article_list', $article_list);
        return view($cateinfo['list_template']);
    }
    public function choice()
    {   
        $article              = Db::name('article');
        $num=$this->site_config['c_list_phb'];
        $choice = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->join('user u', 'u.id=a.uid')->where("a.choice=1")->field('a.*,c.name,c.template,u.username')->order('id desc')->paginate($this->site_config['c_list_choice']);
        $this->assign('artchoice', $choice);

        //阅读排行
        $artphb = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->field('a.*,c.name,c.template')->order('view desc')->limit($this->site_config['c_list_phb'])->select();
        $this->assign('artphb', $artphb);
        //随机推荐
        $max=Db::name('article')->max('id');
        $random=unique_rand(1,$max,$max>$num?$num:$max);
        $randomArt = $article->alias('a')->where('id','in',$random)->order('view desc')->limit($num)->select();
        $this->assign('randomArt', $randomArt);
        return view();
    }
    public function add()
    {
        $site_config = Cache::get('site_config');

        if (!session('userid') || !session('username')) {
            $this->error('亲！请登录', url('user/login/index'));
        } else {

            //检查权限
            $user = new UserModel();
            if (!$user->checkauths(session('userid'))) {
                $this->error('您的账户被限制使用', url('index/index/index'));
            }
            $article = new ArticleModel();
            if (request()->isPost()) {

                $input = input('post.');

                if ($input['tid'] == 0) {
                    return json(array('code' => 0, 'msg' => '版块为空'));
                }

                $data['tid']=$input['tid'];
                // if ($input['content'] == '') {
                //     return json(array('code' => 0, 'msg' => '内容为空'));
                // }
                $data['time'] = $data['updatetime'] = time();

                $data['uid'] = session('userid');

                if ($site_config['article_sh'] == 0) {
                    $isdeveloper = Db::name('user')->where('id', $data['uid'])->value('developer');

                    $data['open'] = $isdeveloper > 0 ? 1 : 0;
                } else {
                    $data['open'] = 1;
                }

                $data['view'] = 1;

                $data['description'] = mb_substr(remove_xss($input['content']), 0, 200, 'utf-8');

                $data['coverpic']   = strip_tags($input['coverpic']);
                $data['title']   = strip_tags($input['title']);
                $data['keywords'] = remove_xss($input['keywords']);

                $data['content'] = remove_xss($input['content']);

                if($data['coverpic']==''){
                    //获取第一张图为封面图
                     $data['coverpic']=get_coverpic($data['content']);
                }
                if ($article->add($data)) {
                    $fid = $article->getLastInsID();
                    point_note($site_config['jifen_add'], session('userid'), 'articleadd', $fid);

                    //附件链接信息
                    if (!empty($input['linkinfo'])) {
                        $input['linkinfo'] = remove_xss($input['linkinfo']);
                        if (!empty($input['score'])) {
                            $input['score']     = 0;
                            $input['otherinfo'] = '';
                        }
                        $res = hook('attachlinksave', array('score' => remove_xss($input['attachscore']), 'linkinfo' => remove_xss($input['linkinfo']), 'otherinfo' => remove_xss($input['otherinfo']), 'id' => $fid, 'edit' => 0, 'type' => 1));
                    }

                     //视频信息
                     if (!empty($input['videolink'])) {
                        $input['linkinfo'] = remove_xss($input['videolink']);
                        $res = hook('saveVideo', array('linkinfo' => remove_xss($input['videolink']), 'id' => $fid, 'edit' => 0, 'type' => 1));
                    }

                    return json(array('code' => 200, 'msg' => '添加成功'));
                } else {
                    return json(array('code' => 0, 'msg' => '添加失败'));
                }
            }

            $category = Db::name('articlecate');
        
            $category_level_list  = (new ArticlecateModel())->catetree();
          
            $this->assign('tptc', $category_level_list);
           

            $this->assign('title', '发布帖子');

            return view();
        }
    }
    public function edit()
    {
        $site_config = Cache::get('site_config');

        if (!session('userid') || !session('username')) {
            $this->error('亲！请登录', url('user/login/index'));
        } else {
            //检查权限
            $user = new UserModel();
            if (!$user->checkauths(session('userid'))) {
                $this->error('您的账户被限制使用', url('index/index/index'));
            }
            $id = is_number(input('id'))?input('id'):'';
            if (!$id) {
                $this->error('非法请求');
            }
            session('editartid', $id);

            $uid     = session('userid');
            $article = new ArticleModel();
            $a       = $article->find($id);
            if (empty($id) || $a == null || $a['uid'] != $uid) {
                $this->error('亲！您迷路了');
            } else {
                if (request()->isPost()) {

                    $input       = input('post.');
                    $data['id'] = session('editartid');
                    $content=remove_xss($input['content']);
                    if (isset($data['downlinks'])) {
                        $data['outlink']   = remove_xss($input['outlink']);
                        $data['downlinks'] = remove_xss($input['downlinks']);
                        if ($data['outlink'] && $content == "") {
                            $data['content'] = '外部链接';
                        }

                    }
                    session('editartid', null);
                    // if ($content == '') {
                    //     return json(array('code' => 0, 'msg' => '内容为空'));
                    // }
                    $data['description'] = mb_substr($content, 0, 200, 'utf-8');
                    $data['title']       = strip_tags($input['title']);
                 
                    $data['updatetime'] = time();
                    $data['coverpic'] = strip_tags($input['coverpic']);
                    $data['content'] = $content;
                    if($data['coverpic']==''){
                        //获取第一张图为封面图
                         $data['coverpic']=get_coverpic($data['content']);
                    }
                    if ($article->edit($data)) {
                        //附件链接信息
                        if (isset($input['oldlinkinfo'])) {
                            $input['linkinfo'] = remove_xss($input['linkinfo']);
                            if (!empty($input['score'])) {
                                $input['score']     = 0;
                                $input['otherinfo'] = '';
                            }
                            $res = hook('attachlinksave', array('score' => $input['attachscore'], 'linkinfo' => $input['linkinfo'], 'otherinfo' => $input['otherinfo'], 'id' => $input['id'], 'edit' => 1, 'type' => 1));
                        }
                        //视频信息
                        if (isset($input['oldvideolink'])) {
                            $linkinfo = remove_xss($input['videolink']);
                            $res = hook('saveVideo', array('linkinfo' => $linkinfo, 'id' => $input['id'], 'edit' => 1, 'type' => 1));
                        }
                        return json(array('code' => 200, 'msg' => '修改成功'));
                    } else {
                        return json(array('code' => 0, 'msg' => '修改失败'));
                    }
                }

                $category      = Db::name('articlecate');
                $tptc          = $article->find($id);
                $tptc['title'] = strip_tags($tptc['title']);
                $tptcs         = $category->select();
                $this->assign(array('tptcs' => $tptcs, 'tptc' => $tptc));
                $this->assign('title', '编辑帖子');
                return view();
            }
        }
    }
    public function delcomment()
    {

        $uid = session('userid');
        $id  = is_number(input('id')) ? input('id') : 0;
        $cid = is_number(input('cid')) ? input('cid') : 0;

        if ($uid > 0 && $id) {

            if ($uid==1) {

                if (db('artcomment')->delete($id)) {
                    return json(array('code' => 200, 'msg' => '删除成功'));
                } else {
                    return json(array('code' => 0, 'msg' => '删除失败'));
                }

            } else {
                return json(array('code' => 0, 'msg' => '没有权限'));
            }

        } else {
            return json(array('code' => 0, 'msg' => '你迷路了'));
        }
    }
}
