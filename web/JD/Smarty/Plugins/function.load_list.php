<?php
function smarty_function_load_list($params, &$smarty)
{
    $vars = array('epage', 'page','op','t');
    foreach($vars as $var){
        if(isset($params[$var]) && $params[$var]=="REQUEST"){
            $params[$var] = $_REQUEST[$var];
        }
    }

    import("@.Model.{$params['name']}Model");
    $modelName = "\\Main\\Model\\{$params['name']}Model";
    $model = new $modelName;
    $data = $model->get_list($params);
    $smarty->assign($params['assign'], $data);
}
?>

