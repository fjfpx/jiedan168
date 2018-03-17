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
            <li><a href="<?php echo U('AdminLoan/refused');?>">拒绝列表</a></li>
            <li><a href="<?php echo U('AdminLoan/refused_info',array('id'=>$id));?>">借款信息</a></li>
            <li class="active"><a href="#">信用信息</a></li>
		</ul>
        <form action="<?php echo U('AdminLoan/add_remark');?>" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data">
			<div class="row-fluid">
				<div class="span9" style="width:90%">
                    <table class="table table-bordered" id="jiben">
                        <tr><td><h5>认证信息</h5></td></tr>
                        <tr>
                            <th>运营商认证</th>
                            <td>
                                <?php if($verify['phone_status']==1): ?><a href="<?php echo U('AdminLoan/base_phone',array('uid'=>$verify['user_id']));?>" target="_blank">基础信息</a>&nbsp;&nbsp;          
                                    <a href="<?php echo U('AdminLoan/moxie_phone',array('uid'=>$verify['user_id']));?>" target="_blank">魔蝎报告</a>
                                <?php else: ?>
                                    未认证<?php endif; ?>
                            </td>
                            <th></th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>支付宝认证</th>
                            <td>
                                <?php if($verify['alipay_status']==1): ?><a href="<?php echo U('AdminLoan/base_alipay',array('uid'=>$verify['user_id']));?>" target="_blank">基础信息</a>&nbsp;&nbsp;
                                    <a href="<?php echo U('AdminLoan/moxie_alipay',array('uid'=>$verify['user_id']));?>" target="_blank">魔蝎报告</a>
                                <?php else: ?>
                                    未认证<?php endif; ?>
                            </td>
                            <th>淘宝认证</th>
                            <td>
                                <?php if($verify['taobao_status']==1): ?><a href="<?php echo U('AdminLoan/base_taobao',array('uid'=>$verify['user_id']));?>" target="_blank">基础信息</a>&nbsp;&nbsp;
                                    <a href="<?php echo U('AdminLoan/moxie_taobao',array('uid'=>$verify['user_id']));?>" target="_blank">魔蝎报告</a>
                                <?php else: ?>
                                    未认证<?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="jiben">
                        <tr><td><h5>借款人基本信息</h5></td></tr>
                        <tr>
                            <th>姓名</th>
                            <td><?php echo ($verify["real_name"]); ?></td>
                            <th>身份证号</th>
                            <td><?php echo ($verify["idcard"]); ?></td>
                            <th>手机号</th>
                            <td><?php echo ($verify["phone"]); ?></td>
                        </tr>
                        <tr>
                            <th>常驻地址</th>
                            <td><?php echo ($verify["family_addr"]); ?></td>
                            <th>申请IP</th>
                            <td><?php echo ($verify["mbaddip"]); ?></td>
                            <th>定位</th>
                            <td><?php echo ($verify["ip_addr"]); ?></td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="danwei">
                        <tr><td><h5>单位信息</h5></td></tr>
                        <tr>
                            <th>单位名称</th>
                            <td><?php echo ($verify["company"]); ?></td>
                            <th>单位电话</th>
                            <td><?php echo ($verify["company_tel"]); ?></td>
                            <th>单位地址</th>
                            <td><?php echo ($verify["company_addr"]); ?></td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="jinji">
                        <tr><td><h5>紧急联系人信息</h5></td></tr>
                        <?php if(is_array($verify['contact'])): foreach($verify['contact'] as $n=>$ic): ?><tr>
                            <th>关系</th>
                            <td><?php echo ($ic["1"]); ?></td>
                            <th>姓名</th>
                            <td><?php echo ($ic["2"]); ?></td>
                            <th>手机号</th>
                            <td><?php echo ($ic["3"]); ?></td>
                        </tr><?php endforeach; endif; ?>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="yinhangka">
                        <tr><td><h5>银行卡信息</h5><br />银行卡四要素验证 <?php if($verify['mkverify_status']=='1'): ?><font color="green">一致</font><?php else: ?><font color="red">不一致</font><?php endif; ?></td></tr>
                        <tr>
                            <th>卡号</th>
                            <td><?php echo ($verify["card"]); ?></td>
                            <th>银行</th>
                            <td><?php echo ($verify["bankname"]); ?></td>
                            <th>开户行</th>
                            <td><?php echo ($verify["branch"]); ?></td>
                            <th>预留手机号</th>
                            <td><?php echo ($verify["mkphone"]); ?></td>
                        </tr>
                    </table>

                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="shenfen">
                        <tr><td><h5>身份认证</h5><br />身份核验 <?php if($verify['real_status']=='1'): ?><font color="green">一致</font><?php else: ?><font color="red">不一致</font><?php endif; ?></td></tr>
                        <tr>
                            <th>
                                <?php if($verify['front_pic']!=''): ?><a href="<?php echo ($verify["front_pic"]); ?>" target="_blank"><img src="<?php echo ($verify["front_pic"]); ?>" width="135" style="cursor: hand" /></a>
                                <?php else: ?>
                                    <img src="/tpl_admin/simpleboot/Public/assets/images/default-thumbnail.png" width="135" style="cursor: hand"/><?php endif; ?>
                            </th>
                            <th>
                                <?php if($verify['behind_pic']!=''): ?><a href="<?php echo ($verify["behind_pic"]); ?>" target="_blank"><img src="<?php echo ($verify["behind_pic"]); ?>" width="135" style="cursor: hand" /></a>
                                <?php else: ?>
                                    <img src="/tpl_admin/simpleboot/Public/assets/images/default-thumbnail.png" width="135" style="cursor: hand"/><?php endif; ?>
                            </th>
                        </tr>
                    </table>

                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="shixin">
                        <tr><td><h5>失信记录</h5></td></tr>
                        <?php if($verify['dis_status']==1 && $verify['is_dis']==1): if(is_array($verify['discredit'])): foreach($verify['discredit'] as $n=>$idis): if(is_array($idis)): foreach($idis as $s=>$sx): ?><td>类型</td>
                                <td>案号</td>
                                <td>立案时间</td>
                                <td>内容</td>
                                <tr>
                                    <td><?php if($n=='sxgg'): ?>失信公告<?php elseif($n=='cpws'): ?>裁判文书<?php else: ?>执行公告<?php endif; ?></td>
                                    <td><?php echo ($sx["caseNO"]); ?></td>
                                    <td><?php echo ($sx["recordTime"]); ?></td>
                                    <td>
                                        <?php if($n=='sxgg'): ?>被执行人姓名：<?php echo ($sx["name"]); ?>,<br />身份证号：<?php echo ($sx["identificationNO"]); ?>,<br />执行依据文号：<?php echo ($sx["exeCid"]); ?>,<br />做出执行依据单位：<?php echo ($sx["executableUnit"]); ?>,<br />失信被执行人行为具体情形：<?php echo ($sx["specificCircumstances"]); ?>,<br />履行情况：<?php echo ($sx["implementationStatus"]); ?>,<br />执行法院名称：<?php echo ($sx["court"]); ?>,<br />标识自然人或企业：<?php echo ($sx["type"]); ?>,<br />省份：<?php echo ($sx["province"]); ?>,<br />发布时间：<?php echo ($sx["postTime"]); ?>
                                        <?php elseif($n=='cpws'): ?>
                                            案件类型：<?php echo ($sx["caseType"]); ?>,<br />标题：<?php echo ($sxtitle); ?>,<br />简述：<?php echo ($sx["desc"]); ?>,<br />详情：<?php echo ($sx["content"]); ?>
                                        <?php else: ?>
                                            被执行人姓名：<?php echo ($sx["name"]); ?>,<br />身份证号：<?php echo ($sx["identificationNO"]); ?>,<br />执行标的：<?php echo ($sx["executionTarget"]); ?>,<br />法院名称：<?php echo ($sx["court"]); endif; ?>
                                    </td>
                                </tr><?php endforeach; endif; endforeach; endif; endif; ?>
                    </table>

                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="fumian">
                        <tr><td><h5>负面记录</h5></td></tr>
                        <?php if($verify['neg_status']==1 && $verify['is_neg']==1): ?><tr>
                            <th>是否在逃</th>
                            <th>是否有前科</th>
                            <th>是否吸毒</th>
                            <th>是否涉毒</th>
                            <th>最近一次案件</th>
                        </tr>
                        <tr>
                            <td><?php if($verify['negative']['escapeCompared']=='1'): ?>是<?php else: ?>否<?php endif; ?> </td>
                            <td><?php if($verify['negative']['crimeCompared']=='1'): ?>是<?php else: ?>否<?php endif; ?> </td>
                            <td><?php if($verify['negative']['drugCompared']=='1'): ?>是<?php else: ?>否<?php endif; ?></td>
                            <td><?php if($verify['negative']['drugRelatedCompared']=='1'): ?>是<?php else: ?>否<?php endif; ?></td>
                            <td><?php echo ($verify['negative']['caseType']); ?></td>
                        </tr><?php endif; ?>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="lishi">
                        <tr><td><h5>历史借款记录</h5></td></tr>
                    		<tr>
                                <th width="65">借款编号</th>
								<th width="65">借款人ID</th>
								<th width="55">借款人</th>
								<th width="80">手机号</th>
								<th width="110">放款时间</th>
								<th width="60">借款金额</th>
								<th width="60">借款期限</th>
								<th width="70">到期时间</th>
								<th width="60">借款状态</th>
								<th width="110">还款日期</th>
							</tr>
                            <?php if(is_array($verify['history'])): foreach($verify['history'] as $n=>$ih): ?><tr>
                                <td><?php echo ($ih["id"]); ?></td>
                                <td><?php echo ($ih["uid"]); ?></td>
                                <td><?php echo ($ih["real_name"]); ?></td>
                                <td><?php echo ($ih["phone"]); ?></td>
                                <td><?php echo ($ih["paytime"]); ?></td>
                                <td><?php echo ($ih["borrowing_money"]); ?></td>
                                <td><?php echo ($ih["borrowing_days"]); ?></td>
                                <td><?php echo ($ih["borrowing_end_time"]); ?></td>
                                <?php $status = array('待打款','待还款','<font color="#2fa4e7">已到期</font>','<font color="green">已还款</font>','<font color="red">已逾期</font>','<font color="#fda42a">逾期还款</font>'); ?>
                                <td><?php if($ih['verify_status']=='2'): ?>已拒绝<?php elseif($ih['verify_status']=='0'): ?>待审核<?php else: echo ($status[$ih['status']]); endif; ?></td>
                                <td><?php echo ($ih["repay_time"]); ?></td>
                            </tr><?php endforeach; endif; ?>
                    </table>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="hidden-books">
                        <tr><td><h5>通讯录 (共<?php echo ($verify["book_nums"]); ?>条)&emsp; <a href="javascript:;" onclick="chgbooksshow()">点击展开</a></h5></td></tr>
                    </table>
                    <div id="show-books" style="display:none;">
                    <p>&nbsp;</p>
                    <table class="table table-bordered">
                        <tr><td><h5>通讯录 (共<?php echo ($verify["book_nums"]); ?>条)&emsp; <a  href="javascript:;" onclick="chgbooksup()">点击收起</a></h5></td></tr>
                        <tr>
                            <th>姓名</th>
                            <th>电话</th>
                        </tr>
                        <?php if(is_array($verify['book'])): foreach($verify['book'] as $n=>$ibook): ?><tr>
                            <td><?php echo ($ibook["name"]); ?></td>
                            <td><?php echo ($ibook["phone"]); ?></td>
                        </tr><?php endforeach; endif; ?>
                    </table>
                    </div>
                    <p>&nbsp;</p>
                    <table class="table table-bordered" id="beizhu">
                        <tr><td><h5>备注</h5></td></tr>
                        <tr>
                            <th>时间</th>
                            <th>姓名</th>
                            <th>内容</th>
                        </tr>
                        <?php if(is_array($verify['remark'])): foreach($verify['remark'] as $n=>$ir): ?><tr>
                            <td><?php echo ($ir["remark_time"]); ?></td>
                            <td><?php echo ($ir["remark_name"]); ?></td>
                            <td><?php echo ($ir["remark_content"]); ?></td>
                        </tr><?php endforeach; endif; ?>
                    </table>
                    <table class="table table-bordered" id="beizhujia">
                        <tr><td>添加新备注</td></tr>
                        <tr>
                            <th width="80">时间</th>
                            <td>
                                <input type="text" name="remark_time" class="js-date" style="width:500px;" placeholder="请选择时间">
                            </td>
                        </tr>
                        <tr>
                            <th>姓名</th>
                            <td>
                                <input type="text" class="input" name="remark_name" placeholder="请输入姓名" style="width:500px;">
                            </td>
                        </tr>
                        <tr>
                            <th>内容</th>
                            <td>
                                <textarea name="remark_content" placeholder="请填写备注内容" style="max-width:500px;min-width:500px;min-height:100px;"></textarea>
                            </td>
                        </tr>
                    </table> 
                </div>
			</div>
			<div class="form-actions" style="padding-left:400px;">
                <input type="hidden" name="uid" value="<?php echo ($verify["uid"]); ?>">
                <button class="btn btn-primary js-ajax-submit" style="width:100px;" type="submit">添加备注</button>
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
        function chgbooksshow(){
            $("#show-books").css('display','block');
            $("#hidden-books").css('display','none');
        }
        function chgbooksup(){
            $("#hidden-books").css('display','block');
            $("#show-books").css('display','none');
        }
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