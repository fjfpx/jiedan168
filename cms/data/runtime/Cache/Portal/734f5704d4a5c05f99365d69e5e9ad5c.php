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
            <li><a href="<?php echo U('AdminLoan/topay');?>">待打款列表</a></li>
            <li class="active"><a href="#">打款</a></li>
		</ul>
        <form action="<?php echo U('AdminLoan/paying_p');?>" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data">
			<div class="row-fluid">
				<div class="span9" style="width:90%">
                    <table class="table table-bordered" id="jiekuan">
                        <tr><td><h5>打款信息</h5></td></tr>
                        <tr>
                            <th>姓名</th>
                            <td><?php echo ($pay["real_name"]); ?></td>
                            <th>手机号</th>
                            <td><?php echo ($pay["phone"]); ?></td>
                        </tr>
                        <tr>
                            <th>借款金额</th>
                            <td><?php echo ($pay["borrowing_money"]); ?> 元</td>
                            <th>放款金额</th>
                            <td><?php echo ($pay["borrowing_actual_money"]); ?> 元</td>
                        </tr>
                        <tr>
                            <th>银行卡号</th>
                            <td><?php echo ($pay["card"]); ?></td>
                            <th>开户行</th>
                            <td><?php echo ($pay["bankname"]); ?></td>
                        </tr>
                        <tr>
                            <th>借款时间</th>
                            <td><?php echo ($pay["borrowing_time"]); ?></td>
                            <th>审核时间</th>
                            <td><?php echo ($pay["verify_time"]); ?></td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <td><h5>打款凭证</h5></td>
                        </tr>
                        <tr>
                            <th>打款账号</th>
                            <td>
                                <input type="text" style="width:500px;" name="paycard" placeholder="请输入打款银行账号"/>
                                <span class="form-required" style="color:red">*</span>
                            </td>
                        </tr>
                        <tr>
                            <th>打款时间</th>
                            <td>
                                <input type="text" class="js-datetime" style="width:500px;" name="paytime" placeholder="请选择打款时间"/>
                                <span class="form-required" style="color:red">*</span>
                            </td>
                        </tr>
                        <tr>
                            <th>流水单号</th>
                            <td>
                                <input type="text" style="width:500px;" name="trade_no" placeholder="请输入转账流水单号"/>
                                <span class="form-required" style="color:red">*</span>
                            </td>
                        </tr>
                    </table>
				</div>
			</div>
			<div class="form-actions" style="padding-left:220px;">
                <input type="hidden" name="id" value="<?php echo ($pay["id"]); ?>">
                <button class="btn btn-primary js-ajax-submit" type="submit" style="width:100px">提交</button>&emsp;&emsp;
                <a class="btn" href="<?php echo U('AdminLoan/topay');?>" style="width:74px;">返回</a>
			</div>
        </form>
	</div>
	<script type="text/javascript" src="/statics/js/common.js"></script>
    <script type="text/javascript" src="/statics/js/content_addtop.js"></script>
	<script type="text/javascript" src="/statics/js/otherdate.js"></script>
	<script>
		$(".js-datetime2").simpleCanleder({timeRange: {
        startYear: 1900,
        endYear: 2049
      }});
	</script>
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
				var form = $('form.js-ajax-forms');
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
					rules : {
                        'trade_no' : {
                            required : 1
                        },
                        'paycard' : {
                            required : 1
                        },
                        'paytime' : {
                            required : 1
                        }
					},
					//验证未通过提示消息
					messages : {
                        'trade_no' : {
                            required : '请输入转账流水号'
                        },
                        'paycard' : {
                            required : '请输入打款银行账号'
                        },
                        'paytime' : {
                            required : '请输入打款时间'
                        }
					},
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
												name : '继续打款？',
												callback : function() {
                                                    location = "<?php echo U('AdminLoan/topay');?>";
													//reloadPage(window);
													return true;
												},
												focus : true
											}, {
												name : '查看贷款列表',
												callback : function() {
													location = "<?php echo U('AdminLoan/index');?>";
													return true;
												}
											} ]
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