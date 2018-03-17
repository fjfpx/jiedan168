<?php
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
use Portal\Lib;
header("Content-Type:text/html; charset=utf-8");
class AdminMessageController extends AdminbaseController {
    protected $message_obj;
    protected $message_users_obj;
    protected $account_obj;
    protected $user_result;

    public function _initialize() {
        parent::_initialize();
        $this->message_obj = D("Portal/Message");
        $this->message_users_obj = D("Portal/MessageUsers");
        $this->account_obj = D("Portal/Account");
        $this->user_result = $this->get("admin");
    }

    public function index(){
        $this->_lists();
        $this->display();
    }

    public function checkindex(){
        $this->_checklists();
        $this->display();
    }

    private function _lists(){
        $where_ands=array(
            'ms.type' => 1,
            'ms.delete_status' => 0,
        );

        $fields=array(
                'status'  => array("field"=>"ms.status","operator"=>"="),
                'start_time'=> array("field"=>"ms.addtime","operator"=>">="),
                'end_time'  => array("field"=>"ms.addtime","operator"=>"<="),
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
                        $get = $get." :00";
                    }
                    if($param=='end_time'){
                        $get = $get." :59";
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
                        $get = $get." :00";
                    }
                    if($param=='end_time'){
                        $get = $get." :59";
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $count=$this->message_obj->alias("ms")
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);
        $data['item']=$this->message_obj->alias("ms")
            ->field("ms.*")
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("ms.addtime desc")->select();
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

    private function _checklists(){
        $where_ands=array(
            'ms.type' => 0,
            'ms.delete_status' => 0,
        );

        $fields=array(
                'status'  => array("field"=>"ms.status","operator"=>"="),
                'user_id'  => array("field"=>"mu.uid","operator"=>"="),
                'phone'  => array("field"=>"m.phone","operator"=>"="),
                'start_time'=> array("field"=>"ms.addtime","operator"=>">="),
                'end_time'  => array("field"=>"ms.addtime","operator"=>"<="),
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
                        $get = $get." :00";
                    }
                    if($param=='end_time'){
                        $get = $get." :59";
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
                        $get = $get." :00";
                    }
                    if($param=='end_time'){
                        $get = $get." :59";
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $join = "LEFT JOIN __MESSAGE_USERS__ mu ON mu.mid=ms.id";
        $_join = "LEFT JOIN __MEMBER__ m ON m.user_id=mu.uid";
        $count=$this->message_obj->alias("ms")
            ->join($join)
            ->join($_join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);
        $data['item']=$this->message_obj->alias("ms")
            ->join($join)
            ->join($_join)
            ->field("ms.*,m.phone,mu.uid,mu.status as readstatus")
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("ms.addtime desc")->select();
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

    protected function verify_admin($id,$status){
        if(isset($id) && $id!=''){
            return M("Message")->where(array("id"=>$id))->save(array("verifyadmin"=>$this->user_result['id'],"status"=>$status));
        }
        return;
    }

    public function add(){
        $this->display();
    }

    public function add_post(){
        if(IS_POST){
            if(!isset($_POST['title']) OR $_POST['title']==''){
                $this->error("标题不能为空");
            }elseif(!isset($_POST['content']) OR $_POST['content']==''){
                $this->error("内容不能为空");
            }else{
                $data = array(
                    'title' => $_POST['title'],
                    'content' => $_POST['content'],
                    'type' => 1,
                    'status' => 0,
                    'url' => $_POST['url'],
                    'addadmin' => $this->user_result['id'],
                    'addtime' => date("Y-m-d H:i:s")
                );

                $rst = $this->message_obj->add($data);

                if($rst){
                    $this->success("添加成功",U("AdminMessage/index"));
                }else{
                    $this->error("添加失败");
                }
            }
        }else{
            $this->error("参数错误");
        }
    }

    public function verify(){
        $id = I("id");
        if(isset($id) && $id!==''){
            $check = $this->message_obj->where(array("id"=>$id,"type"=>1))->find();
            if($check){
                if($check['status']==1){
                    $this->error("请勿重复审核");
                }else{
                    $rs = self::verify_admin($id,1);
                    if($rs!==false){
                        //推送到用户
                        self::pushUsers($id);
                        $this->success("审核上线成功");
                    }else{
                        $this->error("审核上线失败");
                    }
                }
            }else{
                $this->error("数据不存在");
            }
        }else{
            $this->error("参数错误");
        }
    }

    public function unverify(){
        $id = I("id");
        if(isset($id) && $id!==''){
            $check = $this->message_obj->where(array("id"=>$id,"type"=>1))->find();
            if($check){
                if($check['status']==0){
                    $this->error("请勿重复下线");
                }else{
                    $rs = self::verify_admin($id,0);
                    if($rs!==false){
						$this->success("审核下线成功");
                    }else{
                    	$this->error("审核下线失败");
					}
                }
            }else{
                $this->error("数据不存在");
            }
        }else{
            $this->error("参数错误");
        }
    }

    protected function pushUsers($id){
        if(isset($id) && $id!=''){
            $data = array(
                'mid' => $id,
            );

            $member = M("Member")->field("user_id")->select();
            foreach($member as $v){
                $data['uid'] = $v['user_id'];
                if(!M("MessageUsers")->where($data)->find()){
                    M("MessageUsers")->add($data);
                }
            }
        }
    }

    public function delete(){
        if(isset($_GET['id'])){
            $id = intval(I("id"));
            $data['delete_status']=1;
            if ($this->message_obj->where("id=$id")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if(isset($_POST['ids'])){
            $ids=join(",",$_POST['ids']);
            $data['status']=1;
            if ($this->message_obj->where("id in ($ids)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }    
    }
}
