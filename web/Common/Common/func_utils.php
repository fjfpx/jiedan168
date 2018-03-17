<?

function logging($msg, $logname="PATH_LOG_DEFAULT"){
    $fn = C($logname);
    file_put_contents($fn, date("YmdHis")." ".$msg.PHP_EOL, FILE_APPEND);
}

function checkPhoneCodeValid($k, $v){
    // debug
    if(C('DEBUG_FLAG')){
        $valicode = C('DEBUG_PHONE_VALICODE');
        if($v == $valicode) return true;
    }

    if($_SESSION[$k] == $v){
        session($k,null);
        return true;
    }else{
        return false;
    }
}

function sp_password($pw,$authcode=''){
    if(empty($authcode)){
        $authcode=C("AUTHCODE");
    }
    $result="###".md5(md5($authcode.$pw));
    return $result;
}

function sp_password_old($pw){
    $decor=md5(C('DB_PREFIX'));
    $mi=md5($pw);
    return substr($decor,0,12).$mi.substr($decor,-4,4);
}

function sp_compare_password($password,$password_in_db){
    if(strpos($password_in_db, "###")===0){
        return sp_password($password)==$password_in_db;
    }else{
        return sp_password_old($password)==$password_in_db;
    }
}
