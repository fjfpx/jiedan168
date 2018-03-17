<?php
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
class AdminCreditController extends AdminbaseController {
    protected $member_obj;
    protected $user_result;

    function _initialize() {
        parent::_initialize();
        $this->member_obj = D("Common/Member");
        $this->user_result = $this->get("admin");
    }

    public function info(){
        $uid = trim(I('uid'));
        if(isset($uid) && $uid!=''){
            $ck = $this->member_obj->alias("m")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=m.user_id")
                ->join("LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=m.user_id")
                ->join("LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=m.user_id")
                ->join("LEFT JOIN __MEMBER_TAOBAO__ mt ON mt.uid=m.user_id")
                ->join("LEFT JOIN __MEMBER_ALIPAY__ ma ON ma.uid=m.user_id")
                ->join("LEFT JOIN __MEMBER_CREDIT__ mc ON mc.uid=m.user_id")
                ->where(array('m.user_id'=>$uid))
                ->field("mb.real_name,m.phone,mb.idcard,mb.family_addr,mb.addip as mbaddip,mb.ip_addr,mb.company,mb.company_tel,mb.company_addr,mb.front_pic,mb.behind_pic,mb.real_pic,mb.real_status,mb.contact,mk.card,mk.bankname,mk.branch,mk.phone as mkphone,mk.verify_status as mkverify_status,mb.remark,mb.uid,mo.status as phone_status,mt.status as taobao_status,ma.status as alipay_status,m.user_id,mc.discredit,mc.is_dis,mc.dis_status,mc.negative,mc.is_neg,mc.neg_status")
                ->find();
            if(!$ck){
                $this->error("借款人不存在");
            }else{
                M("Member")->where(array('user_id'=>$uid))->save(array('is_hit'=>1));

                $where_ands = array('l.uid'=>$ck['uid']);
                $join = "LEFT JOIN __MEMBER__ m ON m.user_id=l.uid";
                $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid";
                $join .= " LEFT JOIN __USERS__ u ON u.id=l.adduid";
                $join .= " LEFT JOIN __LOAN_REPAY__ lr ON lr.lid=l.id";
                $items=M("Loan")->alias("l")
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
                $ck['contact'] = json_decode($ck['contact'],true);
                $ck['remark'] = isset($ck['remark'])?json_decode($ck['remark'],true):'';

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
                $this->assign("info",$ck);
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
        $where_ands = array('mb.real_status'=>1);

        $fields=array(
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'start_time' => array("field"=>"mb.addtime","operator"=>">="),
                'end_time' => array("field"=>"mb.addtime","operator"=>"<="),
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
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
        $join = "LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=m.user_id";
        $join .= " LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=m.user_id";
        $join .= " LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=m.user_id";
        $count=$this->member_obj->alias("m")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->member_obj->alias("m")
            ->join($join)
            ->field("m.user_id,m.phone,m.createtime,mb.addtime,mb.ip_addr,mb.real_status,m.user_status,mb.real_name,mo.status as phone_status,mk.verify_status as mkverify_status,mb.info_status")
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("mb.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['addtime'] = $v['addtime']?date("Y-m-d H:i",$v['addtime']):'';
            $v['createtime'] = date("Y-m-d H:i",strtotime($v['createtime']));
            
            $v['is_success'] = '否';
            if($v['info_status']==1 && $v['mkverify_status']==1 && $v['real_status']==1 && $v['phone_status']==1){
                $v['is_success'] = '是';
            }

            $sex = M("MemberIdcardinfo")->where(array('uid'=>$v['user_id']))->field("sex")->find();
            $v['sex'] = $sex['sex'];
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

    public function history(){
        A("AdminLoan")->history();
    }

    public function add(){
        $this->display();
    }

    public function add_post(){
        if(IS_POST){
            $var = array('title','desc','min_money','max_money','rate_type','rate','min_date','max_date','fast_time','conditions','need_data','info');
            foreach($var as $v){
                if($_POST[$v] && $_POST[$v]!=''){
                    $data[$v] = trim($_POST[$v]);
                }
            }

            if( !$data['rate'] || $data['rate']=='' || $data['rate']==0 || !is_numeric($data['rate'])){
                $this->error("利率不能为空或格式不正确");
            }elseif( !$data['min_money'] || $data['min_money']=='' || $data['min_money']==0 || !is_numeric($data['min_money'])){
                $this->error("最小额度不能为空或格式不正确");
            }elseif( !$data['max_money'] || $data['max_money']=='' || $data['max_money']==0 || !is_numeric($data['max_money'])){
                $this->error("最大额度不能为空或格式不正确");
            }elseif( $data['min_money'] > $data['max_money'] ){
                $this->error("最小额度不能大于最大额度");
            }elseif( !$data['min_date'] || $data['min_date']=='' || !$data['max_date'] || $data['max_date']=='' ){
                $this->error("最小或最大期限不能为空");
            }elseif( !$data['fast_time'] || $data['fast_time']=='' ){
                $this->error("最快放款不能为空");
            }else{
                $data['rate'] = $data['rate']/100;
                $data['addtime'] = time();
                $data['adduid'] = $this->user_result['id'];

                if($this->product_obj->create($data)){
                    $rst = $this->product_obj->add($data);
                    if($rst){
                        $msg = "添加ID为{".$rst."}的产品成功";
                        $this->logs($msg);
                        $this->success("添加成功");
                    }else{
                        $this->error("添加失败");
                    }
                }else{
                    $this->error("添加异常");
                }
            }
        }else{
            $this->error("参数错误");
        }
    }

    public function edit(){
        $id = intval(I('id'));
        if( $id && $id!=''){
            $info = $this->product_obj->where(array('id'=>$id))->find();

            if($info){
                $info['rate'] = $info['rate']*100;
                $this->assign("info",$info);
            }else{
                $this->error("产品不存在");
            }
        }else{
            $this->error("产品不存在");
        }
        $this->display();
    }

    public function edit_post(){
        if(IS_POST){
            $var = array('title','desc','min_money','max_money','rate_type','rate','min_date','max_date','fast_time','conditions','need_data','info','id');
            foreach($var as $v){
                if($_POST[$v] && $_POST[$v]!=''){
                    $data[$v] = trim($_POST[$v]);
                }
            }

            if( !$data['rate'] || $data['rate']=='' || $data['rate']==0 || !is_numeric($data['rate'])){
                $this->error("利率不能为空或格式不正确");
            }elseif( !$data['min_money'] || $data['min_money']=='' || $data['min_money']==0 || !is_numeric($data['min_money'])){
                $this->error("最小额度不能为空或格式不正确");
            }elseif( !$data['max_money'] || $data['max_money']=='' || $data['max_money']==0 || !is_numeric($data['max_money'])){
                $this->error("最大额度不能为空或格式不正确");
            }elseif( $data['min_money'] > $data['max_money'] ){
                $this->error("最小额度不能大于最大额度");
            }elseif( !$data['min_date'] || $data['min_date']=='' || !$data['max_date'] || $data['max_date']=='' ){
                $this->error("最小或最大期限不能为空");
            }elseif( !$data['fast_time'] || $data['fast_time']=='' ){
                $this->error("最快放款不能为空");
            }else{
                $data['rate'] = $data['rate']/100;

                if($this->product_obj->create($data)){
                    if( $this->product_obj->save($data) !== false ){
                        $msg = "修改ID为{".$data['id']."}的产品成功";
                        $this->logs($msg);
                        $this->success("修改成功");
                    }else{
                        $this->error("修改失败");
                    }
                }else{
                    $this->error("修改异常");
                }
            }
        }else{
            $this->error("参数错误");
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

    public function black(){
        $where_ands = array('m.user_status'=>0);

        $fields=array(
                'user_id' => array("field"=>"m.user_id","operator"=>"="),
                'phone' => array("field"=>"m.phone","operator"=>"="),
                'real_name' => array("field"=>"mb.real_name","operator"=>"like")
                );
        if(IS_POST){
            unset($_GET);
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
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
        $join = "LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=m.user_id";
        $count=$this->member_obj->alias("m")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->member_obj->alias("m")
            ->join($join)
            ->field("m.user_id,m.phone,m.createtime,m.sex,mb.addtime,mb.ip_addr,mb.real_status,m.user_status,mb.real_name")
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("mb.addtime desc")->select();
        foreach($data['item'] as &$v){
            $v['addtime'] = $v['addtime']?date("Y-m-d H:i",$v['addtime']):'';
            $v['createtime'] = date("Y-m-d H:i",strtotime($v['createtime']));
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

    public function cancel(){
        $uid = trim(I('id'));

        if(isset($uid) && $uid!=''){
            if($this->member_obj->where(array('user_id'=>$uid))->find()){
                if( $this->member_obj->where(array('user_id'=>$uid))->save(array('user_status'=>1))!==false ){
                    $this->success("黑名单解除成功");
                }else{
                    $this->error("黑名单解除失败");
                }
            }else{
                $this->error("用户不存在");
            }
        }else{
            $this->error('用户不存在');
        }
    }

    public function shielding(){
        $uid = trim(I('id'));
        
        if(isset($uid) && $uid!=''){
            if($this->member_obj->where(array('user_id'=>$uid))->find()){
                if( $this->member_obj->where(array('user_id'=>$uid))->save(array('user_status'=>0))!==false ){
                    $this->success("添加黑名单成功");
                }else{
                    $this->error("添加黑名单失败");
                }
            }else{
                $this->error("用户不存在");
            }
        }else{
            $this->error('用户不存在');
        }
    }

    public function add_black(){
        $this->display();
    }

    public function add_black_post(){
        $ids = $_POST['blacks'];

        if($ids && $ids!=''){
            $str = str_replace('，',',',$ids);
            $arr = explode(',',$str);

            $id = array();
            $phone = array();
            foreach($arr as $v){
                if($this->member_obj->where(array('user_status'=>1,'user_id'=>$v))->find()){
                    $id[] = $v;
                }
            }
            if($id){
                $_ids = implode(',',$id);
                if($this->member_obj->where(array('user_id'=>array('in',$_ids)))->save(array('user_status'=>0))!==false){
                    $this->success("添加黑名单成功");
                }else{
                    $this->error("添加黑名单失败");
                }
            }else{
                $this->error("id不存在或已经是黑名单");
            }
        }else{
            $this->error("参数不能为空");
        }
    }

    public function base_phone(){
        $uid = trim(I('uid'));
        if($uid){
            $rst = M("MemberOperator")->where(array('uid'=>$uid,'status'=>1))->find();

            if($rst){
                $this->assign('base',json_decode($rst['info'],true));
            }
        }
        $this->display('AdminCredit/base_phone');
    }

    public function moxie_phone(){
        $uid = trim(I("uid"));
        if($uid){
            $rst = M("Moxie")->where(array('uid'=>$uid,'report_status'=>1,'type'=>0))->field("datainfo")->find();

            if($rst && $rst['datainfo']!=''){
                echo base64_decode($rst['datainfo']);
            }else{
                $this->error("魔蝎报告不存在");
            }
        }else{
            $this->error("参数错误");
        }
    }

    public function base_taobao(){
        $uid = trim(I('uid'));
        if($uid){
            $rst = M("MemberTaobao")->where(array('uid'=>$uid,'status'=>1))->find();
            if($rst){
                $this->assign('base',json_decode($rst['info'],true));
            }
        }
        $this->display('AdminCredit/base_taobao');
    }

    public function moxie_taobao(){
        $uid = trim(I("uid"));
        if($uid){
            $rst = M("Moxie")->where(array('uid'=>$uid,'report_status'=>1,'type'=>1))->field("datainfo")->find();

            if($rst && $rst['datainfo']!=''){
                echo base64_decode($rst['datainfo']);
            }else{
                $this->error("魔蝎报告不存在");
            }
        }else{
            $this->error("参数错误");
        }
    }

    public function base_alipay(){
        $uid = trim(I('uid'));
        if($uid){
            $rst = M("MemberAlipay")->where(array('uid'=>$uid,'status'=>1))->find();

            if($rst){
                $this->assign('base',json_decode($rst['info'],true));
            }
        }
        $this->display('AdminCredit/base_alipay');
    }

    public function moxie_alipay(){
        $uid = trim(I("uid"));
        if($uid){
            $rst = M("Moxie")->where(array('uid'=>$uid,'report_status'=>1,'type'=>2))->field("datainfo")->find();

            if($rst && $rst['datainfo']!=''){
                echo base64_decode($rst['datainfo']);
            }else{
                $this->error("魔蝎报告不存在");
            }
        }else{
            $this->error("参数错误");
        }
    }
}
