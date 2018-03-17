<?php
namespace Main\Controller;
use Think\Controller;
use Library\network;
use Library\BaseController;
header("Content-type: text/html; charset=utf-8");
class TaobaoController extends BaseController {

    //任务创建通知URL
    public function taskcreate(){
        header("HTTP/1.1 201 Created");
        $rst = json_decode(file_get_contents('php://input'),true);
        file_put_contents(C('PATH_LOG_MOXIE').'taobao_create',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
            );

            if( D("Member")->where($data)->find() ){
                $arr = array(
                    'submit_time' => time(),
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
                    $arr['type'] = 1;
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
        file_put_contents(C('PATH_LOG_MOXIE').'taobao_login',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
            );
            if( D("Member")->where($data)->find() ){
                $arr = array(
                    'login_time' => time(),
                    'login_message' => $rst['message']
                );
                if($rst['result']){
                    //success
                    $arr['login_status'] = 1;
                }
                if( M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->find() ){
                    if(M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->save($arr) !== false){
                        if($arr['login_status']==1){
                            M("MemberTaobao")->where(array('uid'=>$data['user_id']))->save(array('status'=>3));
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
        file_put_contents(C('PATH_LOG_MOXIE').'taobao_bill',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
            );
            if( D("Member")->where($data)->find() ){
                $bills = array(
                    'bills' => json_encode(
                                array(
                                    'account' => $rst['account'],
                                    'mapping_id' => $rst['mapping_id']
                                )
                            ),
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
        file_put_contents(C('PATH_LOG_MOXIE').'taobao_fail',json_encode($rst).PHP_EOL, FILE_APPEND);               
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
            );
            if( D("Member")->where($data)->find() ){
                $arr = array(
                    'fail_time' => time(),
                    'fail_message' => $rst['message']?$rst['message']:'',
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
        file_put_contents(C('PATH_LOG_MOXIE').'taobao_report',json_encode($rst).PHP_EOL, FILE_APPEND);
        if(!empty($rst) && is_array($rst)){
            $data = array(
                'user_id' => $rst['user_id'],
            );
            if( D("Member")->where($data)->find() ){
                $arr = array(
                    'report_time' => time(),
                    'report_message' => $rst['message'],
                );
                if($rst['result']){
                    $arr['report_status'] = 1;
                }
                if( M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->find() ){
                    if(M("Moxie")->where(array('uid'=>$data['user_id'],'task_id'=>$rst['task_id']))->save($arr) !== false){
                        A("Cb")->getTaobaoData();
                        exit("success");
                    }
                }
            }
        }
        exit("failed");
    }

}
