<?php
/*------------------------------------------------------------------------
# com_k2store - K2 Store
# ------------------------------------------------------------------------
# author    Ramesh Elamathi - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/


/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.parameter' );
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/library/prices.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/library/tax.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/library/inventory.php');
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2store/tables');
class K2StoreHelperCart
{

	private $data = array();


	public function __construct() {

		$this->db = JFactory::getDbo();
		$cart = JFactory::getSession()->get('k2store_cart');
		if (!isset($cart) || !is_array($cart)) {
			JFactory::getSession()->set('k2store_cart', array());
		}
	}


public function add($product_id, $qty=1,  $options = array()) {

	$cart = JFactory::getSession()->get('k2store_cart');

	if (!$options) {
		$key = (int)$product_id;
	} else {
		$key = (int)$product_id . ':' . base64_encode(serialize($options));
	}

	if ((int)$qty && ((int)$qty > 0)) {
		if (!isset($cart[$key])) {
			$cart[$key] = (int)$qty;
		} else {
			$cart[$key] += (int)$qty;
		}
		JFactory::getSession()->set('k2store_cart', $cart);
	}

	$this->data = array();

}

public static function getProducts() {
	JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
	$model = JModelLegacy::getInstance( 'Mycart', 'K2StoreModel');
	$db = JFactory::getDbo();
	//get the products from the cart
	$cartitems = $model->getDataNew();

	//now we have to prepare this data for adding into order table object
	$productitems = array();

	$cartitem= array();


	foreach ($cartitems as $cartitem)
	{
		if ($productItem = K2StoreHelperCart::getItemInfo($cartitem['product_id']))
		{

			//base price
			$price = $productItem->price;

			//now get special price or discounted prices, if any
			$price_override = K2StorePrices::getPrice($productItem->product_id, $cartitem['quantity']);

			if(isset($price_override) && !empty($price_override)) {
				$price = $price_override->product_price;
			}

			//$productItem->price = $productItem->product_price = $cartitem->product_price;

			// TODO Push this into the orders object->addItem() method?
			$orderItem = JTable::getInstance('OrderItems', 'Table');
			$orderItem->product_id                    = $productItem->product_id;
			$orderItem->orderitem_sku                 = $productItem->product_sku;
			$orderItem->orderitem_name                = $productItem->product_name;
			$orderItem->orderitem_quantity            = $cartitem['quantity'];
			//original price
			$orderItem->orderitem_price               = $price;

			//save product options in the json format
			$product_options = K2StoreHelperCart::getReadableProductOptions($cartitem['option']);

			$orderItem->orderitem_attributes          = $db->escape($product_options->product_option_json);
			$orderItem->orderitem_attribute_names     = $db->escape($product_options->product_option_names);
			$orderItem->orderitem_attributes_price    = $cartitem['option_price'];
			$orderItem->orderitem_final_price         = ($orderItem->orderitem_price + $orderItem->orderitem_attributes_price) * $orderItem->orderitem_quantity;

			array_push($productitems, $orderItem);
		}
	}
	return $productitems;

}

public static function getReadableProductOptions($product_options) {

	//initialise values
	$item = new JObject();
	$item->product_option_json = '';
	$item->product_option_names = '';
	$json = '';
	$registry = new JRegistry;
	//load product options into array
	$registry->loadArray($product_options);
	//convert to json
	$json = $registry->toString('JSON');
	$item->product_option_json = $json;
	$option_data = array();
	//now just get the option names
	foreach ($product_options as $option) {

		$value = $option['option_value'];
		$option_data[] = array(
				'name'  => $option['name'],
				'value' => $value
		);
	}
	$registry->loadArray($option_data);
	$item->product_option_names = $registry->toString('JSON');
	return $item;
}


public static function getAjaxCart($item) {

		require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/helpers/strapper.php');
		K2StoreStrapper::addJS();
		K2StoreStrapper::addCSS();
		$app = JFactory::getApplication();
		$html = '';
		JLoader::register( "K2StoreViewMyCart", JPATH_SITE."/components/com_k2store/views/mycart/view.html.php" );
		$layout = 'addtocart';
		$view = new K2StoreViewMyCart( );
		//$view->_basePath = JPATH_ROOT.DS.'components'.DS.'com_k2store';
		$view->addTemplatePath(JPATH_SITE.'/components/com_k2store/views/mycart/tmpl');
		$view->addTemplatePath(JPATH_SITE.'/templates/'.$app->getTemplate().'/html/com_k2store/mycart');
		require_once (JPATH_SITE.'/components/com_k2store/models/mycart.php');
		$model =  new K2StoreModelMyCart();
		$product_id = $item->product_id = $item->id;
		$view->assign( '_basePath', JPATH_SITE.DS.'components'.DS.'com_k2store' );
		$view->set( '_controller', 'mycart' );
		$view->set( '_view', 'mycart' );
		$view->set( '_doTask', true );
		$view->set( 'hidemenu', true );
		$view->setModel( $model, true );
		$view->setLayout( $layout );
		$view->assign( 'product_id', $product_id);
		$config = JComponentHelper::getParams('com_k2store');
		$show_tax = $config->get('show_tax_total','1');
	//	$show_attributes = $config->get( 'show_product_attributes', 1);
		$view->assign( 'show_tax', $show_tax );
		$view->assign( 'params', $config );
		//$view->assign( 'show_attributes', $show_attributes );

		$item->product = $product = self::getItemInfo($product_id);
		$stock = $product->stock;

		if(isset($stock->min_sale_qty) && $stock->min_sale_qty > 1) {
			$item->product_quantity = (int) $stock->min_sale_qty;
			$item->item_minimum_notice = JText::sprintf('K2STORE_MINIMUM_QUANTITY_NOTIFICATION', $item->title, (int) $stock->min_sale_qty);
		} else {
			$item->product_quantity = 1;
		}

		//get attributes
		$attributes = $model->getProductOptions($product_id);

		if(count($attributes) && $product->stock->manage_stock == 1 && K2STORE_PRO == 1 && !$config->get('allow_backorder', 0)) {
			//get unavailable attribute options
			$attributes = $model->processAttributeOptions($attributes, $product);
			//print_r($attributes );
		}

		$item->attributes = $attributes;

		//get k2store product item
		$product = self::getItemInfo($product_id);
		$stock = K2StoreInventory::getStock($product_id);
		//quantity


		//get prices
		$item->prices = K2StorePrices::getPrice($product_id, $item->product_quantity);

		//base price
		$item->price = $item->prices->product_baseprice;

		//tax
		$t = new K2StoreTax();

		//assign tax class to the view. so that we dont have to call it everytime.
		$view->assign( 'tax_class', $t);

		$tax = $t->getProductTax($item->price,$item->product_id);
		$item->tax = isset($tax)?$tax:0;

		//special price
		$item->special_price = isset($item->prices->product_specialprice)? (float) $item->prices->product_specialprice: null;
		$sp_tax = $t->getProductTax($item->special_price,$item->product_id);
		$item->sp_tax = isset($sp_tax)?$sp_tax:0;

		//now get the total stock
		if(K2STORE_PRO == 1) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2store/models');
			$qtyModel = JModelLegacy::getInstance('ProductQuantities', 'K2StoreModel');
			$qtyModel->setState('filter_product', $item->product_id);
			$item->product_stock = $qtyModel->getQuantityTotal();
		} else {
			$item->product_stock = '';
		}

		$view->assign( 'attributes', $attributes );
		$view->assign( 'product', $product);
		$view->assign( 'params', $config );


		//trigger onBeforeAddtocart plugin event
		if(!isset($item->event) || !is_object($item->event)) {
			$item->event = new stdClass();
		}
		$item->event->K2StoreBeforeCartDisplay = '';
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin ('k2store');
		$results = $dispatcher->trigger( "onK2StoreBeforeCartDisplay", array( $item));
		$item->event->K2StoreBeforeCartDisplay = trim(implode("\n", $results));
		$view->assign( 'item', $item );

		ob_start( );
		$view->display( );
		$html = ob_get_contents( );
		ob_end_clean( );

		return $html;

}

public static function dispayPriceWithTax( $price = '0', $tax = '0', $plus='1')
	{
		$txt = '';
		//price+tax
		//first calculate tax
		if ( $plus==2 && $tax )
		{
			$txt .= K2StorePrices::number( $price+$tax );
			//$txt .= JText::sprintf('SHOW_TAX_WITH_PRICE', K2StorePrices::number($tax) );

		}elseif( $plus==3 && $tax )
		{
			$txt .= K2StorePrices::number( $price );
			$txt .= JText::sprintf('K2STORE_SHOW_TAX_WITH_PRICE', K2StorePrices::number($tax) );

		}
		else
		{
			$txt .= K2StorePrices::number( $price );
		}

		return $txt;
	}


	public static function getItemInfo($id) {

		static $itemsets;

		if ( !is_array( $itemsets) )
		{
			$itemsets= array( );
		}
		if(!isset($itemsets[$id])) {
			$db		=JFactory::getDBO();
			$query= "SELECT * FROM #__k2_items WHERE id=".$id;
			$db->setQuery($query);
			$row = $db->loadObject();
			//get k2store variables
			$product = K2StorePrices::getProduct($row->id);

			$item = $product;
			$item->product_id = $row->id;
			$item->product_name = $row->title;
			//
			$item->price = $product->item_price;
			$item->product_sku = $product->item_sku;
			$item->tax_profile_id = $product->item_tax_id;
			$item->item_shipping = $product->item_shipping;
			$item->item_cart_text = $product->item_cart_text;

			//we need item metrics so load info from the products table
			//TODO: Why cant we load all k2store data from this table itself

			$item->item_minimum = isset($product->item_minimum)?$product->item_minimum:'1';
			//get the stock data and assign to it
			$store_config = K2StoreHelperCart::getStoreAddress();

			if($item->use_store_config_min_out_qty > 0) {
				$item->min_out_qty = (float) $store_config->store_min_out_qty;
			}

			if($item->use_store_config_min_sale_qty > 0) {
				$item->min_sale_qty = (float) $store_config->store_min_sale_qty;
			}

			if($item->use_store_config_max_sale_qty > 0) {
				$item->max_sale_qty = (float) $store_config->store_max_sale_qty;
			}

			if($item->use_store_config_notify_qty > 0) {
				$item->notify_qty = (float) $store_config->store_notify_qty;
			}
			$item->stock = $item;
			$item->product = $row;
			$itemsets[$id] = $item;
		}
		return $itemsets[$id];

	}

	/**
	 * Given an order_id, will remove the order's items from the user's cart
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public static function removeOrderItems( $orderpayment_id )
	{
		// load the order to get the user_id
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
		$model = JModelLegacy::getInstance( 'MyCart', 'K2StoreModel' );
		$model->clear();
		return;
	}


	public static function getTaxes() {
		$tax_data = array();
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
		$model = JModelLegacy::getInstance( 'Mycart', 'K2StoreModel');
		$products = $model->getDataNew();
		$t = new K2StoreTax();
		foreach ($products as $product) {
			if ($product['tax_profile_id']) {
				$tax_rates = $t->getRateArray($product['price'], $product['tax_profile_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['taxrate_id']])) {
						$tax_data[$tax_rate['taxrate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['taxrate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public static function getWeight() {
		$weight = 0;
		$weightObject = K2StoreFactory::getWeightObject();
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
		$model = JModelLegacy::getInstance( 'Mycart', 'K2StoreModel');
		$products = $model->getDataNew();
		foreach ($products as $product) {
			if ($product['shipping']) {
				$weight += $weightObject->convert($product['weight_total'], $product['weight_class_id'], self::getStoreAddress()->config_weight_class_id);
			}
		}

		return $weight;
	}

	public static function getTotal() {
		$total = 0;
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
		$model = JModelLegacy::getInstance( 'Mycart', 'K2StoreModel');
		$products = $model->getDataNew();
		$t = new K2StoreTax();
		$tax_amount = 0;
		foreach ($products as $product) {
			//calculate the tax
			$tax_amount = $t->getProductTax($product['price'], $product['product_id']);
			if($tax_amount) {
				$total += ($product['price']+$tax_amount)*$product['quantity'];
			} else {
				$total += $product['price']*$product['quantity'];
			}
		}

		return $total;
	}

	public static function taxCalculate($price, $taxrate) {
		$tax_amount = 0;
		if($taxrate > 0) {
			$tax_amount = $price*$taxrate;
		}
		//add tax to price
		$price = $price+$tax_amount;

		return $price;
	}

	public static function getSubTotal() {
		$total = 0;
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
		$model = JModelLegacy::getInstance( 'Mycart', 'K2StoreModel');

		$products = $model->getDataNew();
		foreach ($products as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public static function countProducts() {
		$product_total = 0;

		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
		$model = JModelLegacy::getInstance( 'Mycart', 'K2StoreModel');

		$products = $model->getDataNew();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}
		return $product_total;
	}

	public static function hasProducts() {
		$cart_items = JFactory::getSession()->get('k2store_cart');
		return count($cart_items);
	}

	public static function getStoreAddress() {
		static $sets;

		if ( !is_array( $sets ) )
		{
			$sets = array( );
		}
		$id = 1;
		if(!isset($sets[$id])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__k2store_storeprofiles');
			$query->where('state=1');
			$query->order('store_id ASC LIMIT 1');
			$db->setQuery($query);
			$sets[$id]	=	$db->loadObject();
		}
		return $sets[$id];
	}

	public static function getCoupon($code) {
		$status = true;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__k2store_coupons')->where('coupon_code='.$db->q($code))->where('state=1');
		$query->where("((valid_from = '0000-00-00' OR valid_from < NOW()) AND (valid_to = '0000-00-00' OR valid_to > NOW()))");
		$db->setQuery($query);
		$row = $db->loadObject();

		//now get coupon history total
		$query = $db->getQuery(true);
		$query->select('COUNT(*) AS total')->from('#__k2store_order_coupons')->where('coupon_id='.$db->q($row->coupon_id));
		$db->setQuery($query);
		$coupon_history = $db->loadResult();

		$product_data = array();

		//now validate
		if($row) {
			//is used coupons count exceeds max use?
			if($row->max_uses > 0 && ($coupon_history >= $row->max_uses)) {
				$status = false;
			}

			//is customer loged
			if ($row->logged && !$user->id) {
				$status = false;
			}

			if ($user->id) {
				$query = $db->getQuery(true);
				$query->select('COUNT(*) AS total')->from('#__k2store_order_coupons')
				->where('coupon_id='.$db->q($row->coupon_id))
				->where('customer_id='.$db->q($user->id));
				$db->setQuery($query);
				$customer_total = $db->loadResult();
					if ($row->max_customer_uses > 0 && ($customer_total  >= $row->max_customer_uses)) {
					$status = false;
				}
			}


			//categories
			$coupon_categories_data = array();
			if($row->product_category) {
				$coupon_categories_data = explode(',', $row->product_category);
			}

			//products
			$coupon_products_data = array();

			if($row->products) {
				$coupon_products_data = explode(',', $row->products);
			}

			$product_data = array();

			if($coupon_categories_data || $coupon_products_data) {
				JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_k2store/models' );
				$model = JModelLegacy::getInstance( 'Mycart', 'K2StoreModel');
				$products = $model->getDataNew();

					foreach ($products as $product) {

						//first get the products
						if (in_array($product['product_id'], $coupon_products_data)) {
							$product_data[] = $product['product_id'];
							continue;
						}

						//now get the product data from categories
						foreach($coupon_categories_data as $category_id) {
							$query = $db->getQuery(true);
							$query->select('COUNT(*) AS total')->from('#__k2_items')
							->where('id='.$db->q($product['product_id']))
							->where('catid='.$db->q($category_id));

							$db->setQuery($query);
							if($db->loadResult()) {
								$product_data[]=$product['product_id'];
							}
							continue;
						}
					}

					//if no specific products available, set to false. Coupon will be applicable to all products.
					if (!$product_data) {
						$status = false;
					}
				}

		} else {
			$status = false;
		}

		//if true
		if ($status) {
			$data = $row;
			if($product_data) {
				$data->product = $product_data;
			} else {
				$data->product = array();
			}
			return $data;
		}

		return false;
	}
 }