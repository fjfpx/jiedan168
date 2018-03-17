<?php

/**
 * 会员
 */
namespace User\Controller;
use Common\Controller\AdminbaseController;
class IndexadminController extends AdminbaseController {
    function index(){
    	$users_model=M("Member");
        $where_ands = array();
		$fields=array(
                'username' => array("field"=>"m.username","operator"=>"like"),
				'user_id'=> array("field"=>"m.user_id","operator"=>"="),
				'black'  => array("field"=>"m.user_status","operator"=>"="),
		);
		if(IS_POST){
			foreach ($fields as $param =>$val){
				if (isset($_POST[$param]) && $_POST[$param] !='') {
					$operator=$val['operator'];
					$field =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
                    if($param=='black' && $get==2){
                        $field = "m.real_status";
                        $get = 1;
                    }
					if($operator=="like"){
                        $get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}else{
			foreach ($fields as $param =>$val){
				if (isset($_GET[$param]) && $_GET[$param]!='') {
					$operator=$val['operator'];
					$field =$val['field'];
					$get=$_GET[$param];
                    if($param=='black' && $get==2){
                        $field = "m.real_status";
                        $get = 1;
                    }
					if($operator=="like"){
                        $get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
    	$count=$users_model->alias('m')
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.uid=m.user_id")
            ->where($where_ands)->count();
    	$page = $this->page($count, 20);
    	$lists = $users_model->alias('m')
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.uid=m.user_id")
            ->where($where_ands)
            ->field("m.*,ac.total_money,ac.use_money,ac.no_use_money,ac.freeze_money,ac.award_total_money,ac.award_use_money,ac.award_no_use_money,ac.award_freeze_money")
            ->order("m.create_time DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign("formget",$_POST);
    	$this->display(":index");
    }

    public function add(){
        $this->display(":add");
    }

    public function add_post(){
        $arr = array('username','password','company','phone');
        foreach($arr as $v){
            if(isset($_POST[$v]) && $_POST[$v]!=''){
                $data[$v] = trim($_POST[$v]);
            }
        }

        if(!isset($data['username']) OR $data['username']==''){
            $this->error("用户名不能为空");
        }elseif(!isset($data['password']) OR $data['password']==''){
            $this->error("密码不能为空");
        }elseif(!isset($data['company']) OR $data['company']==''){
            $this->error("公司名称不能为空");
        }elseif(!isset($data['phone']) OR $data['phone']==''){
            $this->error("电话不能为空");
        }else{
            $mM = M("Member");
            $check = $mM->where(array("username"=>$data['username']))->find();
            if($check){
                $this->error("用户已存在");
            }else{
				$data['user_status'] = 1;
				$data['user_type'] = 2;
				$data['app_devicetype'] = 0;
				$data['password'] = self::PwdMD5(md5(C("PWDKEY_PAY1PRE").$data['password']));
				$data['last_login_ip'] = get_client_ip();
				$data['create_time'] = date("Y-m-d H:i:s",time());

				$user_id = $mM->add($data);

				if($user_id){
					//创建账户
					self::createAccount($user_id);
					$this->success("添加用户成功");
				}else{
					$this->error("添加失败");
				}
            }
        }
    }

	protected static function PwdMD5($pwd){
		return md5( C('PWDKEY_PAY2PRE').$pwd );
	}

    protected static function createAccount($user_id){
        $userModel = M('Member');
        $sql_where = array("user_id"=>$user_id);
        $user = $userModel->where($sql_where)->select();
        if(!$user){
            return;
        }else{
            $account = M("Account")->where($sql_where)->select();
            if( !$account ){
                M("Account")->add(array("uid"=>$user_id));
            }
            return true;
        }
    }

    function ban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Member")->where(array("user_id"=>$id,"user_type"=>2))->setField('user_status','0');
    		if ($rst) {
    			$this->success("会员拉黑成功！", U("indexadmin/index"));
    		} else {
    			$this->error('会员拉黑失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Member")->where(array("user_id"=>$id,"user_type"=>2))->setField('user_status','1');
    		if ($rst) {
    			$this->success("会员启用成功！", U("indexadmin/index"));
    		} else {
    			$this->error('会员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    public function review(){
        $id = intval(I("id"));
        $op = intval(I("op"));
        $op = $op?$op:0;
        if($id){
            $_save['real_status'] = $op;
            $rst = M("Member")->where(array("user_id"=>$id,"user_type"=>2))->find();
            if(!$rst){
                $this->error("用户不存在");
            }else{
                if($_save['real_status'] == 1){
                    $msg = "关闭言论权限";
                }else{
                    $msg = "开启言论权限";
                }
                $result = M("Member")->where(array("user_id"=>$id))->save($_save);
                if($result!==false){
                    $msg .= "成功";
                    $this->success($msg);
                }else{
                    $msg .= "失败";
                    $this->error($msg);
                }
            }
        }else{
            $this->error("参数错误");
        }
    }
}
