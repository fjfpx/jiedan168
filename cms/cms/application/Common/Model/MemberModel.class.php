<?php
namespace Common\Model;
use Common\Model\CommonModel;
class MemberModel extends CommonModel
{
	
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        //array('realname', 'require', '真实姓名不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT  ),
        //array('department', 'require', '部门不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT  ),
        array('username', 'require', '手机号不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT  ),
		array('password', 'require', '密码不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT ),

		//array('realname', 'require', '真实姓名不能为空！', 0, 'regex', CommonModel:: MODEL_UPDATE  ),
        //array('department', 'require', '部门不能为空！', 0, 'regex', CommonModel:: MODEL_UPDATE  ),
		array('password', 'require', '密码不能为空！', 0, 'regex', CommonModel:: MODEL_UPDATE  ),

		array('username','','手机号已经存在！',0,'unique',CommonModel:: MODEL_BOTH ), // 验证user_login字段是否唯一
        //array('user_email','','邮箱帐号已经存在！',0,'unique',CommonModel:: MODEL_BOTH ), // 验证user_email字段是否唯一
		//array('user_email','email','邮箱格式不正确！',0,'',CommonModel:: MODEL_BOTH ), // 验证user_email字段格式是否正确
	);
	
	protected $_auto = array(
	    array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
	);
	
	//用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
	function mGetDate() {
		return date('Y-m-d H:i:s');
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
        if($data['username']){
		    $data['phone'] = $data['username'];
        }
		if(!empty($data['password']) && strlen($data['password'])<25){
            $data['password']=sp_password($data['password']);
		}
	}
	
}

