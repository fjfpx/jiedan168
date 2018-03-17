<?php
namespace Main\Controller;
use Think\Controller;
use Library\network;
use Library\BaseController;
header("Content-type: text/html; charset=utf-8");
class OperatorController extends BaseController {
    //任务创建通知URL
    public function taskcreate(){
        header("HTTP/1.1 201 Created");
        $rst = json_decode(file_get_contents('php://input'),true);
        file_put_contents(C('PATH_LOG_MOXIE').'operator_create',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
                'phone' => $rst['mobile'],
                'idcard' => $rst['idcard'],
                'real_name' => $rst['name'],
            );

            if( D("Member")->checkUsers($data) ){
                $arr = array(
                    'submit_time' => substr($rst['timestamp'],0,10),
                    'task_id' => $rst['task_id']
                );
                if( M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->find() ){
                    //save
                    if( M("Moxie")->where(array('uid'=>$rst['user_id'],'task_id'=>$rst['task_id']))->save($arr)!==false ){
                        exit("success");
                    }
                }else{
                    //add
                    $arr['uid'] = $rst['user_id'];
                    if( M("Moxie")->add($arr) ){
                        exit("success");
                    }
                }
            }else{
                //fail
                exit("failed");
            }
        }else{
            exit("failed");
        }
    }

    //任务授权登录结果通知URL
    public function tasklogin(){
        header("HTTP/1.1 201 Created");
        $rst = json_decode(file_get_contents('php://input'),true);
        file_put_contents(C('PATH_LOG_MOXIE').'operator_login',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
                'phone' => $rst['mobile'],
            );
            if( D("Member")->checkUsers($data) ){
                $arr = array(
                    'login_time' => substr($rst['timestamp'],0,10),
                    'login_message' => $rst['message']
                );
                if($rst['result']){
                    //success
                    $arr['login_status'] = 1;
                }
                if( M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->find() ){
                    if(M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->save($arr) !== false){
                        if($arr['login_status']==1){
                            M("MemberOperator")->where(array('uid'=>$data['user_id']))->save(array('status'=>3));
                        }
                        exit("success");
                    }
                }
            }
        }
        exit("failed");
    }

    //账单通知URL
    public function taskbill(){
        header("HTTP/1.1 201 Created");
        $rst = json_decode(file_get_contents('php://input'),true);
        file_put_contents(C('PATH_LOG_MOXIE').'operator_bill',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
                'phone' => $rst['mobile'],
            );
            if( D("Member")->checkUsers($data) ){
                $bills = array(
                    'bills' => json_encode($rst['bills']),
                    'bills_time' => time()
                );
                if( M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->find() ){
                    if(M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->save($bills)){
                        exit("success");
                    }
                }
            }
        }
        exit("failed");
    }

    //任务采集失败通知URL
    public function taskfail(){
        header("HTTP/1.1 201 Created");
        $rst = json_decode(file_get_contents('php://input'),true);
        file_put_contents(C('PATH_LOG_MOXIE').'operator_fail',json_encode($rst).PHP_EOL, FILE_APPEND);               
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
                'phone' => $rst['mobile'],
            );
            if( D("Member")->checkUsers($data) ){
                $arr = array(
                    'fail_time' => substr($rst['timestamp'],0,10),
                    'fail_message' => $rst['message'],
                );
                if($rst['result']){
                    $arr['fail_status'] = 1;
                }
                if( M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->find() ){
                    if(M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->save($arr) !== false){
                        exit("success");
                    }
                }
            }
        }
        exit("failed");
    }

    //用户资信报告通知URL
    public function taskreport(){
        header("HTTP/1.1 201 Created");
        $rst = json_decode(file_get_contents('php://input'),true);
        file_put_contents(C('PATH_LOG_MOXIE').'operator_report',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
                'phone' => $rst['mobile'],
                'idcard' => $rst['idcard'],
                'real_name' => $rst['name'],
            );
            if( D("Member")->checkUsers($data) ){
                $arr = array(
                    'report_time' => substr($rst['timestamp'],0,10),
                    'report_message' => $rst['message'],
                );
                if($rst['result']){
                    $arr['report_status'] = 1;
                }
                if( M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->find() ){
                    if(M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->save($arr) !== false){
                        A("Cb")->getReportData();
                        exit("success");
                    }
                }
            }
        }
        exit("failed");
    }

}
