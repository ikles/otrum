<?php
/*
 * @package Sj K2 Ajax Tabs
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2016 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
if(!isset($params) || !(count($params) > 0)) return;
if(!class_exists('plgSystemPlg_Sj_K2_Filter')){
    echo '<p style="margin:15px 0;"><b>'.JText::_('WARNING_NOT_INSTALL_PLUGIN').'</b></p>';
    return ;
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
//check the exist of k2 component on the site?

if (file_exists(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php') && file_exists(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'utilities.php')) {
    require_once dirname(__FILE__) . '/core/helper.php';
    $layout = $params->get('layout', 'default');
    $cat_id             = $params->get ('category_id');
    $action = JRoute::_(SjK2FilterHelper::getSearchRoute());
    $list = SjK2FilterHelper::getCategories($params,$cat_id);
	$extrafield_id             = $params->get ('extrafield_id');
	if($extrafield_id != null)
	{
		$listExtra = SjK2FilterHelper::getExtraField($params,$extrafield_id);
	}
    require JModuleHelper::getLayoutPath($module->module, $params->get('position', $layout));

} else {
    echo JText::_('PLEASE_INSTALL_K2_COMPONENT_FIRST');
}