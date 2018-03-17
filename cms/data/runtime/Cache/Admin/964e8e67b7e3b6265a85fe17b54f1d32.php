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
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('user/index');?>"><?php echo L('ADMIN_USER_INDEX');?></a></li>
			<li><a href="<?php echo U('user/add');?>"><?php echo L('ADMIN_USER_ADD');?></a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('User/edit_post');?>">
			<fieldset>
                <div class="control-group">
                    <label class="control-label">账号</label>
                    <div class="controls">
                        <input type="text" name="user_login" value="<?php echo ($user_login); ?>" disabled>
                    </div>
                </div>
				<div class="control-group">
					<label class="control-label"><?php echo L('PASSWORD');?></label>
					<div class="controls">
						<input type="password" name="user_pass" value="" placeholder="******">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo L('ROLE');?></label>
					<div class="controls">
						<?php if(is_array($roles)): foreach($roles as $key=>$vo): ?><label class="checkbox inline">
							<?php $role_id_checked=in_array($vo['id'],$role_ids)?"checked":""; ?>
							<input value="<?php echo ($vo["id"]); ?>" type="checkbox" name="role_id[]" <?php echo ($role_id_checked); ?>><?php echo ($vo["name"]); ?>
						</label><?php endforeach; endif; ?>
					</div>
				</div>
			</fieldset>
			<div class="form-actions">
				<input type="hidden" name="id" value="<?php echo ($id); ?>" />
				<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('SAVE');?></button>
				<a class="btn" href="<?php echo U('user/index');?>"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script src="/statics/js/common.js"></script>
</body>
</html>