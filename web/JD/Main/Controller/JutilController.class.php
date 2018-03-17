<?php
namespace Main\Controller;
use Think\Controller;
use Library\BaseController;
class JutilController extends BaseController {

    public function logout(){
        if(!$this->user_result){
            $jret['msg'] = 'nologin';
            $this->ajaxReturn($jret);
        }

        unset($_SESSION[$this->getUserSessionKey()]);
        unset($_SESSION['login_endtime']);
        session_unset();
        session_destroy();

        $uM = M('Member');
        $data = array(
                'app_token' => '',
                'app_devid' => '',
                'app_devtype' => '',
                'app_systype' => '',
                'app_sysversion' => '',
                'token_timeout' => ''
                );
        $uM->where(array('user_id'=>$this->user_result['user_id']) )->save($data);
        if($this->cache->get($this->user_result['app_token'])){
            $this->cache->rm($this->user_result['app_token']);
        }
        $this->user_result = null;

        $jret['flag'] = 1;
        $this->ajaxreturn($jret);
    }

    public function swfupload(){
        if(IS_POST){
            $savepath=date('Ymd').'/';
            //上传处理类
            $config=array(
                    'rootPath' => './'.C("UPLOADPATH"),
                    'savePath' => $savepath,
                    'maxSize' => 5242880, //最大5MB
                    'saveName'   =>    array('uniqid',''),
                    'exts'       =>    array('jpg', 'png', 'jpeg'/*,'gif',"txt",'zip'*/),
                    'autoSub'    =>    false,
                    );

            $upload = new \Think\Upload($config);
            $info=$upload->upload();
            //开始上传
            if($info){
                //上传成功
                //写入附件数据库信息
                $first=array_shift($info);
                if(!empty($first['url'])){
                    $url=$first['url'];
                }else{
                    $url=C('HTTP_SERVER').'/'.C("UPLOADPATH").$savepath.$first['savename'];
                }

                $this->jret['flag'] = 1;
                $this->jret['result']['url'] = $url;
            }else{
                //上传失败，返回错误
                $this->jret['msg'] = $upload->getError();
            }
        }else{
            $this->jret['msg'] = "非法操作";
        }
        unset($this->jret['loginstatus']);
        $this->ajaxReturn($this->jret);
    }

    /**
     * 检测上传目录
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    protected function checkSavePath($savepath){
        /* 检测并创建目录 */
        if (!self::mkdir($savepath)) {
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($savepath)) {
                return false;
            } else {
                return true;
            }
        }
    }

    protected function mkdir($savepath){
        $dir = $savepath;
        if(is_dir($dir)){
            return true;
        }

        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            return false;
        }
    }

    public function sendSms(){
        if($this->base_params['devicetype']==0){
            $this->jret['msg'] = '参数有误,请重试';
            $this->ajaxReturn($this->jret);
        }
        if(IS_POST){
            $phone = $_POST['phone'];
            $beta =  $_POST['beta'];
            if(!isset($phone) || $phone==''){
                $this->jret['msg'] = '手机号不能为空';
            }elseif($beta==1 && !D("Member")->getOne(array('phone'=>$phone)) ){
                $this->jret['msg'] = '您的手机尚未注册,请先注册';
            }else{
                $ob = new \Main\Lib\SmsPost();
                $ret = $ob->sendSms($phone,$beta);
                if($this->cache->get($phone.'reg_valicode')) $this->cache->rm($phone.'reg_valicode');
                if($this->cache->get($phone.'chg_valicode')) $this->cache->rm($phone.'chg_valicode');
                if($ret['status']==0){
                    if($beta==1){
                        $this->cache->set($phone.'chg_valicode',$ret['code'],60*10); //10 minutes
                    }else{
                        $this->cache->set($phone.'reg_valicode',$ret['code'],60*10); //10 minutes
                    }
                    $this->jret['flag'] = 1;
                }
                $this->jret['msg'] = $ret['msg'];
            }
        }else{
            $this->jret['msg'] = '请求失败';
        }
        unset($this->jret['loginstatus']);
        $this->ajaxReturn($this->jret);
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

