<?php
namespace Main\Controller;
use Think\Controller;
use Library\network;
use Library\BaseController;
header("Content-type: text/html; charset=utf-8");
class InformationController extends BaseController {

    //通讯录存放
    public function addressBook(){
        $lists = json_decode($_REQUEST['list'],true);
        $i = 0;
        if(!empty($lists) && is_array($lists)){
            M("MemberAddressbook")->where(array('uid'=>$this->user_result['user_id']))->delete();
            foreach($lists as $k=>$v){
                if( is_array($v['phoneNumbers']) && !empty($v['phoneNumbers']) && $v['fullName']!='' ){
                    foreach($v['phoneNumbers'] as $_v){
                        $data = array(
                            'uid' => $this->user_result['user_id'],
                            'name' => base64_encode($v['fullName']),
                            'phone' => str_replace(' ','',preg_replace('/-/','',$_v))
                        );
                        if( M("MemberAddressbook")->add($data) ){
                            $i++;
                        }
                    }
                }
            }
        }else{
            $this->jret['msg'] = '请传入通讯录';
        }
        if($i!=0){
            if(M("MemberBase")->where(array('uid'=>$this->user_result['user_id']))->save(array('addressbook_status'=>1))!==false){
                $this->jret['flag'] = 1;
                $this->jret['msg'] = '通讯录获取成功';
            }
        }
        $this->ajaxReturn($this->jret);
    }

    //身份证OCR验证
    public function checkIdcardPics(){
        $var = array('front_pic','behind_pic');
        foreach($var as $v){
            if(isset($_REQUEST[$v]) && $_REQUEST[$v]!=''){
                $data[$v] = trim($_REQUEST[$v]);
            }
        }
        Vendor('Youtu.ocr#class');
        $ocr = new \ocr();
        $rst = false;
        if(isset($data['front_pic']) && $data['front_pic']!=''){
            $rst = $ocr->driverlicenseocr($data['front_pic'],$this->user_result['user_id']);
        }elseif(isset($data['behind_pic']) && $data['behind_pic']!=''){
            if(M("MemberIdcardinfo")->where(array('uid'=>$this->user_result['user_id']))->find()){
                $rst = $ocr->driverlicenseocr($data['behind_pic'],$this->user_result['user_id'],1);
            }else{
                $this->jret['msg'] = '请先认证身份证正面照';
            }
        }else{
            $this->jret['msg'] = '身份证照不能为空,请先上传身份证照!';
        }
        file_put_contents(C('PATH_LOG_OCR'),time().json_encode($rst).PHP_EOL, FILE_APPEND);
        if($rst){
            $this->jret['flag'] = 1;
            $this->jret['result'] = $rst['data'];
        }
        $this->ajaxReturn($this->jret);
    }

    //获取银行卡信息
    public function checkbank(){
        $card = trim($_REQUEST['card']);
        if(isset($card) && $card!=''){
            $ck = M("Banks")->where(array('card'=>$card))->find();
            if(!$ck){
                $cb = new \Main\Lib\BankClass();
                $rst = json_decode($cb->getBankBase($card),true);
                file_put_contents(C('PATH_LOG_BANK'),time().json_encode($rst).PHP_EOL, FILE_APPEND);
                if($rst['error_code']==0 && $rst['reason']=='Succes'){
                    $data = $rst['result'];
                    $data['addtime'] = time();
                    $data['card'] = $card;

                    M("Banks")->add($data);

                    $rt = array(
                        'card' => $card,
                        'bankname' => $rst['result']['bankname'],
                        'logo' => $rst['result']['bankimage']
                    );
                    $this->jret['flag'] = 1;
                    $this->jret['result'] = $rt;
                }else{
                    $this->jret['msg'] = '未知银行卡,请重试或更换另一张银行卡';
                }
            }else{  
                $rt = array(
                    'card' => $card,
                    'bankname' => $ck['bankname'],
                    'logo' => $ck['bankimage']
                );
                $this->jret['flag'] = 1;
                $this->jret['result'] = $rt;
            }
        }else{
            $this->jret['msg'] = '银行卡不能为空';
        }
        $this->ajaxReturn($this->jret);
    }

    //银行卡四要素(银行卡信息提交)
    public function bank4element(){
        if(IS_POST){
            $accountNO = trim($_POST['card']);
            $bankPreMobile = trim($_POST['phone']);
            $bankname = trim($_POST['bankname']);
            //$branch = trim($_POST['branch']);
            if(isset($bankPreMobile) && $bankPreMobile!='' && isset($accountNO) && $accountNO!=''){
                $tx = new \Main\Lib\TxClass();
                $mb = M("MemberBase")->where(array('uid'=>$this->user_result['user_id']))->field("real_name,idcard")->find();
                $params = array(
                    'name' => $mb['real_name'],
                    'accountNO' => $accountNO,
                    'idCard' => $mb['idcard'],
                    'bankPreMobile' => $bankPreMobile
                );
                $rst = json_decode($tx->check4element($params),true);
                file_put_contents(C('PATH_LOG_BANK'),json_encode($params).'=>result=>'.json_encode($rst).PHP_EOL, FILE_APPEND);
                if($rst['data']['checkStatus']=='SAME' && $rst['success']==1){
                    $data = array(
                        'card' => $accountNO,
                        'bankname' => $bankname,
                        //'branch' => $branch,
                        'phone' => $bankPreMobile,
                        'verify_status' => 1
                    );
                    if( M("MemberBank")->where(array('uid'=>$this->user_result['user_id']))->save($data)!==false ){
                        $this->jret['flag'] = 1;
                    }
                    $this->jret['msg'] = $rst['data']['result'];
                }else{
                    $this->jret['msg'] = '银行卡鉴定失败';
                }
            }
        }
        $this->ajaxReturn($this->jret);
    }

    //身份证一致性
    public function idCheck(){
        $var = array('name','idcard','front_pic','behind_pic');
        foreach($var as $v){
            if(isset($_REQUEST[$v]) && $_REQUEST[$v]) $data[$v] = trim($_REQUEST[$v]);
        }
        if(!isset($data['name'])){
            $this->jret['msg'] = '姓名不能为空';
        }elseif(!isset($data['idcard'])){
            $this->jret['msg'] = '身份证号不能为空';
        }elseif(!isset($data['front_pic'])){
            $this->jret['msg'] = '身份证正面照不能为空';
        }elseif(!isset($data['behind_pic'])){
            $this->jret['msg'] = '身份证反面照不能为空';
        }else{
            $tx = new \Main\Lib\TxClass();
            $params = array(
                'name' => $data['name'],
                'idCard' => $data['idcard'],
            );
            $rst = json_decode($tx->idCardCheck($params),true);
            file_put_contents(C('PATH_LOG_IDCHECK'),time().json_encode($rst).PHP_EOL, FILE_APPEND);
            if($rst['data']['compareStatus']=='SAME' && $rst['success']=== true){
                $data['real_name'] = $data['name'];
                unset($data['name']);
                $data['real_status'] = 1;
                $data['addtime'] = time(); //开始验证时间
                if(M("MemberBase")->where(array('uid'=>$this->user_result['user_id']))->save($data)!==false){
                    $this->jret['flag'] = 1;
                }
                $this->jret['msg'] = $rst['data']['compareStatusDesc'];
            }else{
                $this->jret['msg'] = '验证失败,请重试';
            }
        }
        $this->ajaxReturn($this->jret);
    }

    //万象优图签名&鉴权
    public function faceSign(){
        $appid = "1253676832";
        $bucket = "cheheyan";
        $secret_id = "AKIDueBUYNUMUO3Ugc4rCJBpGSQ9fwyldgn";
        $secret_key = "nMgldHhsoVyDRgx795sY7TaS4RbWDgu3";
        $expired = time() + 2592000;
        $onceExpired = 0;
        $current = time();
        $rdm = rand();
        $userid = "0";
        $fileid = "tencentyunSignTest";

        //多次不绑定
        $srcStr = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$expired.'&t='.$current.'&r='.$rdm.'&u='.$userid.'&f=';

        //多次绑定
        $srcWithFile = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$expired.'&t='.$current.'&r='.$rdm.'&u='.$userid.'&f='.$fileid;

        //单次
        $srcStrOnce= 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$onceExpired .'&t='.$current.'&r='.$rdm.'&u='.$userid.'&f='.$fileid;

        //签名 
        $signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true).$srcStr);

        $srcWithFile = base64_encode(hash_hmac('SHA1', $srcWithFile , $secret_key, true).$srcWithFile );

        $signStrOnce = base64_encode(hash_hmac('SHA1',$srcStrOnce,$secret_key, true).$srcStrOnce);

        $this->jret['result'] = array(
            'signStr' => $signStr,
            'srcWithFile' => $srcWithFile,
            'signStrOnce' => $signStrOnce
        );
        $this->jret['flag'] = 1;
        $this->ajaxReturn($this->jret);
    }

    //获取关系列表
    public function getRelationship(){
        $list = array(
            'immediate' => array(
                array('id'=>1,'name'=>'父母'),
                array('id'=>2,'name'=>'兄妹'),
                array('id'=>3,'name'=>'配偶'),
                array('id'=>4,'name'=>'儿女')
            ),
            'others' => array(
                array('id'=>1,'name'=>'父母'),
                array('id'=>2,'name'=>'兄妹'),
                array('id'=>3,'name'=>'配偶'),
                array('id'=>4,'name'=>'儿女'),
                array('id'=>5,'name'=>'朋友'),
                array('id'=>6,'name'=>'同事')
            )
        );

        $this->jret['flag'] = 1;
        $this->jret['result'] = $list;
        $this->ajaxReturn($this->jret);
    }

    //提交个人资料
    public function infoPost(){
        $var = array('area','family_addr','immediate_ship','immediate_name','immediate_phone','others_ship','others_name','others_phone','company','company_tel','company_addr');

        foreach($var as $v){
            if(isset($_REQUEST[$v]) && $_REQUEST[$v]!='') $data[$v] = $_REQUEST[$v];
        }

        if(!isset($data['area'])){
            $this->jret['msg'] = '请输入省市信息';
        }elseif(!isset($data['family_addr'])){
            $this->jret['msg'] = '请输入居住详细地址';
        }elseif(!isset($data['immediate_name'])){
            $this->jret['msg'] = '请输入紧急联系人1姓名';
        }elseif(!isset($data['immediate_phone'])){
            $this->jret['msg'] = '请输入紧急联系人1电话';
        }elseif(!preg_match("/^1[34578]{1}\d{9}$/",$data['immediate_phone'])){
            $this->jret['msg'] = '请输入正确的联系人1手机号';
        }elseif(!isset($data['others_name'])){
            $this->jret['msg'] = '请输入紧急联系人2姓名';
        }elseif(!isset($data['others_phone'])){
            $this->jret['msg'] = '请输入紧急联系人2电话';
        }elseif(!preg_match("/^1[34578]{1}\d{9}$/",$data['others_phone'])){
            $this->jret['msg'] = '请输入正确的联系人2手机号';
        }else{
            if( M("MemberAddressbook")->where(array('uid'=>$this->user_result['user_id']))->select() ){
                M("MemberBase")->where(array('uid'=>$this->user_result['user_id']))->save(array('addressbook_status'=>1));
            }
            if( M("MemberBase")->where(array('uid'=>$this->user_result['user_id'],'addressbook_status'=>1))->find() ){
                $immediate = array('1' => '父母','2' => '兄妹','3' => '配偶','4' => '儿女');
                $others = array('1' => '父母','2' => '兄妹','3' => '配偶','4' => '儿女','5' => '朋友','6' => '同事');

                $params = array(
                    'family_addr' => $data['area'].' '.$data['family_addr'],
                    'contact' => json_encode(array(
                                    'immediate'=>array($data['immediate_ship'],$immediate[$data['immediate_ship']],$data['immediate_name'],$data['immediate_phone']),
                                    'others'=>array($data['others_ship'],$others[$data['others_ship']],$data['others_name'],$data['others_phone']),
                                )),
                    'company' => $data['company']?$data['company']:'',
                    'company_tel' => $data['company_tel']?$data['company_tel']:'',
                    'company_addr' => $data['company_addr']?$data['company_addr']:'',
                    'info_status' => 1,
                    'addip' => network\NetTool::ip_address(),
                );
                $gaode = new \Main\Lib\GaodeClass();
                $ips = json_decode($gaode->getIpAddress(network\NetTool::ip_address())); //ip所属
                if($ips->status == 1){
                    $params['ip_addr'] = $ips->province.$ips->city;
                }

                $rst = M("MemberBase")->where(array('uid'=>$this->user_result['user_id']))->save($params);

                if($rst!==false){
                    $this->jret['flag'] = 1;
                    $this->jret['msg'] = '认证成功';
                }else{
                    $this->jret['msg'] = '认证失败';
                }
            }else{
                $this->jret['msg'] = '请先授权通讯录';
            }
        }
        $this->ajaxReturn($this->jret);
    }
}

