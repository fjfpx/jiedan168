<?php
$http_type = "http";
return array(
    'SP_DEFAULT_THEME' => 'simplebootx',
    'DEFAULT_THEME' => 'simplebootx',
    'SP_ADMIN_STYLE' => 'bluesky',
    'URL_MODEL' => '0',
    'URL_HTML_SUFFIX' => '',
    'UCENTER_ENABLED' => 0,
    'COMMENT_NEED_CHECK' => 0,
    'COMMENT_TIME_INTERVAL' => 60,
    'MOBILE_TPL_ENABLED' => 1,
    'HTTP_TYPE' => $http_type,
    //'SERVER_ONLINE' => "{$http_type}://localhost:8837",


    //send email config
    'SEND_SMTPAUTH' => true, # 必填，SMTP服务器是否需要验证，true为需要，false为不需要
    'SEND_HOST' => 'smtp.baidu.com', # 必填，设置SMTP服务器
    'SEND_USERNAME' => 'kefu@baidu.com', # 必填，开通SMTP服务的邮箱；任意一个163邮箱均可
    'SEND_PASSWORD' => '123456', # 必填， 以上邮箱对应的密码
    'SEND_FROM' => 'kefu@baidu.com', # 必填，发件人Email
    'SEND_FROMNAME' => '百度', # 必填，发件人昵称或姓名

    'PWDKEY_PAY1PRE' => 'uY3m0cT5',
    'PWDKEY_PAY2PRE' => 'Piv6CiD4',

    'DATA_CACHE_TYPE' => 'Memcache',
    'MEMCACHE_HOST' => 'tcp://localhost:11211',
    'DATA_CACHE_TIME' => '7200',
);
?>
