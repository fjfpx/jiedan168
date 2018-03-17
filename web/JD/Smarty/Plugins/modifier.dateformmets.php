<?php

function smarty_modifier_dateformmets($string, $type="Y-m-d"){
    if ($string == 0)
        return date("Y-m-d");
    return date($type,strtotime($string));
}

?>
