<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MainController extends AdminbaseController {
    protected $user_result;

    function _initialize() {
        parent::_initialize();
        $this->user_result = $this->get("admin");
    }

    public function index(){
        /*
        $start = date("Y-m-d")." 00:00:00";
        $end = date("Y-m-d")." 23:59:59";
        $conition = array(
            'user_type' => 2,
            'is_hit' => 0,
            'createtime' => array('BETWEEN',array($start,$end))
        );
        $ishit = M("Member")->where($condition)->count();
        */

        self::regUsers();//用户注册

        self::product(); //产品申请

        self::endtime(); //到期订单

        self::infosuccess(); //信息完善

    	$this->display();
    }

    protected function regUsers(){
        $start = date("Y-m-d")." 00:00:00";
        $end = date("Y-m-d")." 23:59:59";

        $condition = array(
            'user_type' => 2,
            //'createtime' => array('BETWEEN',array($start,$end))
        );
        $res = M("Member")->where($condition)->field("createtime")->select();

        $t = 0;
        $y = 0;
        $s = 0;
        $c = count($res);

        foreach($res as $v){
            if( date("Ymd",strtotime($v['createtime'])) == date("Ymd",time()) ){
                $t++;
            }

            if(date("Ymd",strtotime($v['createtime'])) == date("Ymd",strtotime('-1 day'))){
                $y++;
            }

            if( date("Ymd",strtotime($v['createtime'])) >= date("Ymd",strtotime('-7 day')) ){
                $s++;
            }
        }
        
        $this->assign("reg_users",array('t'=>$t,'y'=>$y,'s'=>$s,'c'=>$c));
    }

    protected function product(){
        $start = strtotime(date("Y-m-d")." 00:00:00");
        $end = strtotime(date("Y-m-d")." 23:59:59");

        $condition = array(
            'addtime' => array('BETWEEN',array($start,$end))
        );

        $res = M("ProductUser")->where($condition)->field("addtime")->select();

        $t = 0;
        $y = 0;
        $s = 0;
        $c = count($res);
        foreach($res as $v){
            if(date("Ymd",$v['addtime']) == date("Ymd",time())){
                $t++;
            }
            if(date("Ymd",$v['addtime']) == date("Ymd",strtotime('-1 day')) ){
                $y++;
            }
            if( date("Ymd",$v['addtime']) >= date("Ymd",strtotime('-7 day')) ){
                $s++;
            }
        }

        $this->assign("product",array('t'=>$t,'y'=>$y,'s'=>$s,'c'=>$c));
    }

    protected function endtime(){
        $end = strtotime(date("Y-m-d")." 00:00:00");
        $condition = array(
            'status' => array('in','1,2'),
            'borrowing_end_time' => $end
        );

        $rst = M("Loan")->where($condition)->count();

        $money = M("Loan")->where($condition)->sum("borrowing_money");
        $this->assign("today_loan",$rst);
        $this->assign("today_money",$money);
    }

    protected function infosuccess(){
        $condition = array(
            'mb.real_status' => 1,
        );
        $rst = M("MemberBase")->alias("mb")
                ->where($condition)
                ->field("mb.addtime,mb.uid")
                ->select();
        $t = 0;
        $y = 0;
        $s = 0;
        $c = count($rst);

        foreach($rst as $v){
            if(date("Ymd",$v['addtime']) == date("Ymd",time())){
                $t++;
            }
            if(date("Ymd",$v['addtime']) == date("Ymd",strtotime('-1 day')) ){
                $y++;
            }
            if( date("Ymd",$v['addtime']) >= date("Ymd",strtotime('-7 day')) ){
                $s++;
            }
        }
        $this->assign("info",array('t'=>$t,'y'=>$y,'s'=>$s,'c'=>$c));
    }
}
