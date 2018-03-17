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
            <li><a <?php if($op=='1'): ?>href="<?php echo U('AdminLoan/expired');?>">到期列表 <?php elseif($op=='2'): ?>href="<?php echo U('AdminLoan/overdue');?>">逾期列表 <?php else: ?>href="<?php echo U('AdminLoan/index');?>">贷款列表<?php endif; ?></a></li>
            <li class="active"><a href="#">借款信息</a></li>
            <li><a href="<?php echo U('AdminLoan/credit',array('id'=>$verify['id'],'op'=>$op));?>">信用信息</a></li>
		</ul>
        <form action="<?php echo U('AdminLoan/add_remark');?>" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data">
			<div class="row-fluid">
                <div class="span9" style="width:90%">
                    <table class="table table-bordered" id="jiekuan">
                        <tr><td><h5>借款信息</h5></td></tr>
                        <tr>
                            <th>借款编号</th>
                            <td><?php echo ($verify["id"]); ?></td>
                            <th>合同号</th>
                            <td><?php echo ($verify["contract"]); ?></td>
                        </tr>
                        <tr>
                            <th>借款人ID</th>
                            <td><?php echo ($verify["uid"]); ?></td>
                            <th>借款人</th>
                            <td><?php echo ($verify["real_name"]); ?></td>
                        </tr>
                        <tr>
                            <th>手机号</th>
                            <td><?php echo ($verify["phone"]); ?></td>
                            <th>借款审核状态</th>
                            <?php $verify_status = array('待审核','通过','拒绝'); ?>
                            <td><?php echo ($verify_status[$verify['verify_status']]); ?></td>
                        </tr>
                        <tr>
                            <th>借款金额</th>
                            <td><?php echo ($verify["borrowing_money"]); ?>元</td>
                            <th>手续费</th>
                            <td><?php echo ($verify["borrowing_poundage"]); ?>元</td>
                        </tr>
                        <tr>
                            <th>实际到账金额</th>
                            <td><?php echo ($verify["borrowing_actual_money"]); ?>元</td>
                            <th>借款期限</th>
                            <td><?php echo ($verify["borrowing_days"]); ?></td>
                        </tr>
                        <tr>
                            <th>利息</th>
                            <td><?php echo ($verify["borrowing_rate"]); ?>元</td>
                            <th>借款时间</th>
                            <td><?php echo ($verify["borrowing_time"]); ?></td>
                        </tr>
                        <tr>
                            <th>到期还款金额</th>
                            <td><?php echo ($verify["borrowing_repay"]); ?>元</td>
                            <th>到期还款时间</th>
                            <td><?php echo ($verify["borrowing_end_time"]); ?></td>
                        </tr>
                        <tr>
                            <th>还款状态</th>
                            <?php $status = array('待打款','待还款','<font color="#2fa4e7">已到期</font>','<font color="green">已还款</font>','<font color="red">已逾期</font>','<font color="#fda42a">逾期还款</font>'); ?>
                            <td><?php echo ($status[$verify['status']]); ?></td>
                            <th><?php if($verify['repay_time']!=''): ?>还款时间<?php endif; ?></th>
                            <td><?php if($verify['repay_time']!=''): echo ($verify["repay_time"]); endif; ?></td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="jt">
                        <tr><td><h5>借款添加信息</h5></td></tr>
                        <tr>
                            <th>添加人</th>
                            <td><?php echo ($verify["user_login"]); ?></td>
                            <th>添加时间</th>
                            <td><?php echo ($verify["addtime"]); ?></td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="js">
                        <tr><td><h5>借款审核信息</h5></td></tr>
                        <tr>
                            <th>审核人</th>
                            <td><?php echo ($verify["verify_user"]); ?></td>
                            <th>审核时间</th>
                            <td><?php echo ($verify["verify_time"]); ?></td>
                        </tr>
                    </table>
                    <?php if($verify['status']!=0): ?><p>&nbsp;</p>
                    <table class="table table-bordered" id="hk">
                        <tr><td><h5>打款信息</h5></td></tr>
                        <tr>
                            <th>打款人</th>
                            <td><?php echo ($verify["pay_user"]); ?></td>
                            <th>打款时间</th>
                            <td><?php echo ($verify["paytime"]); ?></td>
                            <th>打款转账流水号</th>
                            <td><?php echo ($verify["trade_no"]); ?></td>
                            <th>打款账号</th>
                            <td><?php echo ($verify["paycard"]); ?></td>
                        </tr>
                    </table><?php endif; ?>
                    <?php if($verify['status']==3 || $verify['status']==5): ?><p>&nbsp;</p>
                    <table class="table table-bordered" id="hk">
                        <tr><td><h5>收款信息</h5></td></tr>
                        <tr>
                            <th>操作人</th>
                            <td><?php echo ($verify["repay_user"]); ?></td>
                            <th>操作时间</th>
                            <td><?php echo ($verify["repay_time"]); ?></td>
                            <th>收款方式</th>
                            <?php $repay_type=array('银行卡','微信','支付宝'); ?>
                            <td><?php echo ($repay_type[$verify['repay_type']]); ?></td>
                        </tr>
                        <tr>
                            <th>收款金额</th>
                            <td><?php echo ($verify["repay_money"]); ?></td>
                            <th>收款流水单号</th>
                            <td><?php echo ($verify["repay_trade_no"]); ?></td>
                            <th>备注</th>
                            <td><if condition="$verify['repay_remark']!=''" ><a href="<?php echo ($verify["repay_remark"]); ?>" target="_blank"><img src="<?php echo ($verify["repay_remark"]); ?>" width="50px"/></a><?php endif; ?></td>
                        </tr>
                    </table>
                </div>
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
						'remark_time' : {
							required : 1
						},
                        'remark_name' : {
                            required : 1
                        },
                        'remark_content' : {
                            required : 1
                        }
					},
					//验证未通过提示消息
					messages : {
						'remark_time' : {
							required : '请选择备注时间'
						},
                        'remark_name' : {
                            required : '请选择备注姓名或标题'
                        },
                        'remark_content' : {
                            required : '请填写备注内容'
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
													reloadPage(window);
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