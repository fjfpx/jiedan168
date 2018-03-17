<?php
namespace Library;
abstract class JBaseController extends BaseController{

    protected function jerror($msg, $st=-1){
        parent::jerror();
        $data = array(
            'status' => $st,
            'loginstatus' => empty($this->user_result)?-4:0,
            'msg' => $msg
        );
        if(C('LOG_LEVEL')>=4)logging( CONTROLLER_NAME."/".ACTION_NAME.json_encode($data), 'PATH_LOG_ERROR');
        if($data['status'] == -2){
            //error api
            header( "HTTP/1.0 404 Not Found" );
            $this->display('Public:404');
            die();
        }elseif($data['status'] == -3){
            //end of the activity, jump to the index
            header("location:/index");
            exit;
        }else{
            if($data['loginstatus'] == 0){
                header("location:/juser");
            }else{
                $this->display('Uc:login');
                die(); 
            }
        }
    }
}
