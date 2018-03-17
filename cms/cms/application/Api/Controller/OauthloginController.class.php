<?php
namespace Api\Controller;
use Think\Controller;
class OauthloginController extends Controller {
    protected $jret;
    protected $userModel;

    public function _initialize(){
        $this->userModel = M('OauthUser','xcx_',C('DB_CONFIG2'));
        $this->jret = array('flag'=>0,'msg'=>'','result'=>'');
    }
    //目前只有微信
	public function checkOauthUser(){
        if(IS_POST){
            $var = array('name','head_img','openid','access_token','expires_date');
            foreach($var as $v){
                if(isset($_POST[$v]) && $_POST[$v]!=''){
                    $data[$v] = trim($_POST[$v]);
                }
            }
            if(isset($data['openid']) && $data['openid']!=''){
                $userModel = M('OauthUser','xcx_',C('DB_CONFIG2'));
                $data['from'] = '微信';
                $data['last_login_time'] = date("Y-m-d H:i:s");
                $data['last_login_ip'] = get_client_ip();
                $check = $this->userModel->where(array("openid"=>$data['openid']))->find();
                if($check){
                    $data['login_times'] = $check['login_times']+1;
                    $rst = $this->userModel->where(array("user_id"=>$check['user_id']))->save();
                }else{
                    $data['create_time'] = date("Y-m-d H:i:s");
                    $data['login_times'] = 1;
                    $rst = $this->userModel->add($data);
                }

                if($rst !== false){
                    $this->jret['flag'] = 1;
                    $this->jret['result']['user_id'] = is_numeric($rst)?$rst:$check['user_id'];

                    $_data['app_devicetype'] = 0;
                    // login log
                    $_data['addip'] = $data['last_login_ip'];
                    $_data['addtime'] = $data['last_login_time'];
                    $_data['user_id'] = $this->jret['result']['user_id'];
                    self::addLoginLog($_data);
                }
            }else{
                $this->jret['msg'] = 'openid不能为空';
            }
        }
        $this->ajaxReturn($this->jret);
	}

    //支付交易记录
    public function costlog(){
        if(IS_POST){
            $var = array('user_id','out_trade_no','trade_no');
            foreach($var as $v){
                if(isset($_POST[$v]) && $_POST[$v]!=''){
                    $data[$v] = trim($_POST[$v]);
                }
            }
            if(!$data['user_id'] || $data['user_id']==''){
                $this->jret['msg'] = 'user_id不能为空';
            }elseif(!$data['out_trade_no'] || $data['out_trade_no']==''){
                $this->jret['msg'] = 'out_trade_no不能为空';
            }elseif(!$data['trade_no'] || $data['trade_no']!=1){
                $this->jret['msg'] = '请先支付订单金额';
            }else{
                $checkorder = M("Buylist")->where(array("uid"=>$data['user_id'],"out_trade_no"=>$data['out_trade_no']))->find();
                if($checkorder){
                    if($checkorder['status']==1){
                        $this->jret['msg'] = '请勿重复支付';
                    }elseif($checkorder['status']==2){
                        $this->jret['msg'] = '该笔订单已关闭,请重新发起交易订单';
                    }else{
                        
                    }
                }
            }
        }
        $this->ajaxReturn($this->jret);
    }

    //添加订单
    public function addOrder(){
        if(IS_POST){
            $var = array('user_id','openid','money');
            foreach($var as $v){
                if(isset($_POST[$v]) && $_POST[$v]!=''){
                    $data[$v] = $_POST[$v];
                }
            }
            if(!$data['user_id'] || $data['user_id']==''){
                $this->jret['msg'] = 'user_id不能为空';
            }elseif(!$data['openid'] || $data['openid']==''){
                $this->jret['msg'] = 'openid不能为空';
            }elseif(!$data['money'] || $data['money']==''){
                $this->jret['msg'] = 'money不能为空';
            }else{
                $checkuser = $this->userModel->where(array("user_id"=>$data['user_id'],"openid"=>$data['openid']))->find();
                if($checkuser){
                    //创建订单号
                    $order = array(
                            'out_trade_no' => sprintf("%d%08d%d", time(), $data['user_id'], rand(10,99) ),
                            'status' => 0,
                            'addtime' => time(),
                            'addip' => get_client_ip(),
                            'uid' => $data['user_id']
                    );
                    unset($data['user_id'],$data['openid']);
                    $order = array_merge($data,$order);

                    $rst = M("Buylist")->add($order);
                    if($rst){
                        $this->jret['flag'] = 1;
                        $this->jret['result']['out_trade_no'] = $order['out_trade_no'];
                    }else{
                        $this->jret['msg'] = '添加订单失败';
                    }
                }else{
                    $this->jret['msg'] = '用户不存在';
                }
            }
        }
        $this->ajaxReturn($this->jret);
    }

    protected function addLoginLog($params){
        $ulM = M('MemberLogin');
        $ulM->add($params);
    }
}
