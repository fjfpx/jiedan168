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
			<li class="active"><a href="javascript:;">拒绝列表</a></li>
		</ul>
		<form class="well form-search" method="post" action="">
            借款编号: <input type="text" name="id" class="input" value="<?php echo ($formget["id"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            借款人ID: <input type="text" name="uid" class="input" value="<?php echo ($formget["uid"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            借款人: <input type="text" name="real_name" class="input" value="<?php echo ($formget["real_name"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            手机号: <input type="text" name="phone" class="input" value="<?php echo ($formget["phone"]); ?>" style="width:100px;"/>&nbsp;&nbsp;
            添加人: <input type="text" name="user_login" class="input" value="<?php echo ($formget["user_login"]); ?>" style="width:80px;"/><br /><br />
            添加时间: <input type="text" name="ustart_time" class="js-date" value="<?php echo ($formget["ustart_time"]); ?>" style="width:80px;" autocomplete="off"/> - <input type="text" class="js-date" name="uend_time" value="<?php echo ($formget["uend_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			<input type="submit" class="btn btn-primary" value="查询" />
		</form>
        <p>&nbsp;</p>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
                        <!--<th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>-->
                        <th width="70">借款编号</th>
                        <th width="80">借款人ID</th>
                        <th width="80">借款人</th>
                        <th width="120">手机号</th>
                        <th width="120">借款日期</th>
                        <th width="80">借款金额</th>
                        <th width="80">借款期限</th>
                        <th width="110">到期时间</th>
                        <th width="80">状态</th>
                        <th width="80">添加人</th>
                        <th width="80">拒绝原因</th>
                        <th width="110">添加时间</th>
                        <th width="150">操作</th>
					</tr>
				</thead>
                    <?php if(is_array($item['item'])): foreach($item['item'] as $key=>$it): ?><tr>
                        <!--<td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($it["id"]); ?>" title="ID:<?php echo ($it["id"]); ?>"></td>-->
                        <td><?php echo ($it["id"]); ?></td>
                        <td><?php echo ($it["user_id"]); ?></td>
                        <td><?php echo ($it["real_name"]); ?></td>
                        <td><?php echo ($it["phone"]); ?></td>
                        <td><?php echo ($it["borrowing_time"]); ?></td>
                        <td><?php echo ($it["borrowing_money"]); ?></td>
                        <td><?php echo ($it["borrowing_days"]); ?></td>
                        <td><?php echo ($it["borrowing_end_time"]); ?></td>
                        <?php $status = array('待审核','审核通过','审核拒绝'); ?>
                        <td><?php echo ($status[$it['verify_status']]); ?></td>
                        <td><?php echo ($it["user_login"]); ?></td>
                        <td><?php echo ($it["refused_msg"]); ?></td>
                        <td><?php echo ($it["addtime"]); ?></td>
                        <td>
                            <a href="<?php echo U('AdminLoan/refused_info',array('id'=>$it['id']));?>">详细信息</a>
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
				window.location = "<?php echo U('AdminLoan/index',$formget);?>";
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