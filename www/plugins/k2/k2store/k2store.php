<?php
/*
 * --------------------------------------------------------------------------------
   Weblogicx India  - K2 Store
 * --------------------------------------------------------------------------------
 * @package		Joomla! 1.5x
 * @subpackage	K2 Store
 * @author    	Weblogicx India http://www.weblogicxindia.com
 * @copyright	Copyright (c) 2010 - 2015 Weblogicx India Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link		http://weblogicxindia.com
 * --------------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die ('Restricted access');
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php');
require_once (JPATH_SITE.'/components/com_k2store/helpers/utilities.php');
require_once (JPATH_SITE.'/components/com_k2store/helpers/cart.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/library/prices.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/version.php');
JLoader::register('K2Parameter', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2parameter.php');
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2store/tables');
class plgK2K2Store extends K2Plugin {

	// Some params
	var $pluginName = 'k2store';
	var $pluginNameHumanReadable = 'K2 Store';

	function plgK2K2Store( & $subject, $params) {

		parent::__construct($subject, $params);
		//$this->loadLanguage( '', JPATH_ADMINISTRATOR );
		$lang = JFactory::getLanguage();
		$lang->load('com_k2store');
	}

	public function onK2PrepareContent(&$item, &$params, $page=0)
	{

		$app = JFactory::getApplication();
		// Bail out if the page is not HTML
		if($app->input->getCmd('format')!='html' && $app->input->getCmd('format')!='') return;

		// simple performance check to determine whether plugin should process further
		if(JString::strpos($item->text, 'k2storecart') === false) return;
				$k2params = JComponentHelper::getParams('com_k2store');
				if($k2params->get('addtocart_placement', 'default') == 'default') {
					return true;
				}
				// expression to search for k2storecart
				$regex_with_id		= '/{k2storecart\s+(.*?)}/i';
				$regex_without_id	= '/{k2storecart\}/i';

				// Find all instances of plugin and put in $matches for loading k2store cart
				// $matches[0] is full pattern match, $matches[1] is the article id
				preg_match_all($regex_with_id, $item->text, $results_with_id, PREG_SET_ORDER);
				// No matches, skip this
				if($results_with_id) {
					$matches = $results_with_id;
				} else {
					preg_match_all($regex_without_id, $item->text, $results_without_id, PREG_SET_ORDER);
					$matches = $results_without_id;
				}

				if ($matches) {
					foreach ($matches as $match) {
						//$match[0] has the text.
						//check again
						$item_id = NULL;
						if (empty($match[1])) {
							$item_id = $item->id;
						} else {
							$item_id = (int) $match[1];
						}

						if(empty($item_id)) {
							return true;
						}

						$product = new JObject();
						$product->id = $item_id;
						$product->text = $item->text;
						$output = $this->_loadCart($product, $params);
						$item->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $item->text, 1);
					}
				}
	}

	function onK2AfterDisplay( & $item, & $params, $limitstart) {

		$mainframe = JFactory::getApplication();

		$k2params = JComponentHelper::getParams('com_k2store');
		if($k2params->get('addtocart_placement', 'default') == 'tag') {
			$output = '';
		} else {
			$output = $this->_loadCart($item, $params);
		}
		return $output;

	}

	function onAfterK2Save($row, $isNew) {

		$app = JFactory::getApplication();
		if($app->isSite()) {
			return;
		}
		JPluginHelper::importPlugin('k2store');

		$app->triggerEvent('onK2StoreProductFormBeforeSave', array($row));

		$plugins = $this->_getPluginData($row);

		$this->_addProduct($row);
		$this->_addProductOptions($row);
		if(K2STORE_PRO ==1) {
		$this->_addProductStock($row);
		}
		$this->_addMetrics($row);

		$app->triggerEvent('onK2StoreProductFormAfterSave', array($row));

	}

	 function onRenderAdminForm( & $item, $type, $tab='') {

	 	$app = JFactory::getApplication();

		if($type == 'item' && $tab == 'content') {

			if(!$app->isSite()) {

				//render the form
				if ( !empty ($tab)) {
					$path = $type.'-'.$tab;
				}
				else {
					$path = $type;
				}
				if (!isset($item->plugins))
				{
					$item->plugins = NULL;
				}

				$xml_file = JPATH_SITE.'/plugins/k2/'.$this->pluginName.'/'.$this->pluginName.'.xml';

				if (version_compare(JVERSION, '3.0', 'ge')) {

					jimport('joomla.form.form');
					$form = JForm::getInstance('plg_k2_'.$this->pluginName.'_'.$path, $xml_file, array(), true, 'fields[@group="'.$path.'"]');
					//print_r($form);
					$values = array();
					if ($item->plugins)
					{
						foreach (json_decode($item->plugins) as $name => $value)
						{
							$count = 1;
							$values[str_replace($this->pluginName, '', $name, $count)] = $value;
						}
						$form->bind($values);
					}
					$fields = '';
					foreach ($form->getFieldset() as $field)
					{
						$search = 'name="'.$field->name.'"';
						$replace = 'name="plugins['.$this->pluginName.$field->name.']"';
						$input = JString::str_ireplace($search, $replace, $field->__get('input'));
						$fields .= $field->__get('label').' '.$input;
					}
				} else {
					$form = new K2Parameter($item->plugins, $xml_file, $this->pluginName);
				    $fields = $form->render('plugins', $path);
				}

				if ($fields){
					$plugin = new JObject;
					$plugin->set('name', JText::_( 'K2Store' ));
					$plugin->set('fields', $fields);
					return $plugin;
				}

			}

		}

	}

	function _addProduct($row) {

		$plugins = $this->_getPluginData($row);
		$product_id = $row->id;

		if($product_id > 0) {

			$item = JTable::getInstance('Products','Table');
			$item->load(array('product_id'=>$product_id));

			$item_sku =  $plugins->get('item_sku');
			$item_price =  $plugins->get('item_price');
			$special_price =  $plugins->get('special_price');
			$item_tax =  $plugins->get('item_tax');
			$item_shipping =  $plugins->get('item_shipping');
			$item_enabled =  $plugins->get('item_enabled');

			//save basic product info
			$data = array();
			$data['product_id'] = $product_id;
			$data['item_sku'] =  (!empty($item_sku))?$item_sku:'';
			$data['item_price'] =  (!empty($item_price))?$item_price:'0.00000';
			$data['special_price'] = (!empty($special_price))?$special_price:'0.00000';
			$data['item_tax'] =  (!empty($item_tax))?$item_tax:'0';
			$data['item_shipping'] =  (!empty($item_shipping))?$item_shipping:'0';
			$data['item_enabled'] =  (!empty($item_enabled))?$item_enabled:'0';

			//set this if this has an id. If not this will become a new one.
			if($item->p_id){
				$data['p_id'] = $item->p_id;
			}

				$row = JTable::getInstance('Products','Table');
				$row->bind($data);
				$row->store();


		}
	}

	function _addProductOptions($row) {

		$plugins = $this->_getPluginData($row);

		//get option IDs and save them as product option ids
		$pa_options = $plugins->get('item_option')->product_option_ids;

		//get whether the option is required.
		$pa_option_required = $plugins->get('item_option')->product_option_required;

		if(isset($pa_options) && count($pa_options) && isset($pa_option_required) ) {

			//convert option required values object to array
			$registry = new JRegistry;
			$registry->loadObject($pa_option_required );
			$pa_option_required = $registry->toArray();

			foreach ($pa_options as $option_id) {
				$table =  JTable::getInstance('ProductOptions', 'Table');
				//save this stock in the product quantities table.
				$table->product_id = $row->id;
				$table->option_id = $option_id;
				if($pa_option_required[$option_id]) {
					$table->required = 1;
				} else {
					$table->required = 0;
				}
				$table->store();
			}

		}

		//if user modified his option preferences, we got to get the changes and save them as well.
		$modified_option_required = $plugins->get('item_option')->product_option_required_save;
		if(isset($modified_option_required) && count($modified_option_required ) ) {
			foreach($modified_option_required as $po_id=>$value) {
				$item =  JTable::getInstance('ProductOptions', 'Table');
				$item->load($po_id);
				$item->required = $value;
				$item->store();
			}
		}

		$manage_stock = $plugins->get('item_option')->product_option_manage_stock;
		if(isset($manage_stock) && count($manage_stock) ) {
			foreach($manage_stock as $po_id=>$value) {
				$item =  JTable::getInstance('ProductOptions', 'Table');
				$item->load($po_id);
				$item->manage_stock = $value;
				$item->store();
			}
		}

	}

	private function _addProductStock($row) {

		$plugins = $this->_getPluginData($row);
		$product_id = $row->id;

		if($product_id > 0) {

			$stock = $plugins->get('item_stock');

			//get the table
			$table = JTable::getInstance('Products', 'Table');

			try {
				$table->load(array('product_id'=>$product_id));
			}catch (Exception $e) {
				//do nothing. It may be a new record
			}

			//bind the data
			try {
				$table->bind($stock);
			}catch (Exception $e) {
				//no data found. so return
				return;
			}

			//now we need to see if the checkboxes are not set. If not, then we got to zero them
			if(!isset($stock->use_store_config_min_out_qty)) {
				$table->use_store_config_min_out_qty = 0;
			}

			if(!isset($stock->use_store_config_min_sale_qty)) {
				$table->use_store_config_min_sale_qty = 0;
			}

			if(!isset($stock->use_store_config_max_sale_qty)) {
				$table->use_store_config_max_sale_qty = 0;
			}

			if(!isset($stock->use_store_config_notify_qty)) {
				$table->use_store_config_notify_qty = 0;
			}

			$table->product_id = $row->id;

			$table->store();
		}

	}


	function _addMetrics($row) {

		$plugins = $this->_getPluginData($row);
		$product_id = $row->id;

		$metrics = $plugins->get('item_metrics');

		if($product_id > 0) {
			$row = JTable::getInstance('Products','Table');
			$row->load(array('product_id'=>$product_id));
			//save metrics
			if(isset($metrics ) && is_object($metrics)) {
				$data = JArrayHelper::fromObject($metrics);

				$data['p_id'] = $row->p_id;
				$data['product_id'] = $product_id;
				$row = JTable::getInstance('Products','Table');
				$row->bind($data);
				$row->store();
			}
		}


	}


	//function to get plugin data

	protected function _getPluginData($row) {


		$pluginName = 'k2store';

		if(JVERSION==1.7) {
			// Get the output of the K2 plugin fields (the data entered by your site maintainers)
			$plugins = new JParameter($row->plugins, '', $pluginName);
		} else {
			$plugins = new K2Parameter($row->plugins, '', $pluginName);
		}

		return $plugins;
	}

	protected function _loadCart($item, $params){

		$lang = JFactory::getLanguage();
		$lang->load('com_k2store');
		if(empty($item->id) || is_int($item->id == false)) {
			return '';
		}
		//$product = K2StorePrices::_getK2StoreVars($item->id);
		$product = K2StoreHelperCart::getItemInfo($item->id);
		// show/hide add to cart button
		$output = '';
		if(isset($product->item_enabled) && $product->item_enabled == 1) {
			$output = K2StoreHelperCart::getAjaxCart($item);
		}

		return $output;
	}

} // END CLASS
