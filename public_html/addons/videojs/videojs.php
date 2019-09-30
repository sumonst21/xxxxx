<?php
namespace addons\videojs;
use app\common\controller\Addon;
use Think\Db;
use Think\Config;
class videojs extends Addon
{
    private $error;
    public $info = array(
        'name' => 'videojs',
        'title' => 'Videojs视频',
        'description' => 'Videojs视频',
        'status' => 1,
        'author' => 'Cansnow',
        'version' => '1.0'
    );
    public function install()
    {
        $this->getisHook('__after_thread_view__', $this->info['name'], $this->info['description']);
        return true;
    }
    public function uninstall()
    {
        return true;
    }
    public function videojs()
    {
        return $this->run();
    }
	function run(){
        $config = $this->getConfig('videojs');
        $this->assign('random',$config['random']);
        $this->assign('class',$config['class']);
        echo  $this->tplfetch('videojs');
	}
}