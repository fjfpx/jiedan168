<?php
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
class AdminLoanController extends AdminbaseController {
    protected $product_obj;
    protected $user_result;

    function _initialize() {
        parent::_initialize();
        $this->loan_obj = D("Common/Loan");
        $this->user_result = $this->get("admin");
    }
  
    public function detail(){
        $id = trim(I('id'));
        $op = I("op");
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __USERS__ u ON u.id=l.adduid")
                ->join("LEFT JOIN __LOAN_REPAY__ lr ON lr.lid=l.id")
                ->where(array('l.id'=>$id,'l.verify_status'=>1))
                ->field("l.*,mb.real_name,m.phone,u.user_login,lr.repay_uid,lr.repay_money,lr.repay_type,lr.repay_time,lr.repay_trade_no,lr.repay_remark")
                ->find();
            if(!$ck){
                $this->error("贷款不存在或已审核");
            }else{
                $verify_user = M("Users")->where(array('id'=>$ck['verify_uid']))->field('user_login')->find();
                $ck['verify_user'] = $verify_user['user_login'];
                $pay_user = M("Users")->where(array('id'=>$ck['payuid']))->field('user_login')->find();
                $ck['pay_user'] = $pay_user['user_login'];
                $ck['paytime'] = date("Y-m-d H:i",$ck['paytime']);
                if($ck['status']==3 || $ck['status']==5 || $ck['status']==6){
                    $repay_user = M("Users")->where(array('id'=>$ck['repay_uid']))->field('user_login')->find();
                    $ck['repay_user'] = $repay_user['user_login'];
                    $ck['repay_time'] = date('Y-m-d H:i',$ck['repay_time']);

                    if($ck['verify_verify_uid']){
                        $repay_verify_user=M("Users")->where(array('id'=>$ck['verify_verify_uid']))->field('user_login')->find();
                        $ck['repay_verify_user'] = $repay_verify_user['user_login'];
                        $ck['repay_verify_time'] = $ck['repay_verify_time']?date("Y-m-d H:i",$ck['verify_verify_time']):'';
                    }
                    
                }
                $ck['borrowing_time'] = date("Y-m-d H:i",$ck['borrowing_time']);
                $ck['borrowing_end_time'] = date("Y-m-d",$ck['borrowing_end_time']);
                $ck['addtime'] = date("Y-m-d H:i",$ck['addtime']);
                $ck['verify_time'] = date("Y-m-d H:i",$ck['verify_time']);

                $this->assign("verify",$ck);
                $this->assign('op',$op);
                $this->display();
            }
        }else{
            $this->error("参数id不能为空");
        }
    }

    public function credit(){
        $id = trim(I('id'));
        $op = I("op");
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_TAOBAO__ mt ON mt.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_ALIPAY__ ma ON ma.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_CREDIT__ mc ON mc.uid=l.uid")
                ->where(array('l.id'=>$id,'l.verify_status'=>1,'l.status'=>array('not in','0')))
                ->field("l.*,mb.real_name,m.phone,mb.idcard,mb.family_addr,mb.addip as mbaddip,mb.ip_addr,mb.company,mb.company_tel,mb.company_addr,mb.front_pic,mb.behind_pic,mb.real_pic,mb.real_status,mb.contact,mk.card,mk.bankname,mk.branch,mk.phone as mkphone,mk.verify_status as mkverify_status,mb.remark,mo.status as phone_status,mt.status as taobao_status,ma.status as alipay_status,m.user_id,mc.discredit,mc.is_dis,mc.dis_status,mc.negative,mc.is_neg,mc.neg_status")
                ->find();
            if(!$ck){
                $this->error("贷款不存在或已审核");
            }else{
                $where_ands = array('l.uid'=>$ck['uid']);
                $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
                $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
                $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
                $join .= " LEFT JOIN __LOAN_REPAY__ lr ON lr.lid=l.id";
                $items=$this->loan_obj->alias("l")
                    ->join($join)
                    ->where($where_ands)
                    ->field("m.phone,mb.real_name,l.paytime,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.status,lr.repay_time,l.id,l.uid,l.verify_status")
                    ->limit($page->firstRow . ',' . $page->listRows)
                    ->order("l.borrowing_time desc,l.addtime desc")->select();

                foreach($items as &$v){
                    $v['paytime'] = $v['paytime']?date("Y-m-d H:i",$v['paytime']):'';
                    $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
                    $v['repay_time'] = $v['repay_time']?date("Y-m-d H:i",$v['repay_time']):'';
                }
                $ck['history'] = $items;
                $ck['borrowing_time'] = date("Y-m-d",$ck['borrowing_time']);
                $ck['borrowing_end_time'] = date("Y-m-d",$ck['borrowing_end_time']);
                $ck['addtime'] = date("Y-m-d",$ck['addtime']);
                $ck['contact'] = isset($ck['contact'])?json_decode($ck['contact'],true):array();
                $ck['remark'] = isset($ck['remark'])?json_decode($ck['remark'],true):array();

                $book = M("member_addressbook")->where(array('uid'=>$ck['uid']))->select();
                foreach($book as &$v){
                    $v['name'] = base64_decode($v['name'],true);
                }
                $ck['book'] = $book;
                $ck['book_nums'] = count($book);
                $ck['discredit'] = json_decode($ck['discredit'],true);
                foreach($ck['discredit'] as &$v){
                    foreach($v as &$_v){
                        $_v['postTime'] = date("Y-m-d",substr($_v['postTime'],0,10));
                        $_v['recordTime'] = date("Y-m-d",substr($_v['recordTime'],0,10));
                    }
                }
                $ck['negative'] = json_decode($ck['negative'],true);
                $this->assign("verify",$ck);
                $this->assign("id",$id);
                $this->assign('op',$op);
                $this->display();
            }
        }else{
            $this->error("参数id不能为空");
        }
    }

    public function index() { 
        $this->_lists();
        $this->display();
    }

    public function _lists(){
        $where_ands = array('l.verify_status'=>1,'l.status'=>array('in','1,2,3,4,5'));

        $fields=array(
                'id' => array("field"=>"l.id","operator"=>"="),
                'uid' => array("field"=>"l.uid","operator"=>"="),
                'real_name' => array("field"=>"mb.real_name","operator"=>"like"),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'status' => array("field"=>"l.status","operator"=>"="),
                'user_login' => array("field"=>"u.user_login","operator"=>"like"),
                'ustart_time' => array("field"=>"l.addtime","operator"=>">="),
                'uend_time' => array("field"=>"l.addtime","operator"=>"<="),
                'start_time' => array("field"=>"l.borrowing_end_time","operator"=>">="),
                'end_time' => array("field"=>"l.borrowing_end_time","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
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
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
        $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
        $count=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->field("m.user_id,mb.real_name,m.phone,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.status,u.user_login,l.verify_uid,l.borrowing_time,l.verify_status,l.paytime,l.id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.borrowing_end_time asc,l.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['paytime'] = $v['paytime']?date("Y-m-d H:i",$v['paytime']):'';
            $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
            if($v['verify_uid']!=0){
                $vu = M("Users")->where(array('id'=>$v['verify_uid']))->field("user_login")->find();
                $v['verify_user'] = $vu['user_login'];
            }
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

    public function toverify(){
        $where_ands = array('l.verify_status'=>0,'l.status'=>0);

        $fields=array(
                'id' => array("field"=>"l.id","operator"=>"="),
                'uid' => array("field"=>"l.uid","operator"=>"="),
                'real_name' => array("field"=>"mb.real_name","operator"=>"like"),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'user_login' => array("field"=>"u.user_login","operator"=>"like"),
                'ustart_time' => array("field"=>"l.addtime","operator"=>">="),
                'uend_time' => array("field"=>"l.addtime","operator"=>"<="),
                'start_time' => array("field"=>"l.borrowing_end_time","operator"=>">="),
                'end_time' => array("field"=>"l.borrowing_end_time","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
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
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
        $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
        $count=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->field("m.user_id,mb.real_name,m.phone,l.addtime,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.status,u.user_login,l.borrowing_time,l.id,l.verify_status")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.borrowing_time desc,l.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['addtime'] = $v['addtime']?date("Y-m-d",$v['addtime']):'';
            $v['borrowing_time'] = $v['borrowing_time']?date("Y-m-d H:i",$v['borrowing_time']):'';
            $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
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

    public function refused(){
        $where_ands = array('l.verify_status'=>2);
        $fields=array(
                'id' => array("field"=>"l.id","operator"=>"="),
                'uid' => array("field"=>"l.uid","operator"=>"="),
                'real_name' => array("field"=>"mb.real_name","operator"=>"like"),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'user_login' => array("field"=>"u.user_login","operator"=>"like"),
                'ustart_time' => array("field"=>"l.addtime","operator"=>">="),
                'uend_time' => array("field"=>"l.addtime","operator"=>"<="),
                'start_time' => array("field"=>"l.borrowing_end_time","operator"=>">="),
                'end_time' => array("field"=>"l.borrowing_end_time","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
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
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
        $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
        $count=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->field("m.user_id,mb.real_name,m.phone,l.addtime,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.status,u.user_login,l.borrowing_time,l.id,l.verify_status,l.refused_msg")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.borrowing_time desc,l.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['addtime'] = $v['addtime']?date("Y-m-d",$v['addtime']):'';
            $v['borrowing_time'] = $v['borrowing_time']?date("Y-m-d H:i",$v['borrowing_time']):'';
            $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
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

    public function topay(){
		
		
	//	
//	$verify_user = M("moxie")->where(array('datainfo'=>''))->select();
//	
//	//print_r($verify_user);
//	
//	
//	//exit;
//  foreach($verify_user as &$v){
// 
// 
//	
//	
//	 $url = 'https://tenant.51datakey.com/carrier/report_data?data='.$v['report_message'];
//                    $html = file_get_contents($url);
//					
//                    $info = preg_replace('/style=" float: right ;margin-top: 6px ;margin-right: 5px"/i','style="display:none"',$html);
//					
//                    $report = array(
//                        'datainfo' => base64_encode($info),
//                        'id' => $v['id']
//                    );
//					
//                    M("Moxie")->save($report);
//					
//	
//  }
//  
//  exit;
	
	
	
		
        $where_ands = array('l.verify_status'=>1,'l.status'=>0);

        $fields=array(
                'id' => array("field"=>"l.id","operator"=>"="),
                //'uid' => array("field"=>"l.uid","operator"=>"="),
                //'real_name' => array("field"=>"mb.real_name","operator"=>"like"),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'user_login' => array("field"=>"u.user_login","operator"=>"like"),
                //'ustart_time' => array("field"=>"l.addtime","operator"=>">="),
                //'uend_time' => array("field"=>"l.addtime","operator"=>"<="),
                //'start_time' => array("field"=>"l.borrowing_end_time","operator"=>">="),
                //'end_time' => array("field"=>"l.borrowing_end_time","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
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
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
        $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
        $count=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->field("m.user_id,mb.real_name,m.phone,l.borrowing_money,l.borrowing_days,l.status,u.user_login,l.borrowing_time,l.id,l.verify_status,l.verify_uid,l.verify_time,l.borrowing_actual_money")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.verify_time desc")->select();

        foreach($data['item'] as &$v){
            $v['verify_time'] = $v['verify_time']?date("Y-m-d H:i",$v['verify_time']):'';
            $v['borrowing_time'] = $v['borrowing_time']?date("Y-m-d H:i",$v['borrowing_time']):'';
            if($v['verify_uid'] && $v['verify_uid']!=0 ){
                $vu = M("Users")->where(array('id'=>$v['verify_uid']))->field("user_login")->find();
                if($vu){
                    $v['verify_user'] = $vu['user_login'];
                }
            }
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

    public function expired(){
        $where_ands = array('l.verify_status'=>1,'l.status'=>2);

        $fields=array(
                'id' => array("field"=>"l.id","operator"=>"="),
                'uid' => array("field"=>"l.uid","operator"=>"="),
                'real_name' => array("field"=>"mb.real_name","operator"=>"like"),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'start_time' => array("field"=>"l.borrowing_end_time","operator"=>">="),
                'end_time' => array("field"=>"l.borrowing_end_time","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='end_time'){
                        $get = strtotime($get." 23:59:59");
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
                    if($param=='start_time'){
                        $get = strtotime($get." 00:00:00");                         
                    }elseif($param=='end_time'){
                        $get = strtotime($get." 23:59:59");
                    } 
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
        $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
        $count=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->field("m.user_id,mb.real_name,m.phone,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.verify_uid,l.borrowing_time,l.paytime,l.status,u.user_login,l.id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.borrowing_time desc,l.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['ex_time'] = abs(ceil((time()-$v['borrowing_end_time'])/(3600*24)));
            $v['paytime'] = $v['paytime']?date("Y-m-d H:i",$v['paytime']):'';
            $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
            if($v['verify_uid']!=0){
                $vu = M("Users")->where(array('id'=>$v['verify_uid']))->field("user_login")->find();
                $v['verify_user'] = $vu['user_login'];
            }
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

    public function overdue(){
        $where_ands = array('l.verify_status'=>1,'l.status'=>4);

        $fields=array(
                'id' => array("field"=>"l.id","operator"=>"="),
                'uid' => array("field"=>"l.uid","operator"=>"="),
                'real_name' => array("field"=>"mb.real_name","operator"=>"like"),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'start_time' => array("field"=>"l.borrowing_end_time","operator"=>">="),
                'end_time' => array("field"=>"l.borrowing_end_time","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='end_time'){
                        $get = strtotime($get." 23:59:59");
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
                    if($param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='end_time'){
                        $get = strtotime($get." 23:59:59");
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
        $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
        $count=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->field("m.user_id,mb.real_name,m.phone,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.verify_uid,l.borrowing_time,l.overdue,l.paytime,u.user_login,l.status,l.id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.borrowing_time desc,l.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['paytime'] = $v['paytime']?date("Y-m-d H:i",$v['paytime']):'';
            $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
            if($v['verify_uid']!=0){
                $vu = M("Users")->where(array('id'=>$v['verify_uid']))->field("user_login")->find();
                $v['verify_user'] = $vu['user_login'];
            }
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

    public function add(){
        $this->display();
    }

    public function add_post(){
        if(IS_POST){
            $var = array('uid','contract','borrowing_time','borrowing_days','borrowing_money','borrowing_poundage','borrowing_actual_money','borrowing_rate','borrowing_end_time','borrowing_repay');
            foreach($var as $v){
                if($_POST[$v] && $_POST[$v]!=''){
                    $data[$v] = trim($_POST[$v]);
                }
            }

            if( !$data['uid'] || $data['uid']=='' || $data['uid']==0 || !is_numeric($data['uid']) ){
                $this->error("借款人ID不能为空");
            }elseif( !$data['borrowing_money'] || $data['borrowing_money']=='' || $data['borrowing_money']==0 || !is_numeric($data['borrowing_money']) ){
                $this->error("借款金额不能为空或格式不正确");
            }elseif( !$data['borrowing_actual_money'] || $data['borrowing_actual_money']=='' || $data['borrowing_actual_money']==0 || !is_numeric($data['borrowing_actual_money'])){
                $this->error("实际到账金额不能为空或格式不正确");
            }elseif( !$data['borrowing_repay'] || $data['borrowing_repay']=='' || $data['borrowing_repay']==0 || !is_numeric($data['borrowing_repay']) ){
                $this->error("到期还款金额不能为空或格式不正确");
            }elseif( !$data['borrowing_poundage'] || $data['borrowing_poundage']=='' ||  !is_numeric($data['borrowing_poundage']) ){
                $this->error("手续费不能为空或格式不正确");
            }elseif( !$data['borrowing_rate'] || $data['borrowing_rate']=='' || !is_numeric($data['borrowing_rate']) ){
                $this->error("利息不能为空或格式不正确");
            }elseif( !$data['borrowing_days'] || $data['borrowing_days']==''){
                $this->error("借款期限不能为空");
            }elseif( !$data['borrowing_time'] || $data['borrowing_time']==''){
                $this->error("借款时间不能为空");
            }elseif( !$data['borrowing_end_time'] || $data['borrowing_end_time']==''){
                $this->error("借款到期时间不能为空");
            }else{
                if( M("Member")->where(array('user_id'=>$data['uid']))->find() ){
                    $data['borrowing_time'] = strtotime($data['borrowing_time']);
                    $data['borrowing_end_time'] = strtotime($data['borrowing_end_time']);

                    $data['addtime'] = time();
                    $data['adduid'] = $this->user_result['id'];

                    if($this->loan_obj->create($data)){
                        $rst = $this->loan_obj->add($data);
                        if($rst){
                            $msg = "添加借款人ID为{".$rst."},合同编号为{".$data['contract']."}的贷款成功";
                            $this->logs($msg);
                            $this->success("添加贷款成功");
                        }else{
                            $this->error("添加贷款失败");
                        }
                    }else{
                        $this->error("添加贷款异常");
                    }
                }else{
                    $this->error("借款人ID有误,借款人不存在");
                }
            }
        }else{
            $this->error("参数错误");
        }
    }

    public function verify_status(){
        $id = trim(I('id'));
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __USERS__ u ON u.id=l.adduid")
                ->where(array('l.id'=>$id,'l.verify_status'=>0))
                ->field("l.*,mb.real_name,m.phone,u.user_login")
                ->find();
            if(!$ck){
                $this->error("贷款不存在或已审核");
            }else{
                $ck['borrowing_time'] = date("Y-m-d H:i",$ck['borrowing_time']);
                $ck['borrowing_end_time'] = date("Y-m-d",$ck['borrowing_end_time']);
                $ck['addtime'] = date("Y-m-d H:i",$ck['addtime']);
                $this->assign("verify",$ck);
                $this->display();
            }
        }else{
            $this->error("参数id不能为空");
        }
    }

    public function refused_info(){
        $id = trim(I('id'));
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __USERS__ u ON u.id=l.adduid")
                ->where(array('l.id'=>$id,'l.verify_status'=>2))
                ->field("l.*,mb.real_name,m.phone,u.user_login")
                ->find();
            if(!$ck){
                $this->error("贷款不存在或已审核");
            }else{
                $ck['borrowing_time'] = date("Y-m-d H:i",$ck['borrowing_time']);
                $ck['borrowing_end_time'] = date("Y-m-d",$ck['borrowing_end_time']);
                $ck['addtime'] = date("Y-m-d H:i",$ck['addtime']);
                $this->assign("verify",$ck);
                $this->display();
            }
        }else{
            $this->error("参数id不能为空");
        }
    }

    public function verify_credit(){
        $id = trim(I('id'));
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_TAOBAO__ mt ON mt.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_ALIPAY__ ma ON ma.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_CREDIT__ mc ON mc.uid=l.uid")
                ->where(array('l.id'=>$id,'l.verify_status'=>0))
                ->field("l.*,mb.real_name,m.phone,mb.idcard,mb.family_addr,mb.addip as mbaddip,mb.ip_addr,mb.company,mb.company_tel,mb.company_addr,mb.front_pic,mb.behind_pic,mb.real_pic,mb.real_status,mb.contact,mk.card,mk.bankname,mk.branch,mk.phone as mkphone,mk.verify_status as mkverify_status,mb.remark,mo.status as phone_status,mt.status as taobao_status,ma.status as alipay_status,m.user_id,mc.discredit,mc.is_dis,mc.dis_status,mc.negative,mc.is_neg,mc.neg_status")
                ->find();
            if(!$ck){
                $this->error("贷款不存在或已审核");
            }else{

                $where_ands = array('l.uid'=>$ck['uid']);
                $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
                $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
                $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
                $join .= " LEFT JOIN __LOAN_REPAY__ lr ON lr.lid=l.id";
                $items=$this->loan_obj->alias("l")
                    ->join($join)
                    ->where($where_ands)
                    ->field("m.phone,mb.real_name,l.paytime,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.status,lr.repay_time,l.id,l.uid,l.verify_status")
                    ->limit($page->firstRow . ',' . $page->listRows)
                    ->order("l.borrowing_time desc,l.addtime desc")->select();

                foreach($items as &$v){
                    $v['paytime'] = $v['paytime']?date("Y-m-d H:i",$v['paytime']):'';
                    $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
                    $v['repay_time'] = $v['repay_time']?date("Y-m-d H:i",$v['repay_time']):'';
                }
                $ck['history'] = $items;
                $ck['borrowing_time'] = date("Y-m-d",$ck['borrowing_time']);
                $ck['borrowing_end_time'] = date("Y-m-d",$ck['borrowing_end_time']);
                $ck['addtime'] = date("Y-m-d",$ck['addtime']);
                $ck['contact'] = isset($ck['contact'])?json_decode($ck['contact'],true):array();
                $ck['remark'] = isset($ck['remark'])?json_decode($ck['remark'],true):array();
                $book = M("member_addressbook")->where(array('uid'=>$ck['uid']))->select();
                foreach($book as &$v){
                    $v['name'] = base64_decode($v['name'],true);
                }
                $ck['book'] = $book;
                $ck['book_nums'] = count($book);
                $ck['discredit'] = json_decode($ck['discredit'],true);
                foreach($ck['discredit'] as &$v){
                    foreach($v as &$_v){
                        $_v['postTime'] = date("Y-m-d",substr($_v['postTime'],0,10));
                        $_v['recordTime'] = date("Y-m-d",substr($_v['recordTime'],0,10));
                    }
                }
                $ck['negative'] = json_decode($ck['negative'],true);
                $this->assign("verify",$ck);
                $this->assign("id",$id);
                $this->display();
            }
        }else{
            $this->error("参数id不能为空");
        }
    }

    public function refused_credit(){
        $id = trim(I('id'));
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_TAOBAO__ mt ON mt.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_ALIPAY__ ma ON ma.uid=l.uid")
                ->join("LEFT JOIN __MEMBER_CREDIT__ mc ON mc.uid=l.uid")
                ->where(array('l.id'=>$id,'l.verify_status'=>2))
                ->field("l.*,mb.real_name,m.phone,mb.idcard,mb.family_addr,mb.addip as mbaddip,mb.ip_addr,mb.company,mb.company_tel,mb.company_addr,mb.front_pic,mb.behind_pic,mb.real_pic,mb.real_status,mb.contact,mk.card,mk.bankname,mk.branch,mk.phone as mkphone,mk.verify_status as mkverify_status,mb.remark,mo.status as phone_status,mt.status as taobao_status,ma.status as alipay_status,m.user_id,mc.discredit,mc.is_dis,mc.dis_status,mc.negative,mc.is_neg,mc.neg_status")
                ->find();
            if(!$ck){
                $this->error("贷款不存在或已审核");
            }else{

                $where_ands = array('l.uid'=>$ck['uid']);
                $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
                $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
                $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
                $join .= " LEFT JOIN __LOAN_REPAY__ lr ON lr.lid=l.id";
                $items=$this->loan_obj->alias("l")
                    ->join($join)
                    ->where($where_ands)
                    ->field("m.phone,mb.real_name,l.paytime,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.status,lr.repay_time,l.id,l.uid,l.verify_status")
                    ->limit($page->firstRow . ',' . $page->listRows)
                    ->order("l.borrowing_time desc,l.addtime desc")->select();

                foreach($items as &$v){
                    $v['paytime'] = $v['paytime']?date("Y-m-d H:i",$v['paytime']):'';
                    $v['borrowing_end_time'] = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
                    $v['repay_time'] = $v['repay_time']?date("Y-m-d H:i",$v['repay_time']):'';
                }
                $ck['history'] = $items;
                $ck['borrowing_time'] = date("Y-m-d",$ck['borrowing_time']);
                $ck['borrowing_end_time'] = date("Y-m-d",$ck['borrowing_end_time']);
                $ck['addtime'] = date("Y-m-d",$ck['addtime']);
                $ck['contact'] = isset($ck['contact'])?json_decode($ck['contact'],true):array();
                $ck['remark'] = isset($ck['remark'])?json_decode($ck['remark'],true):array();
                $book = M("member_addressbook")->where(array('uid'=>$ck['uid']))->select();
                foreach($book as &$v){
                    $v['name'] = base64_decode($v['name'],true);
                }
                $ck['book'] = $book;
                $ck['book_nums'] = count($book);
                $ck['discredit'] = json_decode($ck['discredit'],true);
                foreach($ck['discredit'] as &$v){
                    foreach($v as &$_v){
                        $_v['postTime'] = date("Y-m-d",substr($_v['postTime'],0,10));
                        $_v['recordTime'] = date("Y-m-d",substr($_v['recordTime'],0,10));
                    }
                }
                $ck['negative'] = json_decode($ck['negative'],true);
                $this->assign("verify",$ck);
                $this->assign("id",$id);
                $this->display();
            }
        }else{
            $this->error("参数id不能为空");
        }
    }

    public function verify_status_p(){
        $id = trim(I('id'));
        $status = trim(I('status'));
        $refused_msg = trim(I('refused_msg'));
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->where(array('id'=>$id,'verify_status'=>0))->find();
            if(!$ck){
                $this->error("贷款不存在或已审核",U('AdminLoan/toverify'));
            }else{
                $data['verify_status'] = 2; //默认拒绝
                if($status==1 || $status==2){
                    $data['verify_status'] = $status;
                }
                $data['verify_time'] = time();
                $data['verify_uid'] = $this->user_result['id'];
                $data['refused_msg'] = $refused_msg;
                if( $this->loan_obj->where(array('id'=>$id))->save($data) !== false){
                    $this->success("审核操作成功",U('AdminLoan/toverify'));
                }else{
                    $this->error("审核操作失败",U('AdminLoan/toverify'));
                }
            }
        }else{
            $this->error("参数id不能为空",U('AdminLoan/toverify'));
        }
    }

    public function paying(){
        $id = trim(I('id'));
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=l.uid")
                ->where(array('l.id'=>$id,'l.verify_status'=>1,'l.status'=>0))
                ->field("l.*,mb.real_name,m.phone,mk.card,mk.bankname")
                ->find();
            if(!$ck){
                $this->error("贷款不存在或已打款");
            }else{
                $ck['borrowing_time'] = date("Y-m-d H:i",$ck['borrowing_time']);
                $ck['verify_time'] = date("Y-m-d H:i",$ck['verify_time']);
                $this->assign("pay",$ck);
                $this->display();
            }
        }else{
            $this->error("参数id不能为空");
        }
    }

    public function paying_p(){
        if(IS_POST){
            $var = array('id','paycard','paytime','trade_no');
            foreach($var as $v){
                if(isset($_POST[$v]) && $_POST[$v]!='') $data[$v] = $_POST[$v];
            }

            if(!isset($data['id']) || $data['id']=='' || $data['id']==0){
                $this->error("操作有误或贷款不存在");
            }elseif( !$data['paycard'] || $data['paycard']==''){
                $this->error("请输入打款银行账号");
            }elseif( !$data['paytime'] || $data['paytime']==''){
                $this->error("请输入打款时间");
            }elseif(!$data['trade_no'] || $data['trade_no']==''){
                $this->error("请输入转账交易流水号");
            }else{
                $ck = $this->loan_obj->where(array('id'=>$data['id'],'verify_status'=>1,'status'=>0))->find();
                if(!$ck){
                    $this->error("贷款不存或未通过审核或已打款");
                }else{
                    $data['paytime'] = strtotime($data['paytime'].":00");
                    $data['payuid'] = $this->user_result['id'];
                    $data['status'] = 1;

                    if($this->loan_obj->save($data)!==false){
                        $this->success("添加打款成功");
                    }else{
                        $this->error("添加打款信息失败");
                    }
                }
            }
        }else{
            $this->error("非法操作");
        }
    }

    public function add_remark(){
        if(IS_POST){
            if(isset($_POST['uid']) && $_POST['uid']!=''){
                if(!$_POST['remark_time'] || $_POST['remark_time']==''){
                    $this->error("请选择备注时间");
                }elseif(!$_POST['remark_name'] || $_POST['remark_name']==''){
                    $this->error("请输入备注名或标题");
                }elseif(!$_POST['remark_content'] || $_POST['remark_content']==''){
                    $this->error("请填写备注内容");
                }else{
                    $rst = M("MemberBase")->where(array('uid'=>$_POST['uid']))->field("remark")->find();

                    if(!$rst){
                        $this->error("借款人不存在");
                    }else{
                        $remark = json_decode($rst['remark'],true);
                        $new = array(
                            'remark_time' => $_POST['remark_time'],
                            'remark_name' => $_POST['remark_name'],
                            'remark_content' => $_POST['remark_content']
                        );
                        if($remark && is_array($remark)){
                            array_push($remark,$new);
                        }else{
                            $remark[] = $new;
                        }
                        if( M("MemberBase")->where(array('uid'=>$_POST['uid']))->save(array('remark'=>json_encode($remark)))!==false ){
                            $this->success("添加备注成功");
                        }else{
                            $this->error("添加备注失败");
                        }
                    }
                }
            }else{
                $this->error("参数错误");
            }
        }else{
            $this->error("非法操作");
        }
    }

    public function repay(){
        $id = trim(I("id"));
        if($id && $id!=''){
            $ck = $this->loan_obj->alias("l")
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->where(array('l.id'=>$id,'l.verify_status'=>1))
                ->field("l.contract,l.borrowing_repay,l.borrowing_end_time,m.phone,mb.real_name,l.status,l.id")
                ->find();
            if(!$ck){
                $this->error("贷款不存在");
            }else{
                $ck['borrowing_end_time'] = date("Y-m-d",$ck['borrowing_end_time']);
                $this->assign("repay",$ck);
                $this->display();
            }
        }else{
            $this->error("参数id错误");
        }
    }

    public function repay_post(){
        if(IS_POST){
            if($_POST['id'] && $_POST['id']!=''){
                if(!$_POST['repay_money'] || $_POST['repay_money']=='' || $_POST['repay_money']<=0){
                    $this->error("还款金额不能为空或不正确");
                }elseif(!$_POST['repay_time'] || $_POST['repay_time']==''){
                    $this->error("还款时间不能为空");
                }elseif(!$_POST['repay_trade_no'] || $_POST['repay_trade_no']==''){
                    $this->error("还款转账流水单号不能为空");
                }else{
                    $ck = $this->loan_obj->where(array('id'=>$_POST['id'],'verify_status'=>1))->find();
                    if(!$ck){
                        $this->error("贷款不存在");
                    }elseif($ck['status']==3 || $ck['status']==5){
                        $this->error("改贷款已还清,请勿重复还款");
                    }else{
                        if( $_POST['repay_money']<$ck['borrowing_repay']){
                            $this->error("还款金额小于待还金额,请重新确认");
                        }else{
                            $data = array(
                                'lid' => $_POST['id'],
                                'repay_type' => $_POST['repay_type']?$_POST['repay_type']:0,
                                'repay_time' => strtotime($_POST['repay_time'].":00"),
                                'repay_money' => $_POST['repay_money'],
                                'repay_trade_no' => $_POST['repay_trade_no'],
                                'repay_uid' => $this->user_result['id'],
                                'repay_remark' => $_POST['remark']
                            );

                            if( M("LoanRepay")->create($data) ){
                                if( M("LoanRepay")->add($data) ){
                                    $status = 3;
                                    if($ck['status']==4){
                                        $status = 5;
                                    }
                                    $this->loan_obj->where(array('id'=>$_POST['id']))->save(array('status'=>$status));
                                    $this->success("添加还款信息成功");
                                }else{
                                    $this->error("添加还款信息失败");
                                }
                            }else{
                                $this->error("参数异常");
                            }
                        }
                    }
                }
            }else{
                $this->error("参数错误");
            }
        }
    }

    public function upd_status(){
        $id = trim(I('id'));
        if(isset($id) && $id!=''){
            $ck = $this->loan_obj->where(array('verify_status'=>1,'id'=>$id,'status'=>array('not in','0')))->find();
            if(!$ck){
                $this->error('贷款不存在');
            }else{
                if($ck['status']!=2){
                    $this->error('操作有误,贷款未到期or已还款or已标记逾期');
                }else{
                    $ov_time = abs(ceil((time()-$ck['borrowing_end_time']) / (3600*24)));
                    if( $this->loan_obj->where(array('id'=>$id))->save(array('status'=>4,'overdue'=>$ov_time)) !== false ){
                        $this->success("标记逾期成功");
                    }else{
                        $this->error("标记逾期失败");
                    }
                }
            }
        }else{
            $this->error('参数id不能为空');
        }
    }

    public function online(){
        $op = I('op');
        $id = I('id');

        if( $id && $id!=''){
            $data['id'] = $id;
            if( isset($op) && $op=='down'){
                $data['status'] = 0;
                $msg = "下架";
            }else{
                $data['status'] = 1;
                $msg = "上架";
            }
            if( $this->product_obj->save($data) !== false ){
                $_msg = $msg."ID为{".$id."}的产品成功";
                $this->logs($_msg);
                $this->success( $msg."成功" );
            }else{
                $this->error( $msg."失败" );
            }
        }else{
            $this->error("非法操作");
        }
    }

    public function listorders() {
        $status = parent::_listorders($this->product_obj);
        if ($status) {
            $val = '';
            foreach($_POST['listorders'] as $k=>$v){
                $val .= $k.'=>'.$v.',';
            }
            $msg = "更新排序ID为{".rtrim($val,',').'}的产品成功';
            $this->logs($msg);
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    public function delete(){
        if(isset($_GET['id'])){
            $id = intval(I("id"));
            if ($this->product_obj->where(array('id'=>$id))->save(array('del_status'=>1))) {
                $msg = '删除ID为{'.$id.'}的产品成功';
                $this->logs($msg);
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    public function loanExcel(){
        $where_ands = array('l.verify_status'=>1,'l.status'=>array('in','1,2,3,4,5'));

        $fields=array(
                'id' => array("field"=>"l.id","operator"=>"="),
                'uid' => array("field"=>"l.uid","operator"=>"="),
                'real_name' => array("field"=>"mb.real_name","operator"=>"like"),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'status' => array("field"=>"l.status","operator"=>"="),
                'user_login' => array("field"=>"u.user_login","operator"=>"like"),
                'ustart_time' => array("field"=>"l.addtime","operator"=>">="),
                'uend_time' => array("field"=>"l.addtime","operator"=>"<="),
                'start_time' => array("field"=>"l.borrowing_end_time","operator"=>">="),
                'end_time' => array("field"=>"l.borrowing_end_time","operator"=>"<="),
                );

            foreach ($fields as $param =>$val){
                if (isset($_REQUEST[$param]) && $_REQUEST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_REQUEST[$param];
                    if($param=='ustart_time' || $param=='start_time'){
                        $get = strtotime($get." 00:00:00");
                    }elseif($param=='uend_time' || $param=='end_time'){
                        $get = strtotime($get." 23:59:59");
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
        $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
        $count=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->loan_obj->alias("l")
            ->join($join)
            ->where($where_ands)
            ->field("m.user_id,mb.real_name,m.phone,l.borrowing_money,l.borrowing_days,l.borrowing_end_time,l.status,u.user_login,l.verify_uid,l.borrowing_time,l.verify_status,l.paytime,l.id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("l.borrowing_end_time asc,l.addtime desc")->select();

        $hb = array();
        $status = array('待打款','待还款','已到期','已还款','已逾期','逾期还款','还款待审核');
        $verify = array('待审核','审核通过','审核拒绝');
        foreach($data['item'] as $v){
            $paytime = $v['paytime']?date("Y-m-d H:i",$v['paytime']):'';
            $borrowing_end_time = $v['borrowing_end_time']?date("Y-m-d",$v['borrowing_end_time']):"";
            $verify_user = '';
            if($v['verify_uid']!=0){
                $vu = M("Users")->where(array('id'=>$v['verify_uid']))->field("user_login")->find();
                $verify_user = $vu['user_login'];
            }
            $put = array(
                'id' => $v['id'],
                'user_id' => $v['user_id'],
                'real_name' => $v['real_name'],
                'phone' => $v['phone'],
                'paytime' => $paytime,
                'borrowing_money' => $v['borrowing_money'].'元',
                'borrowing_days' => $v['borrowing_days'].'天',
                'borrowing_end_time' => $borrowing_end_time,
                'recharge_counts' => $status[$v['status']],
                'adduser' => $v['user_login'],
                'verify_user' => $verify_user,
                'verify_status' => $verify[$v['verify_status']]
            );
            $hb[] = $put;
        }

        vendor("PHPExcel.Classes.PHPExcel#class");
        $excel = new \PHPExcel();
        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L');

        $tableheader = array('借款编号','借款人ID','借款人','手机号','放款时间','借款金额','借款期限','到期时间','借款状态','添加人','审核人','审核状态');

        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
            $excel->getActiveSheet()->getStyle("$letter[$i]")->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        }

        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15); 
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(15); 

        for ($i = 2;$i <= count($hb) + 1;$i++) {
            $j = 0;
            foreach ($hb[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        header('Content-Disposition: attachment;filename="贷款列表'.time().'.xlsx"');  
        header('Cache-Control: max-age=0');  
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function base_phone(){
        //A("AdminCredit")->base_phone();
        exit(A("AdminCredit")->base_phone());
    }

    public function base_taobao(){
        exit(A("AdminCredit")->base_taobao());
    }

    public function base_alipay(){
        exit(A("AdminCredit")->base_alipay());
    }

    public function moxie_phone(){
        exit(A("AdminCredit")->moxie_phone());
    }

    public function moxie_taobao(){
        exit(A("AdminCredit")->moxie_taobao());
    }

    public function moxie_alipay(){
        exit(A("AdminCredit")->moxie_alipay());
    }
}
