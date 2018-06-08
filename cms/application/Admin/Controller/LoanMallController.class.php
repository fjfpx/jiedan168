<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class LoanMallController extends AdminbaseController
{
    protected $loanMall;

    function _initialize()
    {
        parent::_initialize();
        $this->loanMall = D('Common/LoanMall');
    }

    function index()
    {
        $daichao = $this->loanMall->order(array("id" => "asc"))->select();
        $this->assign("daichao", $daichao);
        $this->display();
    }

    function add()
    {
        $this->display();
    }

    function add_post()
    {
        if (IS_POST) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 0;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './uploads/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            $upload->saveName = 'time';
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {// 上传成功
//                $this->success('上传成功！');
            }
            $loanMall=$this->loanMall->create();
            if ($loanMall) {
                $fullpath="http://cms.jiedan168.com/{$info['logo']['savepath']}/{$info['logo']["savename"]}";
                $loanMall['logo']=$fullpath;
                if ($this->loanMall->add($loanMall) !== false) {
                    $this->success("添加成功！", U("LoanMall/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->loanMall->getError());
            }
        }
    }

    function edit()
    {
        $id = I("get.id");
        $link = $this->link_model->where("link_id=$id")->find();
        $this->assign($link);
        $this->assign("targets", $this->targets);
        $this->display();
    }

    function edit_post()
    {
        if (IS_POST) {
            if ($this->link_model->create()) {
                if ($this->link_model->save() !== false) {
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