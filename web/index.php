<?php
// 应用入口文件
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', True);

// 定义应用目录
define('APP_PATH','./JD/');
define('BIND_MODULE','Main');
define('COMMON_PATH', './Common/' );

if(is_file('360safe/360webscan.php')){
    require_once('360safe/360webscan.php');
}
require './ThinkPHP/ThinkPHP.php';
