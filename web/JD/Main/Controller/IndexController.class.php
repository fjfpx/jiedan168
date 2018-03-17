<?php
namespace Main\Controller;
use Think\Controller;
use Library\BaseController;
use Library\endecode;
use Library\network;
class IndexController extends BaseController {

    public function index(){
        $this->ajaxReturn($this->jret);
    }

    public function getSlide(){
        $list = M("Slide")
            ->where(array('slide_status'=>1,'slide_cid'=>0))
            ->field("slide_name,slide_id,slide_des,slide_pic,slide_url")
            ->select();

        foreach($list as &$v){
            $v['slide_pic'] = C('HTTP_SERVER').$v['slide_pic'];
        }
        $this->jret['flag'] = 1;
        $this->jret['result']['list'] = $list;

        unset($this->jret['loginstatus']);
        $this->ajaxReturn($this->jret);
    }

    public function getLists(){
        $var = array('epage','p');
        foreach($var as $v){
            if(isset($_REQUEST[$v]) && $_REQUEST[$v]!='')
            $data[$v] = trim($_REQUEST[$v]);
        }

        $lists = D("Product")->getLists($data);

        $this->jret['flag'] = 1;
        $this->jret['result'] = $lists;
        unset($this->jret['loginstatus']);
        $this->ajaxReturn($this->jret);
    }

    public function getInfo(){
        $id = trim(I('id'));
        $rst = D("Product")->getOne($id);

        $this->jret['flag'] = 1;
        $this->jret['result'] = $rst;
        unset($this->jret['loginstatus']);
        $this->ajaxReturn($this->jret);
    }

    public function application(){
        if(!$this->user_result) $this->jerror('请先登录');

        //检测是否填完资料
        if(!D('Product')->checkInfo($this->user_result['user_id'])){
            $this->jret['msg'] = '请先完善资料后再申请';
        }else{
            $id = trim(I('id'));
            if(isset($id) && $id!=''){
                $data = array(
                    'uid' => $this->user_result['user_id'],
                    'product_id' => $id
                );
                $ck = D('Product')->checkApplication($data);
                if($ck){
                    $this->jret['msg'] = '您已申请,请勿重复';
                }else{
                    $data['addtime'] = time();

                    if( M("ProductUser")->add($data) ){
                        $this->jret['flag'] = 1;
                        $this->jret['msg'] = '申请成功';

                        $rt = D("Product")->getOne($id);
                        $total = $rt['total']+1;
                        D("Product")->where(array('id'=>$id))->save(array('total'=>$total));
                    }else{
                        $this->jret['msg'] = '申请失败';
                    }
                }
            }else{
                $this->jret['msg'] = '请选择产品';
            }
        }
        $this->ajaxReturn($this->jret);
    }
}
