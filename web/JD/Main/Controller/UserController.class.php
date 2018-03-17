<?php
namespace Main\Controller;
use Think\Controller;
use Library\BaseController;
header("Content-type: text/html; charset=utf-8");
class UserController extends BaseController {

    //获取基本信息
    public function getUserInfo(){
        $rst = M("MemberBase")->alias('mb')
                ->join("LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=mb.uid")
                ->join("LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=mb.uid")
                ->join("LEFT JOIN __MEMBER_TAOBAO__ mt ON mt.uid=mb.uid")
                ->join("LEFT JOIN __MEMBER_ALIPAY__ ma ON ma.uid=mb.uid")
                ->where(array('mb.uid'=>$this->user_result['user_id']))
                ->field("mb.real_name,mb.idcard,mb.real_status,mb.info_status,mb.addressbook_status,mk.verify_status,mo.status as phone_status,mb.family_addr,mk.bankname,mk.card,mt.status as taobao_status,ma.status as alipay_status")
                ->find();
        $nm = strlen($rst['idcard'])-2;
        $star = '';
        for($i=1;$i<=$nm;$i++){
            $star .= "*";
        }
        $area = explode(' ',$rst['family_addr']);
        $userinfo = array(
            'user_id' => $this->user_result['user_id'],
            'is_real' => $rst['real_status'],
            'real_str'=>'**'.mb_substr($rst['real_name'],-1,1,'utf-8').'('.substr($rst['idcard'],0,1).$star.substr($rst['idcard'],-1).')',
            'real_name' => $rst['real_name'],
            'phone' => $this->user_result['phone'],
            'idcard' => $rst['idcard'],
            'is_info' => $rst['info_status'],
            'info_str' => $area[0],
            'addressbook_status' => $rst['addressbook_status'],
            'is_bank' => $rst['verify_status'],
            'bank_str' => $rst['bankname'].'/尾号'.substr($rst['card'],-4),
            'is_phone' => $rst['phone_status'],
            'phone_str' => substr($this->user_result['phone'],0,3).'****'.substr($this->user_result['phone'],-4),
            'is_taobao' => $rst['taobao_status'],
            'is_alipay' => $rst['alipay_status'],
            'utype' => $this->user_result['utype'],
        );
        $this->jret['flag']  = 1;
        $this->jret['result'] = $userinfo;
        $this->ajaxReturn($this->jret);
    }

    //添加反馈
    public function addFeedback(){
        if(!$this->user_result) $this->jerror("请先登录");
        $msg = trim($_REQUEST['msg']);
        if(!$msg || $msg==''){
            $this->jret['msg'] = '反馈内容不能为空';
        }else{
            $data = array(
                'uid' => $this->user_result['user_id'],
                'msg' => $msg,
                'addtime' => time(),
                'addip' => get_client_ip()
            );

            if( M("Feedback")->add($data) ){
                $this->jret['flag'] = 1;
                $this->jret['msg'] = '反馈提交成功';
            }else{
                $this->jret['msg'] = '反馈提交失败';
            }
        }
        $this->ajaxReturn($this->jret);
    }

    public function pwdReset(){
        foreach ($_REQUEST as $k => $v) {
            if(in_array($k, array('oldpwd','newpwd','newpwd2'))){
                $params[$k] = $v;
            }
        }
        if(empty($params['oldpwd'])||empty($params['newpwd'])){
            $this->jret['msg'] = '请传入完整参数';
        }elseif($params['newpwd']!=$params['newpwd2']){
            $this->jret['msg'] = '新密码两次不一致';
        }elseif(!sp_compare_password($params['oldpwd'],$this->user_result['password'])){
            $this->jret['msg'] = '账户密码错误';
        }elseif(sp_compare_password($params['oldpwd'],sp_password($params['newpwd']))){
            $this->jret['msg'] = '新旧密码不能相同';
        }else{
            $np = sp_password($params['newpwd']);
            $rst = D("Member")->where(array('user_id'=>$this->user_result['user_id']))->save(array('password'=>$np));
            if($rst!==false){
                $this->jret['flag'] = 1;
                $this->jret['msg'] = '修改成功';
            }else{
                $this->jret['msg'] = '修改失败';
            }
        }
        $this->ajaxreturn($this->jret);
    }
}

