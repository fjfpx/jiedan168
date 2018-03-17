<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
<head>
    <title>运营商数据详情</title>
    <meta charset=utf-8/>
    </head>
    <style>
    body {
        padding-top: 0px;
    }

    div, h1, h2, h3, h4, h5, h6, table, tr, td {
        padding: 0;
        padding-left: 10px;
        margin: 0;
        box-sizing: border-box;
        font-size: 14px;
        white-space: nowrap;
        margin: 0 auto;
    }

    .box {
        width: 90%;
    }

    .table {
        margin: 0 auto 30px;
    }

    .tabbox {
        padding: 0;
        width: 100%;
        overflow-x: scroll;
    }

    table {
        width: 100%;
        border: 1px solid #ccc;
        border-collapse: collapse;
        margin: 0 auto;
    }

    .userinfocheck{
        white-space: normal;
    }

    .center {
        text-align: center;
    }

    .left {
        padding-left: 10px;
        text-align: left;
    }

    th {
        text-align: center;
        height: 30px;
        border-right: 1px solid #ccc;
    }

    .th {

        background: rgb(70, 140, 180);
    }

    td {
        height: 30px;
        border: 1px solid #ccc;
    }

    tr:nth-child(even) {
        background: rgb(235, 235, 235)
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
        color: #fff;
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
        margin: 30px auto;
    }

    .dropdown-menu {
        z-index: 10000;
    }
    tr.warning>td{
        background-color:#fcf8e3;
    }
    .btn-success{
        color: #fff;
        text-shadow:0 -1px 0 rgba(0,0,0,0.25);
        background-color:#5bb75b;

</style>
<body>
<center>
    <div class="row-fluid" style="width:80%">
        <!-- block -->
        <div class="block">
            <table class="table table-bordered">
                <tr>
                    <th colspan="8">
                        <h4><p class="text-center">运营商账单详情</p></h4>
                    </th>
                </tr>
                <tr class="success">
                    <td colspan="8" class="h5" >基本信息</td>
                </tr>
                <tr>
                    <th colspan="2">手机号码:&nbsp;&nbsp;<?php echo ($base["base"]["mobile"]); ?></th>
                    <th colspan="2">归属人:&nbsp;&nbsp;<?php echo ($base["base"]["name"]); ?></th>
                    <th colspan="2">证件号码:&nbsp;&nbsp;<?php echo ($base["base"]["idcard"]); ?></th>
                    <th colspan="2">运营商:&nbsp;&nbsp;<?php echo ($base["base"]["carrier"]); ?></th>
                </tr>
                <tr>
                    <th colspan="2">开卡时间:&nbsp;&nbsp;<?php echo ($base["base"]["open_time"]); ?></th>
                    <th colspan="2">归属地:&nbsp;&nbsp;<?php echo ($base["base"]["province"]); echo ($base["base"]["city"]); ?></th>
                    <th colspan="2">星级:&nbsp;&nbsp;<?php echo ($base["base"]["level"]); ?></th>
                    <th colspan="2">状态:&nbsp;&nbsp;<?php echo ($base["base"]["state"]); ?></th>
                </tr>
            </table>
            <table class="table table-striped">

                <tr class="success">
                    <td colspan="5" class="h5">亲情网</td>
                </tr>
                <tr>
                    <th>成员手机号</th>
                    <th>成员短号</th>
                    <th>成员类型</th>
                    <th>加入日期</th>
                    <th>失效日期</th>
                </tr>
                <?php $ftype = array('MASTER'=>'家长','MEMBER'=>'成员'); ?>
                <?php if(is_array($base['families']['items'])): foreach($base['families']['items'] as $key=>$bf): ?><tr>
                        <td><?php echo ($bf["long_number"]); ?></td>
                        <td><?php echo ($bf["short_number"]); ?></td>
                        <td><?php echo ($ftype[$bf['member_type']]); ?></td>
                        <td><?php echo ($bf["join_date"]); ?></td>
                        <td><?php echo ($bf["expire_date"]); ?></td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <table class="table table-striped">
                <tr class="success">
                    <td colspan="8" class="h5">通话记录<span
                            style="float:right"> 有通话记录月份数:<?php echo ($base["month_info"]["month_count"]); ?>;无通话记录月份数:<?php echo ($base["month_info"]["no_call_month"]); ?>;通话记录获取失败月份数:<?php echo ($base["month_info"]["miss_month_count"]); ?>
                         </span>
                    </td>
                </tr>
                    <?php $dial_type = array('DIAL'=>'主叫','DIALED'=>'被叫'); ?>
                    <?php if(is_array($base['calls'])): foreach($base['calls'] as $key=>$bc): ?><tr class="warning">
                        <td colspan="8"><?php echo ($bc["bill_month"]); ?>&nbsp;&nbsp;
                            <a href="javascript:displayTrs('#call-<?php echo ($bc["bill_month"]); ?>')"
                               style="color: #FFFFFF">
                                <button type="button" class="btn btn-success btn-small">账单详情</button>
                            </a>
                            <span>
                                通话记录数:<?php echo ($bc["total_size"]); ?>条
                            </span>
                        </td>
                    </tr>
                    <tr id="call-<?php echo ($bc["bill_month"]); ?>" style="display: none">
                        <td>
                            <table class="table">
                                <tr>
                                    <th>时间</th>
                                    <th>主叫/被叫</th>
                                    <th>对方号码</th>
                                    <th>通话地(自己的)</th>
                                    <th>通话地类型</th>
                                    <th>通话时长(秒)</th>
                                </tr>
                                <?php if(is_array($bc['items'])): foreach($bc['items'] as $key=>$bi): ?><tr>
                                    <td><?php echo ($bi["time"]); ?></td>
                                    <td><?php echo ($dial_type[$bi['dial_type']]); ?></td>
                                    <td><?php echo ($bi["peer_number"]); ?></td>
                                    <td><?php echo ($bi["location"]); ?></td>
                                    <td><?php echo ($bi["location_type"]); ?></td>
                                    <td><?php echo ($bi["duration"]); ?>秒</td>
                                </tr><?php endforeach; endif; ?>
                            </table>
                        </td>
                    </tr><?php endforeach; endif; ?>
        </div>
    </div>
</center>
</body>
<script type="text/javascript" src="/statics/js/jquery.js"></script>
<script>
    function displayTrs(id) {
        if ($(id).attr("style")) {
            $(id).removeAttr("style");
        } else {
            $(id).attr("style", "display: none")
        }
    }
</script>
</html>