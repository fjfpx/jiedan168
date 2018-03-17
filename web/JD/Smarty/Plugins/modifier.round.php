<?php

function smarty_modifier_round($string, $dot){
    if(!is_numeric($string) || !is_numeric($dot) ) return $string;
    $string = round($string, $dot);
    $arr = explode('.',$string);
    if(strlen($arr[1]) == 0){
        $arr[1] = '00';
    }elseif(strlen($arr[1]) == 1){
        $arr[1] = $arr[1]*10;
    }
    
    return implode('.',$arr);

}

?>
