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
			<li class="active"><a href="javascript:;"><?php echo L('PORTAL_ADMINPOST_INDEX');?></a></li>
			<li><a href="<?php echo U('AdminPost/add',array('term'=>empty($term['term_id'])?'':$term['term_id']));?>" target="_self"><?php echo L('PORTAL_ADMINPOST_ADD');?></a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('AdminPost/index');?>">
			时间：
			<input type="text" name="start_time" class="js-date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			标题： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入关键字...">
			<input type="submit" class="btn btn-primary" value="搜索" />
		</form>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>
                        <th><?php echo L('SORT');?></th>
						<th><?php echo L('TITLE');?></th>
						<th><?php echo L('KEYWORDS');?></th>
						<th><?php echo L('SOURCE');?></th>
						<th><?php echo L('ABSTRACT');?></th>
						<th><?php echo L('THUMBNAIL');?></th>
						<th><?php echo L('AUTHOR');?></th>
						<th><?php echo L('PUBLISH_DATE');?></th>
						<th><?php echo L('STATUS');?></th>
						<th><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<?php $status=array("1"=>"已审核","0"=>"未审核"); ?>
				<?php if(is_array($posts)): foreach($posts as $key=>$vo): ?><tr>
					<td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><input name="listorders[<?php echo ($vo["id"]); ?>]" class="input input-order" type="text" size="5" value="<?php echo ($vo["listorder"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><!--<a href="<?php echo U('portal/article/index',array('id'=>$vo['tid']));?>" target="_blank">--> <span><?php echo ($vo["post_title"]); ?></span></td>
					<td><?php echo ($excerpt_keywords = empty($vo['post_keywords'])?"":$vo['post_keywords']); ?></td>
					<td><?php echo ($excerpt_source = empty($vo['post_source'])?" ":$vo['post_source']); ?></td>
					<td><?php echo ($excerpt_excerpt = empty($vo['post_excerpt'])?" ":$vo['post_excerpt']); ?></td>
					<td>
						<?php $smeta=json_decode($vo['smeta'],true); ?>
						<?php if(!empty($smeta['thumb'])): ?><a href="<?php echo sp_get_asset_upload_path($smeta['thumb']);?>" target='_blank'><img src="<?php echo sp_get_asset_upload_path($smeta['thumb']);?>" width="120"></a><?php endif; ?>
					</td>
					<td><?php echo ($users[$vo['post_author']]['user_login']); ?></td>
					<td><?php echo ($vo["post_date"]); ?></td>
					<td><?php echo ($status[$vo['post_status']]); ?>
					</td>
					<td>
						<a href="<?php echo U('AdminPost/edit',array('id'=>$vo['id']));?>"><?php echo L('EDIT');?></a> | 
						<a href="<?php echo U('AdminPost/delete',array('id'=>$vo['id']));?>" class="js-ajax-delete"><?php echo L('DELETE');?></a></td>
				</tr><?php endforeach; endif; ?>
			</table>
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminPost/listorders');?>"><?php echo L('SORT');?></button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminPost/check',array('check'=>1));?>" data-subcheck="true">审核</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminPost/check',array('uncheck'=>1));?>" data-subcheck="true">取消审核</button>
                <!--<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminPost/top',array('top'=>1));?>" data-subcheck="true">置顶</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminPost/top',array('untop'=>1));?>" data-subcheck="true">取消置顶</button>-->
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminPost/delete');?>" data-subcheck="true" data-msg="你确定删除吗？"><?php echo L('DELETE');?></button>
			</div>
			<div class="pagination"><?php echo ($Page); ?></div>
		</form>
	</div>
	<script src="/statics/js/common.js"></script>
	<script>
		function refersh_window() {
			var refersh_time = getCookie('refersh_time');
			if (refersh_time == 1) {
				window.location = "<?php echo U('AdminPost/index',$formget);?>";
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
					art.dialog.open("/index.php?g=portal&m=AdminPost&a=move&ids="+ id, {
						title : "批量移动",
						width : "80%"
					});
				});
			});
		});
	</script>
</body>
</html>