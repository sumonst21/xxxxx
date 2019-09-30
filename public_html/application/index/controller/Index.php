<?php
namespace app\index\controller;

use app\common\controller\HomeBase;
use app\common\model\Forumcate as ForumcateModel;
use app\common\model\NavCms as NavModel;
use think\Cache;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Index extends HomeBase
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

    public function index()
    {
        //签到榜 //投稿榜 自由打开？
        //$member = Db::name('user_sign')->alias('a')->join('user u', 'u.id=a.uid')->field('u.*,count(*) as forumnum')->group('a.uid')->order('forumnum desc')->limit(12)->select();
        $member = Db::name('article')->alias('f')->join('user u', 'u.id=f.uid')->field('u.*,count(*) as forumnum')->group('f.uid')->order('forumnum desc')->limit(9)->select();
        $this->assign('member', $member);

        //最近更新$this->site_config['c_home_newlist']
        $article_new = Db::name('article')->alias('a')->join('user u', 'u.id=a.uid')->join('articlecate c', 'c.id=a.tid')->where('a.open', 1)->field('u.userhead,u.username,a.id,a.uid,a.title,a.time,c.template,a.settop')->order('a.settop desc,a.time desc')->limit(104)->select();
        $this->assign('article_new', $article_new);

        //分类展示 文字区
        $artbycatelist = Db::name('articlecate')->where('hometextshow=1')->order('sort asc')->select();
        foreach ($artbycatelist as $k => $v) {
            $artbycatelist[$k]['artlists'] = get_articles_by_cid($v['id'], $this->site_config['c_home_text']);
        }
        $this->assign('artbycatelist', $artbycatelist);
        //分类展示 图片区
        $article_pic = Db::name('articlecate')->where('homepicshow=1')->order('sort asc')->select();
        foreach ($article_pic as $k => $v) {

            $article_pic[$k]['artlists'] = get_articles_by_cid($v['id'], $this->site_config['c_home_pic']);
        }
        $this->assign('article_pic', $article_pic);

        //最近30天排行榜
        $maptop30['open'] = 1;

        $maptop30['time'] = array('egt', strtotime("-1 month"));
        $art_top30        = Db::name('article')->alias('a')->join('articlecate c', 'c.id=a.tid')->where($maptop30)->field('a.id,a.view,a.title,a.time,c.template')->order('view desc')->limit(10)->select();
        $this->assign('art_top30', $art_top30);

        if ($this->site_config['open_taoke'] == 0) {
            //站长推荐榜
            $mapchoice['open']   = 1;
            $mapchoice['choice'] = 1;
            //$mapchoice['a.tid']=1;
            $art_choice = Db::name('article')->alias('a')->join('articlecate c', 'c.id=a.tid')->where($mapchoice)->field('a.id,a.coverpic,a.view,a.title,a.time,c.template')->order('a.choice desc,a.time desc')->limit(6)->select();
            $this->assign('art_choice', $art_choice);
        }

          //今日更新数     
          $time=strtotime(date('Y-m-d'));   
          $today_update=Db::name('article')->where("open=1 and updatetime > {$time}")->count();    
          $this->assign('today_update', $today_update);

          //友情链接
          $superlinks = Db::name('superlinks')->where("status = 1 and onwhere ='cms'")->order('level desc,id asc')->select();
    	  foreach($superlinks as $key=>$val){
    		if($val['type'] == 1){
    			$superlinks[$key]['savepath'] = get_cover($val['cover_id'],'savepath');
    		}
    	  }
          $this->assign('superlinks', $superlinks);
        
        
        //幻灯片
        $carousel = Db::name('article')->where('open', 1)->where('carousel', 1)->order('settop desc,time desc')->limit(6)->select();
        $this->assign('carousel', $carousel);

        return view();
    }

    public function search()
    {
        $ks  = input('ks');
        $kss = urldecode(input('ks'));
        if (empty($ks) || $kss == ' ') {
            return $this->error('亲！你没有输入关键字');
        } else {
            $article      = Db::name('article');
            $open['open'] = 1;

            $map['f.title|f.keywords|f.description|f.content'] = ['like', "%{$kss}%"];

            $tptc = $article->alias('f')->join('articlecate c', 'c.id=f.tid')->join('user m', 'm.id=f.uid')->field('f.*,c.id as cid,m.id as userid,m.userhead,m.username,c.name,c.template')->order('f.id desc')->where($open)->where($map)->paginate(5, false, $config = ['query' => array('ks' => $ks)]);
            $this->assign('tptc', $tptc);
            return view();
        }
    }

    public function errors()
    {
        return view();
    }

    public function soft()
    {
        $id = is_number(input('id')) ? input('id') : '';
        action('html',['id'=>$id]);
        return view();
    }

    public function article()
    {
        $id = is_number(input('id')) ? input('id') : '';
        action('html',['id'=>$id]);
        return view();
    }

    public function html($id='')
    {
        if(!$id){
          $id = is_number(input('id')) ? input('id') : '';
        }
        if (empty($id)) {
            return $this->error('亲！你迷路了', 'index/index/index');
        } else {
            $article = Db::name('article');
            $a       = $article->where('open', 1)->find($id);

            if ($a) {
                if ($a['outlink']) {
                    $this->success('正在跳转到外部页面', $a['outlink'], null, 1);
                }

                //根据模板找到所需要的渲染页面
                $cate       = Db::name('articlecate')->where('id',$a['tid'])->find(); 
                 $template=$cate['template'];
                $article->where("id", $id)->setInc('view', 1);
                $t = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->join('user m', 'm.id=a.uid')->field('a.*,c.id as cid,c.name,c.template,c.alias,m.id as userid,m.grades,m.point,m.userhead,m.username,m.sex,m.status')->where('a.id', $id)->find();
                $t['keyword_arr']=explode(',',$t['keywords']);
                $this->assign('t', $t);
                //阅读排行
                $artphb = $article->where('tid', $t['tid'])->order('view desc')->limit($this->site_config['c_list_phb'])->select();
                $this->assign('artphb', $artphb);
                //文章推荐
                $choice['tid']    = $t['tid'];
                $choice['choice'] = 1;
                $artchoice        = $article->where($choice)->order('id desc')->limit($this->site_config['c_view_main'])->select();
                $this->assign('artchoice', $artchoice);

                //查询当前用户是否收藏该文章
                $iscollect  = 0;
                $commentzan = array();
                $uid        = session('userid');
                if ($uid) {
                    $collect = Db::name('collect')->where(array('uid' => $uid, 'sid' => $id, 'type' => 3))->find();
                    if ($collect) {
                        $iscollect = 1;
                    }
                    //查询用户点赞过的文章评论
                    $commentzan = Db::name('zan')->where(array('uid' => $uid, 'type' => 4))->column('sid');

                }
                //评论
                $tptc = Db::name('artcomment')->alias('c')->join('user m', 'm.id=c.uid')->where("fid = {$id}")->order('c.id asc')->field('c.*,c.status as reply_open,m.id as userid,m.grades,m.attestation,m.point,m.userhead,m.username,m.grades,m.sex,m.status')->paginate(10, false, ['query' => Request::instance()->param()]);
                //查询是否为版主
                $isbanzhu = 0;
                if (!empty($uid)) {
                    $catemodel = new ForumcateModel();
                    $res       = $catemodel->isbanzhu($uid, $t['tid']);
                    if ($res) {
                        $isbanzhu = 1;
                    }
                }
                $this->assign('isbanzhu', $isbanzhu);

                $this->assign('tptc', $tptc);

                $this->assign('iscollect', $iscollect);
                $this->assign('commentzan', $commentzan);
                return view($template);
            } else {
                return $this->error('亲！你迷路了', 'index/index/index');
            }
        }
    }
   
}
