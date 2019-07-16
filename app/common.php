<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
error_reporting(E_ERROR | E_WARNING | E_PARSE);
// 应用公共文件
/**
 * [q_add 添加工作队列]
 * @param  string $name [队列标志]
 * @param  array  $data [队列附加数据]
 * @return [type]       [mixed]
 */
function _cny_map_unit($list,$units) { 
    $ul=count($units); 
    $xs=array(); 
    foreach (array_reverse($list) as $x) { 
        $l=count($xs); 
        if ($x!="0" || !($l%4)) $n=($x=='0'?'':$x).($units[($l-1)%$ul]); 
        else $n=is_numeric($xs[0][0])?$x:''; 
        array_unshift($xs,$n); 
    } 
    return $xs; 
}
function tocnupper($ns) { 
    static $cnums=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"), 
        $cnyunits=array("圆","角","分"), 
        $grees=array("拾","佰","仟","万","拾","佰","仟","亿"); 
    list($ns1,$ns2)=explode(".",$ns,2); 
    $ns2=array_filter(array($ns2[1],$ns2[0])); 
    $ret=array_merge($ns2,array(implode("",_cny_map_unit(str_split($ns1),$grees)),"")); 
    $ret=implode("",array_reverse(_cny_map_unit($ret,$cnyunits))); 
    return str_replace(array_keys($cnums),$cnums,$ret); 
}
function q_add($data=[],$queueName='default'){
    $handlers=[
        'email'=>'',
        'sms'=>'',
    ];
    if(!$queueName){
        return '$queueName can\'t empty!';
    }
    // 1.当前任务将由哪个类来负责处理。 
    //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
    
    $jobHandlerClassName  = 'app\home\job\\'.ucwords($queueName);
    // 2.当前任务归属的队列名称，如果为新队列，会自动创建
    // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
    // ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
    $data['ts'] = time();
    $data['bizId'] = uniqid();
    $data['a'] = 1;
    // 4.将该任务推送到消息队列，等待对应的消费者去执行
    $isPushed = \think\Queue::push( $jobHandlerClassName , $data , $queueName );   
    // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
    if( $isPushed !== false ){  
        return true;
    }else{
        return 'Oops, something went wrong.';
    }
}
function P($msg){
	echo '<pre>';
	print_r($msg);
	echo '</pre>';
}
/*function default($a='',$b=''){
    if(!$a) {return $b;}
}*/
function getControllerTitle($name){
    return model('Rule')->where('`name`="Admin/'.$name.'/index"')->column('title')[0];
}
function replace($str='',$search='',$replace=''){
    return str_replace($search, $replace, $str);
}
function GTC($id,$table,$field='title'){
	return model($table)->where('id='.$id)->value($field);
}
function Linkage($v='',$type=''){
	if(empty($type) || $v===""){return '';}
	$Linkages=LinkageList($type);
    return $Linkages[$v];
}
function LinkageList($type='',$out = 'array'){
	if(empty($type)){return '';}
	$Linkages=Cache('LinkageList');
	if(!$Linkages){
        $Linkage = model('Linkage');
        $types=$Linkage->Distinct(true)->Field('type')->select();
        $Linkages=array();
        foreach ($types as $k => $v) {
            $Linkages[$v['type']]=$Linkage->where('type="'.$v['type'].'" and status=1')->order('sort asc')->column('name,title');
        }
        Cache('LinkageList',$Linkages);
	}
	//P($Linkages);
    if($out=='json'){
        return json_encode($Linkages[$type]);
    }else{
        return $Linkages[$type];
    }
}
function getCardInfo($card,$name=''){
    $len=strlen($card);
    $arr=array(
        'year' => '',
        'month' => '',
        'day' => '',
        'birthday' => '',
        'province' => '',
        'city' => '',
        'distruct' => '',
        'address' => '',
        'addrcode' => '',
    );
    $addrcode=intval(substr($card,0,6));
    $arr['addrcode']=$addrcode;
    if($len==18){
        $tyear=intval(substr($card,6,4));
        $tmonth=intval(substr($card,10,2));
        $tday=intval(substr($card,12,2));
        $sex = (substr($card,16,1) % 2==0) ? 2 : 1;
    }elseif($len==15){
        $tyear=intval("19".substr($card,6,2));
        $tmonth=intval(substr($card,8,2));
        $tday=intval(substr($card,10,2));
        $sex = (substr($card,14,1) % 2==0) ? 1 : 2;
    }else{
    }
    $arr['sex']=$sex;
    $arr['zhsex']=Linkage($sex,'Sex');
    $arr['year']=$tyear< 10 ? '0'.$tyear : $tyear;
    $arr['month']=$tmonth< 10 ? '0'.$tmonth : $tmonth;
    $arr['day']=$tday< 10 ? '0'.$tday : $tday;
    $arr['birthday']=$arr['year'].'-'.$arr['month'].'-'.$arr['day'];
    $arr['age']=date('Y')-$tyear;
    if($name==''){
        return $arr;
    }else{
        return $arr[$name];
    }
}

/*获取邮件内容*/
function getEmailContent($body='',$buttons=array()){
    $btns='';
    foreach($buttons as $name => $href){
        $btns.='<a href="'.$href.'" style="padding: 8px 18px; background: #009A61; color: #FFF; text-decoration: none; border-radius: 3px;" target="_blank">'.$name.'</a>';
    }
    $content = '<div style="height: 100%; background: #444; padding: 80px 0; margin: 0; font-size: 14px; line-height: 1.7; font-family: \'Helvetica Neue\', Arial, sans-serif; color: #444;">
        <center>
        <div style="margin: 0 auto; width: 580px; background: #FFF; box-shadow: 0 0 10px #333; text-align:left;">
            <div style="margin: 0 40px; color: #999; border-bottom: 1px dotted #DDD; padding: 40px 0 30px; font-size: 13px; text-align: center;">
                <a href="http://'.C('WEB_URL').'" target="_blank"><img src="http://'.C('WEB_URL').'/Home/Tpl/Home/default/Public/Images/logo.png"></a>
            </div>
            <div style="padding: 30px 40px 40px;word-break: break-all;word-wrap: break-word;">'.str_replace('<a ','<a style="color: #009A61; text-decoration: none;" ',$body).'</div>
            <div style="background: #EEE; border-top: 1px solid #DDD; text-align: center; height: 90px; line-height: 90px;">'.$btns.'</div></div><div style="padding-top: 30px; text-align: center; font-size: 12px; color: #999;display:none;">* 如果不想再收到类似邮件，可点击 <a href="http://segmentfault.com/user/confirm?type=notice&amp;code=826f02961d32e0b4fcc66d171c2ff752" style="color: #009A61; text-decoration: none" target="_blank">退订</a><br>
            <a style="color: #009A61; text-decoration: none;" href="http://twitter.com/segment_fault" target="_blank">Twitter</a> · <a style="color: #009A61; text-decoration: none;" href="http://weibo.com/segmentfault" target="_blank">新浪微博</a>
        </div>
    </center>
    </div>
    <div style="height: 100%; background: #444; padding: 40px 0; margin: 0; font-size: 14px; line-height: 1.7; font-family: \'Helvetica Neue\', Arial, sans-serif; color: #444;"></div>
    <br><br>';
    return str_replace(array('__WEB_TITLE__','__WEB_URL__','__WEB_NAME__'),array(C('WEB_TITLE'),C('WEB_URL'),C('WEB_NAME')),$content);
}

function adminlog($data=array()){
    $Adminlog= D('Adminlog');
    if (!$Adminlog->validate()->create($data)){
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        return false;
    }else{
        $res_id = $Adminlog->add();
        return $res_id;
    }
}


/*发送邮件*/
function sendmail($mailto=null,$name=null,$title=null,$content=null){
	$mail = new \PHPMailer\PHPMailer\PHPMailer();
	$mail->IsSMTP();					// 启用SMTP
	$mail->Host = C("Mail_Smtp");			//SMTP服务器
	$mail->SMTPAuth = true;					//开启SMTP认证
	$mail->Username = C("Mail_Sender");			// SMTP用户名
	$mail->Password = C("Mail_Pw");				// SMTP密码
	$mail->IsHTML(true);					// 是否HTML格式邮件
	$mail->CharSet = "utf-8";				// 这里指定字符集！
	$mail->Encoding = "base64";

	$mail->From = C("MAIL_EMAIL");			//发件人地址
	$mail->FromName = C("WEB_NAME");				//发件人
	//$mail->AddAddress($mailto, $name);	//添加收件人
	//$mail->AddAddress("ellen@example.com");
	if (is_array($mailto)){
		foreach ($mailto as $key => $value) {
			$mail->AddAddress($key, $value);
		}
	}else{
		$mail->AddAddress($mailto, $name);	//添加收件人
	}
	$mail->AddReplyTo(C("MAIL_EMAIL"), C("WEB_NAME"));	//回复地址
	$mail->WordWrap = 50;					//设置每行字符长度
	/** 附件设置
	$mail->AddAttachment("/var/tmp/file.tar.gz");		// 添加附件
	$mail->AddAttachment("/tmp/image.jpg", "new.jpg");	// 添加附件,并指定名称
	*/
	$mail->Subject = $title;			//邮件主题
	$mail->Body    = $content;		//邮件内容
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";	//邮件正文不支持HTML的备用显示

	if(!$mail->Send())
	{
	   return "Message could not be sent. <p>Mailer Error: " . $mail->ErrorInfo;
	}
	return true;
}
function get($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    /*curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
    curl_setopt($ch, CURLOPT_REFERER,_REFERER_);*/
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}
function post($url, $data = null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}

function rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1 :
            $chars = str_repeat ( '0123456789', 3 );
            break;
        case 2 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3 :
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) { //位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
    }
    if ($type != 4) {
        $chars = str_shuffle ( $chars );
        $str = substr ( $chars, 0, $len );
    } else {
        // 中文随机字
        for($i = 0; $i < $len; $i ++) {
            $str .= msubstr( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
        }
    }
    return $str;
}


/* 生成不重复数 */
function build_count_rand ($number,$length=4,$mode=1)
{
    if($mode==1 && $length<strlen($number) )
    {
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++)
    {
        $rand[] =   rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand))
    {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {    
    // 创建Tree    
    $tree = array();    
    if(is_array($list)) {    
        // 创建基于主键的数组引用    
        $refer = array();    
        foreach ($list as $key => $data) {    
            $refer[$data[$pk]] =& $list[$key];    
        }    
        foreach ($list as $key => $data) {    
            // 判断是否存在parent    
            $parentId = $data[$pid];    
            if ($root == $parentId) {    
                $tree[] =& $list[$key];    
            }else{    
                if (isset($refer[$parentId])) {    
                    $parent =& $refer[$parentId];    
                    $parent[$child][] =& $list[$key];    
                }    
            }    
        }    
    }    
    return $tree;    
}  
function getAbstruct($vo){
    return $vo['abstruct'] ? $vo['abstruct'] :substr($vo['content'], 0,250);
}