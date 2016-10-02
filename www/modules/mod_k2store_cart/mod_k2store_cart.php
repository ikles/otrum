<?php
/*------------------------------------------------------------------------
# mod_k2store_cart - K2 Store Cart
# ------------------------------------------------------------------------
# author    Ramesh Elamathi - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/



// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( dirname(__FILE__).'/helper.php' );
JFactory::getLanguage()->load('com_k2store', JPATH_SITE);

$moduleclass_sfx = $params->get('moduleclass_sfx','');
$link_type = $params->get('link_type','link');

$list = modK2StoreCartHelper::getItems();
$ajax = $app->getUserState('mod_k2store_mini_cart.isAjax', 0);
if($ajax == 1) {
	$layout = 'cart';
} else {
	$layout = $params->get('layout', 'default');
}
require( JModuleHelper::getLayoutPath('mod_k2store_cart', $layout));