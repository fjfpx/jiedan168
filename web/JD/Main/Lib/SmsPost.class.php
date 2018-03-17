<?php
namespace Main\Lib;
use Main\Lib\UserClass;
use Library\network;
class SmsPost{
    /*
    *-------------------------------------------------------------------------------------------------------------------------
    * 【车核验】您的验证码是#code#，有效期为10分钟，请尽快使用验证登录。
    * 【车核验】您正在修改手机绑定，验证码是#code#，如有问题请联系客服。
    * 【车核验】尊敬的用户：您购买的#string#的维修保养记录已生成，请在车核验APP或微信上及时查看。
    * 【车核验】尊敬的用户：您购买的#string#的维修保养记录生成失败，订单金额已经退回到您的账户，请在车核验APP或微信上及时查看。
    * 【车核验】尊敬的用户：您购买的#string#的查询无维修保养记录，订单金额已经退回到您的账户，请在车核验APP或微信上及时查看。
    *-------------------------------------------------------------------------------------------------------------------------
    */
    
	public function sendSms($phone,$beta){
		$jret = array(
            'phone' => $phone,
            'status' => 1, 
            'msg'    => ""
        );      
        if( !preg_match("/^1[34578]{1}\d{9}$/", $phone) ){
            $jret['msg'] = "手机号码不正确";
        }else{ 
            //an hour is more than three times
            $result = self::getPhoneValid($phone);
            if($result['success']==0){
                $jret['msg'] = $result['msg'];
            }else{
                $msg = rand(100000,999999);
                $smsg="您的手机验证码为:".$msg;
                $_msg="【利丹科技】您的验证码是".$msg."，请于10分钟内填写。如非本人操作，请忽略本短信。";
                if($beta==1){
                    $_msg = "【利丹科技】你正在使用找回登陆密码，验证码".$msg.",如非本人操作，请忽略。";
                }
                Vendor('Qcloudcms.txsms#class');
                $client = new \txsms();
                $sms_jret = $client->send_one(array('msg'=>$_msg,'phone'=>$phone));
                $sms_jret = json_decode($sms_jret,true);
                $timestamp = date("Y-m-d H:i:s");
                logging("$timestamp: $phone: $smsg: ".$sms_jret['result'], 'PATH_LOG_SMS');
                if($sms_jret['result'] == 0){
                    $jret['status'] = 0;
                    $jret['code'] = $msg;
                    $jret['msg'] = "短信发送成功，请注意查收";
                }else{
                    $jret['msg'] = "短信发送失败";
                }
            }
        }
        return $jret;
	}

    public function sendTplSms($data){
        $jret = array(
            'phone' => $data['phone'],
            'status' => 1,
            'msg'    => ""
            );
        if( !preg_match("/^1[34578]{1}\d{9}$/", $data['phone']) ){
            $jret['msg'] = "手机号码不正确";
        }elseif(!isset($data['msg']) || $data['msg'] ==''){
            $jret['msg'] = '发送内容有误';
        }else{
            $msg = $data['msg'];
            $phone = $data['phone'];

            Vendor('Qcloudcms.txsms#class');
            $client = new \txsms();
            $sms_jret = $client->send_one(array('msg'=>$_msg,'phone'=>$phone));
            $sms_jret = json_decode($sms_jret,true);
            $timestamp = date("Y-m-d H:i:s");
            logging("$timestamp: $phone: $smsg: ".$sms_jret['result'], 'PATH_LOG_SMS');

            if($sms_jret['result'] == 0){
                $jret['status'] = 0;
                $jret['msg'] = "短信发送成功，请注意查收";
            }else{
                $jret['msg'] = "短信发送失败";
            }
        }
        return $jret;
    }

    protected static function getPhoneValid($phone){
        $ret = array('success'=>0, 'msg'=>'操作错误，请联系客');
        $smsValid = M("Smsvalid");

        //请求超过50次, 直接封
        $ip = network\NetTool::ip_address();
        $time = strtotime( date("Y-m-d")." 00:00:00" );
        $_time = strtotime( date("Y-m-d")." 23:59:59" );
        $con = array(
            'msg' => $ip,
            'addtime' => array('between',"$time,$_time")
        );
        $ipcount = $smsValid->where($con)->count();
        if($ipcount>=5){
            $ret['msg'] = '同一IP每天只能请求5次';
            return $ret;
        }

        $sql_3600 = time()-3600/2;
        $condition['phone'] = $phone;
        $condition['addtime'] =array('gt',$sql_3600);
        $res = $smsValid->where($condition)->count();
        if($res >=5){
            $ret['msg'] = "请求过于频繁，请于半个小时后重试";
        }else{
            $data['phone'] = $phone;
            $data['addtime'] = time();
            $data['msg'] = $ip;
            $mark = $smsValid->add($data);
            if($mark){
                $ret['success'] = 1;
                $ret['msg'] = '发送成功';
            }
        } 
        return $ret;
    }
 
    //验证手机验证码是否输入正确
    public static function smsCaptcha($captcha,$phone,$beta){
        $jret = array('status'=>1, 'msg'=>'验证码错误');
        if($beta === 0){
            if($captcha == $_SESSION[$phone."reg_valicode"]){
                $jret = array('status'=>0, 'msg'=>'验证码正确');
            }
        }else{
            if($captcha == $_SESSION[$phone."rst_valicode"]){
                $jret = array('status'=>0, 'msg'=>'验证码正确');
            }
        }
        return $jret;
       
    }

}
