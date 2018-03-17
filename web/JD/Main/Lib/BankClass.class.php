<?php
namespace Main\Lib;
use Library\network;
use Library\endecode;
class BankClass{

    protected $url;
    protected $appcode;

    public function __construct($url = null, $appcode = null)
    {
        //test
        $this->url = 'http://aliyun.apistore.cn/7';
        $this->appcode = 'eb576f80be3940b1916def6f24153ed9';
    }

    //银行卡基本信息
    public function getBankBase($bankcard){
        return $this->httpGet($this->url, $bankcard);
    }

    /**
     * 用curl模拟form表单的post提交
     */
    protected function httpGet($url, $bankcard)
    {
        $method = "GET";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appcode);
        $bodys = "";
        $url = $url."?bankcard=".$bankcard;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $output = curl_exec($curl);
        //var_dump( curl_error($ch) );
        curl_close($curl);
        return $output;
    }

}

