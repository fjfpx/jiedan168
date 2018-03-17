<?php
namespace Main\Controller;
use Think\Controller;
use Library\network;
use Library\BaseController;
header("Content-type: text/html; charset=utf-8");
class MessageController extends BaseController {

    public function msgLists(){
        if(!$this->user_result) $this->jerror("请先登录");
        $var = array("type","epage","p");
        foreach($var as $v){
            if(isset($_REQUEST[$v]) && $_REQUEST[$v]!=''){
                $data[$v] = trim($_REQUEST[$v]);
            }
        }
        $data['uid'] = $this->user_result['user_id'];
        $lists = D("Message")->getMsgList($data);

        $this->jret['flag'] = 1;
        $this->jret['result'] = $lists;
        $this->ajaxReturn($this->jret);
    }

    public function setRead(){
        if(!$this->user_result) $this->jerror("请先登录");
        $ids = $_REQUEST['ids'];

        if(is_array($ids) && !empty($ids)){
            $ids = join(',',$ids);
            $where = array(
                'uid' => $this->user_result['user_id'],
                'mid' => array('in',$ids)
            );
            $res = M("MessageUsers")->where($where)->save(array('status'=>1));
            if($res!==false){
                $this->jret['flag'] = 1;
            }else{
                $this->jret['msg'] = '消息设置已读失败';
            }
        }else{
            $this->jret['msg'] = 'id列表不能为空';
        }
        $this->ajaxReturn($this->jret);
    }

    public function delMsg(){
        if(!$this->user_result) $this->jerror("请先登录");
        $ids = $_REQUEST['ids'];
        if(is_array($ids) && !empty($ids)){
            $ids = join(',',$ids);
            $where = array(
               'uid' => $this->user_result['user_id'],
               'mid' => array('in',$ids)
            );
            $res = M("MessageUsers")->where($where)->save(array('delete_status'=>1));
            if($res!==false){
                $condition = array(
                    'id' => array('in',$ids),
                    'type' => 0,
                );
                M("Message")->where($condition)->save(array('delete_status'=>1));

                $this->jret['flag'] = 1;
            }else{
                $this->jret['msg'] = '消息删除失败';
            }
        }else{
            $this->jret['msg'] = 'id列表不能为空';
        }

        $this->ajaxReturn($this->jret);
    }
}
