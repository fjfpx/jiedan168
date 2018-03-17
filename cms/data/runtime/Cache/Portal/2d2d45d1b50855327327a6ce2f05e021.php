<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
<head>
    <title>淘宝报告</title>
    <meta charset=utf-8 />
    <link rel="shortcut icon" href="/images/favicon.ico" mce_href="/images/favicon.ico" type="image/x-icon">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body data-spy="scroll" data-target=".bs-docs-sidebar" style="font-family:'苹方-简','幼圆','Whitney SSm A','Whitney SSm B','Whitney SSm';">
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
	}
	.table-bordered th, .table-bordered td{
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

    <div class="block" style="margin-left: 40px">
        <div class="navbar navbar-inner block-header">
            <h3 style="text-align: center; font-size: 25px;">
            	淘宝报告
            </h3>
    </div>
        <div class="block-content collapse in">
            <table class="table table-bordered">
                <tr class="success">
                    <td colspan="8">基本信息&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <th colspan="2">淘宝昵称:&nbsp;&nbsp;<?php echo ($base["userinfo"]["nick"]); ?></th>
                    <th colspan="2">姓名:&nbsp;&nbsp;<?php echo ($base["userinfo"]["real_name"]); ?></th>
                    <th colspan="2">电话号码:&nbsp;&nbsp;<?php echo ($base["userinfo"]["phone_number"]); ?></th>
                    <th colspan="2">绑定邮箱:&nbsp;&nbsp;<?php echo ($base["userinfo"]["email"]); ?></th>
                </tr>
                <tr>
                    <th colspan="2">VIP等级:&nbsp;&nbsp;<?php echo ($base["userinfo"]["vip_level"]); ?></th>
                    <th colspan="2">成长值:&nbsp;&nbsp;<?php echo ($base["userinfo"]["vip_count"]); ?></th>
                    <th colspan="2">绑定微博账号:&nbsp;&nbsp;<?php echo ($base["userinfo"]["weibo_account"]); ?></th>
                    <th colspan="2">绑定微博昵称:&nbsp;&nbsp;<?php echo ($base["userinfo"]["weibo_nick"]); ?></th>
                </tr>
                <tr>
                    <th colspan="4">首次交易时间:&nbsp;&nbsp;<?php echo ($base["userinfo"]["first_ordertime"]); ?></th>
                    <th colspan="4">绑定支付宝账号:&nbsp;&nbsp;<?php echo ($base["userinfo"]["alipay_account"]); ?></th>
                </tr>
            </table>
            <table class="table table-bordered">
                
                <tr class="success">
                    <td colspan="8">全部收货地址&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <th>姓名</th>
                    <th>电话</th>
                    <th>邮编</th>
                    <th>地址</th>
                    <th>备注</th>
                </tr>
                    <?php if(is_array($base['deliveraddress'])): foreach($base['deliveraddress'] as $key=>$bd): ?><tr>
                        <td><?php echo ($bd["name"]); ?></td>
                        <td><?php echo ($bd["phone_no"]); ?></td>
                        <td><?php echo ($bd["zip_code"]); ?></td>
                        <td><?php echo ($bd["address"]); echo ($bd["full_address"]); ?></td>
                        <td><?php if($bd['default']){echo "默认收货地址";} ?></td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <table class="table table-bordered">
                
                <tr class="success">
                    <td colspan="8">最近订单收货地址</td>
                </tr>
                <tr>
                    <th>订单时间</th>
                    <th>姓名</th>
                    <th style="width: 300px">收货地址</th>
                    <th>手机</th>
                    <th>固定电话</th>
                    <th>邮编</th>
                    <th>发票抬头</th>
                </tr>
                    <?php if(is_array($base['recentdeliveraddress'])): foreach($base['recentdeliveraddress'] as $key=>$bc): ?><tr>
                        <td><?php echo ($bc["trade_createtime"]); ?></td>
                        <td><?php echo ($bc["deliver_name"]); ?></td>
                        <td><?php echo ($bc["deliver_address"]); ?></td>
                        <td><?php echo ($bc["deliver_mobilephone"]); ?></td>
                        <td><?php echo ($bc["deliver_fixedphone"]); ?></td>
                        <td><?php echo ($bc["deliver_postcode"]); ?></td>
                        <td><?php echo ($bc["invoice_name"]); ?></td>
                    </tr><?php endforeach; endif; ?>
            </table>

            <table class="table table-striped">
                    <tr class="success">
                    	<td colspan="8">订单信息&nbsp;&nbsp;&nbsp;
                    	</td>
                	</tr>
                    <?php if(is_array($base['tradedetails']['tradedetails'])): foreach($base['tradedetails']['tradedetails'] as $key=>$bt): ?><tr class="warning">
                            <td width="17%"><?php echo ($bt["trade_createtime"]); ?></td>
                            <td width="23%">订单号:<?php echo ($bt["trade_id"]); ?></td>
                            <td width="15%">店铺:<?php echo ($bt["seller_shopname"]); ?></td>
                            <td width="15%">卖家:<?php echo ($bt["seller_nick"]); ?></td>
                            <td width="15%">金额:<?php echo $bt['actual_fee']/100 ?>元</td>
                            <td width="15%">交易状态:<?php echo ($bt["trade_text"]); ?></td>
                        </tr>
                        <?php if(is_array($bt['sub_orders'])): foreach($bt['sub_orders'] as $key=>$bs): ?><tr>
                                <td><a href="http:<?php echo ($bs["item_url"]); ?>" target="_blank"><img alt="图片已失效" src="http:<?php echo ($bs["item_pic"]); ?>" width="80px"></img></a>
                                </td>
                                <td colspan="2"><?php echo ($bs["item_name"]); ?></td>
                                <td><?php echo $bs['real_total']/100 ?>元</td>
                                <td></td>
                                <td></td>
                            </tr><?php endforeach; endif; endforeach; endif; ?>
                </table>
    	</div>         
</body>
</html>