<?php
namespace Api\Controller;
use Think\Controller;
class CjdController extends Controller {

    public function cronrun() {
        $p = new \Portal\Lib\CjdClass();
		$list = M("Report")->where(array('rstatus'=>1,'pstatus'=>0))->field("orderid")->select();
        if($list){
            foreach($list as $v){
                $rst = $p->orderInfo($v['orderid']);
                $result = json_decode($rst,true);
                if($result && $result['info']['status']==1){
                    if($result['info']['orderStatus']==2){
                        $save = array(
                            'msg' => $result['info']['message'],
                            'buytime' => time(),
                            'pstatus' => 1,
                        );
                        M("Report")->where(array('rstatus'=>1,'orderid'=>$v['orderid']))->save($save);
                    }else{
                        if($result['info']['orderStatus']!=3){
                            $save = array(
                                'msg' => $result['info']['message'],
                                'pstatus' => 2,
                            );
                            M("Report")->where(array('rstatus'=>1,'orderid'=>$orderid))->save($save);
                        }
                    }
                }
            }
        }
    }


}

