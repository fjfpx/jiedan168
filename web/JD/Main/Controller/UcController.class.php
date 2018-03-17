<?php
namespace Main\Controller;
use Think\Controller;
use Library\endecode;
use Library\network;
use Library\BaseController;
class UcController extends BaseController {
    public function submitlogin(){
        unset($this->jret['loginstatus']);
        // 表单信息验证 
        if(!isset($_REQUEST['phone'])|| $_REQUEST['phone'] == ''){
            $this->jret['msg'] = "手机号不能为空";
        }elseif(!isset($_REQUEST['password'])|| $_REQUEST['password'] == ''){
            $this->jret['msg'] = "密码不能为空";
        }else{
            $userModel = M("Member");
            $phone = trim($_REQUEST['phone']);
            //判断哪种方式登录
            if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                $condition['phone'] = $phone;
            }else{
                $this->jret['msg'] = '手机号格式错误';
                $this->ajaxReturn($this->jret);
            }
            // 用户是否存在
            $mark = $userModel->where($condition)->select();
            if($mark===false){
                $this->jret['msg'] = "系统出错";
            }elseif(empty($mark)){
                //查不到
                $this->jret['msg'] = '账号不存在,请注册';
            }else{
                //查到了
                //判断是否被锁定，锁定时间满解锁
                $user_result = $mark[0];

                if($user_result['unlocktime']>=time() && $user_result['islock']=='1'){
                    $ltm = $user_result['unlocktime']-time();
                    $min = ceil($ltm/3600);
                    $this->jret['msg'] = '该用户已被锁定，请于'.$min.'分钟后重试';
                    $this->ajaxReturn($this->jret);
                //}elseif($user_result['user_status']==0){
                //    $this->jret['msg'] = '该用户已被锁定,请联系管理员';
                //    $this->ajaxReturn($this->jret);
                }elseif(isset($_SESSION['lock_endtime']) && $_SESSION['lock_endtime']<time()){
                    unset($_SESSION['lock_endtime']);
                    $data = array(
                            'failtime' => '0',
                            'islock' => '0',
                            'unlocktime' => null
                            );
                    $userModel->where(array('user_id'=>$user_result['user_id']))->save($data);
                    $user_result = $userModel->where($where)->find();
                }
                if(!sp_compare_password($_REQUEST['password'],$user_result['password'])){
                    //失败5次锁定1小时
                    if($user_result['failtime'] < 5){
                        $data = array('failtime'=>$user_result['failtime']+1);
                        $_SESSION ['lock_endtime'] = time()+20;
                        $userModel->where(array('user_id'=>$user_result['user_id']))->save($data);
                        $this->jret['msg'] = "密码错误";
                        $this->ajaxReturn($this->jret);
                    }else{
                        $data = array(
                                'unlocktime'=>time()+1800,
                                'islock'=>'1'
                                );
                        $userModel->where(array('user_id'=>$user_result['user_id']))->save($data);
                        $this->jret['msg'] = "登录失败次数已达上限，请于30分钟后重试";
                        $this->ajaxReturn($this->jret);
                    }
                }else{
                    //重复登录检查
                    $lc = $this->cache->get($user_result['app_token']);
                    if($lc && $lc['app_token'] && time()<=$lc['token_timeout']){
                        //踢出上次登录设备
                        $this->cache->rm($user_result['app_token']);
                    }
                    //成功登录锁定状态置为初始状态
                    $data = array(
                            'failtime' => '0',
                            'islock' => '0',
                            'unlocktime' => null,
                            'app_token' => \Main\Lib\TokenClass::settoken(),
                            'token_timeout' => time()+3600*24*7
                            );
                    $userModel->where(array('user_id'=>$user_result['user_id']))->save($data);
					// already login
					$this->setLoginStatus($user_result['user_id']);
					$this->jret['flag'] = 1;
					$this->jret['msg'] = "登录成功";
					$this->jret['result']['user_id'] = (int)$user_result['user_id'];
                    $this->jret['result']['ping_session'] = $data['app_token'];
                    $this->jret['result']['utype'] = $user_result['utype'];

                    //cache
                    $user_result['app_token'] = $data['app_token'];
                    $user_result['token_timeout'] = $data['token_timeout'];
                    $base = M("MemberBase")->where(array('uid'=>$user_result['user_id']))->field('real_name,idcard')->find();
                    $user_result['real_name'] = $base['real_name'];
                    $user_result['idcard'] = $base['idcard'];
                    $this->cache->set($data['app_token'],$user_result,3600*24*7);
                }
            }
        }
        $this->ajaxReturn($this->jret);
    }

    public function submitReg(){
        $var = array (
                'phone',
                'phone_valicode',
                'password',
                );
        foreach( $var as $v ){
            if( isset($_REQUEST[$v]) ){
                $data[$v] = trim($_REQUEST[$v]);
            }
        }
        unset($this->jret['loginstatus']);
        if(!isset( $data['phone']) || empty($data['phone'])){
            $this->jret['msg'] = '手机号不能为空';
        }elseif( !isset( $data['password']) || empty($data['password']) ){
            $this->jret['msg'] = '请输入密码';
        }elseif(!preg_match("/^1[34578]{1}\d{9}$/",$data['phone'])){
            $this->jret['msg'] = '手机号格式不正确';
        }elseif(!isset($data['phone_valicode'])|| $data['phone_valicode'] == ''){
            $this->jret['msg'] = "手机验证码不能为空";
        }else{
            $skey_v = "{$data['phone']}reg_valicode";
            $valicode = $this->cache->get($skey_v);
            if( !isset($valicode) && intval($data['phone_valicode'])!=intval(C('DEBUG_PHONE_VALICODE')) ){
                $this->jret['msg'] = '手机验证码不存在';
            }elseif( !self::checkValicode($skey_v,$data['phone_valicode'],$data['phone']) ){
                $this->jret['msg'] = '手机验证码不匹配';
            }else{
                $this->cache->rm($skey_v);
                $ck_user = \Main\Lib\UserClass::checkInfo(array('phone'=>$data['phone']));
                if($ck_user['phone']){
                    $this->jret['msg'] = '手机号已存在';
                }else{
                    $data['user_status'] = 1;
                    $data['own_invitecode'] = \Main\Lib\UserClass::own_invitecode();
                    $data['app_devicetype'] = $this->base_params['devicetype'];
                    $data['app_token'] = \Main\Lib\TokenClass::settoken();
                    $data['app_devid'] = $this->base_params['devid'];
                    $data['app_devicetoken'] = $this->base_params['devicetoken'];
                    $data['app_sysversion'] = $this->base_params['sysversion'];
                    $data['app_version'] = $this->base_params['appversion'];
                    $data['token_timeout'] = time()+3600*24*7;
                    $reg_result = \Main\Lib\UserClass::addUser( $data );
                    if( !$reg_result['success'] ){
                        $this->jret['msg'] = $reg_result['msg'];
                    }else{
                        \Main\Lib\UserClass::supplement( $reg_result['data']['user_id'] );

                        $this->jret['flag'] = 1;
                        $this->jret['msg'] = "注册成功，请登录";

                    }
                }
            }
        }
        $this->ajaxReturn($this->jret);
    }

    public function findPwd(){
        //step 1, 获取验证码,提交验证码验证 
        //step 2, 设置新密码
        $var = array('phone','phone_valicode','password','step');

        foreach($var as $v){
            if(isset($_REQUEST[$v]) && $_REQUEST[$v]!=''){
                $data[$v] = trim($_REQUEST[$v]);
            }
        }

        if($data['step']==2){
            //设置新密码
            if(!isset( $data['phone']) || empty($data['phone'])){
                $this->jret['msg'] = '请输入手机号';
            }elseif(!isset( $data['password']) || empty($data['password'])){
                $this->jret['msg'] = '请输入新密码';
            }else{
                $newpwd = sp_password($data['password']);
                if( M("Member")->where(array('phone'=>$data['phone']))->save(array('password'=>$newpwd))!==false ){
                    $this->jret['flag'] = 1;
                    $this->jret['msg'] = '密码修改成功';
                }else{
                    $this->jret['msg'] = '密码修改失败';
                }
            }
        }else{
            //验证验证码
            if(!isset( $data['phone']) || empty($data['phone'])){
                $this->jret['msg'] = '请输入手机号';
            }elseif(!preg_match("/^1[34578]{1}\d{9}$/",$data['phone'])){
                $this->jret['msg'] = '手机号格式不正确';
            }elseif(!isset($data['phone_valicode'])|| $data['phone_valicode'] == ''){
                $this->jret['msg'] = "手机验证码不能为空";
            }else{
                $skey_v = "{$data['phone']}chg_valicode";
                $valicode = $this->cache->get($skey_v);
                if( !isset($valicode) && intval($data['phone_valicode'])!=intval(C('DEBUG_PHONE_VALICODE')) ){
                    $this->jret['msg'] = '手机验证码不存在';
                }elseif( !self::checkValicode($skey_v,$data['phone_valicode'],$data['phone']) ){
                    $this->jret['msg'] = '手机验证码不匹配';
                }else{
                    $this->cache->rm($skey_v);
                    $this->jret['flag'] = 1;
                    $this->jret['msg'] = '验证成功';
                    $this->jret['result'] = array('phone'=>$data['phone']);
                }
            }
        }
        unset($this->jret['loginstatus']);
        $this->ajaxReturn($this->jret);
    }

    public function checkInvitecode(){
        $invitecode = trim(I("invitecode"));
        if(isset($invitecode) && $invitecode!=''){
            $ck = \Main\Lib\UserClass::checkInvite($invitecode);
            if($ck){
                $this->jret['flag'] = 1;
                $this->jret['msg'] = 'OK';
            }else{
                $this->jret['msg'] = '无此邀请码';
            }
        }else{
            $this->jret['msg'] = '请填写邀请码';
        }
        $this->ajaxReturn($this->jret);
    }

    public function setLoginStatus($user_id){
        $ctime = time () + 3600*24*7;
        /*
        if (isset ( $_POST['cookietime']) && I("post.cookietime") > 0) {  
            $ctime = time () + I("post.cookietime") * 86400;
        }
        $key = $this->getUserSessionKey();
        $value = $this->getUserSessionValue($user_id);
        if ( C('is_cookie') == 1) {
            setcookie ( $key, $value, $ctime ,"/");
        }else{
            $_SESSION [ $key ] = $value;
            $_SESSION ['login_endtime'] = $ctime;
        }
        */
        $urs = D("Member")->getOne( array('user_id'=>$user_id) );
        $data = array(
            'user_id' => $user_id,
            'logintimes' => $urs['logintimes']+1,
            'last_login_time' => date("Y-m-d H:i:s",time()),
            'last_login_ip' => network\NetTool::ip_address() 
        );
        $data['app_devicetype'] = $this->base_params['devicetype'];
        $data['app_token'] = $urs['app_token'];
        if( $this->base_params['devicetype'] == 1 ||$this->base_params['devicetype'] == 2){
            $data['app_devid'] = $this->base_params['devid'];
            $data['app_sysversion'] = $this->base_params['sysversion'];
            $data['app_systype'] = $this->base_params['systype'];
        }
        M("Member")->save($data);
        // login log
        $data['addip'] = $data['last_login_ip'];
        $data['addtime'] = $data['last_login_time'];
        $data['uid'] = $user_id;
        D("Member")->checkLog($data);
        $data['phone'] = $urs['phone'];
        logging( json_encode($data), 'PATH_LOG_LOGIN');
    }

    public function checkValicode($k,$v,$phone){
        // debug
        if(C('DEBUG_FLAG')){
            if(in_array($phone,C('DEBUG_PHONE'))){
                $phone_valicode = C('DEBUG_PHONE_VALICODE');
                if($phone_valicode==$v) return true;
            }
        }

        if($this->cache->get($k) == $v){
            $this->cache->rm($k);
            return true;
        }else{
            return false;
        }
    }
}
