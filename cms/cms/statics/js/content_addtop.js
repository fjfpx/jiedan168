
//弹出对话框
function omnipotent(id, linkurl, title, close_type, w, h) {
    if (!w) w = 700;
    if (!h) h = 500;
    Wind.use("artDialog","iframeTools",function(){
        art.dialog.open(linkurl, {
        id: id,
        title: title,
        width: w,
        height: h,
        lock: true,
        fixed: true,
        background:"#CCCCCC",
        opacity:0,
        button: [{
            name: '确定',
            callback: function () {
                if (close_type == 1) {
                    return true;
                } else {
                    var d = this.iframe.contentWindow;
                    var form = d.document.getElementById('dosubmit');
                    form.click();
                }
                return false;
           },
           focus: true
        }]
    });
    });
    
}

/**
swf上传完回调方法
uploadid dialog id
name dialog名称
textareaid 最后数据返回插入的容器id
funcName 回调函数
args 参数
module 所属模块
catid 栏目id
authkey 参数密钥，验证args
**/
function flashupload(uploadid, name, textareaid, funcName, args, module, catid, authkey) {
    var args = args ? '&args=' + args : '';
    var setting = '&module=' + module + '&catid=' + catid ;
    Wind.use("artDialog","iframeTools",function(){
        art.dialog.open(GV.DIMAUB+'index.php?a=swfupload&m=asset&g=asset' + args + setting, {
        title: name,
        id: uploadid,
        width: '650px',
        height: '420px',
        lock: true,
        fixed: true,
        background:"#CCCCCC",
        opacity:0,
        ok: function() {
            if (funcName) {
                funcName.apply(this, [this, textareaid]);
            } else {
                submit_ckeditor(this, textareaid);
            }
        },
        cancel: true
    });
    });
}
function change_pics(uploadid, returnid) {
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_filename = d.$("#att-name").html().substring(1);
    var contents = in_content.split('|');
    var filenames = in_filename.split('|');
    $('#' + returnid + '_tips').css('display', 'none');
    if (contents == '') return true;

    // pics_select_demo;
    $demohtml = $("#pics_select_demo").html();
    $.each(contents, function(i, n) {
        var ids = parseInt(Math.random() * 10000 + 10 * i);
        var filename = filenames[i].substr(0, filenames[i].indexOf('.'));
        var picids = "pic_" + ids;
        var tmpstr = "";
        tmpstr += "<li style='float:left' id='" + picids + "'>";
        tmpstr += '<a href="javascript:;" id="' +n+ '" onclick="new_view(this.id)"><img src="' + n + '" style="width:135px;height:195px;"></a>';
        tmpstr += "<input type='hidden' name='" + returnid + "_url[]' value='" + n + "' class='input'> ";
        tmpstr += "<br />";
        tmpstr += "&emsp;排序：<input type='text' name='sort[]' value='" + filename + "' style='width:25px'>";
        tmpstr += "<a href=\"javascript:remove_div('" + picids + "')\">移除</a> ";
        tmpstr += "</li>";
        $('#' + returnid).append(tmpstr);
    });
}
function new_view(url){
    /* var href = window.location.host+url; */
    window.open(url);
}
//多图上传，SWF回调函数
function change_images(uploadid, returnid) {
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_filename = d.$("#att-name").html().substring(1);
    var str = $('#' + returnid).html();
    var contents = in_content.split('|');
    var filenames = in_filename.split('|');
    $('#' + returnid + '_tips').css('display', 'none');
    if (contents == '') return true;
    $.each(contents, function(i, n) {
        var ids = parseInt(Math.random() * 10000 + 10 * i);
        var filename = filenames[i].substr(0, filenames[i].indexOf('.'));
        str += "<li id='image" + ids + "'><input title='双击查看' type='text' name='" + returnid + "_url[]' value='" + n + "' style='width:310px;' ondblclick='image_priview(this.value);' class='input image-url-input'> <input type='text' name='" + returnid + "_alt[]' value='" + filename + "' style='width:160px;' class='input image-alt-input' onfocus=\"if(this.value == this.defaultValue) this.value = ''\" onblur=\"if(this.value.replace(' ','') == '') this.value = this.defaultValue;\"> <a href=\"javascript:flashupload('replace_albums_images', '图片替换','image"+ids+"',replace_image,'10,gif|jpg|jpeg|png|bmp,0','','','')\">替换</a>  <a href=\"javascript:remove_div('image" + ids + "')\">移除</a> </li>";
    });

    $('#' + returnid).html(str);
}

//单图上传，SWF回调函数
function img_one(uploadid, returnid) {
    //取得iframe对象
    var d = uploadid.iframe.contentWindow;
    //取得选择的图片
    var in_content = d.$("#att-status").html().substring(1);
    if (in_content == '') return false;
    if (!IsImg(in_content)) {
        isalert('选择的类型必须为图片类型！');
        return false;
    }

    if ($('#' + returnid + '_preview').attr('src')) {
        $('#' + returnid + '_preview').attr('src', in_content);
    }
    $('#' + returnid).val(in_content);
}

function replace_image(uploadid, returnid) {
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_filename = d.$("#att-name").html().substring(1);
    var str = $('#' + returnid).html();
    var contents = in_content.split('|');
    var filenames = in_filename.split('|');
    $('#' + returnid + '_tips').css('display', 'none');
    if (contents == '') return true;
    
    $("#"+returnid).find(".image-url-input").val(contents[0]);
    var filename = filenames[0].substr(0, filenames[0].indexOf('.'));
    $("#"+returnid).find(".image-alt-input").val(filename);
}

//多图上传，SWF回调函数
function upload_zip(uploadid, returnid) {
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_filename = d.$("#att-name").html().substring(1);
    var str = $('#' + returnid).html();
    var contents = in_content.split('|');
    var filenames = in_filename.split('|');
    $('#' + returnid + '_tips').css('display', 'none');
    if (contents == '') return true;
    $.each(contents, function(i, n) {
        var ids = parseInt(Math.random() * 10000 + 10 * i);
        var filename = filenames[i].substr(0, filenames[i].indexOf('.'));
        str += "<li id='image" + ids + "'><input title='双击查看' type='text' name='" + returnid + "_url' value='" + n + "' style='width:310px;' ondblclick='image_priview(this.value);' class='input'> <input type='text' name='" + returnid + "_alt[]' value='" + filename + "' style='width:160px;' class='input' onfocus=\"if(this.value == this.defaultValue) this.value = ''\" onblur=\"if(this.value.replace(' ','') == '') this.value = this.defaultValue;\"> <a href=\"javascript:remove_div('image" + ids + "')\">移除</a> </li>";
    });

    $('#' + returnid).html(str);
}

//编辑器ue附件上传
function ueAttachment(uploadid, returnid){
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    if (in_content == ''){
        return false;
    }
    in_content = in_content.split("|");
    var i;
    var in_filename = d.$("#att-name").html().substring(1);
    var filenames = in_filename.split('|');

    eval("var ue = editor"+ returnid);
    
    for(i=0; i<in_content.length; i++){
        ue.execCommand('inserthtml', '<a href="'+in_content[i]+'" target="_blank">附件：'+filenames[i]+'</a>');
    }
    
}

//多文件上传回调
function change_multifile(uploadid, returnid) {
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_filename = d.$("#att-name").html().substring(1);
    var str = '';
    var contents = in_content.split('|');
    var filenames = in_filename.split('|');
    $('#' + returnid + '_tips').css('display', 'none');
    if (contents == '') return true;
    $.each(contents, function(i, n) {
        var ids = parseInt(Math.random() * 10000 + 10 * i);
        var filename = filenames[i].substr(0, filenames[i].indexOf('.'));
        str += "<li id='multifile" + ids + "'><input type='text' name='" + returnid + "_fileurl[]' value='" + n + "' style='width:310px;' class='input'> <input type='text' name='" + returnid + "_filename[]' value='" + filename + "' style='width:160px;' class='input' onfocus=\"if(this.value == this.defaultValue) this.value = ''\" onblur=\"if(this.value.replace(' ','') == '') this.value = this.defaultValue;\"> <a href=\"javascript:remove_div('multifile" + ids + "')\">移除</a> </li>";
    });
    $('#' + returnid).append(str);
}

//缩图上传回调
function thumb_images(uploadid, returnid) {
    //取得iframe对象
    var d = uploadid.iframe.contentWindow;
    //取得选择的图片
    var in_content = d.$("#att-status").html().substring(1);
    if (in_content == '') return false;
    if (!IsImg(in_content)) {
        isalert('选择的类型必须为图片类型！');
        return false;
    }

    if ($('#' + returnid + '_preview').attr('src')) {
        $('#' + returnid + '_preview').attr('src', in_content);
        $('#' + returnid + '_preview').attr("width","135");
        $('#' + returnid + '_preview').attr("height","200");
    }
    $('#' + returnid).val(in_content);
}

function change_onefile(uploadid, returnid){
	var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_filename = d.$("#att-name").html().substring(1);
    var str = '';
    var contents = in_content.split('|');
    var filenames = in_filename.split('|');
    $('#' + returnid + '_tips').css('display', 'none');
    if (contents == '') return true;
    $('#' + returnid).val(contents[0]);
}

//提示框 alert
function isalert(content,icon){
    if(content == ''){
        return;
    }
    icon = icon|| "error";
    Wind.use("artDialog",function(){
        art.dialog({
            id:icon,
            icon: icon,
            fixed: true,
            lock: true,
            background:"#CCCCCC",
            opacity:0,
            content: content,
            cancelVal: '确定',
            cancel: true
        });
    });
}

//图片使用dialog查看
function image_priview(img) {
    if(img == ''){
        return;
    }
    if (!IsImg(img)) {
        isalert('选择的类型必须为图片类型！');
        return false;
    }
    Wind.use("artDialog",function(){
        art.dialog({
            title: '图片查看',
            fixed: true,
            width:"500px",
            height: '320px',
            id:"image_priview",
            lock: true,
            background:"#CCCCCC",
            opacity:0,
            content: '<img src="' + img + '" />',
            time: 15
        });
    });
}

//添加/修改文章时，标题加粗
function input_font_bold() {
    if ($('#title').css('font-weight') == '700' || $('#title').css('font-weight') == 'bold') {
        $('#title').css('font-weight', 'normal');
        $('#style_font_weight').val('');
    } else {
        $('#title').css('font-weight', 'bold');
        $('#style_font_weight').val('bold');
    }
}


//图片上传回调
function submit_images(uploadid, returnid) {
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_content = in_content.split('|');
    IsImg(in_content[0]) ? $('#' + returnid).attr("value", in_content[0]) : alert('选择的类型必须为图片类型');
}

//移除ID
function remove_id(id) {
    $('#' + id).remove();
}

//输入长度提示
function strlen_verify(obj, checklen, maxlen) {
    var v = obj.value,
        charlen = 0,
        maxlen = !maxlen ? 200 : maxlen,
        curlen = maxlen,
        len = strlen(v);
    var charset = 'utf-8';
    for (var i = 0; i < v.length; i++) {
        if (v.charCodeAt(i) < 0 || v.charCodeAt(i) > 255) {
            curlen -= charset == 'utf-8' ? 2 : 1;
        }
    }
    if (curlen >= len) {
        $('#' + checklen).html(curlen - len);
    } else {
        obj.value = mb_cutstr(v, maxlen, true);
    }
}

//长度统计
function strlen(str) {
    return ($.browser.msie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}
function mb_cutstr(str, maxlen, dot) {
    var len = 0;
    var ret = '';
    var dot = !dot ? '...' : '';

    maxlen = maxlen - dot.length;
    for (var i = 0; i < str.length; i++) {
        len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == 'utf-8' ? 3 : 2) : 1;
        if (len > maxlen) {
            ret += dot;
            break;
        }
        ret += str.substr(i, 1);
    }
    return ret;
}

//移除指定id内容
function remove_div(id) {
    $('#' + id).remove();
}

//转向地址
function ruselinkurl() {
    if ($('#islink').is(':checked') == true) {
        $('#linkurl').attr('disabled', null);
        return false;
    } else {
        $('#linkurl').attr('disabled', 'true');
    }
}

//验证地址是否为图片
function IsImg(url) {
    var sTemp;
    var b = false;
    var opt = "jpg|gif|png|bmp|jpeg|zip";
    var s = opt.toUpperCase().split("|");
    for (var i = 0; i < s.length; i++) {
        sTemp = url.substr(url.length - s[i].length - 1);
        sTemp = sTemp.toUpperCase();
        s[i] = "." + s[i];
        if (s[i] == sTemp) {
            b = true;
            break;
        }
    }
    return b;
}

//验证地址是否为Flash
function IsSwf(url) {
    var sTemp;
    var b = false;
    var opt = "swf";
    var s = opt.toUpperCase().split("|");
    for (var i = 0; i < s.length; i++) {
        sTemp = url.substr(url.length - s[i].length - 1);
        sTemp = sTemp.toUpperCase();
        s[i] = "." + s[i];
        if (s[i] == sTemp) {
            b = true;
            break;
        }
    }
    return b;
}

//添加地址
function add_multifile(returnid) {
    var ids = parseInt(Math.random() * 10000);
    var str = "<li id='multifile" + ids + "'><input type='text' name='" + returnid + "_fileurl[]' value='' style='width:310px;' class='input'> <input type='text' name='" + returnid + "_filename[]' value='附件说明' style='width:160px;' class='input'> <a href=\"javascript:remove_div('multifile" + ids + "')\">移除</a> </li>";
    $('#' + returnid).append(str);
}

//添加工作经历
function add_job(value){
	var values = value+1;
	$('.addjob').remove();
    var str="<tr><th width='80'>时间段</th><td><input readonly='true' class='js-datetime2' type='text' name='job["+value+"][job_jointime]' style='width: 120px' placeholder='请选择时间'>&emsp;至&emsp;<input type='text' readonly='true' class='js-datetime2' name='job["+value+"][job_leavetime]' style='width: 120px' placeholder='请选择时间'></td></tr><tr><th>公司名称</th><td><input type='text' name='job["+value+"][job_company]' style='width: 200px' placeholder='例:北京社工科技有限公司'></td></tr><tr><th>工作部门</th><td><input type='text' name='job["+value+"][job_department]' style='width: 200px' placeholder='例:工程建设管理办公室'></td></tr><tr><th>职位名称</th><td><input type='text' name='job["+value+"][job_position]' style='width: 200px' placeholder='例:委员'></td></tr><tr><th>单位性质</th><td><select name='job["+value+"][job_nature]' style='width:215px'><option value='0'>暂无</option><option value='1'>国有企业</option><option value='2'>私营企业</option><option value='3'>事业单位</option><option value='4'>政府部门</option><option value='5'>其他</option></select></td></tr><tr><th>主管领导</th><td><input type='text' name='job["+value+"][job_director]' style='width: 200px' placeholder='例:张三'></td></tr><tr><th>分管工作</th><td><input type='text' name='job["+value+"][job_content]' style='width: 660px' placeholder='例:负责市城市副中心行政办公区工程建设办公室'></td></tr><tr><th></th><td>&emsp;<span class='addjob' onclick='add_job("+values+")'>+工作经历</span></td></tr>";
	$('#job').append(str);
    $(".js-datetime2").simpleCanleder({timeRange: {
        startYear: 1900,
        endYear: 2049
      }});
}

//添加教育经历
function add_education(value){
	var values = value+1;
	$('.addeducation').remove();
	var str = "<tr><th width='80'>时间段</th><td><input readonly='true' class='js-datetime2' type='text' name='ed["+value+"][ed_jointime]' style='width: 120px' placeholder='请选择时间'>&emsp;至&emsp;<input type='text' readonly='true' class='js-datetime2' name='ed["+value+"][ed_leavetime]' style='width: 120px' placeholder='请选择时间'></td></tr><tr><th>学校名称</th><td><input type='text' name='ed["+value+"][school]' style='width: 200px' placeholder='例:西南交通大学'></td></tr><tr><th>院系</th><td><input type='text' name='ed["+value+"][ed_department]' style='width: 200px' placeholder='例:机械工程学院'></td></tr><tr><th>专业</th><td><input type='text' name='ed["+value+"][major]' style='width: 200px' placeholder='例:隧道与地下铁道工程专业'></td></tr><tr><th>导师</th><td><input type='text' name='ed["+value+"][mentor]' style='width: 200px' placeholder='例:王小二'></td></tr><tr><th>辅导员</th><td><input type='text' name='ed["+value+"][counselor]' style='width: 200px' placeholder='例:王小三'></td></tr><tr><th>班主任</th><td><input type='text' name='ed["+value+"][teacher]' style='width: 200px' placeholder='例:王小四'></td></tr><tr><th></th><td>&emsp;<span class='addeducation' onclick='add_education("+values+")'>+教育经历</span></td></tr>";
	$('#education').append(str);
            $(".js-datetime2").simpleCanleder({timeRange: {
                    startYear: 1900,
                    endYear: 2049
                  }});
}
