<?php

function smarty_modifier_url_dot($string, $pre){
    $len = strlen($string);
    if ($len == 0)
        return '';
    
    $str = substr($string,0,1);

    if ($str == $pre) {
        $string = substr($string,1,$len-1);
    }
    
    return $string;
}

?>
