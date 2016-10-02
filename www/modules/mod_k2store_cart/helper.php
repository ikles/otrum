<?php
/*------------------------------------------------------------------------
# mod_k2store_cart - K2Store Cart
# ------------------------------------------------------------------------
# author    Ramesh Elamathi - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/



// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_SITE.'/components/com_k2store/helpers/cart.php');
class modK2storeCartHelper {

	public static function getItems() {

		$list = array();

		$k2params = JComponentHelper::getParams('com_k2store');

		if(K2StoreHelperCart::hasProducts()) {
			require_once(JPATH_SITE.'/components/com_k2store/models/mycart.php');
			$cart_model = new K2StoreModelMyCart();
			$totals = $cart_model->getTotals();
			$product_count = K2StoreHelperCart::countProducts();

			if($k2params->get('auto_calculate_tax', 1)) {
				$total = $totals['total'];
			} else {
				$total = $totals['total_without_tax'];
			}
			$list['total'] = $total;
			$list['product_count'] = $product_count;
			//$html = JText::sprintf('k2store_CART_TOTAL', $product_count, k2storePrices::number($total));
		} else {
			$list['total'] = 0;
			$list['product_count'] = 0;
			//$html = JText::_('k2store_NO_ITEMS_IN_CART');
		}

		return $list;
	}
}