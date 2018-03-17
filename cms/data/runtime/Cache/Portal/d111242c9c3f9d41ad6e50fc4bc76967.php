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
			<li class="active"><a href="javascript:;">幻灯片列表</a></li>
            <li><a href="<?php echo U('AdminBanner/add');?>">添加幻灯片</a></li>
		</ul>
        <p>&nbsp;</p>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
                        <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>
                        <th width="20">ID</th>
                        <th width="70">名称</th>
						<th width="60">预览图</th>
                        <th width="150">描述</th>
                        <th width="50">跳转链接</th>
                        <th width="80">状态</th>
                        <th width="120">操作</th>
					</tr>
				</thead>
                    <?php if(is_array($item['item'])): foreach($item['item'] as $key=>$it): ?><tr>
                        <td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($it["id"]); ?>" title="ID:<?php echo ($it["id"]); ?>"></td>
                        <td><?php echo ($it["slide_id"]); ?></td>
                        <td><?php echo ($it["slide_name"]); ?></td>
                        <td><a target="_blank" href="<?php echo ($it["slide_pic"]); ?>"><img src="<?php echo ($it["slide_pic"]); ?>" width="100"/></a></td>
                        <td><?php echo ($it["slide_des"]); ?></td>
                        <td><a target="_blank" href="<?php echo ($it["slide_url"]); ?>" >点击查看</a></td>
                        <?php $status = array("不显示","显示"); ?>
                        <td><?php echo ($status[$it['slide_status']]); ?> &emsp;
                            <?php if($it['slide_status']==0): ?><a href="<?php echo U('AdminBanner/view',array('id'=>$it['slide_id']));?>">上线</a>
                            <?php else: ?>
                                <a href="<?php echo U('AdminBanner/unview',array('id'=>$it['slide_id']));?>">下线</a><?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo U('AdminBanner/edit',array('id'=>$it['slide_id']));?>">编辑</a> | 
                            <a href="<?php echo U('AdminBanner/delete',array('id'=>$it['slide_id']));?>" class="js-ajax-delete">删除</a>
                        </td>
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
				window.location = "<?php echo U('AdminBanner/index',$formget);?>";
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
					art.dialog.open("/index.php?g=portal&m=AdminItem&a=move&ids="+ id, {
						title : "批量移动",
						width : "80%"
					});
				});
			});
		});
	</script>
</body>
</html>