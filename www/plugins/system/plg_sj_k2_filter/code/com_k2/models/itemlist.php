<?php
/**
 * @version    2.7.x
 * @package    K2
 * @author     JoomlaWorks http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;
jimport('joomla.application.component.model');
jimport('joomla.application.module.helper' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'tables');
class K2ModelItemlist extends K2ModelItemlistDefault
{

	function getData($ordering = NULL)
	{

		$user = JFactory::getUser();
		$aid = $user->get('aid');
		$db = JFactory::getDBO();
		$params = K2HelperUtilities::getParams('com_k2');
		$limitstart = JRequest::getInt('limitstart');
		$limit = JRequest::getInt('limit');
		$task = JRequest::getCmd('task');
		if ($task == 'search' && $params->get('googleSearch'))
			return array();

		$jnow = JFactory::getDate();
		$now = K2_JVERSION == '15' ? $jnow->toMySQL() : $jnow->toSql();
		$nullDate = $db->getNullDate();

		if (JRequest::getWord('format') == 'feed')
			$limit = $params->get('feedLimit');

		$query = "SELECT i.*,";

		if($ordering == 'modified')
		{
			$query .= " CASE WHEN i.modified = 0 THEN i.created ELSE i.modified END as lastChanged, ";
		}

		$query .= " c.name as categoryname,c.id as categoryid, c.alias as categoryalias, c.params as categoryparams";



		if ($ordering == 'best')
			$query .= ", (r.rating_sum/r.rating_count) AS rating";

		$query .= " FROM #__k2_items as i RIGHT JOIN #__k2_categories AS c ON c.id = i.catid";

		if ($ordering == 'best')
			$query .= " LEFT JOIN #__k2_rating r ON r.itemID = i.id";

		//Changed the query for the tag case for better performance
		//if ($task == 'tag')
		//		$query .= " LEFT JOIN #__k2_tags_xref AS tags_xref ON tags_xref.itemID = i.id LEFT JOIN #__k2_tags AS tags ON tags.id = tags_xref.tagID";

		if ($task == 'user' && !$user->guest && $user->id == JRequest::getInt('id'))
		{
			$query .= " WHERE ";
		}
		else
		{
			$query .= " WHERE i.published = 1 AND ";
		}

		if (K2_JVERSION != '15')
		{

			$query .= "i.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"." AND i.trash = 0"." AND c.published = 1"." AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"." AND c.trash = 0";

			$mainframe = JFactory::getApplication();
			$languageFilter = $mainframe->getLanguageFilter();
			if ($languageFilter)
			{
				$languageTag = JFactory::getLanguage()->getTag();
				$query .= " AND c.language IN (".$db->quote($languageTag).",".$db->quote('*').")
						AND i.language IN (".$db->quote($languageTag).",".$db->quote('*').")";
			}
		}
		else
		{
			$query .= "i.access <= {$aid}"." AND i.trash = 0"." AND c.published = 1"." AND c.access <= {$aid}"." AND c.trash = 0";
		}

		if (!($task == 'user' && !$user->guest && $user->id == JRequest::getInt('id')))
		{
			$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
			$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";
		}

		//Build query depending on task
		switch ($task)
		{

			case 'category' :
				$id = JRequest::getInt('id');

				$category = JTable::getInstance('K2Category', 'Table');
				$category->load($id);
				$cparams = class_exists('JParameter') ? new JParameter($category->params) : new JRegistry($category->params);

				if ($cparams->get('inheritFrom'))
				{

					$parent = JTable::getInstance('K2Category', 'Table');
					$parent->load($cparams->get('inheritFrom'));
					$cparams = class_exists('JParameter') ? new JParameter($parent->params) : new JRegistry($parent->params);
				}

				if ($cparams->get('catCatalogMode'))
				{
					$query .= " AND c.id={$id} ";
				}
				else
				{
					$categories = $this->getCategoryTree($id);
					$sql = @implode(',', $categories);
					$query .= " AND c.id IN ({$sql})";
				}

				break;

			case 'user' :
				$id = JRequest::getInt('id');
				$query .= " AND i.created_by={$id} AND i.created_by_alias=''";
				$categories = $params->get('userCategoriesFilter', NULL);
				if (is_array($categories))
				{
					$categories = array_filter($categories);
					JArrayHelper::toInteger($categories);
					if(count($categories))
					{
						$query .= " AND c.id IN(".implode(',', $categories).")";
					}
				}
				if (is_string($categories) && $categories > 0)
				{
					$query .= " AND c.id = {$categories}";
				}
				break;

			case 'search' :
				$badchars = array(
					'#',
					'>',
					'<',
					'\\'
				);
				$search = JString::trim(JString::str_ireplace($badchars, '', JRequest::getString('searchword', null)));
				$sql = $this->prepareSearch($search);
				if (!empty($sql))
				{
					$query .= $sql;
				}
				else
				{
					$rows = array();
					return $rows;
				}
				break;

			case 'filter' :
				$badchars = array(
					'#',
					'>',
					'<',
					'\\'
				);
				$search = JString::trim(JString::str_ireplace($badchars, '', JRequest::getString('searchword', null)));
				$sql = $this->prepareSearch($search);
				if (!empty($sql))
				{
					$query .= $sql;
				}
				else
				{
					$rows = array();
					return $rows;
				}
				break;

			case 'date' :
				if ((JRequest::getInt('month')) && (JRequest::getInt('year')))
				{
					$month = JRequest::getInt('month');
					$year = JRequest::getInt('year');
					$query .= " AND MONTH(i.created) = {$month} AND YEAR(i.created)={$year} ";
					if (JRequest::getInt('day'))
					{
						$day = JRequest::getInt('day');
						$query .= " AND DAY(i.created) = {$day}";
					}

					if (JRequest::getInt('catid'))
					{
						$catid = JRequest::getInt('catid');
						$query .= " AND c.id={$catid}";
					}

				}
				break;

			case 'tag' :
				$tag = JRequest::getString('tag');
				jimport('joomla.filesystem.file');
				if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php') && $task == 'tag')
				{

					$registry = JFactory::getConfig();
					$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

					$sql = " SELECT reference_id FROM #__jf_content as jfc LEFT JOIN #__languages as jfl ON jfc.language_id = jfl.".K2_JF_ID;
					$sql .= " WHERE jfc.value = ".$db->Quote($tag);
					$sql .= " AND jfc.reference_table = 'k2_tags'";
					$sql .= " AND jfc.reference_field = 'name' AND jfc.published=1";

					$db->setQuery($sql, 0, 1);
					$result = $db->loadResult();

				}

				if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_falang'.DS.'falang.php') && $task == 'tag')
				{

					$registry = JFactory::getConfig();
					$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

					$sql = " SELECT reference_id FROM #__falang_content as fc LEFT JOIN #__languages as fl ON fc.language_id = fl.lang_id";
					$sql .= " WHERE fc.value = ".$db->Quote($tag);
					$sql .= " AND fc.reference_table = 'k2_tags'";
					$sql .= " AND fc.reference_field = 'name' AND fc.published=1";

					$db->setQuery($sql, 0, 1);
					$result = $db->loadResult();

				}

				if (!isset($result) || $result < 1)
				{
					$sql = "SELECT id FROM #__k2_tags WHERE name=".$db->Quote($tag);
					$db->setQuery($sql, 0, 1);
					$result = $db->loadResult();
				}

				$query .= " AND i.id IN (SELECT itemID FROM #__k2_tags_xref WHERE tagID=".(int)$result.")";

				/*if (isset($result) && $result > 0) {
				 $query .= " AND (tags.id) = {$result}";
				 } else {
				 $query .= " AND (tags.name) = ".$db->Quote($tag);
				 }*/

				$categories = $params->get('categoriesFilter', NULL);
				if (is_array($categories))
				{
					JArrayHelper::toInteger($categories);
					$query .= " AND c.id IN(".implode(',', $categories).")";
				}
				if (is_string($categories))
					$query .= " AND c.id = {$categories}";
				break;

			default :
				$searchIDs = $params->get('categories');

				if (is_array($searchIDs) && count($searchIDs))
				{

					if ($params->get('catCatalogMode'))
					{
						$sql = @implode(',', $searchIDs);
						$query .= " AND c.id IN ({$sql})";
					}
					else
					{

						$result = $this->getCategoryTree($searchIDs);
						if (count($result))
						{
							$sql = @implode(',', $result);
							$query .= " AND c.id IN ({$sql})";
						}
					}
				}

				break;
		}

		//Set featured flag
		if ($task == 'category' || empty($task))
		{
			if (JRequest::getInt('featured') == '0')
			{
				$query .= " AND i.featured != 1";
			}
			else if (JRequest::getInt('featured') == '2')
			{
				$query .= " AND i.featured = 1";
			}
		}

		//Remove duplicates
		//$query .= " GROUP BY i.id";

		//Set ordering
		switch ($ordering)
		{

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
				if (JRequest::getInt('featured') == '2')
					$orderby = 'i.featured_ordering';
				else
					$orderby = 'c.ordering, i.ordering';
				break;

			case 'rorder' :
				if (JRequest::getInt('featured') == '2')
					$orderby = 'i.featured_ordering DESC';
				else
					$orderby = 'c.ordering DESC, i.ordering DESC';
				break;

			case 'featured' :
				$orderby = 'i.featured DESC, i.created DESC';
				break;

			case 'hits' :
				$orderby = 'i.hits DESC';
				break;

			case 'rand' :
				$orderby = 'RAND()';
				break;

			case 'best' :
				$orderby = 'rating DESC';
				break;

			case 'modified' :
				$orderby = 'lastChanged DESC';
				break;

			case 'publishUp' :
				$orderby = 'i.publish_up DESC';
				break;

			case 'id' :
			default :
				$orderby = 'i.id DESC';
				break;
		}

		$query .= " ORDER BY ".$orderby;
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('k2');
		$dispatcher->trigger('onK2BeforeSetQuery', array(&$query));
		$db->setQuery($query, $limitstart, $limit);
		$rows = $db->loadObjectList();
		$rows_ = array();
		/*Get Param module Sj K2 Filter*/
		$module = &JModuleHelper::getModule('mod_sj_k2_filter');
		$moduleParams = json_decode($module->params); 
		$extra_fields_value = array();
		if(isset($moduleParams->extrafield_id))
		{
			$extrafield_ids = $moduleParams->extrafield_id;
			foreach($extrafield_ids as $extrafield_id)
			{
				if(JRequest::getVar('extra_'.$extrafield_id.'') != '')
				{
					$extra_fields_value[] = $extrafield_id.'_'.JRequest::getVar('extra_'.$extrafield_id.'');
				}
			}
		}
		
		if(count($extra_fields_value))
		{
			foreach($rows as $item)
			{
				$field_id = array();
				$field_item = array();
				$extra_fields_new = array();
				if($item->extra_fields != '')
				{
					
					$extra_fields = json_decode($item->extra_fields);
					foreach($extra_fields as $field)
					{
						$field_info = $this->getExtraField_info($field->id);
						$field_info_value = json_decode($field_info->value);
						
						foreach($field_info_value as $value)
						{
							if($value->value == $field->value)	$value_name = $value->name;
						}
						$extra_fields_new[]= array(
						'id'	=> $field_info->id,
						'name' 	=> $field_info->name,
						'value'	=> $value_name,
						'type'	=> $field_info->type,
						'group' => $field_info->group,
						'published'	=> $field_info->published,
						'ordering'	=> $field_info->ordering
						);
						$field_item[] = $field->id .'_'.$field->value; 							
					}
					$item->extra_fields =$extra_fields_new;
					if(count(array_intersect($field_item,$extra_fields_value)) == count($extra_fields_value))
					{
						$rows_[] = $item;
					}
				}
			}
			return $rows_;
		}		
		return $rows;
	}
	public static function getExtraField_info($extrafield_id)
    {
		$published = array(1);
        $mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $aid = (int)$user->get('aid');
        $db = JFactory::getDbo();
		$query = $db->getQuery(true)
				->select('a.*')
				->from('#__k2_extra_fields AS a');
		$query->where('a.id='.$extrafield_id.' AND a.published IN (' . implode(',', $published) . ')');
		$query->order('a.name ASC');
		$db->setQuery($query);
		$row = $db->loadObject();
		try {
			$extrafield = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage);
		}
        return $row;
    }

	function getTotal()
	{

		$user = JFactory::getUser();
		$aid = $user->get('aid');
		$db = JFactory::getDBO();
		$params = K2HelperUtilities::getParams('com_k2');
		$task = JRequest::getCmd('task');

		if ($task == 'filter' && $params->get('googleSearch'))
			return 0;

		$jnow = JFactory::getDate();
		$now = K2_JVERSION == '15' ? $jnow->toMySQL() : $jnow->toSql();
		$nullDate = $db->getNullDate();

		$query = "SELECT COUNT(*) FROM #__k2_items as i RIGHT JOIN #__k2_categories c ON c.id = i.catid";

		if ($task == 'tag')
			$query .= " LEFT JOIN #__k2_tags_xref tags_xref ON tags_xref.itemID = i.id LEFT JOIN #__k2_tags tags ON tags.id = tags_xref.tagID";

		if ($task == 'user' && !$user->guest && $user->id == JRequest::getInt('id'))
		{
			$query .= " WHERE ";
		}
		else
		{
			$query .= " WHERE i.published = 1 AND ";
		}

		if (K2_JVERSION != '15')
		{
			$query .= "i.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"." AND i.trash = 0"." AND c.published = 1"." AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"." AND c.trash = 0";

			$mainframe = JFactory::getApplication();
			$languageFilter = $mainframe->getLanguageFilter();
			if ($languageFilter)
			{
				$languageTag = JFactory::getLanguage()->getTag();
				$query .= " AND c.language IN (".$db->quote($languageTag).",".$db->quote('*').")
						AND i.language IN (".$db->quote($languageTag).",".$db->quote('*').")";
			}
		}
		else
		{
			$query .= "i.access <= {$aid}"." AND i.trash = 0"." AND c.published = 1"." AND c.access <= {$aid}"." AND c.trash = 0";
		}

		$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
		$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";

		//Build query depending on task
		switch ($task)
		{

			case 'category' :
				$id = JRequest::getInt('id');

				$category = JTable::getInstance('K2Category', 'Table');
				$category->load($id);
				$cparams = class_exists('JParameter') ? new JParameter($category->params) : new JRegistry($category->params);

				if ($cparams->get('inheritFrom'))
				{

					$parent = JTable::getInstance('K2Category', 'Table');
					$parent->load($cparams->get('inheritFrom'));
					$cparams = class_exists('JParameter') ? new JParameter($parent->params) : new JRegistry($parent->params);
				}

				if ($cparams->get('catCatalogMode'))
				{
					$query .= " AND c.id={$id} ";
				}
				else
				{
					$categories = $this->getCategoryTree($id);
					$sql = @implode(',', $categories);
					$query .= " AND c.id IN ({$sql})";
				}

				break;

			case 'user' :
				$id = JRequest::getInt('id');
				$query .= " AND i.created_by={$id} AND i.created_by_alias=''";
				$categories = $params->get('userCategoriesFilter', NULL);
				if (is_array($categories))
				{
					$categories = array_filter($categories);
					JArrayHelper::toInteger($categories);
					if(count($categories))
					{
						$query .= " AND c.id IN(".implode(',', $categories).")";
					}
				}
				if (is_string($categories) && $categories > 0)
				{
					$query .= " AND c.id = {$categories}";
				}
				break;

			case 'search' :
				$badchars = array(
					'#',
					'>',
					'<',
					'\\'
				);
				$search = trim(str_replace($badchars, '', JRequest::getString('searchword', null)));
				$sql = $this->prepareSearch($search);
				if (!empty($sql))
				{
					$query .= $sql;
				}
				else
				{
					$result = 0;
					return $result;
				}
				break;

			case 'filter' :
				$badchars = array(
					'#',
					'>',
					'<',
					'\\'
				);
				$search = trim(str_replace($badchars, '', JRequest::getString('searchword', null)));
				$sql = $this->prepareSearch($search);
				if (!empty($sql))
				{
					$query .= $sql;
				}
				
				else
				 {
					$result = 0;
					return $result;
				}
				break;

			case 'date' :
				if ((JRequest::getInt('month')) && (JRequest::getInt('year')))
				{
					$month = JRequest::getInt('month');
					$year = JRequest::getInt('year');
					$query .= " AND MONTH(i.created) = {$month} AND YEAR(i.created)={$year} ";
					if (JRequest::getInt('day'))
					{
						$day = JRequest::getInt('day');
						$query .= " AND DAY(i.created) = {$day}";
					}

					if (JRequest::getInt('catid'))
					{
						$catid = JRequest::getInt('catid');
						$query .= " AND c.id={$catid}";
					}

				}
				break;

			case 'tag' :
				$tag = JRequest::getString('tag');
				jimport('joomla.filesystem.file');
				if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php') && $task == 'tag')
				{

					$registry = JFactory::getConfig();
					$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

					$sql = " SELECT reference_id FROM #__jf_content as jfc LEFT JOIN #__languages as jfl ON jfc.language_id = jfl.".K2_JF_ID;
					$sql .= " WHERE jfc.value = ".$db->Quote($tag);
					$sql .= " AND jfc.reference_table = 'k2_tags'";
					$sql .= " AND jfc.reference_field = 'name' AND jfc.published=1";

					$db->setQuery($sql, 0, 1);
					$result = $db->loadResult();

				}

				if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_falang'.DS.'falang.php') && $task == 'tag')
				{

					$registry = JFactory::getConfig();
					$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

					$sql = " SELECT reference_id FROM #__falang_content as fc LEFT JOIN #__languages as fl ON fc.language_id = fl.lang_id";
					$sql .= " WHERE fc.value = ".$db->Quote($tag);
					$sql .= " AND fc.reference_table = 'k2_tags'";
					$sql .= " AND fc.reference_field = 'name' AND fc.published=1";

					$db->setQuery($sql, 0, 1);
					$result = $db->loadResult();

				}

				if (isset($result) && $result > 0)
				{
					$query .= " AND (tags.id) = {$result}";
				}
				else
				{
					$query .= " AND (tags.name) = ".$db->Quote($tag);
				}
				$categories = $params->get('categoriesFilter', NULL);
				if (is_array($categories))
					$query .= " AND c.id IN(".implode(',', $categories).")";
				if (is_string($categories))
					$query .= " AND c.id = {$categories}";
				break;

			default :
				$searchIDs = $params->get('categories');

				if (is_array($searchIDs) && count($searchIDs))
				{

					if ($params->get('catCatalogMode'))
					{
						$sql = @implode(',', $searchIDs);
						$query .= " AND c.id IN ({$sql})";
					}
					else
					{
						$result = $this->getCategoryTree($searchIDs);
						if (count($result))
						{
							$sql = @implode(',', $result);
							$query .= " AND c.id IN ({$sql})";
						}
					}
				}

				break;
		}

		//Set featured flag
		if ($task == 'category' || empty($task))
		{
			if (JRequest::getVar('featured') == '0')
			{
				$query .= " AND i.featured != 1";
			}
			else if (JRequest::getVar('featured') == '2')
			{
				$query .= " AND i.featured = 1";
			}
		}
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('k2');
		$dispatcher->trigger('onK2BeforeSetQuery', array(&$query));
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}


	function prepareSearch($search)
	{

		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();
		$language = JFactory::getLanguage();
		$defaultLang = $language->getDefault();
		$currentLang = $language->getTag();
		$length = JString::strlen($search);
		$from_date = JRequest::getVar('created-from');
		$from_to = JRequest::getVar('created-to');
		$sql = '';
		if($from_date != '' && $from_to !=''){
			$sql .= " AND i.created BETWEEN '$from_date' AND '$from_to 23:59:59.999'";
		}elseif($from_date !=''){
			$sql .= " AND i.created  >= '$from_date'";
		}elseif($from_to !=''){
			$sql .= " AND i.created  <= '$from_to 23:59:59.999'";
		}
		if (JRequest::getVar('categories'))
		{
			$categories = @explode(',', JRequest::getVar('categories'));
			JArrayHelper::toInteger($categories);
			if(JRequest::getVar('categories') == '*'){
				$sql .= " AND c.published = 1 ";
			}else{
				$sql .= " AND c.id IN (".@implode(',', $categories).") ";
			}

		}

		if (empty($search))
		{
			return $sql;
		}

		if (JString::substr($search, 0, 1) == '"' && JString::substr($search, $length - 1, 1) == '"')
		{
			$type = 'exact';
		}
		else
		{
			$type = 'any';
		}

		if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php') && $currentLang != $defaultLang)
		{

			$conditions = array();
			$search_ignore = array();

			$ignoreFile = $language->getLanguagePath().DS.$currentLang.DS.$currentLang.'.ignore.php';

			if (JFile::exists($ignoreFile))
			{
				include $ignoreFile;
			}

			if ($type == 'exact')
			{

				$word = JString::substr($search, 1, $length - 2);

				if (JString::strlen($word) > 3 && !in_array($word, $search_ignore))
				{
					$escaped = K2_JVERSION == '15' ? $db->getEscaped($word, true) : $db->escape($word, true);
					$langField = K2_JVERSION == '15' ? 'code' : 'lang_code';
					$word = $db->Quote('%'.$escaped.'%', false);

					$jfQuery = " SELECT reference_id FROM #__jf_content as jfc LEFT JOIN #__languages as jfl ON jfc.language_id = jfl.".K2_JF_ID;
					$jfQuery .= " WHERE jfc.reference_table = 'k2_items'";
					$jfQuery .= " AND jfl.".$langField."=".$db->Quote($currentLang);
					$jfQuery .= " AND jfc.published=1";
					$jfQuery .= " AND jfc.value LIKE ".$word;
					$jfQuery .= " AND (jfc.reference_field = 'title'
								OR jfc.reference_field = 'introtext'
								OR jfc.reference_field = 'fulltext'
								OR jfc.reference_field = 'image_caption'
								OR jfc.reference_field = 'image_credits'
								OR jfc.reference_field = 'video_caption'
								OR jfc.reference_field = 'video_credits'
								OR jfc.reference_field = 'extra_fields_search'
								OR jfc.reference_field = 'metadesc'
								OR jfc.reference_field = 'metakey'
					)";
					$db->setQuery($jfQuery);
					$result = K2_JVERSION == '30' ? $db->loadColumn() : $db->loadResultArray();
					$result = @array_unique($result);
					JArrayHelper::toInteger($result);
					if (count($result))
					{
						$conditions[] = "i.id IN(".implode(',', $result).")";
					}

				}

			}
			else
			{
				$search = explode(' ', JString::strtolower($search));
				foreach ($search as $searchword)
				{

					if (JString::strlen($searchword) > 3 && !in_array($searchword, $search_ignore))
					{

						$escaped = K2_JVERSION == '15' ? $db->getEscaped($searchword, true) : $db->escape($searchword, true);
						$word = $db->Quote('%'.$escaped.'%', false);
						$langField = K2_JVERSION == '15' ? 'code' : 'lang_code';

						$jfQuery = " SELECT reference_id FROM #__jf_content as jfc LEFT JOIN #__languages as jfl ON jfc.language_id = jfl.".K2_JF_ID;
						$jfQuery .= " WHERE jfc.reference_table = 'k2_items'";
						$jfQuery .= " AND jfl.".$langField."=".$db->Quote($currentLang);
						$jfQuery .= " AND jfc.published=1";
						$jfQuery .= " AND jfc.value LIKE ".$word;
						$jfQuery .= " AND (jfc.reference_field = 'title'
									OR jfc.reference_field = 'introtext'
									OR jfc.reference_field = 'fulltext'
									OR jfc.reference_field = 'image_caption'
									OR jfc.reference_field = 'image_credits'
									OR jfc.reference_field = 'video_caption'
									OR jfc.reference_field = 'video_credits'
									OR jfc.reference_field = 'extra_fields_search'
									OR jfc.reference_field = 'metadesc'
									OR jfc.reference_field = 'metakey'
						)";
						$db->setQuery($jfQuery);
						$result = K2_JVERSION == '30' ? $db->loadColumn() : $db->loadResultArray();
						$result = @array_unique($result);
						foreach ($result as $id)
						{
							$allIDs[] = $id;
						}

						if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php') && $currentLang != $defaultLang)
						{

							if (isset($allIDs) && count($allIDs))
							{
								JArrayHelper::toInteger($allIDs);
								$conditions[] = "i.id IN(".implode(',', $allIDs).")";
							}

						}

					}

				}

			}

			if (count($conditions))
			{
				$sql .= " AND (".implode(" OR ", $conditions).")";
			}

		}
		else
		{

			$escaped = K2_JVERSION == '15' ? $db->getEscaped($search, true) : $db->escape($search, true);
			$quoted = $db->Quote('%'.$escaped.'%', false);

			if ($type == 'exact')
			{
				$text = JString::trim($search, '"');
				$escaped = K2_JVERSION == '15' ? $db->getEscaped($text, true) : $db->escape($text, true);
				$quoted = $db->Quote('%'.$escaped.'%', false);
				$sql .= " AND ( LOWER(i.title) = ".$quoted." OR LOWER(i.introtext) = ".$quoted." OR LOWER(i.`fulltext`) = ".$quoted." OR LOWER(i.extra_fields_search) = ".$quoted." OR LOWER(i.image_caption) = ".$quoted." OR LOWER(i.image_credits) = ".$quoted." OR LOWER(i.video_caption) = ".$quoted." OR LOWER(i.video_credits) = ".$quoted." OR LOWER(i.metadesc) = ".$quoted." OR LOWER(i.metakey) = ".$quoted.") ";
			}
			else
			{
				$escaped = K2_JVERSION == '15' ? $db->getEscaped($search, true) : $db->escape($search, true);
				$text = $db->Quote($escaped);
				$sql .= " AND ( LOWER(i.title) LIKE ".$quoted." OR LOWER(i.introtext) LIKE ".$quoted." OR LOWER(i.`fulltext`) LIKE ".$quoted." OR LOWER(i.extra_fields_search) LIKE ".$quoted." OR LOWER(i.image_caption) LIKE ".$quoted." OR LOWER(i.image_credits) LIKE ".$quoted." OR LOWER(i.video_caption) LIKE ".$quoted." OR LOWER(i.video_credits) LIKE ".$quoted." OR LOWER(i.metadesc) LIKE ".$quoted." OR LOWER(i.metakey) LIKE ".$quoted.") ";
			}

		}

		return $sql;
	}

}
