<?php if (!defined('THINK_PATH')) exit();?>﻿<!doctype html>
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
<style type="text/css">
.pic-list li {
	margin-bottom: 5px;
}
</style>
</head>
<body>
	<div class="wrap js-check-wrap">
			<div class="row-fluid">
				<div class="span9">
					<table class="table table-bordered">
                    <h5>欢迎光临</h5>
						<tr>
							<td>用户注册</td>
							<td>申请产品</td>
                            <td>今日到期订单</td>
                            <td>今日到期金额</td>
						</tr>
						<tr>
							<td><?php echo ($reg_users["c"]); ?> （<font color="red">+<?php echo ($reg_users["t"]); ?>）</font></td>
							<td><?php echo ($product["c"]); ?></td>
							<td><?php echo ($today_loan); ?></td>
							<td><?php echo ((isset($today_money) && ($today_money !== ""))?($today_money):"0.00"); ?> 元</td>
						</tr>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered">
                        <tr>
                            <td></td>
                            <td>注册</td>
                            <td>完善信用</td>
                            <td>产品申请</td>
                        </tr>
                        <tr>
                            <td>今日</td>
                            <td><?php echo ($reg_users["t"]); ?></td>
                            <td><?php echo ($info["t"]); ?></td>
                            <td><?php echo ($product["t"]); ?></td>
                        </tr>
                        <tr>
                            <td>昨日</td>
                            <td><?php echo ($reg_users["y"]); ?></td>
                            <td><?php echo ($info["y"]); ?></td>
                            <td><?php echo ($product["y"]); ?></td>
                        </tr>
                        <tr>
                            <td>近七天</td>
                            <td><?php echo ($reg_users["s"]); ?></td>
                            <td><?php echo ($info["s"]); ?></td>
                            <td><?php echo ($product["s"]); ?></td>
                        </tr>
                        <tr>
                            <td>累计</td>
                            <td><?php echo ($reg_users["c"]); ?></td>
                            <td><?php echo ($info["c"]); ?></td>
                            <td><?php echo ($product["c"]); ?></td>
                        </tr>
                    </table>
				</div>
			</div>
	</div>
	<script type="text/javascript" src="/statics/js/common.js"></script>
	<script type="text/javascript" src="/statics/js/content_addtop.js"></script>
	<script type="text/javascript">
		//编辑器路径定义
		var editorURL = GV.DIMAUB;
	</script>
	<script type="text/javascript" src="/statics/js/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/statics/js/ueditor/ueditor.all.min.js"></script>
	<script type="text/javascript">
		$(function() {
			$(".js-ajax-close-btn").on('click', function(e) {
				e.preventDefault();
				Wind.use("artDialog", function() {
					art.dialog({
						id : "question",
						icon : "question",
						fixed : true,
						lock : true,
						background : "#CCCCCC",
						opacity : 0,
						content : "您确定需要关闭当前页面嘛？",
						ok : function() {
							setCookie("refersh_time", 1);
							window.close();
							return true;
						}
					});
				});
			});
			/////---------------------
			Wind.use('validate', 'ajaxForm', 'artDialog', function() {
				//javascript

				//编辑器
				editorcontent = new baidu.editor.ui.Editor();
				editorcontent.render('content');
				try {
					editorcontent.sync();
				} catch (err) {
				}
				//增加编辑器验证规则
				jQuery.validator.addMethod('editorcontent', function() {
					try {
						editorcontent.sync();
					} catch (err) {
					}
					return editorcontent.hasContents();
				});
				var form = $('#form');
				//ie处理placeholder提交问题
				if ($.browser.msie) {
					form.find('[placeholder]').each(function() {
						var input = $(this);
						if (input.val() == input.attr('placeholder')) {
							input.val('');
						}
					});
				}

				var formloading = false;
				//表单验证开始
				form.validate({
					//是否在获取焦点时验证
					onfocusout : false,
					//是否在敲击键盘时验证
					onkeyup : false,
					//当鼠标掉级时验证
					onclick : false,
					//验证错误
					showErrors : function(errorMap, errorArr) {
						//errorMap {'name':'错误信息'}
						//errorArr [{'message':'错误信息',element:({})}]
						try {
							$(errorArr[0].element).focus();
							art.dialog({
								id : 'error',
								icon : 'error',
								lock : true,
								fixed : true,
								background : "#CCCCCC",
								opacity : 0,
								content : errorArr[0].message,
								cancelVal : '确定',
								cancel : function() {
									$(errorArr[0].element).focus();
								}
							});
						} catch (err) {
						}
					},
					//验证规则
					/*rules : {
						'ch_title' : {
							required : 1
						},
						'sortorder' : {
							required : 1
						},
						'ch_content' : {
							required : 1
						}
					},*/
					//验证未通过提示消息
					/*messages : {
						'ch_title' : {
							required : '章节标题不能为空'
						},
						'sortorder' : {
							required : '顺序不能为空'
						},
						'ch_content' : {
							required : '章节内容不能为空'
						}
					},*/
					//给未通过验证的元素加效果,闪烁等
					highlight : false,
					//是否在获取焦点时验证
					onfocusout : false,
					//验证通过，提交表单
					submitHandler : function(forms) {
						if (formloading)
							return;
						$(forms).ajaxSubmit({
							url : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
							dataType : 'json',
							beforeSubmit : function(arr, $form, options) {
								formloading = true;
							},
							success : function(data, statusText, xhr, $form) {
								formloading = false;
								if (data.status) {
									setCookie("refersh_time", 1);
									//添加成功
									Wind.use("artDialog", function() {
										art.dialog({
											id : "succeed",
											icon : "succeed",
											fixed : true,
											lock : true,
											background : "#CCCCCC",
											opacity : 0,
											content : data.info,
											button : [ {
												name : '继续修改!',
												callback : function() {
													reloadPage(window);
													return true;
												},
												focus : true
											}, {
												name : '返回作品页',
												callback : function() {
													location = "<?php echo U('AdminItem/index');?>";
													return true;
												}
											} ],
                                            close: function() {
                                                window.location.reload();//关闭子页面后要进行的操作
                                            },
                                            time : 3
										});
									});
								} else {
									isalert(data.info);
								}
							}
						});
					}
				});
			});
			////-------------------------
		});
	</script>
</body>
</html>