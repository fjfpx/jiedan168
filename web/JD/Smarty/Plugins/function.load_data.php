<?php
use Library\cls;
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/**
 * Smarty {load_data} function plugin
 *
 * Type:     function<br>
 * Name:     eval<br>
 * Purpose:  evaluate a template variable as a template<br>
 * @link http://smarty.php.net/manual/en/language.function.eval.php {eval}
 * @param array
 * @param Smarty
 */
function smarty_function_load_data($params, &$smarty)
{
    $vars = array('epage', 'page','op','classify');
    foreach($vars as $var){
        if(isset($params[$var]) && $params[$var]=="REQUEST"){
            $params[$var] = $_REQUEST[$var];
        }
    }

    import("@.Model.{$params['name']}Model");
    $modelName = "\\Main\\Model\\{$params['name']}Model";
    $model = new $modelName;
    $data = $model->get_lists($params);
    $smarty->assign($params['assign'], $data);
}
?>
