<?php
namespace Main\Model;
use Think\Model;
class MessageModel extends Model {

    public function getMsgList($params){
        if(!$params['uid']) return;
        $page = empty($params['p'])?1:$params['p'];
        $epage = empty($params['epage'])?8:$params['epage'];
        $limit = " ".($epage*($page-1)).",".$epage;

        $condition = array(
            'mu.uid' => $params['uid'],
            'm.type' => array('in','0,1'),
            'm.status' => 1,
            'm.delete_status' => 0,
            'mu.delete_status' => 0,
        ); 

        if(isset($params['type']) && $params['type']!=''){
            if($params['type']==0 || $params['type']==1){
                $condition['m.type'] = $params['type'];
            }
        }

        $total = M("MessageUsers")->alias("mu")
            ->join("LEFT JOIN __MESSAGE__ m ON m.id=mu.mid")
            ->where($condition)->count();

        $total_page = ceil($total / $epage);

        $field = "m.content,m.title,m.type,m.addtime,mu.status,mu.mid,m.url";
        $list = M("MessageUsers")->alias("mu")
            ->join("LEFT JOIN __MESSAGE__ m ON m.id=mu.mid")
            ->field($field)
            ->where($condition)->limit($limit)->order("status,addtime")->select();

        $ret = array(
                'list' => $list,
                'total' => $total,
                'page' => $page,
                'epage' => $epage,
                'total_page' => $total_page,
                );
        return $ret;
    }

    public function addMsg($params){
        if( $this->create($params) ){
            return $this->add($params);
        }
        return;
    }

}

?>
