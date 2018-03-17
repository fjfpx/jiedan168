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
            <li class="active"><a href="#">还款</a></li>
		</ul>
        <form action="<?php echo U('AdminLoan/repay_post');?>" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data">
			<div class="row-fluid">
				<div class="span9" style="width:90%">
                    <table class="table table-bordered" id="jiekuan">
                        <tr><td><h5>待还信息</h5></td></tr>
                        <tr>
                            <th>合同号</th>
                            <td><?php echo ($repay["contract"]); ?></td>
                            <th></th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>借款人</th>
                            <td><?php echo ($repay["real_name"]); ?></td>
                            <th>手机号</th>
                            <td><?php echo ($repay["phone"]); ?></td>
                        </tr>
                        <tr>
                            <th>待还金额</th>
                            <td><?php echo ($repay["borrowing_repay"]); ?>元</td>
                            <th>到期日期</th>
                            <td><?php echo ($repay["borrowing_end_time"]); ?></td>
                        </tr>
                    </table>
                    <table class="table table-bordered" id="beizhujia">
                        <tr><td><h5>还款凭证</h5></td></tr>
                        <tr>
                            <th width="80">还款方式</th>
                            <td>
                                <select name="repay_type">
                                    <option value="0">银行卡转账</option>
                                    <option value="1">微信</option>
                                    <option value="2">支付宝</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>还款时间</th>
                            <td>
                                <input type="text" name="repay_time" class="js-datetime" style="width:500px;" placeholder="请选择时间">
                            </td>
                        </tr>
                        <tr>
                            <th>还款金额</th>
                            <td>
                                <input type="text" class="input" name="repay_money" placeholder="请输入还款金额" style="width:500px;">
                            </td>
                        </tr>
                        <tr>
                            <th>流水单号</th>
                            <td>
                                <input type="text" class="input" name="repay_trade_no" placeholder="请输入还款转账流水单号" style="width:500px;">
                            </td>
                        </tr>
                        <tr>
                            <th>备注</th>
                            <td>
                                <div>
                                    <input type="hidden" name="remark" id="thumb" value="">
                                    <a href="javascript:void(0);" onclick="flashupload('thumb_images', '附件上传','thumb',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,1','','','');return false;">
                                        <img src="/tpl_admin/simpleboot/Public/assets/images/default-thumbnail.png" id="thumb_preview" width="135" style="cursor: hand" />
                                    </a>
                                    <input type="button" class="btn btn-small" onclick="$('#thumb_preview').attr('src','/tpl_admin/simpleboot/Public/assets/images/default-thumbnail.png');$('#thumb').val('');return false;" value="取消图片">
                                </div>
                            </td>
                        </tr>
                    </table> 
                </div>
			</div>
			<div class="form-actions" style="padding-left:400px;">
                <input type="hidden" name="id" value="<?php echo ($repay["id"]); ?>">
                <button class="btn btn-primary js-ajax-submit" style="width:100px;" type="submit">提交</button>
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
						'repay_time' : {
							required : 1
						},
                        'repay_money' : {
                            required : 1
                        },
                        'remark_trade_no' : {
                            required : 1
                        }
					},
					//验证未通过提示消息
					messages : {
						'repay_time' : {
							required : '请选择还款时间'
						},
                        'repay_money' : {
                            required : '请输入还款金额'
                        },
                        'repay_trade_no' : {
                            required : '请输入还款转账流水单号'
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
												name : '确定',
												callback : function() {
                                                    location = "<?php echo U('AdminLoan/index');?>";
													return true;
												},
												focus : true
											}, {
												name : '返回贷款列表',
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