<?
namespace Library\endecode;
class UserCode{
    // password 
    public static function PwdMD5($password){
        return md5( C('PWDKEY_PAY2PRE').$password );
    }
	/**
	 * 将ID转化为URL格式
	 *
	 * @param Integer $goods_id
	 * @param String(eg:goods_vps/goods_hire) $goods_type
	 * @return String
	 */
	public static function Key2Url($key,$type) {
		return  base64_encode ($type .$key ) ;
	}

	/* code & decode
	 * 2897 => 279X8   X=(2+8+9+7)%10=6
	*/
	public static function encode_more_100($v){
		$data = array();
		$sum = 0;
		while($v>0){
			$el = $v%10;
			$sum += $el;
			$data[] = $el;
			$v = ($v - $el) / 10;
		}
		$ret = $data[2] + ($sum%10)*10 + $data[1]*100 + $data[0]*1000;
		$factor = 10000;
		for($i=3;$i<count($data);$i++){
			$ret += $data[$i]*$factor;
			$factor *= 10;
		}
		return $ret;
	}
	public static function decode_more_100($v){
		$v2 = $v%10;
		$v1 = intval($v/100)%10;
		$v0 = intval($v/1000)%10;
		return intval($v/10000) * 1000 + $v2*100 + $v1*10 + $v0;
	}

	public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
		$ckey_length = 4;
		// 密匙
		$key = md5($key ? $key : AKEY);
		// 密匙a会参与加解密
		$keya = md5(substr($key, 0, 16));
		// 密匙b会用来做数据完整性验证
		$keyb = md5(substr($key, 16, 16));
		// 密匙c用于变化生成的密文
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
		// 参与运算的密匙
		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);
		// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
		// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);
		$result = '';
		$box = range(0, 255);
		$rndkey = array();

		// 产生密匙簿
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		// 核心加解密部分
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			// 从密匙簿得出密匙进行异或，再转成字符
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}


		if($operation == 'DECODE') {
			// substr($result, 0, 10) == 0 验证数据有效性
			// substr($result, 0, 10) - time() > 0 验证数据有效性
			// substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
			// 验证数据有效性，请看未加密明文的格式
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
			// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
	/*
	*param string $lenght要截取的长度，$sart,开始截取位置
	*return string
	*/
	public static function StrTruncate($string,$lenght,$start,$etc,$code = 'UTF-8'){
		if($lenght == 0) return '';
		if ($code == 'UTF-8') {
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		}else {
			$pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
		}
		preg_match_all($pa, $string, $t_string);
		$result = "";
		if($start <= 0){
			$result = join('',array_slice($t_string[0],$start,$lenght));
		}else{
			foreach($t_string[0] as $key=>$value){
				if($key >= $start && $key < (count($t_string[0])-$lenght)){
					$result .=$etc;
				}else{
					$result .= $value;
				} 
			}
		}
		return $result;	
	}
}
?>
