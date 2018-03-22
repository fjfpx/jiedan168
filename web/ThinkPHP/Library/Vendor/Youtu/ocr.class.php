<?php
include_once('QcloudImage/CIClient.php');
include_once('QcloudImage/Conf.php');
include_once('QcloudImage/Error.php');
include_once('QcloudImage/Auth.php');
include_once('QcloudImage/HttpClient.php');
class ocr{
    protected $appid = '10111059';
    protected $secretId = 'AKIDb58WzNpacYLwMbJZIwWwFOM8C4kEABnH';
    protected $secretKey = 'Uny0rVyFOM8bzkJl15wrs10rWyEH81mL';
    protected $bucket = 'jiedan';
    
    public function driverlicenseocr($images,$uid,$type=0){
        $jret = array('status'=>0,'msg'=>'请先上传证件图片');
        if( isset($images) && $images!='' ){
            $client = new CIClient($this->appid, $this->secretId, $this->secretKey, $this->bucket);
            $client->setTimeout(30);

            $rst = json_decode($client->idcardDetect(array('urls'=>array($images)), $type),true);
            if($rst && !empty($rst['result_list']) && is_array($rst['result_list']) && $rst['result_list'][0]['code']==0){
                $result = false;
                if($type==0){
                    $data = array(
                        'name' => $rst['result_list'][0]['data']['name'],
                        'idcard' => $rst['result_list'][0]['data']['id'],
                        'sex' => $rst['result_list'][0]['data']['sex'],
                        'nation' => $rst['result_list'][0]['data']['nation'],
                        'birth' => $rst['result_list'][0]['data']['birth'],
                        'address' => $rst['result_list'][0]['data']['address'],
                        'uid' => $uid,
                    );
                    M("MemberIdcardinfo")->where(array('uid'=>$uid))->save($data);
                    unset($data['uid']);
                }else{
                    $data = array(
                        'authority' => $rst['result_list'][0]['data']['authority'],
                        'valid_date' => $rst['result_list'][0]['data']['valid_date']
                    );
                    M("MemberIdcardinfo")->where(array('uid'=>$uid))->save($data);
                }
                $jret['status'] = 1;
                $jret['data'] = $data;
                $jret['msg'] = '识别成功';
                logging('身份证OCR识别成功: '.json_encode($rst['result_list'][0]['data']) ,'PATH_LOG_OCR');
            }else{
                $jret['msg'] = '识别失败, 请确认图片是否为身份证正面或反面,并保证照片无反光或完整';
                logging('身份证OCR识别失败: '.json_encode($rst) ,'PATH_LOG_OCR');
            }
        }
        return $jret;
    }
}
?>
