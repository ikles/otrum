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

require_once(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'utilities.php');

include_once dirname(__FILE__) . '/helper_base.php';

abstract class K2ListingTabsHelper extends K2ListingTabsBaseHelper
{
	public static function getList(&$params,$cgid = -1,$count = 0,$orderSelect=false,$tab_all_display = null)
	{
		jimport('joomla.filesystem.file');
		$mainframe = JFactory::getApplication();
		if($count != 0){
			$limit = 99999;
		}else{
			$limit = $params->get('itemCount', 5);
		}		
		
		if(JRequest::getVar('field_responsive',null) == null){
		if($params->get('filter_type') != 'filter_orders'){
			if($tab_all_display != 1){
				$cid = $params->get('catid_preload');
				$ordering = $params->get('source_order', '');
				if($cgid > -1){
					$cid = $cgid;
				}
			}else{
				$cid = $params->get('catid_preload');
				$ordering = $params->get('source_order', '');
			}
		}else{
			$cid = 0;
			$ordering = $params->get('field_preload', '');
		}
		}else{
			if($params->get('filter_type') != 'filter_orders'){
				$ordering = $params->get('source_order', '');
				$cid = JRequest::getVar('field_responsive',null);
			}else{
				$cid = 0;
				$ordering = JRequest::getVar('field_responsive',null);
			}
		}
		if($orderSelect){
			$ordering = $orderSelect;
		}
		
		$componentParams = JComponentHelper::getParams('com_k2');
		if(JRequest::getInt('limitstart')){
			$limitstart = JRequest::getInt('limitstart');
		}else if(JRequest::getVar ( 'module_id', null ) == null){
			$limitstart = 0;
		}else{
			if(JRequest::getInt('type_js', 1) == 2){
				$page = 0;
			}else{
				$page = JRequest::getVar ( 'page', null ) - 1;
			}
			
			$limitstart = $limit * $page;
		}
		$user = JFactory::getUser();
		$aid = $user->get('aid');
		$db = JFactory::getDBO();

		$jnow = JFactory::getDate();
		$now = K2_JVERSION == '15' ? $jnow->toMySQL() : $jnow->toSql();
		$nullDate = $db->getNullDate();
			$query = "SELECT i.*, CASE WHEN i.modified = 0 THEN i.created ELSE i.modified END as lastChanged, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams";

			if ($ordering == 'best')
				$query .= ", (r.rating_sum/r.rating_count) AS rating";

			if ($ordering == 'comments')
				$query .= ", COUNT(comments.id) AS numOfComments";

			$query .= " FROM #__k2_items as i RIGHT JOIN #__k2_categories c ON c.id = i.catid";

			if ($ordering == 'best')
				$query .= " LEFT JOIN #__k2_rating r ON r.itemID = i.id";

			if ($ordering == 'comments')
				$query .= " LEFT JOIN #__k2_comments comments ON comments.itemID = i.id";

			if (K2_JVERSION != '15') {
				$query .= " WHERE i.published = 1 AND i.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") AND i.trash = 0 AND c.published = 1 AND c.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ")  AND c.trash = 0";
			} else {
				$query .= " WHERE i.published = 1 AND i.access <= {$aid} AND i.trash = 0 AND c.published = 1 AND c.access <= {$aid} AND c.trash = 0";
			}

			$query .= " AND ( i.publish_up = " . $db->Quote($nullDate) . " OR i.publish_up <= " . $db->Quote($now) . " )";
			$query .= " AND ( i.publish_down = " . $db->Quote($nullDate) . " OR i.publish_down >= " . $db->Quote($now) . " )";
			
			if (($params->get('catfilter') == 1 && $cid == 0) || ($params->get('catfilter') == 1 && $params->get('filter_type') == 'filter_orders')) {
				$cid = $params->get('category_id', NULL);
				if (!is_null($cid)) {
					if (is_array($cid)) {
						if ($params->get('getChildren') == 1) {
							$itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
							$categories = $itemListModel->getCategoryTree($cid);
							$sql = @implode(',', $categories);
							$query .= " AND i.catid IN ({$sql})";

						} else {
							JArrayHelper::toInteger($cid);
							$query .= " AND i.catid IN(" . implode(',', $cid) . ")";
						}

					} else {
						if ($params->get('getChildren') == 1) {
							$itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
							$categories = $itemListModel->getCategoryTree($cid);							
							$sql = @implode(',', $categories);
							$query .= " AND i.catid IN ({$sql})";
						} else {
							$query .= " AND i.catid=" . (int)$cid;
						}

					}
				}
			}else if($cid != 0){
				if ($params->get('getChildren') == 1) {
					$itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
					$categories = $itemListModel->getCategoryTree($cid);							
					$sql = @implode(',', $categories);
					$query .= " AND i.catid IN ({$sql})";
				} else {
					$query .= " AND i.catid=" . (int)$cid;
				}
			}
			

			if ($params->get('FeaturedItems') == '0')
				$query .= " AND i.featured != 1";

			if ($params->get('FeaturedItems') == '2')
				$query .= " AND i.featured = 1";

			if ($params->get('videosOnly'))
				$query .= " AND (i.video IS NOT NULL AND i.video!='')";

			if ($ordering == 'comments')
				$query .= " AND comments.published = 1";

			if (K2_JVERSION != '15') {
				if ($mainframe->getLanguageFilter()) {
					$languageTag = JFactory::getLanguage()->getTag();
					$query .= " AND c.language IN (" . $db->Quote($languageTag) . ", " . $db->Quote('*') . ") AND i.language IN (" . $db->Quote($languageTag) . ", " . $db->Quote('*') . ")";
				}
			}

			switch ($ordering) {

				case 'date' :
					$orderby = 'i.created ASC';
					break;

				case 'rdate' :
					$orderby = 'i.created DESC';
					break;

				case 'alpha' :
					$orderby = 'i.title';
					break;

				case 'ralpha' :
					$orderby = 'i.title DESC';
					break;

				case 'order' :
					if ($params->get('FeaturedItems') == '2')
						$orderby = 'i.featured_ordering';
					else
						$orderby = 'i.ordering';
					break;

				case 'rorder' :
					if ($params->get('FeaturedItems') == '2')
						$orderby = 'i.featured_ordering DESC';
					else
						$orderby = 'i.ordering DESC';
					break;

				case 'hits' :
					if ($params->get('popularityRange')) {
						$datenow = JFactory::getDate();
						$date = K2_JVERSION == '15' ? $datenow->toMySQL() : $datenow->toSql();
						$query .= " AND i.created > DATE_SUB('{$date}',INTERVAL " . $params->get('popularityRange') . " DAY) ";
					}
					$orderby = 'i.hits DESC';
					break;

				case 'rand' :
					$orderby = 'RAND()';
					break;

				case 'best' :
					$orderby = 'rating DESC';
					break;

				case 'comments' :
					if ($params->get('popularityRange')) {
						$datenow = JFactory::getDate();
						$date = K2_JVERSION == '15' ? $datenow->toMySQL() : $datenow->toSql();
						$query .= " AND i.created > DATE_SUB('{$date}',INTERVAL " . $params->get('popularityRange') . " DAY) ";
					}
					$query .= " GROUP BY i.id ";
					$orderby = 'numOfComments DESC';
					break;

				case 'modified' :
					$orderby = 'lastChanged DESC';
					break;

				case 'publishUp' :
					$orderby = 'i.publish_up DESC';
					break;

				default :
					$orderby = 'i.id DESC';
					break;
			}

			$query .= " ORDER BY " . $orderby;
			// $sql = str_replace('#__','jos_',$query);
			// echo $sql;die;
			$db->setQuery($query, $limitstart, $limit);
			$items = $db->loadObjectList();
		

		$model = K2Model::getInstance('Item', 'K2Model');
		$show_introtext = $params->get('item_desc_display', 0);
		$introtext_limit = $params->get('item_desc_max_characs', 100);
		if (count($items)) {

			foreach ($items as $item) {

				//Clean title
				$item->title = JFilterOutput::ampReplace($item->title);

				//Read more link
				$item->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id . ':' . urlencode($item->alias), $item->catid . ':' . urlencode($item->categoryalias))));

				//Tags
				$item->tags = '';
				if ($params->get('item_tags_display')) {
					$tags = $model->getItemTags($item->id);
					for ($i = 0; $i < sizeof($tags); $i++) {
						$tags[$i]->link = JRoute::_(K2HelperRoute::getTagRoute($tags[$i]->name));
					}
					$item->tags = $tags;
				} else {
					$item->tags = '';
				}

				// Restore the intotext variable after plugins execution
				self::getK2Images($item, $params);
				//Clean the plugin tags
				$item->introtext = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $item->introtext);
				if ($item->fulltext != '') {
					$item->fulltext = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $item->fulltext);
					$item->introtext = self::_cleanText($item->introtext . $item->fulltext);
				} else {
					$item->introtext = self::_cleanText($item->introtext);
				}
				$item->displayIntrotext = $show_introtext ? self::truncate($item->introtext, $introtext_limit) : '';
				
				$item->numOfvotes = $model->getVotesNum($item->id);
				$item->votingPercentage = $model->getVotesPercentage($item->id);
				
				$rows[] = $item;
			}		
			if($count != 0){
				return count($rows);
			}else{
				return $rows;
			}
			
		}

	}
	
	public static function getCategoriesInfor($params){
		$cid = $params->get('category_id', NULL);
		$db = JFactory::getDBO();
		$send = array();
		$fields = array("date" => "Oldest First","rdate" => "Date Created","rand" => "Random","publishUp" => "Date Published","alpha" => "Title","ralpha" => "Title Reverse","order" => "Ordering","rorder" => "Ordering Reverse","hits" => "Most Popular","best" => "Highest Rated","comments" => "Most Commented","modified" => "Latest Modified");
		if($params->get('filter_type') != 'filter_orders'){
			
			if($cid == NULL){
				if($params->get('showParent') == 1){
					$query = "SELECT * FROM #__k2_categories WHERE parent =0";
				}else{
					$query = "SELECT * FROM #__k2_categories";
				}
			}else{
				$cid = join(',',$cid);
				$query = "SELECT * FROM #__k2_categories WHERE id IN ($cid)";
			}
			if($params->get('cat_order_by') == 'title'){
				$query = $query . ' ORDER BY name';
			}
			if($params->get('cat_order_by') == 'id'){
				$query = $query . ' ORDER BY id';
			}
			if($params->get('cat_order_by') == 'random'){
				$query = $query . ' ORDER BY RAND()';
			}
			$query = $query . ' ' .$params->get("cat_ordering_direction");
			$db->setQuery($query);
			$items = $db->loadObjectList();
			$send = array();
			if($params->get('tab_all_display') == 1){
				$item = new stdClass();
				$item->id = '*';
				$item->name = 'All';
				$item->countI = self::getList($params,$cgid = -1,$count = 1,$orderSelect=false,$tab_all_display = 1);
				if($params->get('catid_preload') == 0){
					$item->sel = "sel";
				}
				$item->field_order = $params->get('source_order');
				$send[] = $item;
			}
			foreach($items as $i){
				$i->countI = self::getList($params,$i->id,$count = 1,$orderSelect=false);
				$i->k2_image = $i->image;
				if($i->id == $params->get('catid_preload')){
					$i->sel = "sel";
				}
				$i->field_order = $params->get('source_order');
				$send[] = $i;
			}
			
		}else{
			$countItems = self::getList($params,$cgid = -1,$count = 1,$orderSelect=false);
			$send = array();
			foreach($params->get('filter_order_by') as $i){
				$item = new stdClass();
				$item->id = $i;
				$item->name = $fields[$i];
				$item->countI = $countItems;
				if($i == $params->get('field_preload')){
					$item->sel = 'sel';
				}
				$send[] = $item;
			}			
		}
		
		return $send;
	}
	
	public static function getRandom($send){
		
	}
}
