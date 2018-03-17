<?php
namespace Main\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
use Library\BaseController;
use Think\Model;

class CbController extends BaseController {
  
    //超过5分钟,魔蝎报告判定失败
    public function verifyMoxie(){
        $lists = M("Moxie")->alias('mx')
            ->where(array('mx.report_status'=>0,'mx.login_status'=>1))
            ->join("LEFT JOIN __MEMBER__ m ON m.user_id=mx.uid")
            ->field("m.user_id,mx.submit_time,mx.type,mx.id")
            ->select();

        foreach($lists as $v){
            if($v['submit_time'] && $v['submit_time']!=''){
                $time = substr($v['submit_time'],0,10);
                if( time() - $time > 5*60 ){
                    if($v['type']==0){
                        $md = M("MemberOperator");
                    }elseif($v['type']==1){
                        $md = M("MemberTaobao");
                    }else{
                        $md = M("MemberAlipay");
                    }
                    $md->where(array('uid'=>$v['user_id']))->save(array('status'=>2));
                    M("Moxie")->where(array('id'=>$v['id']))->save(array('submit_time'=>'','login_status'=>0));
                }
            }
        }
    }

    public function reportLists( $type=0 ){
        //运营商:0, 淘宝:1, 支付宝:2
        $condition = array(
            'mx.type' => $type,
            'mx.report_status' => 1
        );
        if($type==0){
            $condition['mo.status'] = 3;
            $join = "LEFT JOIN __MEMBER_OPERATOR__ mo ON mo.uid=mx.uid";
        }elseif($type==1){
            $condition['mt.status'] = 3;
            $join = "LEFT JOIN __MEMBER_TAOBAO__ mt ON mt.uid=mx.uid";
        }else{
            $condition['ma.status'] = 3;
            $join = "LEFT JOIN __MEMBER_ALIPAY__ ma ON ma.uid=mx.uid";
        }
        return M("Moxie")->alias('mx')
            ->join("LEFT JOIN __MEMBER__ m ON m.user_id=mx.uid")
            ->join($join)
            ->field("m.phone,mx.task_id,mx.uid,mx.report_message,mx.id,mx.datainfo")
            ->where($condition)->select();
    }

    public function getReportData(){
        //获取运营商报告成功列表
        $lists = self::reportLists();
        $mx = new \Main\Lib\MoxieClass();
        foreach($lists as $v){
            if($v['phone']!=''){
                $rst = $mx->getCarrier(array(
                    'phone' => $v['phone'],
                    'task_id' => $v['task_id']
                ));

                $rst = json_decode($rst,true);
                if($rst && $rst['code']==0){
                    $save = array(
                        'info' => json_encode(array(
                                'families' => $rst['families'],
                                'calls' => array_slice($rst['calls'],0,3),
                                'month_info' => self::getMonthInfo($rst['month_info']),
                                'base' => array(
                                    'mobile' => $rst['mobile'],
                                    'name' => $rst['name'],
                                    'idcard' => $rst['idcard'],
                                    'carrier' => self::getCarrier($rst['carrier']),
                                    'province' => $rst['province'],
                                    'city' => $rst['city'],
                                    'open_time' => $rst['open_time'],
                                    'level' => $rst['level'],
                                    'package_name' => $rst['package_name'],
                                    'state' => self::getState($rst['state']),
                                    'reliability' => self::getReliability($rst['reliability']),
                                    'message' => $rst['message'],
                                    'address' => $rst['address'],
                                    'email' => $rst['email'],
                                    'imsi' => $rst['imsi'],
                                    'last_modify_time' => $rst['last_modify_time']
                                ),
                            )),
                        'status' => 1,
                    );
                    if( !M("MemberOperator")->where(array('uid'=>$v['uid'],'status'=>1))->find() ){
                        M("MemberOperator")->where(array('uid'=>$v['uid']))->save($save);
                    }
                }
                //存储魔蝎报告
                if($v['report_message']!='' && !$v['datainfo'] && $v['datainfo']==''){
                    $url = 'https://tenant.51datakey.com/carrier/report_data?data='.$v['report_message'];
                    $html = file_get_contents($url);
                    $info = preg_replace('/style=" float: right ;margin-top: 6px ;margin-right: 5px"/i','style="display:none"',$html);
                    $report = array(
                        'datainfo' => base64_encode($info),
                        'id' => $v['id']
                    );
                    M("Moxie")->save($report);
                }
            }
        }
    }

    protected function getState($state){
        switch ($state){
            case 0:
                $msg = '正常';
                break;
            case 1:
                $msg = '单向停机';
                break;
            case 2:
                $msg = '停机';
                break;
            case 3:
                $msg = '预销户';
                break;
            case 4:
                $msg = '销户';
                break;
            case 5:
                $msg = '过户';
                break;
            case 6:
                $msg = '改号';
                break;
            case 99:
                $msg = '号码不存在';
                break;
            default:
                $msg = '未知';
        }
        return $msg;
    }

    protected function getReliability($reliability){
        switch ($reliability){
            case 0:
                $msg = '未实名';
                break;
            case 1:
                $msg = '已实名';
                break;
            default:
                $msg = '未知';
        }
        return $msg;
    }

    protected function getCarrier($carrier){
        switch ($carrier){
            case 'CHINA_MOBILE':
                $msg = '中国移动';
                break;
            case 'CHINA_TELECOM':
                $msg = '中国电信';
                break;
            case 'CHINA_UNICOM':
                $msg = '中国联通';
                break;
            default :
                $msg = '未知';
        }
        return $msg;
    }

    protected function getMonthInfo($params){
        krsort($params['month_list']);
        $key = array_slice(array_keys($params['month_list']),0,3);

        foreach($key as $v){
            $arr[$v] = $params['month_list'][$v];
        }

        $params['month_list'] = $arr;
        $params['month_count'] = count($params['month_list']);
        return $params;
    }

    public function getTaobaoData(){
        //获取淘宝成功列表
        $lists = self::reportLists(1);
        $mx = new \Main\Lib\MoxieClass();
        foreach($lists as $v){
            $rst = $mx->getTaobao(array(
                'task_id' => $v['task_id']
            ));

            $rst = json_decode($rst,true);
            if($rst && is_array($rst)){
                $save = array(
                    'info' => json_encode(array(
                                'userinfo' => $rst['userinfo'],
                                'alipaywealth' => $rst['alipaywealth'],
                                'deliveraddress' => $rst['deliveraddress'],
                                'recentdeliveraddress' => $rst['recentdeliveraddress'],
                                'tradedetails' => $rst['tradedetails']
                            )),
                    'status' => 1,
                );
                if( !M("MemberTaobao")->where(array('uid'=>$v['uid'],'status'=>1))->find() ){
                    M("MemberTaobao")->where(array('uid'=>$v['uid']))->save($save);
                }
            }
            //存储魔蝎报告
            if($v['report_message']!='' && !$v['datainfo'] && $v['datainfo']==''){
                $url = 'https://tenant.51datakey.com/taobao/report_data?data='.$v['report_message'];
                $html = file_get_contents($url);
                $info = preg_replace('/style=" float: right ;margin-top: 6px ;margin-right: 5px"/i','style="display:none"',$html);
                $report = array(
                    'datainfo' => base64_encode($info),
                    'id' => $v['id']
                );
                M("Moxie")->save($report);
            }
        }
    }

    public function getAlipayData(){
        //获取支付宝成功列表
        $lists = self::reportLists(2);
        $mx = new \Main\Lib\MoxieClass();
        foreach($lists as $v){
            $rst = $mx->getAlipay(array(
                'task_id' => $v['task_id']
            ));

            $rst = json_decode($rst,true);
            if($rst && is_array($rst)){
                $save = array(
                    'info' => json_encode(array(
                                'userinfo' => $rst['userinfo'],
                                'wealth' => $rst['wealth'],
                                'alipaydeliveraddresses' => $rst['alipaydeliveraddresses'],
                                'bankinfo' => $rst['bankinfo'],
                                'alipaycontacts' => $rst['alipaycontacts']
                            )),
                    'status' => 1,
                );
                if( !M("MemberAlipay")->where(array('uid'=>$v['uid'],'status'=>1))->find() ){
                    M("MemberAlipay")->where(array('uid'=>$v['uid']))->save($save);
                }
            }
            //存储魔蝎报告
            if($v['report_message']!='' && !$v['datainfo'] && $v['datainfo']==''){
                $url = 'https://tenant.51datakey.com/alipay/report_data?data='.$v['report_message'];
                $html = file_get_contents($url);
                $info = preg_replace('/style=" float: right ;margin-top: 6px ;margin-right: 5px"/i','style="display:none"',$html);
                $report = array(
                    'datainfo' => base64_encode($info),
                    'id' => $v['id']
                );
                M("Moxie")->save($report);
            }
        }
    }

    //获取已完成申请人列表
    protected function getusers(){
        $condition = array(
            'mb.real_status' => 1,
        );

        return M("Member")->alias("m")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=m.user_id")
                ->where($condition)
                ->field("mb.real_name,mb.idcard,m.user_id")
                ->select();
    }
    //失信
    public function brokePromise(){
        $list = self::getusers();
        foreach($list as $v){
            if( M("MemberCredit")->where(array('uid'=>$v['user_id'],'dis_status'=>0))->find() ){
                $data = array(
                    'name' => $v['real_name'], //'刘双成'
                    'idCard' => $v['idcard'] //'110108196206010474'
                );
                $tx = new \Main\Lib\TxClass();
                $rst = json_decode($tx->personal($data),true);

                if($rst['success'] === true && $rst){
                    $save = array(
                        'is_dis' => 0,
                        'discredit' => json_encode(array(
                            'sxgg' => $rst['data']['sxgg']['pageData'],
                            'cpws' => $rst['data']['cpws']['pageData'],
                            'zxgg' => $rst['data']['zxgg']['pageData']
                            )),
                        'dis_status' => 1,
                        );
                    if($rst['data']['sxgg']['checkStatus']!='NO_DATA' || $rst['data']['cpws']['checkStatus']!='NO_DATA' && $rst['data']['zxgg']['checkStatus']!='NO_DATA'){
                        $save['is_dis'] = 1;
                    }
                    M("MemberCredit")->where(array('uid'=>$v['user_id']))->save($save);
                }else{
                    M("MemberCredit")->where(array('uid'=>$v['user_id']))->save(array('dis_status'=>2));
                }
            }
        }
    }

    //负面
    public function negative(){
        $list = self::getusers();
        foreach($list as $v){
            if( M("MemberCredit")->where(array('uid'=>$v['user_id'],'neg_status'=>0))->find() ){
                $data = array(
                    'name' => $v['real_name'], //'杜华健'
                    'idCard' => $v['idcard'] //'450923198708203270'
                );
                $tx = new \Main\Lib\TxClass();
                $rst = json_decode($tx->negative($data),true);
                if($rst['success'] === true && $rst){
                    $save = array(
                        'is_neg' => 0,
                        'neg_status' => 1
                    );
                    $var = array('escapeCompared','crimeCompared','drugCompared','drugRelatedCompared');
                    foreach($var as $_v){
                        if($rst['data'][$_v]=='一致'){
                            $rst['data'][$_v] = 1;
                            $save['is_neg'] = 1;
                        }else{
                            $rst['data'][$_v] = 0;
                        }
                    }
                    $save['negative'] = json_encode($rst['data']);
                    M("MemberCredit")->where(array('uid'=>$v['user_id']))->save($save);
                }else{
                    M("MemberCredit")->where(array('uid'=>$v['user_id']))->save(array('neg_status'=>2));
                }
            }
        }
    }

    //贷款到期变更已到期状态
    public function chgLoanStatus(){
        $end = strtotime(date("Y-m-d")." 00:00:00");
        $condition = array(
            'status' => 1,
            'borrowing_end_time' => array('elt',$end)
        );

        $rst = M("Loan")->where($condition)->field("id")->select();
        foreach($rst as $v){
            $data = array(
                'id' => $v['id'],
                'status' => 2
            );
            M("Loan")->save($data);
        }
    }

    //贷款到期短信提醒
    public function loanEndSms(){
        $end = strtotime(date("Y-m-d")." 00:00:00");
        $condition = array(
            'l.status' => array('in','1,2'),
            'l.borrowing_end_time' => $end
        );

        $rst = M("Loan")->alias('l')
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->where($condition)
                ->field("m.phone,l.borrowing_money,l.borrowing_end_time,mb.real_name")
                ->select();
        $ob = new \Main\Lib\SmsPost();
        foreach($rst as $v){
            $msg = "【利丹科技】尊敬的".$v['real_name']."，您在利丹的".$v['borrowing_money']."元贷款于今日到期，为了避免影响您的信用，请您及时联系客服或业务经理办理还款业务。";
            $ob->sendTplSms(array('phone'=>$v['phone'],'msg'=>$msg));
        }
    }

    //贷款到期前一天短信提醒
    public function loanEndSms2(){
        $end = strtotime(date("Y-m-d",strtotime('+1 day'))." 00:00:00");
        $condition = array(
            'status' => 1,
            'borrowing_end_time' => $end
        );

        $rst = M("Loan")->alias('l')
                ->join("LEFT JOIN __MEMBER__ m ON m.user_id=l.uid")
                ->join("LEFT JOIN __MEMBER_BASE__ mb ON mb.uid=l.uid")
                ->where($condition)
                ->field("m.phone,l.borrowing_money,l.borrowing_end_time,mb.real_name")
                ->select();
        $ob = new \Main\Lib\SmsPost();
        foreach($rst as $v){
            $msg = "【利丹科技】尊敬的".$v['real_name']."，您在利丹的".$v['borrowing_money']."元贷款于明日到期，为了避免影响您的信用，请您及时联系客服或业务经理办理还款业务。";
            $ob->sendTplSms(array('phone'=>$v['phone'],'msg'=>$msg));
        }
    }
}
