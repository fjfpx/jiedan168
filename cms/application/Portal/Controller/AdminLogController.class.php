<?php
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
class AdminLogController extends AdminbaseController {
    protected $log_obj;

    function _initialize() {
        parent::_initialize();
        $this->log_obj = D("Common/Log");
    }

    public function index() { 
        $this->_lists();
        $this->display();
    }

    public function oindex(){
        $where_ands = array('l.utype'=>1);

        $fields=array(
                'realname' => array("field"=>"m.realname","operator"=>"like"),
                'username' => array("field"=>"m.username","operator"=>"="),
                'start_time' => array("field"=>"l.addtime","operator"=>">="),
                'end_time' => array("field"=>"l.addtime","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='start_time' || $param=='end_time'){
                        $get = strtotime($get);
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }else{
            $assign = 1;
            foreach ($fields as $param =>$val){
                if (isset($_GET[$param]) && $_GET[$param]!='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_GET[$param];
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $field = "l.*,m.username,m.realname";
        $count=$this->log_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->log_obj->alias("l")
            ->join($join)
            ->field($field)
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.addtime desc")->select();
        foreach($data['item'] as &$v){
            $v['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
        }

        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page",$page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        if($assign==1){
            $this->assign("formget",$_GET);
        }else{
            $this->assign("formget",$_POST);
        }
        $this->assign("item",$data);
        $this->display();
    }

    public function _lists(){
        $where_ands = array('l.utype'=>0);

        $fields=array(
                'realname' => array("field"=>"u.user_login","operator"=>"like"),
                'start_time' => array("field"=>"l.addtime","operator"=>">="),
                'end_time' => array("field"=>"l.addtime","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='start_time' || $param=='end_time'){
                        $get = strtotime($get);
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }else{
            $assign = 1;
            foreach ($fields as $param =>$val){
                if (isset($_GET[$param]) && $_GET[$param]!='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_GET[$param];
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __USERS__ u ON u.id=l.uid";
        $field = "l.*,u.user_login";
        $count=$this->log_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->log_obj->alias("l")
            ->join($join)
            ->field($field)
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
        }

        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page",$page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        if($assign==1){
            $this->assign("formget",$_GET);
        }else{
            $this->assign("formget",$_POST);
        }
        $this->assign("item",$data);
    }
}
