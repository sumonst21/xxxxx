<?php
namespace app\home\controller;

class Index
{
   public function index(){
        //$auth = new Auth('FwbFcOniG6Af7osdFaoXepZWY25SxH6fmM1dwXre', 'Ym-jNPMMYLLCf13IxuuCC_LkeL9gTDC0pyqbVGyf');
        //$bucketMgr = New BucketManager($auth);
        
        import("ORG.Util.Feedcreator");
        //define channel
        $feed = new UniversalFeedCreator();
        $feed->useCached();
        $feed->title=C('WEB_NAME');
        $feed->description=C('DESCRIPTION');
        $feed->link=C('WEB_URL');
        $feed->syndicationURL="http://www.sssui.com/rss.html";
        $cid=$this->_get('cid');
        $cid= empty($cid) ? '0' : $cid;
        $cate=($cid==0) ? C('WEB_NAME') : GTC($cid,'Cate');
        $feed->category= $cate;
        $feed->copyright = C('WEB_NAME')." (c) ".date('Y');

        //Valid parameters are RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated),
        // MBOX, OPML, ATOM, ATOM1.0, ATOM0.3, HTML, JS
        $feed=$this->addItem($feed,$cid);
        //$feed=$this->addItem($feed,$cid,$bucketMgr);
        //P($feed);
        if ($_GET['type'] == 'atom') {
            $feed->outputFeed("ATOM1.0"); 
            //$feed->saveFeed("ATOM1.0", "news/feed.xml"); 
        } else if ($_GET['type'] == 'atom0'){
            $feed->outputFeed("ATOM0.3"); 
        } else if ($_GET['type'] == 'rss'){
            $feed->outputFeed("RSS"); 
        }  else  {
            $feed->outputFeed("RSS2.0"); 
        }
    }
    protected function addItem($feed,$cid){
        $Article=M('Article');
        $where='status=1 and id >0';
        $where= $cid ? $where.' and cid='.$cid : $where;
        $list=$Article->where($where)->limit(0,10)->order('id asc')->select();
        //添加每条文章
        foreach ($list as $key => $v) {
            $item = new FeedItem();
            $item->title        = $v['title'];
            $item->link         = "http://sssui.com/".$v['id'].".html";
            $item->guid         = "urn:feeds-archive-org:validator:".$v['id'];
            $item->description  = getContent($v['content']);
            $item->source       = "http://sssui.com/";
            //$item->image        = new FeedImage();
            //$item->image->url   = $v['img'];
            //$item->image->title = $v['title'];
            //$item->image        = $v['img'];
            $item->author       = C('WEB_NAME');
            $item->authorEmail  = C('MAIL_EMAIL');
            $item->authorURL    = "http://sssui.com/Spage/1.html";
            $item->descriptionHtmlSyndicated = true;
            $item->category     = GTC($v['cid'],'Cate');
            //可选附件支持
            $item->enclosure = new EnclosureItem();
            if($v['img']=='/Static/cover.jpg'){
                $item->enclosure->length="6951";
                $item->enclosure->type='image/jpeg';
                $item->enclosure->url='/Static/cover.jpg';
            }else{
                $item->enclosure->url=$v['img'];
                //$key = str_replace('http://static.sssui.com/','',$v['img']);
                //$ret = $bucketMgr->stat('sssui',$key);
                //$item->enclosure->length=$ret['fsize'];
                //$item->enclosure->type=$ret['mimeType'];
                //P($item);
                //exit;
            }
            $feed->addItem($item);
        }
        return $feed;
    }
}
