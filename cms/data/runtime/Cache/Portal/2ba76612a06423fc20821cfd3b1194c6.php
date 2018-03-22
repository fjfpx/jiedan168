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
			<li class="active"><a href="javascript:;">产品列表</a></li>
            <li><a href="<?php echo U('AdminLists/add',$formget);?>">添加产品</a></li>
		</ul>
		<form class="well form-search" method="post" action="">
            产品名: <input type="text" name="title" class="input" value="<?php echo ($formget["title"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
			<input type="submit" class="btn btn-primary" value="查询" />
		</form>
        <p>&nbsp;</p>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
                        <!--<th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>-->
                        <th width="80">排序</th>
                        <th width="80">产品ID</th>
                        <th width="250">产品名称</th>
                        <th width="80">日利率</th>
                        <th width="80">申请次数</th>
                        <th width="80">状态</th>
                        <th width="150">操作</th>
					</tr>
				</thead>
                    <?php if(is_array($item['item'])): foreach($item['item'] as $key=>$it): ?><tr>
                        <!--<td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($it["id"]); ?>" title="ID:<?php echo ($it["id"]); ?>"></td>-->
                        <td><input name="listorders[<?php echo ($it["id"]); ?>]" class="input input-order" type="text" size="5" value="<?php echo ($it["listorder"]); ?>" title="ID:<?php echo ($it["id"]); ?>"></td>
                        <td><?php echo ($it["id"]); ?></td>
                        <td><?php echo ($it["title"]); ?></td>
                        <td><?php echo $it['rate']*100; ?>%</td>
                        <td><?php echo ($it["nums"]); ?></td>
                        <?php $status = array('已下架','上架中'); ?>
                        <td><?php echo ($status[$it['status']]); ?></td>
                        <td>
                            <?php if($it['status']=='0'): ?><a href="<?php echo U('AdminLists/edit',array('id'=>$it['id']));?>">编辑</a><?php else: ?>编辑<?php endif; ?>&emsp;&emsp;
                            <?php if($it['status']=='0'): ?><a href="<?php echo U('AdminLists/online',array('id'=>$it['id']));?>" class="js-ajax-focus">上架</a>&emsp;&emsp; 
                                <a href="<?php echo U('AdminLists/delete',array('id'=>$it['id']));?>" class="js-ajax-delete">删除</a>
                            <?php else: ?>
                                <a href="<?php echo U('AdminLists/online',array('id'=>$it['id'],'op'=>'down'));?>" class="js-ajax-focus">下架</a><?php endif; ?>
                        </td>
                    </tr><?php endforeach; endif; ?>
			</table>
            <div class="table-actions">
                <button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminLists/listorders');?>"><?php echo L('SORT');?></button>
            </div>
			<div class="pagination"><?php echo ($Page); ?></div>
		</form>
	</div>
	<script src="/statics/js/common.js"></script>
	<script>
		function refersh_window() {
			var refersh_time = getCookie('refersh_time');
			if (refersh_time == 1) {
				window.location = "<?php echo U('AdminLists/index',$formget);?>";
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
					art.dialog.open("/index.php?g=portal&m=AdminLists&a=move&ids="+ id, {
						title : "批量移动",
						width : "80%"
					});
				});
			});
		});
	</script>
</body>
</html>