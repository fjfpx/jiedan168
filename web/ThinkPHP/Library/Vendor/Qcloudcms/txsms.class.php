<?
include_once('src/SmsSingleSender.php');
include_once('src/SmsSenderUtil.php');
class txsms{

    protected $apiId = '1400054531';
    protected $apiKey = '5c21f296c1c3f353fdeed1852d4a166e';

    //单发
    public function send_one($params=array()){
        $send = new SmsSingleSender($this->apiId,$this->apiKey);

        return $send->send(0,86,$params['phone'],$params['msg']);
    }

    //群发
    public function send_more($params){
        $send = new SmsSingleSender($this->apiId,$this->apiKey);

    }
}
