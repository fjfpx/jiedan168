<?php

function smarty_modifier_explode($string,$delimiter='.'){
    $arr = explode($delimiter,$string);
    return $arr[0] ;
}

?>
