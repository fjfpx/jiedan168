<?php
namespace Comment\Controller;
use Common\Controller\AdminbaseController;
class CommentadminController extends AdminbaseController{
	
	protected $comments_model;
	
	function _initialize(){
		parent::_initialize();
		$this->comments_model=D("Common/ItemComment");
	}

	public function index(){
		$where_ands=array(
            'i.delete_status' => 0
        );

        $fields=array(
                //'username' => array("field"=>"m.username","operator"=>"like"),
                //'user_type' => array("field"=>"**","operator"=>"like"),
                'title'=> array("field"=>"i.title","operator"=>"like"),
                'start_time'=> array("field"=>"acl.addtime","operator"=>">="),
                'end_time'  => array("field"=>"acl.addtime","operator"=>"<="),
        );

        /*if(isset($_REQUEST['user_type']) && $_REQUEST['user_type']!=''){
            if($_REQUEST['user_type'] == 1){
                $join = "LEFT JOIN __MEMBER__ m ON m.user_id=ict.user_id";
                $where_ands['ict.utype'] = 0;
            }elseif($_REQUEST['user_type'] == 2){
                $fields['username'] = array("field"=>"ou.name","operator"=>"like");
                $join = "LEFT JOIN __OAUTH_USER__ ou ON ou.user_id=ict.user_id";
                $where_ands['ict.utype'] = array("gt",0);
            }
        }else{
            $fields['username'] = array("field"=>"ou.name","operator"=>"like");
            $join = "LEFT JOIN __OAUTH_USER__ ou ON ou.user_id=ict.user_id";
            $where_ands['ict.utype'] = array("gt",0);
        }*/

        if(IS_POST){
            foreach ($fields as $param =>$val){
                if (isset($_POST[$param]) && $_POST[$param] !='') {
                    if($param=='user_type'){
                        $get=trim($_POST[$param]);
                        $_GET[$param]=$get;
                    }else{
                        $operator=$val['operator'];
                        $field =$val['field'];
                        $get=trim($_POST[$param]);
                        $_GET[$param]=$get;
                        if($param == 'start_time'){
                            $get = $get." 00:00:00";
                        }elseif($param == 'end_time'){
                            $get = $get." 23:59:59";
                        }
                        if($operator=="like"){
                            $get="%$get%";
                        }
                        array_push($where_ands, "$field $operator '$get'");
                    }
                }
            }
        }else{
            $assign = 1;
            foreach ($fields as $param =>$val){
                if (isset($_GET[$param]) && $_GET[$param]!='') {
                    if($param!='user_type'){
                        $operator=$val['operator'];
                        $field =$val['field'];
                        $get=trim($_GET[$param]);
                        if($param == 'start_time'){
                            $get = $get." 00:00:00";
                        }elseif($param == 'end_time'){
                            $get = $get." 23:59:59";
                        }
                        if($operator=="like"){
                            $get="%$get%";
                        }
                        array_push($where_ands, "$field $operator '$get'");
                    }
                }
            }
        }

		$count=$this->comments_model->alias("ict")
            ->join("LEFT JOIN __ITEM__ i ON i.id=ict.item_id")
			//->join($join)
            ->where($where_ands)->count();
		$page = $this->page($count, 20);
		$comments=$this->comments_model->alias("ict")
            ->join("LEFT JOIN __ITEM__ i ON i.id=ict.item_id")
			//->join($join)
            ->where($where_ands)
            ->field("ict.*,i.title")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("addtime DESC")
            ->select();
        foreach($comments as &$v){
            if($v['utype']>0){
                $mo = M('OauthUser','mh_',C('DB_CONFIG2'));
                $condition = array("user_id"=>$v['user_id'],"utype"=>$v['utype']);
            }else{
                $mo = M('Member','mh_',C('DB_CONFIG2'));
                $condition = array("user_id"=>$v['user_id']);
            }

            $user = $mo->where($condition)->find();
            $v['username'] = $user['username']?$user['username']:$user['name'];
        }
		$this->assign("comments",$comments);
		$this->assign("Page", $page->show('Admin'));
		if($assign==1){
			$this->assign("formget",$_GET);
		}else{
			$this->assign("formget",$_POST);
		}
		$this->display(":index");
	}

	public function detail(){
        $id = trim(I("id"));
        $content = $this->comments_model->alias("ict")
            ->join("LEFT JOIN __ITEM__ i ON i.id=ict.item_id")
            ->field("ict.*,i.title")
            ->where(array("ict.id"=>$id))->find();

        $result = M("ItemCommentCover")->alias("icc")
            ->where(array("comment_id"=>$id,"cover_type"=>0))
            ->order(array("addtime" => "DESC"))
            ->select();

        //拿到三级评论
        foreach($result as $val){
            $three = M("ItemCommentCover")->where(array("comment_id"=>$val['id'],"cover_type"=>1))->order("addtime desc")->select();
            foreach($three as $k=>$v){
                $count = count($three);
                if($count >0){
                    $v['spaces'] = '└─';
                    if(($k+1 == $count)){
                        $v['spacer'] = '&nbsp;&nbsp;&nbsp;└─ ';
                    }else{
                        $v['spacer'] = '&nbsp;&nbsp;&nbsp;├─ ';
                    }
                }
                $result[] = $v;
            }
        }
        import("Tree");
        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        $newmenus=array();
        foreach ($result as &$m){
            if($m['utype']>0){
                $mo = M('OauthUser','mh_',C('DB_CONFIG2'));
                $condition = array("user_id"=>$m['user_id'],"utype"=>$m['utype']);
            }else{
                $mo = M('Member','mh_',C('DB_CONFIG2'));
                $condition = array("user_id"=>$m['user_id']);
            }

            $mb = $mo->where($condition)->find();
            $m['username'] = $mb['username']?$mb['username']:$mb['name'];
            $m['obj_name'] = $content['content'];

        }

        foreach ($result as $n=> $r) {
            $result[$n]['str_manage'] = '<a class="js-ajax-delete" href="' . U("commentadmin/delete", array("id" => $r['id'], "cover_type" => $r['cover_type']) ). '">'.L('DELETE').'</a> ';
        }
        $tree->init($result);
        $str = "<tr id='node-\$id'>
                    <td style='padding-left:20px;'>\$spaces\$id</td>
                    <td>\$spacer\$username</td>
                    <td>\$spacer\$content</td>
                    <td>\$support</td>
                    <td>\$addtime</td>
                    <td>\$str_manage</td>
                </tr>";
        $categorys = $tree->get_tree(0, $str);
        $this->assign("detail",$content);
        $this->assign("categorys", $categorys);
        $this->display(":detail");
	}

	function delete(){
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			if ($this->comments_model->where("id=$id")->delete()!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if ($this->comments_model->where("id in ($ids)")->delete()!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
	
	function check(){
		if(isset($_POST['ids']) && $_GET["check"]){
			$data["status"]=1;
				
			$ids=join(",",$_POST['ids']);
			
			if ( $this->comments_model->where("id in ($ids)")->save($data)!==false) {
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["uncheck"]){
				
			$data["status"]=0;
			$ids=join(",",$_POST['ids']);
			if ( $this->comments_model->where("id in ($ids)")->save($data)!==false) {
				$this->success("取消审核成功！");
			} else {
				$this->error("取消审核失败！");
			}
		}
	}
	
}
