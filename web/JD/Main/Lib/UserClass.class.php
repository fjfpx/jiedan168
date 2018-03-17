<?php
namespace Main\Lib;
use Library\network;
use Library\endecode;
class UserClass{
    public static function checkInfo($params){
        $userModel = M('Member');
        foreach( $params as $k=>$v){
            $condition[$k] =$v;
            $mark = $userModel->where($condition)->find();
            if (is_array($mark)&&!empty($mark)) {
                $arr[$k] = true;
            } else {
                $arr[$k] = false;
            }
        }
        return $arr;
    }

    public static function checkInvite($invitecode){
        $rst = false;
        if(isset($invitecode) && $invitecode!=''){
            $condition['own_invitecode'] = $invitecode;
            $condition['user_type'] = 2;
            $condition['user_status'] = 1;
            $rst = M("Member")->field("user_id,phone")->where($condition)->find();
        }
        return $rst;
    }

    public static function addUser($params){
        $ret = array('success'=>0, 'msg'=>'', 'data'=>array() );

        if( !isset($params['phone']) || !isset($params['password'])){
            $ret['msg'] = '请填写手机号或密码';
            return  $ret;
        }

        $data = array();
        $data['user_status'] = $params['user_status'];
        $data['user_type'] = 2;
        $data['phone'] = $data['username'] = $params['phone'];
        $data['app_devicetype'] = $params['app_devicetype'];
        $data['app_token'] = $params['app_token'];
        $data['app_devid'] = $params['app_devid'];
        $data['app_devicetoken'] = $params['app_devicetoken'];
        $data['app_sysversion'] = $params['app_sysversion'];
        $data['app_version'] = $params['app_version'];
        $data['token_timeout'] = $params['token_timeout'];
        $data['own_invitecode'] = $params['own_invitecode'];
        $data['password'] = sp_password($params['password']);
        $data['last_login_ip'] = network\NetTool::ip_address();
        $data['create_time'] = date("Y-m-d H:i:s",time());
        //$data['invitecode'] = $params['invitecode'];

        //废弃ip库查询
        $data['utype'] = 0;
        $ip = new \Library\network\IP();
        $rst = $ip->find('',$data['phone']);
        if($rst){
            if($rst[0]!='中国' && $rst[0]!='本机地址'){
                $data['utype'] = 1;
            }
        }
        $userModel = M("Member");
        //$userModel = M('Member',C("DB_PREFIX"),C('DB_CONFIG2'));
        $user_id = $userModel->add($data);
        if(!$user_id){
            $ret['msg'] = "创建用户失败";
        }else{
            $ret['success']=1;
            $ret['data']['user_id']=$user_id;
            // 加入缓存
            //$userCacheModel = M('UserCache');
            //$userCacheModel->add(array("user_id" => $user_id));
        }

        return $ret;
    }

    public static function own_invitecode(){
        $own_invitecode = rand_string(5,0,'123456789');
        $ck = M("Member")->where(array('own_invitecode'=>$own_invitecode))->find();
        if($ck){
            self::own_invitecode();
        }else{
            return $own_invitecode;
        }
    }

    public function supplement($uid){
        if(isset($uid) && $uid!=''){
            if( M("Member")->where(array('user_id'=>$uid))->find() ){
                $var = array('Base','Bank','Credit','Operator','Taobao','Alipay','Idcardinfo');
                $condition = array('uid'=>$uid);
                foreach($var as $v){
                    if( !M("Member".$v)->where($condition)->find() ){
                        M("Member".$v)->add($condition);
                    }
                }
            }
        }
    }
}
