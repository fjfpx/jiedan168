<?php
$log_path = '/coldata1/log/jiedan';
return array(
    // 
    'MODULE_DENY_LIST'  => array('Common','Runtime','Library', 'Smarty'),
    'MODULE_ALLOW_LIST' => array('Main'),
    'DEFAULT_MODULE'    => 'Main',

    'TMPL_ENGINE_TYPE'=>'Smarty',
    'TMPL_ENGINE_CONFIG'=>array(
        'plugins_dir'=>'./Jserv/Smarty/Plugins/',
    ),

    // path
    'LOG_LEVEL'         => 4, // 0, no; 1, info; 4, debug;
    'PATH_LOG_DEFAULT'  => $log_path.'/dmxpt.log',
    'PATH_LOG_ERROR'    => $log_path.'/error.log',
    'PATH_LOG_RECHARGE' => $log_path.'/recharge.log',
    'PATH_LOG_ZW'     => $log_path.'/zw.log',

    'PATH_LOG_SMS'      => $log_path.'/smsrecordDM.txt',
    'PATH_LOG_ALIPAY' => $log_path.'/alipay.txt',
    'PATH_LOG_ALIPAY_ERROR' => $log_path.'/alipay_error.log',
    'PATH_LOG_WXPAY' => $log_path.'/wxpay.log',
    'PATH_LOG_WXPAY_ERROR' => $log_path.'/wxpay_error.log',
    'PATH_LOG_OCR' => $log_path.'/ocr.log',
    'PATH_LOG_REPORT' => $log_path.'/report.log',
    'PATH_LOG_LOGIN' => $log_path.'/login.log',
    'PATH_LOG_MEIQIA' => $log_path.'/meiqia.log',
    'PATH_LOG_IPCONFIG' => $log_path.'/ipconfig.log',
    'PATH_LOG_MOXIE' => $log_path.'/moxie/',
    'PATH_LOG_BANK' => $log_path.'/bank.log',
    'PATH_LOG_IDCHECK' => $log_path.'/idcheck.log',
    //
);
