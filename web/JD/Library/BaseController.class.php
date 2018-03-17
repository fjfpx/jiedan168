<?php
namespace Library;
use Think\Controller;
use Library\endecode;
use Library\network;
use Main\Model\UserModel;
use Think\Cache;
/**
* check login & privileges & security
* devid
* devicetype: 0, web or wap; 1, apple; 2,android;
*/
abstract class BaseController extends Controller{
    protected $user_result;
    protected $base_params;
    protected $jret;
    protected $cache;

    /** error related
    */
    public function __call($function,$args){
        $this->jerror("The request API doesn't exist!",-2);
    }
    protected function jerror($msg, $st=-1){
        $data = array(
            'flag' => 2,
            'status' => $st,
            'loginstatus' => empty($this->user_result)?-4:0,
            'msg' => $msg
        );
        if(C('LOG_LEVEL')>=4)logging( CONTROLLER_NAME."/".ACTION_NAME.json_encode($data), 'PATH_LOG_ERROR');
        if($data['status'] == -2 ){
            if($this->base_params['devicetype']==0){
                $this->display( 'Public:404' );
                exit;
            }
        }
        $this->ajaxReturn($data);
    }

    /** login related
    */
    // web&wap vs. app(client)
    public function _initialize(){
        $this->jret = array('flag'=>0,'msg'=>'','data'=>'', 'islocked'=>0,'loginstatus'=>-4);
        $this->user_result = null;

        //开启memcached
        $this->cache = Cache::getInstance('Memcache');

        // device
        $vars = array('devid', 'devicetype', 'sysversion','appversion','devicetoken','ping_session');
        $this->base_params = array('devicetype'=>0);
        if(ACTION_NAME=='edit_post'){
            $_data = json_decode(file_get_contents('php://input'),true);
        }else{
            $_data = $_REQUEST;
        }
        foreach($vars as $k ){
            if( isset( $_data[$k] ) ){
                $this->base_params[$k] = $_data[$k];
            }
        }
        if( $this->base_params['devicetype'] ){
            $this->base_params['devicetype'] = intval( $this->base_params['devicetype'] );
        }

        $this->jret['data']['prepage'] = "";
        if(isset($_SESSION['prepage']) && $_SESSION['prepage']!="" && $_SESSION['prepage']!="/null" ){
            $this->jret['data']['prepage'] = $_SESSION['prepage'];
        }
        $user_id = $this->getSessionUserId();
        // login status
        if( $this->base_params['devicetype'] == 0 ){
            $this->loginByWeb();
        }elseif( $this->base_params['devicetype'] == 1 || $this->base_params['devicetype'] == 2){
            $this->loginByWeb();
        }else{
            $this->jerror("device not support");
        }

        $this->checkPrivilege();

        // TODO: ip,acccess control

        if($this->user_result){
            $this->jret['loginstatus'] = 0;
            $this->jret['islocked'] = $this->user_result['islock'];
        }
    }
    private function loginByApp(){
        if( isset($this->base_params['ping_session']) && $this->base_params['ping_session'] ){
            $userM = D('Member');
            $user_result = $userM->getOne(array('app_token'=>$this->base_params['ping_session']));
            if($user_result ){
                $this->user_result= $user_result;
            }
        }
    }

    private function loginByWeb(){
        if( isset($this->base_params['ping_session']) && $this->base_params['ping_session'] ){
            $get = $this->cache->get($this->base_params['ping_session']);
            if( $get['app_token']==$this->base_params['ping_session'] && time()<=$get['token_timeout'] ){
                $this->user_result = $get;
            }
        }
    }
        
    protected function checkPrivilege(){
        // step 1
        $privileges = C('access_control');
        $prequire = array("all");
        if(isset($privileges['default'])){
            $prequire = $privileges['default'];
        }

        // step 2
        $classname = get_class($this);
        if( isset($privileges['details'][$classname]) ){
            $prequire = $privileges['details'][$classname];
        }

        // 
        $loginstatus = 'nologin';
        if($this->user_result){
            $loginstatus = 'login';
            if($this->user_result['islock'] !=0){
                $this->jerror("账户已锁定,请联系管理员");
            }
        }
        // judge
        if( !in_array('all', $prequire) && !in_array($loginstatus, $prequire) ){
            $this->jerror("u don't have this privilege $loginstatus");
        }
    }

    protected function getSessionUserId(){
        $user_id = 0;
        $ck_key = $this->getUserSessionKey();
        $ck_v = "";
        if (C('is_cookie') ==1){
            $ck_v = isset($_COOKIE[$ck_key])?$_COOKIE[$ck_key]:"";
        }else{
            if (isset($_SESSION['login_endtime']) && $_SESSION['login_endtime']>time()){
                $ck_v = isset($_SESSION[$ck_key])?$_SESSION[$ck_key]:"";
            }
        }
        $_user_id = explode(",", endecode\UserCode::authcode($ck_v, "DECODE"));
        if($_user_id[0]){
            $user_id = intval($_user_id[0]);
        }
        return $user_id;
    }

    protected function getUserSessionKey(){
        return endecode\UserCode::Key2Url ( "id", C('cookie_key') );
    }
    protected function getUserSessionValue($user_id){
        return endecode\UserCode::authcode ( $user_id . "," . time (), "ENCODE" );
    }
    protected function getCurrentUserSession() {
        $key = $this->getUserSessionKey();
        return isset($_SESSION[$key]) ? $_SESSION[$key] :null;
    }
    protected function setCurrentUserSession($user_id){
        $_SESSION[$this->getUserSessionKey()] = $this->getUserSessionValue($user_id);
    }
}
?>
