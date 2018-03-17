<?php
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
class AdminListsController extends AdminbaseController {
    protected $product_obj;
    protected $user_result;

    function _initialize() {
        parent::_initialize();
        $this->product_obj = D("Common/Product");
        $this->user_result = $this->get("admin");
    }

    public function info(){
        $this->display();
    }

    public function index() { 
        $this->_lists();
        $this->display();
    }

    public function _lists(){
        $where_ands = array('p.del_status'=>0);

        $fields=array(
                'title' => array("field"=>"p.title","operator"=>"like"),
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
        $count=$this->product_obj->alias("p")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->product_obj->alias("p")
            ->join($join)
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("p.listorder asc,p.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['addtime'] = $v['addtime']?date("Y-m-d H:i:s",$v['addtime']):'';
            $v['nums'] = M("ProductUser")->where(array('product_id'=>$v['id']))->count();
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

    public function applyfor(){
        $where_ands = array('p.del_status'=>0);

        $fields=array(
                'title' => array("field"=>"p.title","operator"=>"like"),
                'user_id' => array("field"=>"m.user_id","operator"=>"like"),
                'start_time' => array("field"=>"pu.addtime","operator"=>">="),
                'end_time' => array("field"=>"pu.addtime","operator"=>"<="),
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
        $join = "LEFT JOIN __PRODUCT__ p ON p.id=pu.product_id";
        $join .= " LEFT JOIN __MEMBER__ m ON m.user_id=pu.uid";
        $join .= " LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=pu.uid";
        $count=M("ProductUser")->alias("pu")
            ->join($join)
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=M("ProductUser")->alias("pu")
            ->join($join)
            ->where($where_ands)
            ->field("p.title,p.id,m.phone,mb.real_name,pu.addtime,p.rate,m.user_id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("pu.addtime desc")->select();

        foreach($data['item'] as &$v){
            $v['addtime'] = $v['addtime']?date("Y-m-d H:i:s",$v['addtime']):'';
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
}
