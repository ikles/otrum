<?php
/*------------------------------------------------------------------------
# com_k2store - K2Store
# ------------------------------------------------------------------------
# author    Ramesh Elamathi - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die;
/**
 * K2StorePluginsj3Form Field class for the K2Store component
 */
class JFormFieldK2StorePluginsj3 extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'K2StorePluginsj3';

	protected function getInput() {

		$app = JFactory::getApplication();
		$product_id = $app->input->getInt('cid');

		JPluginHelper::importPlugin('k2store');
		$results = $app->triggerEvent('onK2StoreProductFormInput', array($product_id, $this));
		return trim(implode('/n', $results));

	}

	protected function getLabel() {

		$app = JFactory::getApplication();
		$product_id = $app->input->getInt('cid');

		JPluginHelper::importPlugin('k2store');
		$results = $app->triggerEvent('onK2StoreProductFormLabel', array($product_id, $this));
		return trim(implode('/n', $results));
	}

}