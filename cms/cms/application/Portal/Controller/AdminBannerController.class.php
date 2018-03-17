<?php
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
use Portal\Lib;
header("Content-Type:text/html; charset=utf-8");
class AdminBannerController extends AdminbaseController {
    protected $slide_obj;
    protected $user_result;

    public function _initialize() {
        parent::_initialize();
        $this->slide_obj = D("Slide");
        $this->user_result = $this->get("admin");
    }

    public function index(){
        $this->_lists();
        $this->display();
    }

    private function _lists(){
        $where_ands['slide_cid'] = 0;
        $count=$this->slide_obj
            ->where($where_ands)
            ->count();

        $page = $this->page($count, 20);

        $data['item']=$this->slide_obj
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("slide_status desc,listorder asc")->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page",$page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("item",$data);
    }

    protected function verify_admin($id){
        if(isset($id) && $id!=''){
            $rst = M("AccountRechargeInfo")->where(array("rid"=>$id))->save(array("verifyuid"=>$this->user_result['id']));
        }
        return true;
    }

    public function add(){
        $this->display();
    }

    public function add_post(){
        if(IS_POST){
            $post = $_POST;
            $post['adduid'] = $this->user_result['id'];
            if(!isset($post['slide_pic']) || $post['slide_pic']==''){
                $this->error("请上传幻灯片");
            }elseif(!isset($post['slide_name']) || $post['slide_name']==''){
                $this->error("请填写幻灯片名称");
            }else{
                if($this->slide_obj->create($post)){
                    if($this->slide_obj->add($post)){
                        $this->success("幻灯片添加成功");
                    }else{
                        $this->error("幻灯片添加失败");
                    }
                }
            }
        }
    }

    public function edit(){
        $id = trim(I("id"));

        if(isset($id) && $id!=''){
            $rst = $this->slide_obj->where(array("slide_id"=>$id))->find();

            $this->assign("item",$rst);
        }

        $this->display();
    }

    public function edit_post(){
        if(IS_POST){
            if(!isset($_POST['slide_pic']) || $_POST['slide_pic']==''){
                $this->error("请上传幻灯片");
            }elseif(!isset($_POST['slide_name']) || $_POST['slide_name']==''){
                $this->error("请填写幻灯片名称");
            }else{
                if($this->slide_obj->create()){
                    if($this->slide_obj->save()){
                        $this->success("幻灯片修改成功");
                    }else{
                        $this->error("幻灯片修改失败");
                    }
                }
            }
        }
    }

    public function view(){
        $id = trim(I('id'));

        if(isset($id) && $id!=''){
            if( $this->slide_obj->where(array('slide_id'=>$id))->save(array('slide_status'=>1)) ){
                $this->success("上线成功");
            }else{
                $this->error("上线失败");
            }
        }
    }

        public function unview(){
            $id = trim(I('id'));

            if(isset($id) && $id!=''){
                if($this->slide_obj->where(array('slide_id'=>$id))->save(array('slide_status'=>0))){
                    $this->success("下线成功");
                }else{
                    $this->error("下线失败");
                }
            }
        }

    public function delete(){
        $id = trim(I('id'));
        if(isset($id) && $id!=''){
            if($this->slide_obj->where("slide_id=".$id)->delete()){
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }
    }
}
