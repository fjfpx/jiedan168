<?php

function smarty_modifier_smeta($string,$param='thumb'){
    $arr = json_decode($string);
    if($param == 'thumb'){
        return $arr->thumb;
    }elseif($param == 'photo'){
        return $arr->photo[0]->url;
    }elseif($param == 'photos'){
        return $arr->photo;
    }
}

?>
