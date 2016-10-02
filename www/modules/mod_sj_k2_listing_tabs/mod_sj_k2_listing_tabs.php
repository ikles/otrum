<?php
/**
 * @package SJ Listing Tabs For K2
 * @version 1.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
if(!isset($params) || !(count($params) > 0)) return;
require_once dirname(__FILE__) . '/core/helper.php';

$layout = $params->get('layout', 'default');
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$is_ajax = $is_ajax || JRequest::getInt('is_ajax_listing_tabs', 0);
if ($is_ajax) {
    $listing_tabs_moduleid = JRequest::getVar('listing_tabs_moduleid', null);
//	$instance = rand() . time();
    if ($listing_tabs_moduleid == $module->id) {
		$instance = JRequest::getVar('sj_class_responsive',null);
		if ($params->get('filter_type') == "filter_categories") {
			$categoryid = JRequest::getVar('categoryid', null);
			$field_order = JRequest::getVar('field_order');
			$list = K2ListingTabsHelper::getList($params,$categoryid,$count = 0,$field_order,$tab_all_display = null);
		} else {
			$field_order = JRequest::getVar('field_order');
			$list = K2ListingTabsHelper::getList($params,$cgid = -1,$count = 0,$field_order,$tab_all_display = 1);
		}
			$result = new stdClass();
			ob_start();
			if(JRequest::getInt('type_js', 1) != 2){
				require JModuleHelper::getLayoutPath($module->module, $layout . '_items');
			}else{
				require JModuleHelper::getLayoutPath ( $module->module, $layout . '_items_responsive_content' );
			}
			$buffer = ob_get_contents();
			$result->items_markup = preg_replace(
				array(
					'/ {2,}/',
					'/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'
				),
				array(
					' ',
					''
				),
				$buffer
			);
			ob_end_clean();
			die ('k2_listing_tab_ajax:123::::123'.json_encode($result));
    }
	$sj_module_id = JRequest::getVar ( 'module_id', null );	
	if ($sj_module_id == $module->id) {
		$instance = JRequest::getVar('sj_class_responsive',null);
		$list = K2ListingTabsHelper::getList($params);
		ob_start ();
		require JModuleHelper::getLayoutPath ( $module->module, $layout . '_items_responsive_content' );					
		$buffer = ob_get_contents ();
		$result = preg_replace ( array ('/ {2,}/', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s' ), array (' ', '' ), $buffer );
		ob_end_clean ();
		die ($result);
	}
} else {
	$list = K2ListingTabsHelper::getList($params);
	$tabs = K2ListingTabsHelper::getCategoriesInfor($params);
    require JModuleHelper::getLayoutPath($module->module, $layout);
    require JModuleHelper::getLayoutPath($module->module, $layout . '_js');
}

?>
