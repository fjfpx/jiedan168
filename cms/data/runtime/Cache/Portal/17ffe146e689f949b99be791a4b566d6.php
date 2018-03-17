<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/statics/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/statics/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/statics/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/statics/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		.length_3{width: 180px;}
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "/",
    JS_ROOT: "statics/js/",
    TOKEN: ""
};
</script>
<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/statics/js/jquery.js"></script>
    <script src="/statics/js/wind.js"></script>
    <script src="/statics/simpleboot/bootstrap/js/bootstrap.min.js"></script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>
</head>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">到期列表</a></li>
		</ul>
		<form class="well form-search" method="post" action="">
            借款编号: <input type="text" name="id" class="input" value="<?php echo ($formget["id"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            借款人ID: <input type="text" name="uid" class="input" value="<?php echo ($formget["uid"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            借款人: <input type="text" name="real_name" class="input" value="<?php echo ($formget["real_name"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            手机号: <input type="text" name="phone" class="input" value="<?php echo ($formget["phone"]); ?>" style="width:100px;"/>&nbsp;&nbsp;
            到期时间: <input type="text" name="start_time" class="js-date" value="<?php echo ($formget["start_time"]); ?>" style="width:80px;" autocomplete="off"/> - <input type="text" class="js-date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			<input type="submit" class="btn btn-primary" value="查询" />
		</form>
        <p>&nbsp;</p>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
                        <!--<th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>-->
                        <th width="65">借款编号</th>
                        <th width="65">借款人ID</th>
                        <th width="80">借款人</th>
                        <th width="80">手机号</th>
                        <th width="115">放款时间</th>
                        <th width="65">借款金额</th>
                        <th width="65">借款期限</th>
                        <th width="80">到期时间</th>
                        <th width="60">状态</th>
                        <th width="80">添加人</th>
                        <th width="80">审核人</th>
                        <th width="80">到期天数</th>
                        <th width="180">操作</th>
					</tr>
				</thead>
                    <?php if(is_array($item['item'])): foreach($item['item'] as $key=>$it): ?><tr>
                        <!--<td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($it["id"]); ?>" title="ID:<?php echo ($it["id"]); ?>"></td>-->
                        <td><?php echo ($it["id"]); ?></td>
                        <td><?php echo ($it["user_id"]); ?></td>
                        <td><?php echo ($it["real_name"]); ?></td>
                        <td><?php echo ($it["phone"]); ?></td>
                        <td><?php echo ($it["paytime"]); ?></td>
                        <td><?php echo ($it["borrowing_money"]); ?></td>
                        <td><?php echo ($it["borrowing_days"]); ?></td>
                        <td><?php echo ($it["borrowing_end_time"]); ?></td>
                        <?php $status = array('待打款','待还款','<font color="#2fa4e7">已到期</font>','已还款','已逾期','逾期还款'); ?>
                        <td><?php echo ($status[$it['status']]); ?></td>
                        <td><?php echo ($it["user_login"]); ?></td>
                        <td><?php echo ($it["verify_user"]); ?></td>
                        <td><?php echo ($it["ex_time"]); ?> 天</td>
                        <td>
                            <a href="<?php echo U('AdminLoan/detail',array('id'=>$it['id'],'op'=>1));?>" >详细资料</a>&emsp;
                            <a href="<?php echo U('AdminLoan/repay',array('id'=>$it['id']));?>">还款</a>&emsp;
                            <a class="js-ajax-focus" href="<?php echo U('AdminLoan/upd_status',array('id'=>$it['id']));?>">标记逾期</a>
                        </td>
                    </tr><?php endforeach; endif; ?>
			</table>
			<div class="pagination"><?php echo ($Page); ?></div>
		</form>
	</div>
	<script src="/statics/js/common.js"></script>
	<script>
		function refersh_window() {
			var refersh_time = getCookie('refersh_time');
			if (refersh_time == 1) {
				window.location = "<?php echo U('AdminLoan/expired',$formget);?>";
			}
		}
		setInterval(function() {
			refersh_window();
		}, 2000);
		$(function() {
			setCookie("refersh_time", 0);
			Wind.use('ajaxForm', 'artDialog', 'iframeTools', function() {
				//批量移动
				$('.js-articles-move').click(function(e) {
					var str = 0;
					var id = tag = '';
					$("input[name='ids[]']").each(function() {
						if ($(this).attr('checked')) {
							str = 1;
							id += tag + $(this).val();
							tag = ',';
						}
					});
					if (str == 0) {
						art.dialog.through({
							id : 'error',
							icon : 'error',
							content : '您没有勾选信息，无法进行操作！',
							cancelVal : '关闭',
							cancel : true
						});
						return false;
					}
					var $this = $(this);
					art.dialog.open("/index.php?g=portal&m=AdminLoan&a=move&ids="+ id, {
						title : "批量移动",
						width : "80%"
					});
				});
			});
		});
	</script>
</body>
</html>