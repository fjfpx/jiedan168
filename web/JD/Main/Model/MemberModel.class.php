<?php
namespace Main\Model;
use Think\Model;
class MemberModel extends Model {

    public function getOne($data){
        $ukeys = array('user_id', 'app_token', 'username','phone');
        $has_ukey = false;
        foreach($data as $k => $v){
            if( in_array( $k, $ukeys) ){
                $has_ukey = true;
            }
        }
        if($has_ukey){
            $userM = M('Member');
            $ulist = $userM->where($data)->select();
            $u = $ulist[0];

            return $u;
        }else{
            return null;
        }
    }

    public function checkLog($params){
        if(isset($params['uid']) && $params['uid']!=''){
            $condition['uid'] = trim($params['uid']);
        }
        $start_time = date("Y-m-d")." 00:00:00";
        $end_time = date("Y-m-d")." 23:59:59";

        $condition['addtime'] = array("between",array($start_time,$end_time));

        $result = M('MemberLogin')->field("id")->where($condition)->select();

        if($result){
            $params['id'] = $result[0]['id'];
        }
        self::addLoginLog($params);
    }

    public function addLoginLog($params){
        $ulM = M('MemberLogin');
        if(isset($params['id']) && $params['id']!=''){
            $ulM->save($params);
        }else{
            $ulM->add($params);
        }
    }

    public function getShareList($params){
        if(!$params['uid']) return;
        $page = empty($params['p'])?1:$params['p'];
        $epage = empty($params['epage'])?8:$params['epage'];
        $limit = " ".($epage*($page-1)).",".$epage;

        $condition = array(
            'from_uid' => $params['uid'],
            'type' => 0,
            'ctype' => 1
        );

        $total = M("Coupon")->where($condition)->count();
        $total_page = ceil($total / $epage);
        $list = M("Coupon")->field("id,status,uid,endtime")
            ->where($condition)->limit($limit)->order("status,addtime")->select();

        //如有过期,更新券状态
        foreach($list as $k=>&$v){
            if($v['status'] == 0){
                if(time() > $v['endtime']){
                    M("Coupon")->where(array('id'=>$v['id']))->save(array('status'=>2));
                    $v['status'] = "2";
                }
            }
            $inviteuser = $this->where(array('user_id'=>$v['uid']))->getField("phone");
            $v['invite_user'] = substr_replace($inviteuser,'****',3,4);

            unset($v['uid'],$v['id'],$v['endtime']);
        }

        $getcoupons = M("Coupon")->where(array('from_uid'=>$params['uid'],'ctype'=>1,'status'=>1))->count();
        $ret = array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'epage' => $epage,
            'total_page' => $total_page,
            'coupons' => $getcoupons?$getcoupons:'0',
            );
        return $ret;
    }

    public function checkUsers($params){
        $condition = array(
            'mb.uid' => $params['user_id'],
            'm.phone' => $params['phone']
        );

        if(isset($params['idcard']) && $params['idcard']!=''){
            $condition['mb.idcard'] = $params['idcard'];
        }

        if(isset($params['real_name']) && $params['real_name']!=''){
            $condition['mb.real_name'] = $params['real_name'];
        }

        return $this->alias('m')
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=m.user_id")
                ->field("m.user_id")
                ->where($condition)
                ->find();
    }
}

?>
