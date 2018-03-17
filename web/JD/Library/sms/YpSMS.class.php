<?php
namespace Library\sms;
/*
 *  Copyright (c) 2014 The CCP project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a Beijing Speedtong Information Technology Co.,Ltd license
 *  that can be found in the LICENSE file in the root of the web site.
 *
 *   http://www.yuntongxun.com
 *
 *  An additional intellectual property rights grant can be found
 *  in the file PATENTS.  All contributing project authors may
 *  be found in the AUTHORS file in the root of the source tree.
 */
class YpSMS{
    /**
     * 在PHP 5.5.17 中测试通过。
     * 默认用通用接口(send)发送，若需使用模板接口(tpl_send),请自行将代码注释去掉。
     */
    private $apikey = "0c9afc19d38f190ee91d88768dc5c1ff";
    private $url = "http://yunpian.com/v1/sms/send.json";
    private $tpl_id = 1880786;
    //通用接口发送样例
    /* $apikey = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"; //请用自己的apikey代替
       $mobile = "xxxxxxxxxxx"; //请用自己的手机号代替
       $text="【云片网】您的验证码是1234";
       echo send_sms($apikey,$text,$mobile); */

    //模板接口样例（不推荐。需要测试请将注释去掉。)
    /* 以下代码块已被注释
       $apikey = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"; //请用自己的apikey代替
       $mobile = "xxxxxxxxxxx"; //请用自己的手机号代替
       $tpl_id = 1; //对应默认模板 【#company#】您的验证码是#code#
       $tpl_value = "#company#=云片网&#code#=1234";
       echo tpl_send_sms($apikey,$tpl_id, $tpl_value, $mobile);
     */


    /**
     * 通用接口发短信
     * apikey 为云片分配的apikey
     * text 为短信内容
     * mobile 为接受短信的手机号
     */
    public function send_sms($text, $mobile){
        $encoded_text = urlencode("$text");
        $post_string="apikey=$this->apikey&text=$encoded_text&mobile=$mobile";
        return self::sock_post($this->url, $post_string);
    }


    /**
     * 模板接口发短信
     * apikey 为云片分配的apikey
     * tpl_id 为模板id
     * tpl_value 为模板值
     * mobile 为接受短信的手机号
     */
    public function tpl_send_sms($tpl_value, $mobile){
        $encoded_tpl_value = urlencode($tpl_value);  //tpl_value需整体转义
        $post_string="apikey=$this->apikey&tpl_id=$this->tpl_id&tpl_value=$encoded_tpl_value&mobile=$mobile";
        return self::sock_post($this->url, $post_string);
    }

    /**
     * url 为服务的url地址
     * query 为请求串
     */
    public function sock_post($url,$query){
        $data = "";
        $info=parse_url($url);
        $fp=fsockopen($info["host"],80,$errno,$errstr,30);
        if(!$fp){
            return $data;
        }
        $head="POST ".$info['path']." HTTP/1.0\r\n";
        $head.="Host: ".$info['host']."\r\n";
        $head.="Referer: http://".$info['host'].$info['path']."\r\n";
        $head.="Content-type: application/x-www-form-urlencoded\r\n";
        $head.="Content-Length: ".strlen(trim($query))."\r\n";
        $head.="\r\n";
        $head.=trim($query);
        $write=fputs($fp,$head);
        $header = "";
        while ($str = trim(fgets($fp,4096))) {
            $header.=$str;
        }
        while (!feof($fp)) {
            $data .= fgets($fp,4096);
        }
        return $data;
    }

    /***v2***/
    public function send_sms_v2($text,$phone){
        $data = array('text'=>$text,'apikey'=>$this->apikey,'mobile'=>$phone);
        return stripslashes(self::send_v2($ch,$data));
    }

    public function send_v2($ch,$data){
        foreach($data as $k => $v)
        {
            $postData .= $k . '='.$v.'&';
        }
        $postData = rtrim($postData, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $result = curl_exec($ch);
        curl_close($ch);
        //$error = curl_error($ch);
        return $result;
    }
    /***v2***/
}
?>

