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



// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
//load the cart data
require_once (JPATH_COMPONENT.'/helpers/cart.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/library/tax.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/library/inventory.php');
class K2StoreControllerMyCart extends K2StoreController
{

	private $_data = array();

	var $tax = null;

	public function __construct($config = array())
		{
			parent::__construct($config);
			$this->tax = new K2StoreTax();
			//language
			$language = JFactory::getLanguage();
			/* Set the base directory for the language */
			$base_dir = JPATH_SITE;
			/* Load the language. IMPORTANT Becase we use ajax to load cart */
			$language->load('com_k2store', $base_dir, $language->getTag(), true);
		}

	function display($cachable = false, $urlparams = array()) {

		//initialist system objects
		$app = JFactory::getApplication();
		$session=  JFactory::getSession();
		K2StoreUtilities::cleanCache();
		$params = JComponentHelper::getParams('com_k2store');
		$view = $this->getView( 'mycart', 'html' );
		$view->set( '_view', 'mycart' );
		//get post vars
		$post = $app->input->getArray($_POST);

		$model = $this->getModel('Mycart');
		$checkout_model = $this->getModel('checkout');
		$store = K2StoreHelperCart::getStoreAddress();
        if (K2StoreHelperCart::hasProducts()) {
        	$items = $model->getDataNew();
        } else {
        	$items = array();
        }

        //coupon
        $post_coupon = $app->input->getString('coupon', '');
        //first time applying? then set coupon to session
        if (isset($post_coupon) && !empty($post_coupon)) {
        	try {
        		if($this->validateCoupon()) {
        			$session->set('coupon', $post_coupon, 'k2store');
        			$msg = JText::_('K2STORE_COUPON_APPLIED_SUCCESSFULLY');
        		}
        	} catch(Exception $e) {
        		$msg = $e->getMessage();
        	}
        	$this->setRedirect( JRoute::_( "index.php?option=com_k2store&view=mycart"), $msg);
        }

        if ($post_coupon) {
        	$view->assign( 'coupon', $post_coupon);
        } elseif ($session->has('coupon', 'k2store')) {
        	$view->assign( 'coupon', $session->get('coupon', '', 'k2store'));
        } else {
        	$view->assign( 'coupon', '');
        }

        //shipping tax calculator
        //get countries
        $db = JFactory::getDbo();
        $db->setQuery($db->getQuery(true)->select('country_id, country_name')->from('#__k2store_countries')->where('state=1'));
        $countries = $db->loadObjectList();

        $country_id = $app->input->getInt('country_id');

        if (isset($country_id)) {
        	$session->set('billing_country_id', $country_id, 'k2store');
        	$session->set('shipping_country_id', $country_id, 'k2store');
        } elseif ($session->has('shipping_country_id', 'k2store')) {
        	$country_id = $session->get('shipping_country_id', '', 'k2store');
        } else {
        	$country_id = $store->country_id;
        }

        $countryList = JHtml::_('select.genericlist', $countries, 'country_id', $attribs = null, $optKey = 'country_id', $optText = 'country_name', $country_id, $idtag = 'cart_country', $translate = false);

        $zone_id = $app->input->getInt('zone_id');
        if (isset($zone_id)) {
        	$session->set('billing_zone_id', $zone_id, 'k2store');
        	$session->set('shipping_zone_id', $zone_id, 'k2store');
        } elseif($session->has('shipping_zone_id', 'k2store')) {
        	$zone_id = $session->get('shipping_zone_id', '', 'k2store');
        } else {
        	$zone_id = $store->zone_id;
        }

        $postcode = $app->input->getString('postcode');

        if (isset($postcode )) {
        	$session->set('shipping_postcode', $postcode, 'k2store');
        } elseif ($session->has('shipping_postcode', 'k2store')) {
        	$postcode = $session->get('shipping_postcode', '', 'k2store');
        } else {
        	$postcode = $store->store_zip;
        }

        $view->assign( 'countryList', $countryList);
        $view->assign( 'country_id', $country_id);
        $view->assign( 'zone_id', $zone_id);
        $view->assign( 'postcode', $postcode);


        //assign a single selected method if it had been selected
        if($session->has('shipping_values', 'k2store')) {

        	//get exisitng values
        	$shipping_values = $session->get('shipping_values', array(), 'k2store');

        	$rates = $checkout_model->getShippingRates();
        	$session->set('shipping_methods', $rates, 'k2store');
        	if(count($rates) < 1) {
        		$session->set('shipping_method', array(), 'k2store');
        	}
        	$is_same = false;
        	foreach($rates as $rate) {

        		if($shipping_values['shipping_name'] == $rate['name']) {
        			$shipping_values['shipping_price']    = isset($rate['price']) ? $rate['price'] : 0;
        			$shipping_values['shipping_extra']   = isset($rate['extra']) ? $rate['extra'] : 0;
        			$shipping_values['shipping_code']     = isset($rate['code']) ? $rate['code'] : '';
        			$shipping_values['shipping_name']     = isset($rate['name']) ? $rate['name'] : '';
        			$shipping_values['shipping_tax']      = isset($rate['tax']) ? $rate['tax'] : 0;
        			$shipping_values['shipping_plugin']     = isset($rate['element']) ? $rate['element'] : '';
        			$session->set('shipping_method', $shipping_values['shipping_plugin'], 'k2store');
        			$session->set('shipping_values', $shipping_values, 'k2store');
        			$is_same = true;
        		}
        	}
        	if($is_same === false ) {
        		//sometimes the previously selected method may not apply. In those cases, we will have remove the selected shipping.
        		$session->set('shipping_values', array(), 'k2store');
        	}
        	$view->assign( 'shipping_values', $session->get('shipping_values', array(), 'k2store'));
        } else {
        	$view->assign( 'shipping_values', array());
        }

        //do we have shipping methods
        if($session->has('shipping_methods', 'k2store')) {
        	$view->assign( 'shipping_methods', $session->get('shipping_methods', array(), 'k2store'));
        }
        //assign a single selected method if it had been selected
        if($session->has('shipping_method', 'k2store')) {
        	$view->assign( 'shipping_method', $session->get('shipping_method', array(), 'k2store'));
        } else {
        	$view->assign( 'shipping_method', array());
        }


		$cartobject = $model->checkItems($items, $params->get('show_tax_total'));

		$totals = $model->getTotals();

		JPluginHelper::importPlugin('k2store');
		$results = $app->triggerEvent('onK2StoreAfterDisplayCart',array($items));
		$view->assign('onK2StoreAfterDisplayCart',trim(implode("\n", $results)));

		$view->assign( 'cartobj', $cartobject);
		$view->assign( 'totals', $totals);
		$view->assign( 'model', $model);
		$view->assign( 'params', $params );
		if(isset($post['return'])) {
			$view->assign( 'return', $post['return']);
		}
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->setLayout( 'default');
		$view->display();

	}


	function add() {
		$app = JFactory::getApplication();
		JFactory::getDocument()->setCharset('utf-8');
		$params = JComponentHelper::getParams('com_k2store');
		$model = $this->getModel('mycart');
		$cart_helper = new K2StoreHelperCart();
		require_once(JPATH_COMPONENT.'/helpers/cart.php');
		$error = array();
		$json = array();
		//get the product id
		$product_id = $app->input->getInt('product_id', 0);

		//no product id?. return an error
		if(empty($product_id)) {
			$error['error']['product']=JText::_('K2STORE_ADDTOCART_ERROR_MISSING_PRODUCT_ID');
			echo json_encode($error);
			$app->close();
		}

		//Ok. we have a product id. so proceed.
		//get the quantity
		$quantity = $app->input->get('product_qty');
		if (isset($quantity )) {
			$quantity = $quantity;
		} else {
			$quantity = 1;
		}

		$product = $cart_helper->getItemInfo($product_id);

		//get the product options
		$options = $app->input->get('product_option', array(0), 'ARRAY');
		if (isset($options )) {
			$options =  array_filter($options );
		} else {
			$options = array();
		}
		$product_options = $model->getProductOptions($product_id);

		//iterate through stored options for this product and validate
		foreach($product_options as $product_option) {
			if ($product_option['required'] && empty($options[$product_option['product_option_id']])) {
				$json['error']['option'][$product_option['product_option_id']] = JText::sprintf('K2STORE_ADDTOCART_PRODUCT_OPTION_REQUIRED', $product_option['option_name']);
			}
		}


		//trigger before addtocart plugin event now... send post values
		$post_data = $app->input->getArray($_POST);
		JPluginHelper::importPlugin ('k2store');
		$results = $app->triggerEvent("onK2StoreBeforeAddCart", array( $post_data ));
		if(isset($results) && count($results)) {
			foreach ($results as $result) {
				if(!empty($result['error'])) {
					$json['warning'] =$result['error'];
				}

			}
		}
		//validation is ok. Now add the product to the cart.
		if(!$json) {
			$cart_helper->add($product_id, $quantity, $options);

			//trigger plugin event- after addtocart
			$app->triggerEvent( "onK2StoreAfterAddCart", array( $post_data));

			$product_info = K2StoreHelperCart::getItemInfo($product_id);
			$cart_link = JRoute::_('index.php?option=com_k2store&view=mycart');
			$json['success'] = true;
			$json['successmsg'] = $product_info->product_name.JText::sprintf('K2STORE_ADDTOCART_ADDED_TO_CART');

			//$total =  K2StoreHelperCart::getTotal();
			$totals = $model->getTotals();
			if($params->get('auto_calculate_tax', 1)) {
				$total = $totals['total'];
			} else {
				$total = $totals['total_without_tax'];
			}
			$product_count = K2StoreHelperCart::countProducts();
			//get product total
			$json['total'] = JText::sprintf('K2STORE_CART_TOTAL', $product_count, K2StorePrices::number($total));
			//get cart info

			//do we have to redirect to the cart
			if($params->get('popup_style', 1)==3) {
				$json['redirect'] = $cart_link;
			}


		} else {

			//do we have to redirect
		//	$url = 'index.php?option=com_k2&view=item&id='.$product_id;
		//	$json['redirect'] = JRoute::_($url);
		}
		echo json_encode($json);
		$app->close();
	}


function ajaxmini() {
			//initialise system objects
			$app = JFactory::getApplication();
		$document	= JFactory::getDocument();

		$db = JFactory::getDbo();
		$language = JFactory::getLanguage()->getTag();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__modules')->where('module='.$db->q('mod_k2store_cart'))->where('published=1')
			->where('language='.$db->q($language));
		$db->setQuery($query);
		$modules = $db->loadObjectList();
		if(count($modules) < 1) {
			$query = $db->getQuery(true);
			$query->select('*')->from('#__modules')->where('module='.$db->q('mod_k2store_cart'))->where('published=1')
			->where('language="*" OR language="en-GB"');
			$db->setQuery($query);
			$modules = $db->loadObjectList();
		}

		$renderer	= $document->loadRenderer('module');
		$json = array();
		if (count($modules) < 1)
		{
			$json['response'][$module->id] = '';
		} else {
			foreach($modules as $module) {
				$app->setUserState( 'mod_k2store_mini_cart.isAjax', '1' );
				$json['response'][$module->id] = $renderer->render($module);
			}
			echo json_encode($json);
		$app->close();

		}
		$app->close();
	}



	function displayCart()
	{
		$app = JFactory::getApplication();
		$document	= JFactory::getDocument();

		$db = JFactory::getDbo();
		$language = JFactory::getLanguage()->getTag();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__modules')->where('module='.$db->q('mod_k2store_detailcart'))->where('published=1')->where('language='.$db->q($language));
		$db->setQuery($query);
		$modules = $db->loadObjectList();

		if(count($modules) < 1) {
			$query = $db->getQuery(true);
			$query->select('*')->from('#__modules')->where('module='.$db->q('mod_k2store_detailcart'))->where('published=1')
			->where('language="*" OR language="en-GB"');
			$db->setQuery($query);
			$modules = $db->loadObjectList();
		}

		$renderer	= $document->loadRenderer('module');
		if (count($modules) < 1)
		{
			//echo '';
			$app->close();
		} else {
			foreach($modules as $module) {
				$app->setUserState( 'mod_k2storecart.isAjax', '1' );
				echo $renderer->render($module);
			}
			$app->close();

		}
		$app->close();
	}

	 /**
     *
     * @return unknown_type
     */
    function update()
    {
        $app = JFactory::getApplication();
        $params = JComponentHelper::getParams('com_k2store');
        $model 	= $this->getModel('mycart');
        $errors= array();
        $key = $app->input->getString('key');

        $quantities = $app->input->get('quantity', array(0), 'ARRAY');
        $original_quantities = $app->input->get('original_quantities', array(0), 'ARRAY');
	   	$msg = JText::_('K2STORE_CART_UPDATED');

        $remove = $app->input->get('remove');
        $removeCoupon = $app->input->getInt('removeCoupon', 0);
        if ($remove)
        {
        	$model->remove($key);
        }elseif($removeCoupon) {
        	$model->removeCoupon();
        }
        else
        {
            foreach ($quantities as $key=>$value)
            {
               $model->update($key, $value);
            }
        }

        if($remove || $removeCoupon) {
			$items = $model->getDataNew();

			$cartobject = $model->checkItems($items, $params->get('show_tax_total'));

			$view = $this->getView( 'mycart', 'html' );
			$view->set( '_view', 'mycart' );
			$view->set( '_doTask', true);
			$view->set( 'hidemenu', true);
			$view->setLayout( 'default');

			$totals = $model->getTotals();

			JPluginHelper::importPlugin('k2store');
			$results = $app->triggerEvent('onK2StoreAfterDisplayCart',array($items));
			$view->assign('onK2StoreAfterDisplayCart',trim(implode("\n", $results)));

			$view->assign( 'cartobj', $cartobject);
			$view->assign( 'totals', $totals);
			$view->setModel( $model, true );
			$view->assign( 'params', $params );
			$view->assign( 'remove', $remove);

			ob_start();
			$view->display();
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
			$app->close();
		}

        $redirect = JRoute::_( "index.php?option=com_k2store&view=mycart");
       	$this->setRedirect( $redirect, $msg);
    }


    function validateCoupon() {

    	$app = JFactory::getApplication();
    	$coupon_info = K2StoreHelperCart::getCoupon($app->input->getString('coupon', ''));

    	if($coupon_info ) {
    		return true;
    	} else {
			throw new Exception(JText::_('K2STORE_COUPON_INVALID'));
    		return false;
    	}


    }



    function setcurrency() {

    	$app = JFactory::getApplication();
    	$currency = K2StoreFactory::getCurrencyObject();
    	$post = $app->input->getArray($_POST);
    	if(isset($post['currency_code'])) {
    		$currency->set($post['currency_code']);
    	}

    	//get the redirect
    	if(isset($post['redirect'])) {
    		$url = base64_decode($post['redirect']);
    	} else {
    		$url = 'index.php';
    	}

    	$app->redirect($url);
    }

    function estimate() {

    	$app = JFactory::getApplication();
    	$session = JFactory::getSession();
    	$country_id = $app->input->getInt('country_id', 0);
    	$zone_id = $app->input->getInt('zone_id', 0);
    	$postcode  = $app->input->getString('postcode', 0);
    	$cart_model = $this->getModel('mycart');
    	$checkout_model = $this->getModel('checkout');

    	if($country_id || $zone_id) {
    		if($country_id) {
    			$session->set('billing_country_id', $country_id, 'k2store');
    			$session->set('shipping_country_id', $country_id, 'k2store');
    		}

    		if($zone_id) {
    			$session->set('billing_zone_id', $zone_id, 'k2store');
    			$session->set('shipping_zone_id', $zone_id, 'k2store');
    		}
    	}

    	if($postcode) {
    		$session->set('shipping_postcode', $postcode, 'k2store');
    	}
    	$showShipping = false;
    	if ($isShippingEnabled = $cart_model->getShippingIsEnabled())
    	{
    		$showShipping = true;
    	}

    	if($showShipping)
    	{
    		$rates = $checkout_model->getShippingRates();
    		$session->set('shipping_methods', $rates, 'k2store');
    		if(count($rates) < 1) {
    			$session->set('shipping_method', array(), 'k2store');
    		}
    	}

    	$url = JRoute::_('index.php?option=com_k2store&view=mycart');
    	echo json_encode(array('redirect'=>$url));
    	$app->close();

    }

    function shippingUpdate() {

    	$app = JFactory::getApplication();
    	$session = JFactory::getSession();
    	$values = $app->input->getArray($_POST);

    	$shipping_values = array();
    	$shipping_values['shipping_price']    = isset($values['shipping_price']) ? $values['shipping_price'] : 0;
    	$shipping_values['shipping_extra']   = isset($values['shipping_extra']) ? $values['shipping_extra'] : 0;
    	$shipping_values['shipping_code']     = isset($values['shipping_code']) ? $values['shipping_code'] : '';
    	$shipping_values['shipping_name']     = isset($values['shipping_name']) ? $values['shipping_name'] : '';
    	$shipping_values['shipping_tax']      = isset($values['shipping_tax']) ? $values['shipping_tax'] : 0;
    	$shipping_values['shipping_plugin']     = isset($values['shipping_plugin']) ? $values['shipping_plugin'] : '';

    	$session->set('shipping_method', $shipping_values['shipping_plugin'], 'k2store');
    	$session->set('shipping_values', $shipping_values, 'k2store');

    	$redirect = JRoute::_('index.php?option=com_k2store&view=mycart');
    	echo json_encode(array('redirect'=>$redirect));
    	$app->close();
    }

}
