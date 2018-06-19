<?php

namespace Admin\Controller;

use Think\Controller;

class LoanMallShowController extends Controller
{
    protected $loanMall;
    public function index()
    {
        $this->display("./tpl_admin/simpleboot/Admin/LoanMallShow/index.html");
    }

    public function showData()
    {
        $this->loanMall = D('Common/LoanMall');
        $daichao=$this->loanMall->order('shunxu desc')->select();
        foreach($daichao as $k=>$v){
            $daichao[$k]['descr']="{$v['qixian_start']}天-{$v['qixian_end']}天";
            $daichao[$k]['edu']="￥{$v['edu_start']}-￥{$v['edu_end']}";
            $daichao[$k]['shenqing']=rand(1,100);
        }
        $this->ajaxReturn($daichao);
    }
}