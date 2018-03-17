<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class UserController extends AdminbaseController{
	protected $users_model,$role_model,$member_obj;
	
	function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
		$this->member_obj = D("Common/Member");
		$this->role_model = D("Common/Role");
	}
	function index(){
		$count=$this->users_model->where(array("user_type"=>1))->count();
		$page = $this->page($count, 20);
		$users = $this->users_model
		->where(array("user_type"=>1))
		->order("create_time DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();
		
		$roles_src=$this->role_model->select();
		$roles=array();
		foreach ($roles_src as $r){
			$roleid=$r['id'];
			$roles["$roleid"]=$r;
		}
		$this->assign("page", $page->show('Admin'));
		$this->assign("roles",$roles);
		$this->assign("users",$users);
		$this->display();
	}
	
    public function ordinary(){
        $where_ands = array();
        $fields = array(
            'user_id' => array("field"=>"m.user_id","operator"=>"="),
            'phone' => array("field"=>"m.phone","operator"=>"="),
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
        $count=M("Member")->count();
        $page = $this->page($count, 20);
        $users = M("Member")->alias('m')
            ->join("LEFT JOIN __ACCOUNT__ ac ON ac.uid=m.user_id")
            ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=m.user_id")
            ->field("m.*,ac.total_money,ac.use_money,ac.no_use_money,ac.award_total_money,ac.award_use_money,ac.award_no_use_money,mb.real_name")
            ->order("createtime DESC")
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach($users as &$v){
            if($v['total_money']>0) $v['is_recharge'] = 1;
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("users",$users);
        $this->assign("current_page",$page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        if($assign==1){
            $this->assign("formget",$_GET);
        }else{
            $this->assign("formget",$_POST);
        }
        $this->display();
    }

	function add(){
		$roles=$this->role_model->where("status=1")->order("id desc")->select();
		$this->assign("roles",$roles);
		$this->display();
	}

    function add_ord(){
        $this->display();
    }
	
	function add_post(){
		if(IS_POST){
			if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
				$role_ids=$_POST['role_id'];
				unset($_POST['role_id']);
                if(strpos($_POST['user_login'],"@")>0){//邮箱登陆
                    $_POST['user_email']=$_POST['user_login'];
                }
				if ($this->users_model->create($_POST)) {
					$result=$this->users_model->add($_POST);
					if ($result!==false) {
						$role_user_model=M("RoleUser");
						foreach ($role_ids as $role_id){
							$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$result));

                            //role name
                            $role_name[] = M("Role")->where(array('id'=>$role_id))->getField("name");
						}
                        $msg = '添加管理员成功,管理员ID为{'.$result.'}';
                        $this->logs($msg,$result,1);

                        if(is_array($role_name) && !empty($role_name)){
                            $rname = implode("&",$role_name);
                            $msg = '添加管理员权限成功,管理员ID为{'.$result.'},权限为:'.$rname;
                            $this->logs($msg,$result,2);
                        }
						$this->success("添加成功！", U("user/index"));
					} else {
						$this->error("添加失败！");
					}
				} else {
					$this->error($this->users_model->getError());
				}
			}else{
				$this->error("请为此用户指定角色！");
			}
			
		}
	}

    function add_post_ord(){
        if(IS_POST){
			if ($this->member_obj->create()) {
                $_POST['own_invitecode'] = self::own_invitecode();
                $_POST['user_status'] = 1;
                $_POST['app_devicetype'] = 0;
				$result=$this->member_obj->add($_POST);
				if ($result!==false) {
                    $msg = '添加普通用户成功,用户ID为{'.$result.'}';
                    $this->logs($msg,$result,1);
					$this->success("添加成功！", U("user/ordinary"));
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error($this->member_obj->getError());
			}
        }
    }
	
	
	function edit(){
		$id= intval(I("get.id"));
		$roles=$this->role_model->where("status=1")->order("id desc")->select();
		$this->assign("roles",$roles);
		$role_user_model=M("RoleUser");
		$role_ids=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
		$this->assign("role_ids",$role_ids);
			
		$user=$this->users_model->where(array("id"=>$id))->find();
		$this->assign($user);
		$this->display();
	}

    function edit_ord(){
        $id= intval(I("get.id"));
        $user=$this->member_obj->where(array("user_id"=>$id))->find();
        $this->assign($user);
        $this->display();
    }
	
	function edit_post(){
		if (IS_POST) {
			if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
				if(empty($_POST['user_pass'])){
					unset($_POST['user_pass']);
				}
				$role_ids=$_POST['role_id'];
				unset($_POST['role_id']);
				if ($this->users_model->create()) {
					$result=$this->users_model->save();
					if ($result!==false) {
						$uid=intval($_POST['id']);
						$role_user_model=M("RoleUser");
						$role_user_model->where(array("user_id"=>$uid))->delete();
						foreach ($role_ids as $role_id){
							$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$uid));

                            $role_name[] = M("Role")->where(array('id'=>$role_id))->getField("name");
						}
                        $msg = '修改管理员成功,管理员ID为{'.$uid.'}';
                        $this->logs($msg,$uid,1);

                        if(is_array($role_name) && !empty($role_name)){
                            $rname = implode("&",$role_name);
                            $msg = '修改管理员权限成功,管理员ID为{'.$uid.'},权限为:'.$rname;
                            $this->logs($msg,$uid,2);
                        }
                        $this->success("保存成功！");
					} else {
						$this->error("保存失败！");
					}
				} else {
					$this->error($this->users_model->getError());
				}
			}else{
				$this->error("请为此用户指定角色！");
			}
			
		}
	}

    function edit_post_ord(){
        if (IS_POST) {
			if(empty($_POST['password'])){
				unset($_POST['password']);
			}
			if ($this->member_obj->create()) {
				$result=$this->member_obj->save();
                $ur = $this->member_obj->where(array("user_id"=>$_POST['user_id']))->find();
                if($this->cache->get($ur['app_token'])){
                    $_ur = $this->member_obj->where(array("user_id"=>$_POST['user_id']))->find();
                    $this->cache->set($ur['app_token'],$_ur,3600*24*7);
                }
                $msg = '修改普通用户成功,用户ID为{'.$_POST['user_id'].'}';
                $this->logs($msg,$_POST['user_id'],1);
				if ($result!==false) {
					$this->success("保存成功！");
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->member_obj->getError());
			}
        }
    }
	
	/**
	 *  删除
	 */
	function delete(){
		$id = intval(I("get.id"));
		if($id==1){
			$this->error("最高管理员不能删除！");
		}
		
		if ($this->users_model->where("id=$id")->delete()!==false) {
            M("RoleUser")->where(array("user_id"=>$id))->delete();
            $msg = '删除管理员成功,管理员ID为{'.$id.'}';
            $this->logs($msg,$id,1);

            $msg = '删除管理员权限成功,管理员ID为{'.$id.'}';
            $this->logs($msg,$id,2);
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

    function mdelete(){
        $id = intval(I("get.id"));
        $ck = $this->member_obj->where("user_id=$id")->find();
        if ($this->member_obj->where("user_id=$id")->delete()!==false) {
            $var = array('Base','Bank','Credit','Addressbook','Operator','Taobao','Alipay','Idcardinfo');
            $condition = array('uid'=>$id);
            foreach($var as $v){
                if( M("Member".$v)->where($condition)->find() ){
                    M("Member".$v)->where($condition)->delete();
                }
            }
            if($this->cache->get($ck['app_token'])){
                $this->cache->rm($ck['app_token']);
            }
            M("OauthUser")->where(array('uid'=>$id))->delete();
            $msg = '删除普通用户成功,用户ID为{'.$id.'}';
            $this->logs($msg,$id,1); 
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }
	
	
	function userinfo(){
		$id=get_current_admin_id();
		$user=$this->users_model->where(array("id"=>$id))->find();
		$this->assign($user);
		$this->display();
	}
	
	function userinfo_post(){
		if (IS_POST) {
			$_POST['id']=get_current_admin_id();
			$create_result=$this->users_model
			->field("user_login,user_email,last_login_ip,last_login_time,create_time,user_activation_key,user_status,role_id,score,user_type",true)//排除相关字段
			->create();
			if ($create_result) {
				if ($this->users_model->save()!==false) {
					$this->success("保存成功！");
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->users_model->getError());
			}
		}
	}
	
    function ban(){
        $id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->users_model->where(array("id"=>$id))->setField('user_status','0');
    		if ($rst) {
                $msg = '锁定管理员成功,管理员ID为{'.$id.'}';
                $this->logs($msg,$id,1);  
    			$this->success('管理员锁定成功！', U("user/index"));
    		} else {
    			$this->error('管理员锁定失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
   
	function mban(){
        $id=intval($_GET['id']);
        if ($id) {
            $app_token = $this->member_obj->where(array("user_id"=>$id))->getField("app_token");
            $save = array('user_status'=>0,'app_token'=>'','token_timeout'=>'');
            $rst = $this->member_obj->where(array("user_id"=>$id))->save($save);
            if ($rst!==false) {
                $ur = $this->member_obj->where(array("user_id"=>$id))->find();
                if($app_token){
                    if($this->cache->get($app_token)) $this->cache->rm($app_token);
                }
                $msg = '锁定普通用户成功,用户ID为{'.$id.'}';
                $this->logs($msg,$id,1); 
                $this->success('普通用户锁定成功！', U("user/ordinary"));
            } else {
                $this->error('普通用户锁定失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    function cancelban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->users_model->where(array("id"=>$id))->setField('user_status','1');
    		if ($rst) {
                $msg = '解锁管理员成功,管理员ID为{'.$id.'}';
                $this->logs($msg,$id,1);  
    			$this->success('管理员解锁成功！', U("user/index"));
    		} else {
    			$this->error('管理员解锁失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

    function mcancelban(){
        $id=intval($_GET['id']);
        if ($id) {
            $rst = $this->member_obj->where(array("user_id"=>$id))->setField('user_status','1');
            if ($rst) {
                $msg = '解锁普通用户成功,用户ID为{'.$id.'}';
                $this->logs($msg,$id,1); 
                $this->success('普通用户解锁成功！', U("user/ordinary"));
            } else {
                $this->error('普通用户解锁失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }	

    protected function own_invitecode(){
        $own_invitecode = rand_string(5,0,'123456789');
        $ck = M("Member")->where(array('own_invitecode'=>$own_invitecode))->find();
        if($ck){
            self::own_invitecode();
        }else{
            return $own_invitecode;
        }
    }
	
    public function ordinaryExcel(){
        $where_ands = array();
        $fields = array(
            'user_id' => array("field"=>"m.user_id","operator"=>"="),
            'phone' => array("field"=>"m.phone","operator"=>"="),
        );
            foreach ($fields as $param =>$val){
                if (isset($_REQUEST[$param]) && $_REQUEST[$param] !='') {
                    $operator=$val['operator'];
                    $field =$val['field'];
                    $get=$_REQUEST[$param];
                    if($operator=="like"){
                        $get="%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }

        $count=M("Member")->count();
        $page = $this->page($count, 20);
        $users = M("Member")->alias('m')
            ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=m.user_id")
            ->field("m.*,mb.real_name")
            ->order("createtime DESC")
            ->where($where_ands)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $hb = array();
        foreach($users as $v){
            $put = array(
                'user_id' => $v['user_id'],
                'real_name' => $v['real_name'],
                'phone' => $v['phone'],
                'ip' => $v['last_login_ip'],
                'last_time' => $v['last_login_time'],
                'createtime' => $v['createtime'],
                'user_type' => '会员',
                'status' => ($v['user_status']==0)?'锁定':'正常',
                'black' => ($v['user_status']==0)?'是':'否',
            );
            $hb[] = $put;
        }

        vendor("PHPExcel.Classes.PHPExcel#class");
        $excel = new \PHPExcel();
        $letter = array('A','B','C','D','E','F','G','H','I');

        $tableheader = array('用户ID','用户姓名','手机号','最后登录IP','最后登录时间','注册时间','用户类型','用户状态','是否黑名单');
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
            $excel->getActiveSheet()->getStyle("$letter[$i]")->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        }

        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20); 
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20); 
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(15); 

        for ($i = 2;$i <= count($hb) + 1;$i++) {
            $j = 0;
            foreach ($hb[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        header('Content-Disposition: attachment;filename="用户列表'.time().'.xlsx"');  
        header('Cache-Control: max-age=0');  
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
