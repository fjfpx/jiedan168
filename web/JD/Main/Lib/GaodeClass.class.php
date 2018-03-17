<?php
namespace Main\Lib;
use Library\network;
use Library\endecode;
class GaodeClass{

    protected $url;
    protected $key;

    public function __construct($url = null, $uid = null, $pwd = null, $priKeyFile = null)
    {
        //test
        $this->url = 'http://restapi.amap.com/v3/ip';
        $this->key = '77d34bb55913408e8856e223c5abbdac';
    }

    //银行四要素
    public function getIpAddress($ip){
        $data = array(
            'key' => $this->key,
            'ip' => $ip,
            'output' => 'json'
        );
        return $this->httpGet($this->url, $data);
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

}

