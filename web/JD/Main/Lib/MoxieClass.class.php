<?php
namespace Main\Lib;
use Library\network;
use Library\endecode;
class MoxieClass{

    protected $token;

    public function __construct($url = null, $appcode = null)
    {
       // $this->token = 'ff772e7bce694bd6826e507d38ab3dcb';
    }

    //运营商信息
    public function getCarrier($params){
        $url = 'https://api.51datakey.com/carrier/v3/mobiles/'.$params['phone'].'/mxdata-ex?task_id='.$params['task_id'];
       // return $this->httpGet($url);
    }

    //淘宝信息
    public function getTaobao($params){
        $url = 'https://api.51datakey.com/gateway/taobao/v5/data/'.$params['task_id'];
        return $this->httpGet($url);
    }

    //支付宝信息
    public function getAlipay($params){
        $url = 'https://api.51datakey.com/gateway/alipay/v5/data/'.$params['task_id'];
        return $this->httpGet($url);
    }

    /**
     * 用curl模拟form表单的post提交
     */
    protected function httpGet($url)
    {
        $ch = curl_init();

        $headers = array();
        array_push($headers, "Authorization: token " . $this->token);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING,'gzip');

        $output = curl_exec($ch);
        //var_dump( curl_error($ch) );exit;
        curl_close($ch);
        return $output;
    }

}

