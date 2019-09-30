<?php
namespace addons\Encrypt;
use app\common\controller\Addon;
use Think\Db;
use Think\Config;
class Encrypt extends Addon
{
    private $error;
    public $info = array(
        'name' => 'Encrypt',
        'title' => 'Encrypt加密解密',
        'description' => 'Encrypt加密解密库',
        'status' => 1,
        'author' => 'Cansnow',
        'version' => '1.0'
    );
    public function install()
    {
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
	}
	function __construct(){
		parent::__construct();
		$this->config  = $this->getConfig();
	}
	
	/**
	 * 加密解密用户ID,
	 * @param unknown $string
	 * @param string $action  encode|decode
	 * @return string
	*/
	function UserId($string, $action = 'encode') {
		$startLen = 13;
		$endLen = 8;
		$coderes = '';
		#TOD 暂设定uid字符长度最大到9
		if ($action=='encode') {
			$uidlen = strlen($string);
			$salt = $this->appkey;
			$codestr = $string.$salt;
			$encodestr = hash('md4', $codestr);
			$coderes = $uidlen.substr($encodestr, 5,$startLen-$uidlen).$string.substr($encodestr, -12,$endLen);
			$coderes = strtoupper($coderes);
		}elseif($action=='decode'){
			$strlen = strlen($string);
			$uidlen = $string[0];
			$coderes = substr($string, $startLen-$uidlen+1,$uidlen);
		}
		return $coderes;
	}
	public function aes($str="",$type='encode'){
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $secret_key = hex2bin(strtolower($this->config['appkey']));
        mcrypt_generic_init($td, $secret_key, $iv);
        $ret = "";
        if($type=="encode"){
            $str = $this->pkcs5_pad($str);
            $cyper_text = mcrypt_generic($td, $str);
            $ret = strtoupper(bin2hex($cyper_text));
        }else{
            $decrypted_text = mdecrypt_generic($td, hex2bin(strtolower($str)));
            $ret = $this->pkcs5_unpad($decrypted_text);
        }
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td); 
        return $ret;
    }
    public function rsa($str="",$type='encode',$code='base64'){
        if($type=='encode'){
            $pubkeyid    = openssl_pkey_get_public(file_get_contents($this->config['key_path'].'public.key'));            
            if (openssl_public_encrypt($str, $crypttext, $pubkeyid))
            {
                if($code == 'base64'){
                    return base64_encode("".$crypttext);
                }else{
                    return bin2hex("".$crypttext);
                }
            }
        }else{
            $prikeyid    = openssl_get_privatekey(file_get_contents($this->config['key_path'].'private.key'));
            if($code == 'base64'){
                $str   = base64_decode($str);
            }else{
                $str   = hex2bin($str);
            }
            if (openssl_private_decrypt($str, $sourcestr, $prikeyid, OPENSSL_PKCS1_PADDING))
            {
                return $sourcestr;
            }
        }
        return ;
    }
 
    public function pkcs5_pad($text)
    {
        $blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
 
    public function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }
    public function sha($data='',$type='encode'){
        $str = $data;
        if($type == 'encode'){
            $secret_key = hex2bin(strtolower($this->config['appkey']));
            $secret_key = $this->config['appkey'];
            return strtoupper(bin2hex(hash_hmac("sha256",$str , $secret_key, true)));
        }else{
            $secret_key = hex2bin(strtolower($this->config['appkey']));
            $secret_key = $this->config['appkey'];
            return strtoupper(bin2hex(hash_hmac("sha256",$str , $secret_key, true)));            
        }
    }
    public function createRsaKey(){
        //私钥 转PEM openssl rsa -in private.key -text > private.pem 
        $config = array(
            "digest_alg"        =>  "sha512",//摘要算法或签名哈希算法，通常是 openssl_get_md_methods() 之一。
            "private_key_bits"  =>  1024,   //  指定应该使用多少位来生成私钥
            "private_key_type"  =>  OPENSSL_KEYTYPE_RSA,    //选择在创建CSR时应该使用哪些扩展。可选值有 OPENSSL_KEYTYPE_DSA, OPENSSL_KEYTYPE_DH, OPENSSL_KEYTYPE_RSA 或 OPENSSL_KEYTYPE_EC. 默认值是 OPENSSL_KEYTYPE_RSA.
            //"encrypt_key"       =>  '661223',   //是否应该对导出的密钥（带有密码短语）进行加密?
            'config'            =>  $this->config['opensslConfigPath']
        );       
         
        //创建密钥对
        $res = openssl_pkey_new($config);
        //生成私钥
        openssl_pkey_export($res, $privkey, null, $config);
        $details = openssl_pkey_get_details($res);
        $e = bin2hex($details['rsa']['e']);
        //生成公钥
        $pubKey = $details['key'];  
        file_put_contents($this->config['key_path'].'private.key',$privkey);
        file_put_contents($this->config['key_path'].'public.key',$pubKey);
        //file_put_contents($this->config['key_path'].'key.js',"var public_key = '".str_replace("\n",'\n',$pubKey)."';\nmodulus=\"".$_modules."\";\nvar rsa_n=\"".$n."\";\nvar rsa_e='".$e."';");
        file_put_contents($this->config['key_path'].'key.js',"var public_key = '".str_replace("\n",'\n',$pubKey)."';\nvar rsa_e='".$e."';");
        return $pubKey;
    }
}