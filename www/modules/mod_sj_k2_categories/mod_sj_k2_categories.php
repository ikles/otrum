<?php
/**
 * @package Sj K2 Categories
 * @version 2.5.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2016 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;
if(!isset($params) || !(count($params) > 0)) return;
// Include the helper functions only once
require_once dirname(__FILE__).'/core/helper.php';
$list = SjK2CategoriesHelper::getCategories($params);
if ( !empty($list) ) {
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
	require JModuleHelper::getLayoutPath('mod_sj_k2_categories', $params->get('layout', 'theme1'));
}
else { echo JText::_('Has no content to show!');}
 