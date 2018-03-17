<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>支付宝报告</title>
    <meta charset=utf-8 />
</head>
<style>
    body {
        padding-top: 0px;
    }

    div, h1, h2, h3, h4, h5, h6, table, tr, td {
        padding: 0;
        padding-left: 10px;
        padding-right: 10px;
        margin: 0;
        box-sizing: border-box;
        font-size: 14px;
        white-space: nowrap;
        margin: 0 auto;
    }


    .table {
        margin: 0 auto 30px;
    }


    table {
        width: 100%;
        border: 1px solid #ccc;
        border-collapse: collapse;
        margin: 0 auto;
    }

    td {
        height: 30px;
    }

    .sort {
        height: 30px;
        line-height: 30px;
        width: 100%;
        margin: 0 auto;
        font-size: 12px;
        color: rgb(119, 119, 119);
        text-align: left;
        font-weight: 100;
        padding: 0;
    }

    .h5 {
        width: 100%;
        height: 30px;
        margin: 0 auto;
        border-bottom: none;
        background: rgb(70, 140, 180);
        line-height: 30px;

    }

    h3 {
        font-size: 18px;
        font-weight: 700;
        width: 100%;
    }

    .dropdown-menu {
        z-index: 10000;
    }
    .success{
        background-color: #dff0d8;
    }
    .table tbody tr.warning>td {
    background-color: #fcf8e3;
    }
    .table th, .table td{
        border-top:1px solid #ddd;  
        padding:8px;
        line-height:20px;
        text-align:left;
        vertical-align:top;
        border-left:1px solid #ddd;
    }
    .btn-warning{
        color:#FFFFFF;
        text-shadow:0 -1px 0 rgba(0,0,0,0.25);
        background-color:#faa732;
        background-image:linear-gradient(to bottom,#fbb450,#f89406);
    }
    .btn-small{
        padding:4px 20px;
        font-size:12px;
        border-radius:3px;
    }

</style>

<body data-spy="scroll" data-target=".bs-docs-sidebar" style="font-family:'苹方-简','幼圆','Whitney SSm A','Whitney SSm B','Whitney SSm';">
<div class="block" style="margin-left: 40px">
        <div class="navbar navbar-inner block-header">
            <h3 style="text-align: center; font-size: 25px;">支付宝报告</h3>
        </div>
        <div class="block-content collapse in">
            <!-- 支付宝基本信息 -->
            <table class="table table-bordered">
                <tr class="success">
                    <td colspan="8">基本信息</td>
                </tr>
                <tr>
                    <th colspan="2">姓名:&nbsp;&nbsp;<?php echo ($base["userinfo"]["user_name"]); ?></th>
                    <th colspan="2">性别:&nbsp;&nbsp;<?php echo ($base["userinfo"]["gender"]); ?></th>
                    <th colspan="2">身份证号:&nbsp;&nbsp;<?php echo ($base["userinfo"]["idcard_number"]); ?></th>
                    <th colspan="2">是否实名认证:&nbsp;&nbsp;<?php if($base['userinfo']['certified']){ echo "是";}else{ echo "否";} ?></th>
                </tr>
                <tr>
                    <th colspan="2">邮箱:&nbsp;&nbsp;<?php echo ($base["userinfo"]["email"]); ?></th>
                    <th colspan="2">手机号:&nbsp;&nbsp;<?php echo ($base["userinfo"]["phone_number"]); ?></th>
                    <th colspan="2">淘宝会员名:&nbsp;&nbsp;<?php echo ($base["userinfo"]["taobao_id"]); ?></th>
                    <th colspan="2">支付宝注册时间:&nbsp;&nbsp;<?php echo ($base["userinfo"]["register_time"]); ?></th>
                </tr>
            </table>
            <!-- 支付宝资产状态 -->
            <table class="table table-bordered">
                
                <tr class="success">
                    <td colspan="8">资产状况</td>
                </tr>
                <tr>
                    <th colspan="2">余额:&nbsp;&nbsp;<?php echo $base['wealth']['yue']/100; ?>元</th>
                    <th colspan="2">余额宝:&nbsp;&nbsp;<?php echo $base['wealth']['yeb']/100; ?>元</th>
                    <th colspan="2">招财宝:&nbsp;&nbsp;<?php echo $base['wealth']['zcb']/100; ?>元</th>
                    <th colspan="2">基金:&nbsp;&nbsp;<?php echo $base['wealth']['fund']/100; ?>元</th>
                </tr>
                <tr>
                    <th colspan="2">存金宝:&nbsp;&nbsp;<?php echo $base['wealth']['cjb']/100; ?>元</th>
                    <th colspan="2">淘宝理财:&nbsp;&nbsp;<?php echo $base['wealth']['taolicai']/100; ?>元</th>
                    <th colspan="4">花呗额度:&nbsp;&nbsp;<?php echo $base['wealth']['huabai_limit']/100; ?>元</th>
                </tr>
            </table>
            <!-- 支付宝收货地址 -->
            <table class="table table-bordered">
                <tr class="success">
                    <td colspan="8">收货地址</td>
                </tr>
                <tr>
                    <th>姓名</th>
                    <th>手机号</th>
                    <th>邮政编码</th>
                    <th>详细地址</th>
                </tr>
                <?php if(is_array($base['alipaydeliveraddresses'])): foreach($base['alipaydeliveraddresses'] as $key=>$ba): ?><tr>
                    <td><?php echo ($ba["name"]); ?></td>
                    <td><?php echo ($ba["phone_number"]); ?></td>
                    <td><?php echo ($ba["post_code"]); ?></td>
                    <td><?php echo ($ba["address"]); echo ($ba["full_address"]); ?></td>
                </tr><?php endforeach; endif; ?>
            </table>
            <!-- 支付宝绑定银行卡信息 -->
            <table class="table table-striped">

                <tr class="success">
                    <td colspan="8">绑定银行卡信息</td>
                </tr>
                <tr>
                    <th>银行名称</th>
                    <th>银行卡类型</th>
                    <th>银行卡标志</th>
                    <th>用户姓名</th>
                    <th>银行卡尾号(后四位)</th>
                    <th>绑定手机号码</th>
                    <th>银行卡绑定时间</th>
                    <th>是否开通快捷支付</th>
                </tr>
                <?php if(is_array($base['bankinfo'])): foreach($base['bankinfo'] as $key=>$bb): ?><tr>
                    <td><?php echo ($bb["bank_name"]); ?></td>
                    <td><?php echo ($bb["card_type"]); ?></td>
                    <td><?php echo ($bb["sign_id"]); ?></td>
                    <td><?php echo ($bb["user_name"]); ?></td>
                    <td><?php echo ($bb["card_number"]); ?></td>
                    <td><?php echo ($bb["mobile"]); ?></td>
                    <td><?php echo ($bb["active_date"]); ?></td>
                    <td><?php if($bb['open_fpcard']){echo "是";}else{ echo "否";} ?></td>
                </tr><?php endforeach; endif; ?>
            </table>
            <!-- 支付宝联系人信息 -->
            <table class="table table-striped">

                <tr class="success">
                    <td colspan="8">联系人信息</td>
                </tr>
                <tr>
                    <th>联系人Id</th>
                    <th>真实姓名</th>
                    <th>账号</th>
                </tr>
                <?php if(is_array($base['alipaycontacts'])): foreach($base['alipaycontacts'] as $key=>$bal): ?><tr>
                    <td><?php echo ($bal["alipay_userid"]); ?></td>
                    <td><?php echo ($bal["real_name"]); ?></td>
                    <td><?php echo ($bal["account"]); ?></td>
                </tr><?php endforeach; endif; ?>
            </table>

        </div>
            
</div>
</body>
</html>