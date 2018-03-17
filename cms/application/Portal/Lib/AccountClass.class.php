<?
namespace Portal\Lib;
use Think\Model;

class AccountClass{
    // change account, simutaniously add log
    // request:
    //      $data   input parameters
    //      $tranDb transaction, using this if not null
    public static function addLog ( $data, $tranDb=NULL ){
        $user_id = isset($data['uid'])?$data['uid']:"";
        if (empty($user_id)) return 0;
   
        $user = null;
        if($tranDb){
            $user = $tranDb->table(C('DB_PREFIX').'member')
                ->where("`user_id`=$user_id")->select();
        }else{
            $user = M('Member')->where("`user_id`=$user_id")->select();
        }
        if(!$user) return 0;

        //account表所需字段
        $account_var = array('total_money', 'use_money', 'no_use_money', 'freeze_money', 'award_total_money', 'award_use_money','uid','award_no_use_money','award_freeze_money');
        foreach ( $account_var as $k ){
            if(isset($data[$k])){
                $account[$k] = $data[$k];
                if( $data[$k]<-20) return 0;
            }
        }
        $account_result = self::actionAccount($account, $tranDb);
        if(!$account_result) return 0;

        // real add log
        $accountLog['addtime'] = date("Y-m-d H:i:s");
        $accountLog['addip'] = get_client_ip();
        foreach($data as $k => $v){
            $accountLog[$k] = $v;
        }
        if($tranDb){
            $tranDb->table(C('DB_PREFIX').'account_recharge_log')->lock(true)
                    ->add($accountLog);
        }else{
            M('AccountRechargeLog')->add($accountLog);
        }
        return true;
    }

    // operate table account directly
    public static function actionAccount($data=array(), $tranDb=NULL){
        if ( !isset($data['uid']) ) return 0;

        $userResult = NULL;
        $where = "`user_id`='{$data['uid']}'";
        $sql_where = "`user_id`='{$data['uid']}'";
        if($tranDb){
            $userResult = $tranDb->table(C('DB_PREFIX').'member')
                ->where($where)->select();
        }else{
            $userResult = M('Member')
                ->where($where)->select();
        }
        if( !$userResult ) return 0;

        // TODO transaction
        $accountModel = M('Account');
        $accountResult = self::getOrInsert($data['uid']);
        $accountUpdateResult = $accountModel->where("`uid`='{$data['uid']}'")->save($data);

        if(!$accountUpdateResult) return 0;

        return 1;
    }

    private function getOrInsert($user_id){
        $userModel = M('Member');
        $sql_where = array("user_id"=>$user_id);
        $user = $userModel->where($sql_where)->select();
        if(!$user){
            return 0;
        }else{
            $account = M("Account")->where(array("uid"=>$user_id))->select();
            if( !$account ){
                M("Account")->add(array("uid"=>$user_id));
                $account = M("Account")->where(array("uid"=>$user_id))->select();
            }

            return $account[0];
        }
    }

	public static function getAccountOne($data=array()){
		if (!isset($data['uid'])) return 0;
		$accountModel = M('Account');
		$where = "uid=".$data['uid'];
		$accountResult = $accountModel->where($where)->find();
		return $accountResult;
	}

    // 0, for success
    public static function addRecharge($record){
        $record['addtime'] = date("Y-m-d H:i:s");
        $record['addip'] = get_client_ip();
        $arM = M('Account_recharge');
        $check = $arM->where(array("out_trade_no"=>$record['out_trade_no'],"status"=>array("between","0,3")))->find();
        if($check) return 0;
        $response = $arM->lock(true)->add($record); 
        if($response){
            return $response;
        }
        return 0;
    }    

    // success to call this
    // response: status=0, success;
    public function rechargeSetSuccess($params){
        $jret = array('status'=>0, 'msg'=>'');

        if(!isset($params['trade_no']) OR $params['trade_no']==''){
            if($params['type']!=2){
                $jret['msg'] = '交易号错误';
                return $jret;
            }
        }

        self::getOrInsert($params['uid']);
        // check status 
        $arM = M('Account_recharge');
        $rechargeOld = $arM->where(array("out_trade_no"=>$params['out_trade_no'], 'status'=>0))->find();
        if( !$rechargeOld ){
            $jret['msg'] = "无此充值订单或订单已完成";
            return $jret;
        }

        // money
        $money = 0;
        if(isset($params['money_order']))$money = floatval($params['money_order']);
        if(abs($money-floatval($rechargeOld['money'])) < 0.01 ){
            $recharge_result = self::func_recharge_view($rechargeOld, array(
                'id' => $rechargeOld['id'],
                'status' => 1, // success
                'trade_no' => isset($params['type'])&&$params['type']==2 ?'':$params['trade_no'],
                'verify_remark' => "充值{$money}元成功",
                'verify_time' => $params['verify_time']?$params['verify_time']:time()
            ));
            if($recharge_result){
                $jret['status'] = 1;
                $jret['msg'] = "充值成功";
            }
        }else{
            $jret['msg'] = '交易失败,金额不正确';
        }
        return $jret;
    }

    // TODO @kyf
    public static function func_recharge_view($rechargeToChange, $data){
        $tranDb = new Model();
        $tranDb->startTrans();

        $result = $tranDb->table(C('DB_PREFIX').'account_recharge')
                    ->where(array('id'=>$data['id']))
                    ->save($data);

        if($result){
            $accounts = $tranDb->table(C('DB_PREFIX').'account')
                               ->where(array("uid"=>$rechargeToChange['uid']))
                               ->select();

            $account_result = $accounts[0]; //变动前的账户

            //构造变动参数
            $log['uid'] = $rechargeToChange['uid'];
            $log['money'] = $rechargeToChange['money'];
			$log['type'] = $rechargeToChange['type'];
            $log['total_money'] = $account_result['total_money'] + $log['money'];
            $log['use_money'] =  $account_result['use_money'] + $log['money'];
            $log['no_use_money'] =  $account_result['no_use_money'];
            $log['freeze_money'] = $account_result['freeze_money'];
            $log['award_total_money'] = $account_result['award_total_money'] + $rechargeToChange['award_money'];
            $log['award_use_money'] =  $account_result['award_use_money'] + $rechargeToChange['award_money'];
            $log['award_no_use_money'] =  $account_result['award_no_use_money'];
            $log['award_freeze_money'] = $account_result['award_freeze_money'];
            $log['remark'] = $data['verify_remark'];
            $log['rid'] = $data['id'];
            if( !self::addLog($log, $tranDb) ){
                $result =0;
            }
        }

        if($result){
            $tranDb->commit();
        }else{
            $tranDb->rollback(); 
        }

        return $result;
    }
};


?>
