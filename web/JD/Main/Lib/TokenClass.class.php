<?php
namespace Main\Lib;
use Library\network;
use Library\endecode;
class TokenClass{
	//下面是生成token方法代码
	public static function settoken(){
		$str = md5(uniqid(md5(microtime(true)),true));  //生成一个不会重复的字符串
		$str = sha1($str);  //加密
		return $str;
	}

    //token验证方法，db::是数据库操作类，这里设置是token如果七天没被调用则需要重新登陆（也就是说用户7天没有操作APP则需要重新登陆），如果某个接口被调用，则会重新刷新过期时间
    public static function checktokens($token){
        $res = M("Member")->where(array("app_token"=>$token))->find();
        if ($res){
            if (time() - $res['token_timeout'] > 3600*24*7){
                return;  //token长时间未使用而过期，需重新登陆
            }
            $new_time_out = time() + 3600*24*7;
            if (M("Member")->where(array('app_token'=>$token))->save(array('token_timeout'=>$new_time_out))){
                return $res;  //token验证成功，token_timeout刷新成功，可以获取接口信息
            }
        }
        return;  //token错误验证失败
    }
}

