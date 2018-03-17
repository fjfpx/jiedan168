<?php

function smarty_modifier_add($string, $adder){
    if(!is_numeric($string) || !is_numeric($adder) ) return 0;
    return $string+$adder;
}

?>
