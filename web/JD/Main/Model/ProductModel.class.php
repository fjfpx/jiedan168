<?php
namespace Main\Model;
use Think\Model;
class ProductModel extends Model {

    public function getLists($params){
        $page = empty($params['p'])?1:$params['p'];
        $epage = empty($params['epage'])?8:$params['epage'];
        $limit = " ".($epage*($page-1)).",".$epage;

        $condition = array(
            'del_status' => 0,
            'status' => 1
        ); 

        $total = $this->where($condition)->count();

        $total_page = ceil($total / $epage);

        $field = 'id,title,min_money,max_money,min_date,max_date,rate,rate_type,total,fast_time,desc';
        $list = $this->where($condition)->limit($limit)->order("listorder desc,addtime desc")->field($field)->select();
        foreach($list as &$v){
            $v['rate'] = ($v['rate']*100)."%";
        }
        $ret = array(
                'list' => $list,
                'total' => $total,
                'page' => $page,
                'epage' => $epage,
                'total_page' => $total_page,
                );
        return $ret;
    }

    public function getOne($id){
        if(isset($id) && $id!=''){
            $rst = $this->where(array('id'=>$id,'del_status'=>0,'status'=>1))->find();
            unset($rst['adduid'],$rst['del_status'],$rst['status'],$rst['listorder']);
            $rst['addtime'] = date('Y-m-d',$rst['addtime']);
            $rst['rate']  = ($rst['rate']*100)."%";
            return $rst;
        }
        return;
    }

    public function checkApplication($params){
        $start_time = strtotime(date('Y-m-d')." 00:00:00");
        $end_time = strtotime(date('Y-m-d')." 23:59:59" );
        $params['addtime'] = array('between',array($start_time,$end_time));

        return M("ProductUser")->where($params)->find();
    }

    public function checkInfo($uid){
        $condition = array(
            'mb.uid' => $uid,
            'mb.real_status' => 1,
            'mb.info_status' => 1,
            'mk.verify_status' => 1,
            'mo.status' => 1
        );

        return M("MemberBase")->alias('mb')
            ->join("LEFT JOIN __MEMBER_BANK__ mk ON mk.uid=mb.uid")
            ->join("LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=mb.uid")
            ->where($condition)
            ->find();

    }
}

?>
