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
        $index=0;
        foreach ($daichao as $k=>$v) {
            $daichao[$k]['descr']="{$v['qixian_start']}天-{$v['qixian_end']}天";
            $daichao[$k]['edu']="￥{$v['edu_start']}-￥{$v['edu_end']}";
            $index+=1;
            if ($index<3) {
                $daichao[$k]['shenqing']=rand(10000, 15000);
            } else {
                $daichao[$k]['shenqing']=rand(3000, 9999);
            }
        }
        $this->ajaxReturn($daichao);
    }
}
