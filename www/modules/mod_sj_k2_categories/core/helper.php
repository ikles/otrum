<?php
/**
 * @version		$Id: helper.php 1751 2012-10-31 16:15:00Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');
require_once dirname(__FILE__).'/helper_base.php';

class SjK2CategoriesHelper extends K2CategoriesBaseHelper
{
	public static function getList(&$params)
	{
		$mainframe = JFactory::getApplication();
		if ($params->get('catfilter')){
			$cid = $params->get('category_id', NULL);
		} else{
			$itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
			$cid = $itemListModel->getCategoryTree($category=0);
		}
		$items = self::getItems($cid, $params);
		return $items;
	}
}
