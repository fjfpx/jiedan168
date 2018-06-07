<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LoanMallController extends AdminbaseController{
    function _initialize() {
        parent::_initialize();
    }

    function index(){
        $daichao=M('daichao')->order(array("id"=>"asc"))->select();
        $this->assign("daichao",$daichao);
        $this->display();
    }

    function add(){
        $this->assign("targets",$this->targets);
        $this->display();
    }

    function add_post(){
        if(IS_POST){
            if ($this->link_model->create()) {
                if ($this->link_model->add()!==false) {
                    $this->success("添加成功！", U("link/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->link_model->getError());
            }

        }
    }

    function edit(){
        $id=I("get.id");
        $link=$this->link_model->where("link_id=$id")->find();
        $this->assign($link);
        $this->assign("targets",$this->targets);
        $this->display();
    }

    function edit_post(){
        if (IS_POST) {
            if ($this->link_model->create()) {
                if ($this->link_model->save()!==false) {
                    $this->success("保存成功！");
                } else {
                    $this->error("保存失败！");
                }
            } else {
                $this->error($this->link_model->getError());
            }
        }
    }

}