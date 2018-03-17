<?php

function smarty_modifier_default($string, $default){
    if(empty($string) ) return $default;
    return $string;
}

?>
