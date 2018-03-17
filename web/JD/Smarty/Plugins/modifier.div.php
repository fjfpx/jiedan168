<?php

function smarty_modifier_div($string, $divider){
    if(!is_numeric($string) || !is_numeric($divider) ) return 0;
    return floatval($string)/floatval($divider);
}

?>
