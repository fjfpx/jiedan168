<?php
namespace User\Controller;
use Common\Controller\AdminbaseController;
class OauthadminController extends AdminbaseController {

	//用户列表
	function index(){
		$where_ands=array("ou.status"=>0);
		$fields=array(
				'username' => array("field"=>"ou.name","operator"=>"like"),
				'user_id' => array("field"=>"ou.user_id","operator"=>"="),
				'sex' => array("field"=>"ou.sex","operator"=>"="),
                'vip' => array("field"=>"ou.vip","operator"=>"="),
				'channel_no' => array("field"=>"ch.channel_no","operator"=>"="),
                'subscribe' => array("field"=>"ou.subscribe","operator"=>"="),
				'start_time'=> array("field"=>"ou.create_time","operator"=>">="),
				'end_time'  => array("field"=>"ou.create_time","operator"=>"<="),
				'utype' => array("field"=>"ou.utype","operator"=>"="),
				);
		if(!isset($_REQUEST['start_time']) || $_REQUEST['start_time']=='' || !isset($_REQUEST['end_time']) || $_REQUEST['end_time']==''){
			$_GET['start_time'] = date("Y-m-d")." 00:00";
			$_GET['end_time'] = date("Y-m-d")." 23:59";
		}
		if(IS_POST){
			foreach ($fields as $param =>$val){
				if (isset($_POST[$param]) && $_POST[$param] !='') {
					$operator=$val['operator'];
					$field =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
					if($param == 'start_time'){
						$get = $get.":00";
					}elseif($param == 'end_time'){
						$get = $get.":59";
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
					if($param == 'start_time'){
						$get = $get.":00";
					}elseif($param == 'end_time'){
						$get = $get.":59";
					}
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}

        $where_ands['ac.utype'] = array("not in","0");
		$oauth_user_model=M('OauthUser','mh_',C('DB_CONFIG2'));
		$count=$oauth_user_model->alias("ou")
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.user_id=ou.user_id")
            ->join("LEFT JOIN __CHANNEL__ ch ON ch.id=ou.channel_id")
            ->where($where_ands)->count();
		$page = $this->page($count, 20);
		$lists = $oauth_user_model->alias("ou")
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.user_id=ou.user_id")
            ->join("LEFT JOIN __CHANNEL__ ch ON ch.id=ou.channel_id")
			->where($where_ands)
            ->field("ou.*,ch.channel_no,ac.recharge_total,ac.use_coin,ac.award_use_coin")
			->order("ou.create_time DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

        foreach($lists as &$v){
            //充值次数
            $_condition = array("user_id"=>$v['user_id'],"utype"=>$v['utype'],"status"=>1);
            $v['recharge_counts'] = M("AccountRecharge")->where($_condition)->count();
        }
		$this->assign("page", $page->show('Admin'));
		$this->assign('lists', $lists);
        if($assign == 1){
            $this->assign("formget",$_GET);
        }else{
		    $this->assign("formget",$_POST);
        }
		$this->assign("total",$count);
		$this->display();
	}

    public function login_total(){
        $where_ands=array("ou.status"=>0);
        $fields=array(
                'username' => array("field"=>"ou.name","operator"=>"like"),
                'user_id' => array("field"=>"ou.user_id","operator"=>"="),
                'sex' => array("field"=>"ou.sex","operator"=>"="),
                'vip' => array("field"=>"ou.vip","operator"=>"="),
                'channel_no' => array("field"=>"ch.channel_no","operator"=>"="),
                'start_time'=> array("field"=>"ml.addtime","operator"=>">"),
                'subscribe' => array("field"=>"ou.subscribe","operator"=>"="),
                'end_time'  => array("field"=>"ml.addtime","operator"=>"<="),
                'utype' => array("field"=>"ou.utype","operator"=>"="),
                );

		if(!isset($_REQUEST['start_time']) || $_REQUEST['start_time']=='' || !isset($_REQUEST['end_time']) || $_REQUEST['end_time']==''){
			$_GET['start_time'] = date("Y-m-d")." 00:00";
			$_GET['end_time'] = date("Y-m-d")." 23:59";
		}
        if(IS_POST){
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_POST[$param];
                    $_GET[$param]=$get;
                    if($param == 'start_time'){
                        $get = $get.":00";
                    }elseif($param == 'end_time'){
                        $get = $get.":59";
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
                    if($param == 'start_time'){
                        $get = $get.":00";
                    }elseif($param == 'end_time'){
                        $get = $get.":59";
                    }
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }

        $where_ands['ml.utype'] = array("not in","0");
        $where_ands['ac.utype'] = array("not in","0");
        $login_model=M('MemberLogin','mh_',C('DB_CONFIG2'));
        $counts=$login_model->alias("ml")
            ->join("LEFT JOIN __OAUTH_USER__ ou ON ml.user_id=ou.user_id")
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.user_id=ml.user_id")
            ->join("LEFT JOIN __CHANNEL__ ch ON ch.id=ou.channel_id")
            ->where($where_ands)
            //->group("ml.user_id")
            ->select();
        $count = count($counts);
        $page = $this->page($count, 20);
        $lists = $login_model->alias("ml")
            ->join("LEFT JOIN __OAUTH_USER__ ou ON ml.user_id=ou.user_id")
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.user_id=ml.user_id")
            ->join("LEFT JOIN __CHANNEL__ ch ON ch.id=ou.channel_id")
            ->where($where_ands)
            ->field("ou.*,ml.addtime,ch.channel_no,ac.recharge_total,ac.use_coin,ac.award_use_coin")
            ->order("ml.addtime desc")
            //->group("ml.user_id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        foreach($lists as &$v){
            //充值次数
            $_condition = array("user_id"=>$v['user_id'],"utype"=>$v['utype'],"status"=>1);
            $v['recharge_counts'] = M("AccountRecharge")->where($_condition)->count();
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign('lists', $lists);
        if($assign == 1){
            $this->assign("formget",$_GET);
        }else{
            $this->assign("formget",$_POST);
        }
        $this->assign("total",$count);
        $this->display();
    }

	//删除用户
	function delete(){
		$id=intval($_GET['id']);
		if(empty($id)){
			$this->error('非法数据！');
		}
		$rst = M("OauthUser")->where(array("user_id"=>$id))->delete();
		if ($rst!==false) {
			$this->success("删除成功！", U("oauthadmin/index"));
		} else {
			$this->error('删除失败！');
		}
	}

    public function oauthExcel(){
        $where_ands=array("ou.status"=>0);
        $fields=array(
                'username' => array("field"=>"ou.name","operator"=>"like"),
                'user_id' => array("field"=>"ou.user_id","operator"=>"="),
                'sex' => array("field"=>"ou.sex","operator"=>"="),
                'vip' => array("field"=>"ou.vip","operator"=>"="),
                'channel_no' => array("field"=>"ch.channel_no","operator"=>"="),
                'start_time'=> array("field"=>"ou.create_time","operator"=>">="),
                'end_time'  => array("field"=>"ou.create_time","operator"=>"<="),
                'utype' => array("field"=>"ou.utype","operator"=>"="),
                'subscribe' => array("field"=>"ou.subscribe","operator"=>"="),
                );
        if(!isset($_REQUEST['start_time']) || $_REQUEST['start_time']=='' || !isset($_REQUEST['end_time']) || $_REQUEST['end_time']==''){
            $_REQUEST['start_time'] = date("Y-m-d")." 00:00";
            $_REQUEST['end_time'] = date("Y-m-d")." 23:59";
        }
		foreach ($fields as $param =>$val){
			if (isset($_REQUEST[$param]) && $_REQUEST[$param]!='') {
				$operator=$val['operator'];
				$field =$val['field'];
				$get=$_REQUEST[$param];
				if($param == 'start_time'){
					$get = $get.":00";
				}elseif($param == 'end_time'){
					$get = $get.":59";
				}
				if($operator=="like"){
					$get="%$get%";
				}
				array_push($where_ands, "$field $operator '$get'");
			}
		}

        $oauth_user_model=M('OauthUser','mh_',C('DB_CONFIG2'));
        $where_ands['ac.utype'] = array("not in","0");
        $lists = $oauth_user_model->alias("ou")
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.user_id=ou.user_id")
            ->join("LEFT JOIN __CHANNEL__ ch ON ch.id=ou.channel_id")
            ->where($where_ands)
            ->field("ou.*,ch.channel_no,ac.recharge_total,ac.use_coin,ac.award_use_coin")
            ->order("ou.create_time DESC")
            ->select();
		$hb = array();
		$_sex = array("未知","男","女");
        $_subscribe = array("未关注","已关注");
        foreach($lists as $v){
            //充值次数
            $_condition = array("user_id"=>$v['user_id'],"utype"=>$v['utype'],"status"=>1);
            $recharge_counts = M("AccountRecharge")->where($_condition)->count();
			
			$put = array(
				'user_id' => $v['user_id'],
                'username' => base64_encode($v['name']),
				'from' => $v['from'],
				'channel_no' => $v['channel_no'],
                'subscribe' => $_subscribe[$v['subscribe']],
				'recharge_total' => sprintf("%.2f", $v['recharge_total']).'元',
				'coin' => $v['use_coin']+$v['award_use_coin']."金币",
				'recharge_counts' => $recharge_counts."次",
				'sex' => $_sex[$v['sex']],
				'create_time' => $v['create_time'],
				'last_login_time' => $v['last_login_time'],
				'last_login_ip' => $v['last_login_ip'],
				'login_times' => $v['login_times']."次"
			);
			$hb[] = $put;
        }

        //设置内存占用
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        //为fputcsv()函数打开文件句柄
        $output = fopen('php://output', 'w') or die("can't open php://output");
        //告诉浏览器这个是一个csv文件
        $filename = "三方用户统计" . date('Y-m-d', time());
        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=$filename.csv");
        //表头数组
		$tableheader = array('用户ID','用户名','来源','渠道号','是否关注','充值金额','金币余额','充值次数','性别','注册时间','最后登录时间','最后登录IP','登录次数');
		$tableheader = eval('return '.iconv('utf-8','gbk',var_export($tableheader,true)).';'); //数组转码gbk
        fputcsv($output, $tableheader);

        foreach ($hb as $e) {
			$e = eval('return '.iconv('utf-8','gbk',var_export($e,true)).';');
            //输出内容
            fputcsv($output, $e);
        }

        fclose($output) or die("can't close php://output");  
        exit;
    }

    public function oauthloginExcel(){
        $where_ands=array("ou.status"=>0);
        $fields=array(
                'username' => array("field"=>"ou.name","operator"=>"like"),
                'user_id' => array("field"=>"ou.user_id","operator"=>"="),
                'sex' => array("field"=>"ou.sex","operator"=>"="),
                'vip' => array("field"=>"ou.vip","operator"=>"="),
                'channel_no' => array("field"=>"ch.channel_no","operator"=>"="),
                'start_time'=> array("field"=>"ml.addtime","operator"=>">"),
                'end_time'  => array("field"=>"ml.addtime","operator"=>"<="),
                'utype' => array("field"=>"ou.utype","operator"=>"="),
                'subscribe' => array("field"=>"ou.subscribe","operator"=>"="),
                );

        if(!isset($_REQUEST['start_time']) || $_REQUEST['start_time']=='' || !isset($_REQUEST['end_time']) || $_REQUEST['end_time']==''){
            $_GET['start_time'] = date("Y-m-d")." 00:00";
            $_GET['end_time'] = date("Y-m-d")." 23:59";
        }
        foreach ($fields as $param =>$val){
            if (isset($_REQUEST[$param]) && $_REQUEST[$param]!='') {
                $operator=$val['operator'];
                $field =$val['field'];
                $get=$_REQUEST[$param];
                if($param == 'start_time'){
                    $get = $get.":00";
                }elseif($param == 'end_time'){
                    $get = $get.":59";
                }
                if($operator=="like"){
                    $get="%$get%";
                }
                array_push($where_ands, "$field $operator '$get'");
            }
        }

        $where_ands['ml.utype'] = array("not in","0");
        $where_ands['ac.utype'] = array("not in","0");
        $login_model=M('MemberLogin','mh_',C('DB_CONFIG2'));
        $lists = $login_model->alias("ml")
            ->join("LEFT JOIN __OAUTH_USER__ ou ON ml.user_id=ou.user_id")
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.user_id=ml.user_id")
            ->join("LEFT JOIN __CHANNEL__ ch ON ch.id=ou.channel_id")
            ->where($where_ands)
            ->field("ou.*,ml.addtime,ml.addip,ch.channel_no,ac.recharge_total,ac.use_coin,ac.award_use_coin")
            ->order("ml.addtime desc")
            //->group("ml.user_id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $hb = array();
        $_sex = array("未知","男","女");
        $_subscribe = array("未关注","已关注");
        foreach($lists as $v){
            //充值次数
            $_condition = array("user_id"=>$v['user_id'],"utype"=>$v['utype'],"status"=>1);
            $recharge_counts = M("AccountRecharge")->where($_condition)->count();

            $put = array(
                'user_id' => $v['user_id'],
                'username' => base64_encode($v['name']),
                'from' => $v['from'],
                'channel_no' => $v['channel_no'],
                'subscribe' => $_subscribe[$v['subscribe']],
                'recharge_total' => sprintf("%.2f", $v['recharge_total']).'元',
                'coin' => $v['use_coin']+$v['award_use_coin']."金币",
                'recharge_counts' => $recharge_counts."次",
                'sex' => $_sex[$v['sex']],
                'addtime' => $v['addtime'],
                'login_times' => $v['login_times']."次"
            );
            $hb[] = $put;
        }

        //设置内存占用
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        //为fputcsv()函数打开文件句柄
        $output = fopen('php://output', 'w') or die("can't open php://output");
        //告诉浏览器这个是一个csv文件
        $filename = "三方登录统计" . date('Y-m-d', time());
        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=$filename.csv");
        //表头数组
        $tableheader = array('用户ID','用户名','来源','渠道号','是否关注','充值金额','金币余额','充值次数','性别','登录时间','登录次数');
        $tableheader = eval('return '.iconv('utf-8','gbk',var_export($tableheader,true)).';'); //数组转码gbk
        fputcsv($output, $tableheader);

        foreach ($hb as $e) {
            $e = eval('return '.iconv('utf-8','gbk',var_export($e,true)).';');
            //输出内容
            fputcsv($output, $e);
        }

        fclose($output) or die("can't close php://output");
        exit;
    }
}
