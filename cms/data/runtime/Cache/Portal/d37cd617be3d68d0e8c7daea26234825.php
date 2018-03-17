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
			<li class="active"><a href="javascript:;">报告消息列表</a></li>
		</ul>
		<form class="well form-search" method="post" action="">
            起止时间 <input type="text" name="start_time" class="js-datetime" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>" style="width: 120px;" autocomplete="off">-
			<input type="text" class="js-datetime" name="end_time" value="<?php echo ((isset($formget["end_time"]) && ($formget["end_time"] !== ""))?($formget["end_time"]):''); ?>" style="width: 120px;" autocomplete="off"> &nbsp;&nbsp;
            用户ID <input type="text" name="user_id" class="input" value="<?php echo ($formget["user_id"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            手机号 <input type="text" name="phone" class="input" value="<?php echo ($formget["phone"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            状态:
            <select class="select_2" name="status" style="width:120px;">
                <option value="">全部</option>
                <option value="0" <?php if( $formget['status'] == '0'): ?>selected<?php endif; ?> >未读</option>
                <option value="1" <?php if( $formget['status'] == '1'): ?>selected<?php endif; ?> >已读</option>
            </select>
			<input type="submit" class="btn btn-primary" value="检索" />
		</form>
        <p>&nbsp;</p>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
                        <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>
                        <th>用户ID</th>
                        <th>用户名</th>
						<th width="140">标题</th>
                        <th>内容</th>
                        <th>报告链接</th>
						<th>状态</th>
                        <th>类型</th>
                        <th>添加时间</th>
					</tr>
				</thead>
                    <?php if(is_array($item['item'])): foreach($item['item'] as $key=>$it): ?><tr>
                        <td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($it["id"]); ?>" title="ID:<?php echo ($it["id"]); ?>"></td>
                        <td><?php echo ($it["uid"]); ?></td>
                        <td><?php echo ($it["phone"]); ?></td>
                        <td><?php echo ($it["title"]); ?></td>
                        <td><?php echo ($it["content"]); ?></td>
                        <td><?php if($it['url']): ?><a href="<?php echo ($it["url"]); ?>"></a><?php endif; ?></td>
                        <?php $status = array("未读","已读"); ?>
                        <td><?php echo ($status[$it['readstatus']]); ?></td>
                        <td>核验消息</td>
                        <td><?php echo ($it["addtime"]); ?></td>
                    </tr><?php endforeach; endif; ?>
			</table>
            <p>&nbsp;</p>
			<div class="pagination"><?php echo ($Page); ?></div>
		</form>
	</div>
	<script src="/statics/js/common.js"></script>
	<script>
		function refersh_window() {
			var refersh_time = getCookie('refersh_time');
			if (refersh_time == 1) {
				window.location = "<?php echo U('AdminMessage/index',$formget);?>";
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
					art.dialog.open("/index.php?g=portal&m=AdminMessage&a=move&ids="+ id, {
						title : "批量移动",
						width : "80%"
					});
				});
			});
		});
	</script>
</body>
</html>