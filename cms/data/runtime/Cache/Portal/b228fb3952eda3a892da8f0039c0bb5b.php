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
			<li class="active"><a href="#">黑名单列表</a></li>
			<li><a href="<?php echo U('AdminCredit/add_black');?>">添加黑名单</a></li>
		</ul>
        <form class="well form-search" method="post" action="">
            借款人ID: <input type="text" name="user_id" class="input" value="<?php echo ($formget["user_id"]); ?>" style="width:80px;"/>&nbsp;&nbsp;
            手机号: <input type="text" name="phone" class="input" value="<?php echo ($formget["phone"]); ?>" style="width:120px;"/>&nbsp;&nbsp;
            借款人姓名: <input type="text" name="real_name" class="input" value="<?php echo ($formget["real_name"]); ?>" style="width:120px;"/>&nbsp;&nbsp;

            &emsp;&emsp;
            <input type="submit" class="btn btn-primary" value="查询" />
        </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>借款人ID</th>
					<th>手机号</th>
                    <th>借款人姓名</th>
					<th>最后登录IP</th>
					<th>最后登录时间</th>
					<th>状态</th>
					<th width="100">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php $user_statuses=array("0"=>"已锁定","1"=>"正常","2"=>L('USER_STATUS_UNVERIFIED')); ?>
				<?php if(is_array($item['item'])): foreach($item['item'] as $key=>$vo): ?><tr <?php if($vo['is_recharge']=='1'): ?>style='color:red'<?php endif; ?> >
					<td><?php echo ($vo["user_id"]); ?></td>
					<td><?php echo ($vo["phone"]); ?></td>
                    <td><?php echo ($vo["real_name"]); ?></td>
					<td><?php echo ($vo["last_login_ip"]); ?></td>
					<td><?php echo ($vo["last_login_time"]); ?></td>
					<td><?php echo ($user_statuses[$vo['user_status']]); ?></td>
					<td>
                        <a href="<?php echo U('AdminCredit/cancel',array('id'=>$vo['user_id']));?>" class="js-ajax-dialog-btn" data-msg="确定解锁吗?">解除黑名单</a>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/statics/js/common.js"></script>
</body>
</html>