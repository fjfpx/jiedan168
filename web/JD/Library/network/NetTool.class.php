<?
namespace Library\network;
use Library\endecode;
class NetTool{
    public static function ip_address() {
        if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip_address = $_SERVER["HTTP_CLIENT_IP"];
        }else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $ip_address = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        }else if(!empty($_SERVER["REMOTE_ADDR"])){
            $ip_address = $_SERVER["REMOTE_ADDR"];
        }else{
            $ip_address = '';
        }
        return $ip_address;
    }
    public static function checkPhoneFormat($mobile){
        if( !preg_match("/^1[34578]{1}\d{9}$/", $mobile) ){
            return false;
        }else{
            return true;
        }
    }
    public static function RegEmailMsg($data = array()){
        $host = C('HTTP_HOST');
        $user_id = $data['user_id'];
        $username = $data['username'];
        $webname = C('HTTP_WEBNAME');
        $email = $data['email'];
        $query_url = isset($data['query_url'])?$data['query_url']:"uc/activemail";
        $active_id = urlencode(endecode\UserCode::authcode($user_id.",".time(),"ENCODE"));
        $_url = "http://{$host}/{$query_url}?id={$active_id}";
        $user_url = "http://{$host}/index.php?user";
        $send_email_msg = '
    <div style="font-size:14px; width: 588px; ">
    <div style="padding: 10px 0px;">
        <div style="padding: 2px 20px 30px;">
            <p>亲爱的 <span style="color: rgb(196, 0, 0);">'.$username.'</span> , 您好！</p>
            <p>感谢您注册'.$webname.'，您登录的邮箱帐号为 <strong style="font-size: 16px;">'.$email.'</strong></p>
            <p>请点击下面的链接即可完成激活。</p>
            <p style="overflow: hidden; width: 100%; word-wrap: break-word;"><a title="点击完成注册" href="'.$_url.'" target="_blank" swaped="true">'.$_url.'</a>
            <br><span style="color: rgb(153, 153, 153);">(如果链接无法点击，请将它拷贝到浏览器的地址栏中)</span></p>

            <p>感谢您光临'.$webname.'用户中心，我们的宗旨：为您提供优秀的产品和优质的服务！ <br>现在就登录吧!
            <a title="点击登录'.$webname.'用户中心" style="color: rgb(15, 136, 221);" href="http://'.$host.'/uc/login" target="_blank" swaped="true">http://'.$host.'/uc/login</a> 
            </p>
            <p style="text-align: right;"><br>'.$webname.'用户中心 敬启</p>
            <p><br>此为自动发送邮件，请勿直接回复！如您有任何疑问，请点击<a title="点击联系我们" style="color: rgb(15, 136, 221);" href="http://'.$host.'/" target="_blank" >联系我们&gt;&gt;</a></p>
        </div>
    </div>
</div>
        ';
        return $send_email_msg;
    }
};




?>
