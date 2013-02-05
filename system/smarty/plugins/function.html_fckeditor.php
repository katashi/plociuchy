<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

////////////////////////////////////////////////////////////
function smarty_function_html_fckeditor($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
    require_once $smarty->_get_plugin_filepath('function','html_options');
    
    /* Default values. */
    $name = "date";

    /* Unparsed attributes common to *ALL* the <select>/<input> tags.
       An example might be in the template: all_extra ='class ="foo"'. */
    $all_extra       = null;
    $class			 = 'field';
    /* Separate attributes for the tags. */
    /* Order in which to display the fields.
       "D" -> day, "M" -> month, "Y" -> year. */
    $value = null;
    $dir = '';
    $Height = '400px';
    $Width = '100%';
    $Toolbar = 'default';

    foreach ($params as $_key=>$_value) {
        switch ($_key) {
            case 'name': $name=$_value;break;
            case 'all_extra':$all_extra=$_value;break;
            case 'dir':$dir=$_value;break;
            case 'value':$value=$_value;break;
            case 'class': $class=$_value;break;
            case 'toolbar': $Toolbar=$_value;break;
            case 'height': $Height=$_value;break;
            case 'width': $Width=$_value;break;
            default: $smarty->trigger_error("[html_fckeditor] unknown parameter $_key", E_USER_WARNING);
        }
    }

    require_once('fck/fckeditor.php');
    $fck=new fckeditor($name);
    $fck->BasePath=$dir.'fck/';
    $fck->Height=$Height;
    $fck->ToolbarSet=$Toolbar;
    $fck->Width=$Width;
    $fck->Value = $value;
					
    return $fck->createhtml();
}
?>
