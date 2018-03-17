<?php
namespace Main\Lib;
use Library\network;
use Library\endecode;
class TxClass{

    protected $auth_url;
    protected $report_url;
    protected $uid;
    protected $pwd;
    protected $sign;

    public function __construct($url = null, $uid = null, $pwd = null, $priKeyFile = null)
    {
        //test
        $this->auth_url = 'http://tianxingshuke.com/api/rest/common/organization/auth';
        $this->report_url = 'http://search.tianxingshuke.com:7080/tianXingDataApi/rest/traffic/maintain/reportV2';
        $this->uid = 'ldkj';
        $this->pwd = '123456';
        $this->sign = '143f9ce603c448df96ccc2cda7f7f66e';
    }

    //银行四要素
    public function check4element($params=array()){
        $url = 'http://tianxingshuke.com/api/rest/unionpay/auth/4element';
        $data = array(
            'accessToken' => $this->getTxToken(),
            'account' => $this->uid,
            'name' => $params['name'],
            'idCard' => $params['idCard'],
            'accountNO' => $params['accountNO'],
            'bankPreMobile' => $params['bankPreMobile']
        );
        return $this->httpGet($url, $data);
    }

    //身份验证
    public function idCardCheck($params=array()){
        $url = 'http://tianxingshuke.com/api/rest/police/identity';
        $data = array(
            'accessToken' => $this->getTxToken(),
            'account' => $this->uid,
            'name' => $params['name'],
            'idCard' => $params['idCard'],
        );
        return $this->httpGet($url, $data);
    }

    //个人涉诉
    public function personal($params){
        $url = 'http://tianxingshuke.com/api/rest/law/highcourt/personal';
        $data = array(
            'accessToken' => $this->getTxToken(),
            'account' => $this->uid,
            'name' => $params['name'],
            'idCard' => $params['idCard'],
            'dataType' => 'all'
        );
        return $this->httpGet($url, $data);
    }

    //负面记录
    public function negative($params){
        $url = 'http://tianxingshuke.com/api/rest/police/negativeV2';
        $data = array(
            'accessToken' => $this->getTxToken(),
            'account' => $this->uid,
            'name' => $params['name'],
            'idCard' => $params['idCard'],
            'escape' => 'true',
            'crime' => 'true',
            'drug' => 'true',
            'drugRelated' => 'true',
        );
        return $this->httpGet($url, $data);
    }
    /**
     * 获取授权码
     */
    protected function getAccessToken()
    {
        $params = array(
            'account' => $this->uid,
            'signature' => $this->sign,
        );
        return $this->getHttpResponsePOST($this->auth_url, $params);
    }


    protected function getHttpResponsePOST($url, $para) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        //curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl,CURLOPT_POST,true); // post传输数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }

    /**
     * 用curl模拟form表单的post提交
     */
    protected function httpGet($url, $params)
    {
        foreach($params as $k=>$v){
            $data .= $k.'='.$v.'&';
        }
        $data = rtrim($data,'&');
        $url = $url.'?'.$data;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        //var_dump( curl_error($ch) );exit;
        curl_close($ch);
        return $output;
    }
    /*
    * 获取天行token, token持续时间为24h
    * 超过token有效期重新获取
    */
    protected function getTxToken(){
        $token = M("Tianxing")->select();
        $accessToken = '';
        if($token){
            $accessToken = $token[0]['accesstoken'];
            if(time() > $token[0]['expiretime']){
                $a = json_decode($this->getAccessToken(),true);

                $_token = array(
                    'addtime' => time(),
                    'accesstoken' => $a['data']['accessToken'],
                    'expiretime' => substr($a['data']['expireTime'],0,-3),
                );
                M("Tianxing")->where(array('id'=>$token[0]['id']))->save($_token);

                $accessToken = $a['data']['accessToken'];
            }
        }else{
            $a = json_decode($this->getAccessToken(),true);
            $_token = array(
                'accesstoken' => $a['data']['accessToken'],
                'addtime' => time(),
                'expiretime' => substr($a['data']['expireTime'],0,-3),
            );
            M("Tianxing")->add($_token);
            $accessToken = $a['data']['accessToken'];
        }
        return $accessToken;
    }

}

