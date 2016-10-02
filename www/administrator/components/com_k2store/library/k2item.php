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
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2store/tables');
class K2StoreItem
{

		/**
	 *
	 * @return unknown_type
	 */
	public static function display( $articleid )
	{
		$html = '';

		$item= K2StoreItem::_getK2Item($articleid);
		// Return html if the load fails
		if (!$item->id)
		{
			return $html;
		}

		$item->title = JFilterOutput::ampReplace($item->title);


		//import plugins

		$item->text = '';

		$item->text = $item->introtext . chr(13).chr(13) . $item->fulltext;

		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$params		=JComponentHelper::getParams('com_k2');
		$dispatcher = JDispatcher::getInstance();

		// process k2 plugins

		//Init K2 plugin events
		$item->event = new JObject();
		$item->event->K2BeforeDisplay = '';
		$item->event->K2AfterDisplay = '';
		$item->event->K2AfterDisplayTitle = '';
		$item->event->K2BeforeDisplayContent = '';
		$item->event->K2AfterDisplayContent = '';
		$item->event->K2CommentsCounter = '';


		JPluginHelper::importPlugin('k2');
		$results = $dispatcher->trigger('onK2BeforeDisplay', array(&$item, &$params, $limitstart));
		$item->event->K2BeforeDisplay = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onK2AfterDisplay', array(&$item, &$params, $limitstart));
		$item->event->K2AfterDisplay = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onK2AfterDisplayTitle', array(&$item, &$params, $limitstart));
		$item->event->K2AfterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onK2BeforeDisplayContent', array(&$item, &$params, $limitstart));
		$item->event->K2BeforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onK2AfterDisplayContent', array(&$item, &$params, $limitstart));
		$item->event->K2AfterDisplayContent = trim(implode("\n", $results));

		$dispatcher->trigger('onK2PrepareContent', array(&$item, &$params, $limitstart));
		$item->introtext = $item->text;


		// Use param for displaying article title
		$k2store_params = JComponentHelper::getParams('com_k2store');
		$show_title = $k2store_params->get('show_title', $params->get('show_title') );
		if ($show_title)
		{
			$html .= "<h3>{$item->title}</h3>";
		}
		$html .= $item->introtext;

		return $html;
	}

	public static function getK2Image($id, $k2params=NULL) {

		$app = JFactory::getApplication();
		$k2params =JComponentHelper::getParams('com_k2store');

		jimport('joomla.filesystem.file');

		//get the params right first
		$image_source = $k2params->get('show_thumb_cart');

		$image = '';
		$image_path = '';

		if($image_source == 'within_text') {
			$item= K2StoreItem::_getK2Item($id);

			$image_path = K2StoreItem::getImages($item->introtext);
			$image = '<img src="'.$image_path.
					'" class="itemImg'.$k2params->get('cartimage_size','small').'" />';

		} elseif($image_source == 'intro') {

			$image_size = $k2params->get('cartimage_size','small');

			if($image_size == 'Large') {
				$size = '_L';
			} elseif($image_size == 'Medium') {
				$size = '_M';
			} else {
				$size = '_S';
			}

			if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$id).$size.'.jpg')) {
				$image_path = JURI::root().'media/k2/items/cache/'.md5("Image".$id).$size.'.jpg';
			}

		} else {
			$image_path = '';
		}

		return $image_path;

	}

	public static function getImages($text) {
        $matches = array();
		preg_match("/\<img.+?src=\"(.+?)\".+?\/>/", $text, $matches);
		$images = '';
		$images = false;
		$paths = array();
		if (isset($matches[1])) {

			$image_path = $matches[1];

			//joomla 1.5 only
			$full_url = JURI::base();

			//remove any protocol/site info from the image path
			$parsed_url = parse_url($full_url);

			$paths[] = $full_url;
			if (isset($parsed_url['path']) && $parsed_url['path'] != "/") $paths[] = $parsed_url['path'];


			foreach ($paths as $path) {
				if (strpos($image_path,$path) !== false) {
					$image_path = substr($image_path,strpos($image_path, $path)+strlen($path));
				}
			}

			// remove any / that begins the path
			if (substr($image_path, 0 , 1) == '/') $image_path = substr($image_path, 1);

			//if after removing the uri, still has protocol then the image
			//is remote and we don't support thumbs for external images
			if (strpos($image_path,'http://') !== false ||
				strpos($image_path,'https://') !== false) {
				return false;
			}

			$images = JURI::Root(True)."/".$image_path;
			}
		return $images;
	}

	public static function isShippingEnabled($id) {
		//TODO:: depricate and move to prices library
		require_once(JPATH_ADMINISTRATOR.'/components/com_k2store/library/prices.php');
		return K2StorePrices::isShippingEnabled($id);
	}

	public static function _getK2Item($id) {

		static $sets;

		if ( !is_array( $sets) )
		{
			$sets= array( );
		}
		if ( !isset( $sets[$id])) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
			$item =  JTable::getInstance('K2Item', 'Table');
			$id = intval($id);
			$item->load($id);
			$sets[$id] = $item;
		}
		return $sets[$id];
	}


	public static function getK2Link($item_id) {
		static $sets;

		if ( !is_array( $sets) )
		{
			$sets= array( );
		}
		if ( !isset( $sets[$item_id])) {
			require_once(JPATH_SITE.'/components/com_k2/helpers/route.php');
			$db = JFactory::getDBO();
			$query = "SELECT i.id,i.alias,i.catid,c.alias AS categoryalias
			FROM #__k2_items as i
			LEFT JOIN #__k2_categories c ON c.id = i.catid
			WHERE i.published =1 AND i.id=".$db->Quote($item_id);
			$db->setQuery($query);
			$item = $db->loadObject();
			$sets[$item_id] = K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias));
		}
		return $sets[$item_id];
	}


	/**
	 * Given a multi-dimensional array,
	 * this will find all possible combinations of the array's elements
	 *
	 * Given:
	 *
	 * $traits = array
	 * (
	 *   array('Happy', 'Sad', 'Angry', 'Hopeful'),
	 *   array('Outgoing', 'Introverted'),
	 *   array('Tall', 'Short', 'Medium'),
	 *   array('Handsome', 'Plain', 'Ugly')
	 * );
	 *
	 * Returns:
	 *
	 * Array
	 * (
	 *      [0] => Happy,Outgoing,Tall,Handsome
	 *      [1] => Happy,Outgoing,Tall,Plain
	 *      [2] => Happy,Outgoing,Tall,Ugly
	 *      [3] => Happy,Outgoing,Short,Handsome
	 *      [4] => Happy,Outgoing,Short,Plain
	 *      [5] => Happy,Outgoing,Short,Ugly
	 *      etc
	 * )
	 *
	 * @param string $string   The result string
	 * @param array $traits    The multi-dimensional array of values
	 * @param int $i           The current level
	 * @param array $return    The final results stored here
	 * @return array           An Array of CSVs
	 */
	static function getCombinations1( $string, $traits, $i, &$return )
	{
		if ( $i >= count( $traits ) )
		{
			$return[] = str_replace( ' ', ',', trim( $string ) );
		}
		else
		{
			foreach ( $traits[$i] as $trait )
			{
				K2StoreItem::getCombinations( "$string $trait", $traits, $i + 1, $return );
			}
		}
	}
	public static function getCombinations($traits) {
		$max_attribute_combination = 1;
		foreach ( $traits as $trait ) {
			$max_attribute_combination = $max_attribute_combination * count ( $trait );
		}
		for($i = 0; $i < $max_attribute_combination; $i ++) {
			$output = "";
			$quotient = $i;

			foreach ( array_reverse ( $traits ) as $trait ) {
				$divisor = count ( $trait );
				$remainder = $quotient % $divisor;
				$quotient = $quotient / $divisor;
				$output = $trait [$remainder] . ',' . $output;
			}
			$result [] = trim ( $output, "," );
		}
		return $result;
	}

	/**
	 * Will return all the CSV combinations possible from a product's attribute options
	 *
	 * @param unknown_type $product_id
	 * @param
	 *        	$attributeOptionId
	 * @return unknown_type
	 */
	static function getProductAttributeCSVs($product_id, $attributeOptionId = '0') {
		$return = array ();
		$traits = array ();

		JModelLegacy::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_k2store/models' );
		// get all productattributes
		$model = JModelLegacy::getInstance ( 'ProductOptionValues', 'K2StoreModel' );
		$model->setState ( 'filter_product', $product_id );
		$model->setState ( 'filter_option_type', 'select or radio' );
		$model->setState ( 'filter_array', 1 );
		$model->setState( 'filter_option_stock', 1);
		if ($attributes = $model->getProductOptions ()) {
			foreach ( $attributes as $attribute ) {
				$paoModel = JModelLegacy::getInstance ( 'ProductOptionValues', 'K2StoreModel' );
				$paoModel->setState ( 'filter_productoption', $attribute->product_option_id );
				if ($paos = $paoModel->getProductOptionValues ()) {
					$options = array ();
					foreach ( $paos as $pao ) {
						// Genrate the arrray of single value with the id of newly created attribute option
						if ($attributeOptionId == $pao->product_optionvalue_id) {
							$newOption = array ();
							$newOption [] = ( string ) $attributeOptionId;
							$options = $newOption;
							break;
						}

						$options [] = $pao->product_optionvalue_id;
					}
					$traits [] = $options;
				}
			}
		}
		// run recursive function on the data
		// K2StoreItem::getCombinations( "", $traits, 0, $return );
		$return = K2StoreItem::getCombinations ( $traits );

		// before returning them, loop through each record and sort them
		$result = array ();
		foreach ( $return as $csv ) {
			$values = explode ( ',', $csv );
			sort ( $values );
			$result [] = implode ( ',', $values );
		}

		return $result;
	}

	 	/**
	 	* Given a product_id and vendor_id
	 	* will perform a full CSV reconciliation of the _productquantities table
	 	*
	 	* @param $product_id
	 	* @param $vendor_id
	 	* @param $attributeOptionId
	 	* @return unknown_type
	 	*/
	 static function doProductQuantitiesReconciliation( $product_id, $vendor_id = '0', $attributeOptionId = '0' )
	 {
	 if ( empty( $product_id ) )
	 {
	 return false;
	 }
	 if(K2STORE_PRO != 1) return false;

	 $params = JComponentHelper::getParams('com_k2store');
	 if(!$params->get('enable_inventory', 0)) return false;

		$csvs = K2StoreItem::getProductAttributeCSVs( $product_id, $attributeOptionId );
			JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_k2store/models' );
			$model = JModelLegacy::getInstance( 'ProductQuantities', 'K2StoreModel' );
			$model->setState( 'filter_productid', $product_id );
		$items = $model->getList( );

			$results = K2StoreItem::reconcileProductAttributeCSVs( $product_id, $vendor_id, $items, $csvs );
	 }

	/**
	 * Adds any necessary _productsquantities records
	 *
	 * @param unknown_type $product_id
	 *        	Product ID
	 * @param unknown_type $vendor_id
	 *        	Vendor ID
	 * @param array $items
	 *        	Array of productQuantities objects
	 * @param unknown_type $csvs
	 *        	CSV output from getProductAttributeCSVs
	 * @return array $items Array of objects
	 */
	static function reconcileProductAttributeCSVs($product_id, $vendor_id, $items, $csvs) {
		// remove extras
		$done = array ();
		foreach ( $items as $key => $item ) {
			if (! in_array ( $item->product_attributes, $csvs ) || in_array ( $item->product_attributes, $done )) {
				$row = JTable::getInstance ( 'ProductQuantities', 'Table' );
				if (! $row->delete ( $item->productquantity_id )) {
					JError::raiseNotice ( '1', $row->getError () );
				}
				unset ( $items [$key] );
			}
			$done [] = $item->product_attributes;
		}

		// add new ones
		$existingEntries = K2StoreItem::getColumn ( $items, 'product_attributes' );
		JTable::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_k2store/tables' );
		foreach ( $csvs as $csv ) {
			if (! in_array ( $csv, $existingEntries )) {
				$row = JTable::getInstance ( 'ProductQuantities', 'Table' );
				$row->product_id = $product_id;
				$row->product_attributes = $csv;
				if (! $row->save ()) {
					JError::raiseNotice ( '1', $row->getError () );
				}
				$items [] = $row;
			}
		}
		return $items;
	}

	/**
	 * Extracts a column from an array of arrays or objects
	 *
	 * @static
	 *
	 * @param array $array
	 *        	array
	 * @param string $index
	 *        	of the column or name of object property
	 * @return array of values from the source array
	 * @since 1.5
	 */
	public static function getColumn(&$array, $index) {
		$result = array ();

		if (is_array ( $array )) {
			foreach ( @$array as $item ) {
				if (is_array ( $item ) && isset ( $item [$index] )) {
					$result [] = $item [$index];
				} elseif (is_object ( $item ) && isset ( $item->$index )) {
					$result [] = $item->$index;
				}
			}
		}
		return $result;
	}


}