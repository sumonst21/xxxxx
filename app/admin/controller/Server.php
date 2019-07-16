<?php
namespace app\admin\controller;
use app\admin\controller\Base;
define('VEID','840182');
define('API_KEY','private_E3A1RO6F8JKNWdiTw3RV4gTv');
class Server extends Base
{
    //Get information about server
    function serverInfo(){
        $request = 'https://api.64clouds.com/v1/getServiceInfo?veid='.VEID.'&api_key='.API_KEY;
        $serviceInfo = get($request);
        print_r ($serviceInfo);
    }
    //Create a snapshot
    function createSnapshot(){
        $request = 'https://api.64clouds.com/v1/snapshot/create?description=Automatic_Snapshot&veid='.VEID.'&api_key='.API_KEY;
        $serviceInfo = get($request);
        print_r ($serviceInfo);        

    }
    //Restart VPS
    function restartVPS(){
        $request = 'https://api.64clouds.com/v1/restart?veid='.VEID.'&api_key='.API_KEY;
        $serviceInfo = get($request);
        print_r ($serviceInfo);
    }
    //Set PTR record
    function setPTRRecord(){
        $request = 'https://api.64clouds.com/v1/setPTR?veid='.VEID.'&api_key='.API_KEY;
        $serviceInfo = get($request);
        print_r ($serviceInfo);
    }
    function get($url){
        $ret = json_decode (file_get_contents ($url),true);
        return $ret;
    }
    function _empty($a,$v=1){
        $apis=[
            'start'=>[],
            'stop'=>[],
            'restart'=>[],
            'kill'=>[],
            'getServiceInfo'=>[],
            'getLiveServiceInfo'=>[],
            'getAvailableOS'=>[],
            'reinstallOS'=>['os'],
            'resetRootPassword'=>[],
            'getUsageGraphs'=>[],
            'getRawUsageStats'=>[],
            'setHostname'=>['newHostname'],
            'setPTR'=>['ip', 'ptr'],
            'iso/mount'=>['iso'],
            'iso/unmount'=>[],
            'basicShell/cd'=>['currentDir', 'newDir'],
            'basicShell/exec'=>['command'],
            'shellScript/exec'=>['script'],
            'snapshot/create'=>['description'],
            'snapshot/list'=>[],
            'snapshot/delete'=>['snapshot'],
            'snapshot/restore'=>['snapshot'],
            'snapshot/toggleSticky'=>['snapshot','sticky'],
            'snapshot/export'=>['snapshot'],
            'snapshot/import'=>['sourceVeid', 'sourceToken'	],
            'backup/list'=>[],
            'backup/copyToSnapshot'=>['backup_token'],
            'ipv6/add'=>['ip'],
            'ipv6/delete'=>['ip'],
            'migrate/getLocations'=>[],
            'migrate/start'=>['location'],
            'cloneFromExternalServer'=>['externalServerIP','externalServerSSHport','externalServerRootPassword'],
            'getSuspensionDetails'=>[],
            'unsuspend'=>['record_id'],
            'getRateLimitStatus'=>[],
            'privateIp/getAvailableIps	'=>[],
            'privateIp/assign'=>['ip'],
            'privateIp/delete'=>['ip'], 
        ];
        if(array_key_exists($a,$apis)){
            P($a);
            P($v);
        }else{
            echo '_404';
        }
    }
}
