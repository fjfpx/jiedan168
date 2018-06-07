<?php
/**
 * Created by PhpStorm.
 * User: litengfei
 * Date: 2018/6/7
 * Time: 下午8:03
 */

namespace Common\Model;


class LoanMall extends CommonModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '合作商名称不能为空', 1, 'regex', 3),
    );
}