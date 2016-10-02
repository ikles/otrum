<?php
/*
 * @package Sj K2 Ajax Tabs
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

require_once(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'utilities.php');


abstract class SjK2FilterHelper
{

    public static function getSearchRoute()
    {
		
        $needles = array('filter' => 'filter');
        $link = 'index.php?option=com_k2&view=itemlist&task=filter';
        if ($item = K2HelperRoute::_findItem($needles)) {
            $link .= '&Itemid=' . $item->id;
        }
		
        return $link;
    }

    public static function getCategories($params,$cat_id)
    {
        $mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $aid = (int)$user->get('aid');
        $db = JFactory::getDBO();
        if($params->get('catfilter')){
            if($params->get ('getChildren')){
                $catids = self::getCategoryChildren($cat_id);
                $_id = array_merge($cat_id, $catids);
                $id = '('.implode(',',$_id).')';
                $query = "SELECT id,name,parent FROM #__k2_categories WHERE id IN {$id} AND published=1 AND trash=0 ";
            }else{
                $id = '('.implode(',',$cat_id).')';
                $query = "SELECT id,name,parent FROM #__k2_categories WHERE id IN {$id} AND published=1 AND trash=0 ";
            }
        }else{
            $query = "SELECT id,name,parent FROM #__k2_categories WHERE  published=1 AND trash=0 ";
        }

        if (K2_JVERSION != '15') {
            $query .= " AND access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") ";
            if ($mainframe->getLanguageFilter()) {
                $languageTag = JFactory::getLanguage()->getTag();
                $query .= " AND language IN (" . $db->Quote($languageTag) . ", " . $db->Quote('*') . ") ";
            }
        } else {
            $query .= " AND access <= {$aid}";
        }
        $query .= " ORDER BY ordering";

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        $categories = array();
        if(count($rows)) {
            foreach ($rows as $key => $value) {
                self::recurseTree($value, 0, $rows, $categories);
            }
        }
        return $rows;
    }
    public static function getCategoryChildren($catid)
    {
        static $array = array();
        $mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $aid = (int)$user->get('aid');
		if(count($catid)>1)
            $_catid = implode(',', $catid);
        else
            $_catid = implode($catid);
        $db = JFactory::getDBO();
        $query = "SELECT id,name,parent FROM #__k2_categories WHERE parent IN ($_catid) AND published=1 AND trash=0 ";
        if (K2_JVERSION != '15')
        {
            $query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
            if ($mainframe->getLanguageFilter())
            {
                $languageTag = JFactory::getLanguage()->getTag();
                $query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
            }
        }
        else
        {
            $query .= " AND access <= {$aid}";
        }
        $query .= " ORDER BY ordering ";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum())
        {
            echo $db->stderr();
            return false;
        }
        foreach ($rows as $row)
        {
            array_push($array, $row->id);
            if (SjK2FilterHelper::hasChildren($row->id))
            {
                SjK2FilterHelper::getCategoryChildren($row->id);
            }
        }
        return $array;
    }
    public static function hasChildren($id)
    {

        $mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $aid = (int)$user->get('aid');
        $id = (int)$id;
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__k2_categories  WHERE parent={$id} AND published=1 AND trash=0 ";
        if (K2_JVERSION != '15')
        {
            $query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
            if ($mainframe->getLanguageFilter())
            {
                $languageTag = JFactory::getLanguage()->getTag();
                $query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
            }

        }
        else
        {
            $query .= " AND access <= {$aid}";
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum())
        {
            echo $db->stderr();
            return false;
        }

        if (count($rows))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function recurseTree($cat, $level, $all_cats, &$categories) {
        $probil = '';
        for ($i = 0; $i < $level; $i++) {
            $probil .= '-- ';
        }
        $cat->name = ($probil . $cat->name);
        $categories[] = $cat;
        foreach ($all_cats as $categ) {
            if($categ->parent == $cat->id) {
                self::recurseTree($categ, ++$level, $all_cats, $categories);
                $level--;
            }
        }
        return $categories;
    }
	
	public static function getExtraField($params,$extrafield_id)
    {
		foreach($extrafield_id as $extra)
		{
			$extra_info = self::getExtraField_info($extra);
			$extra_id	= $extra;
			$extra_name	= $extra_info->name;
			$extra_value = json_decode($extra_info->value);
			foreach($extra_value as $value)
			{
				$extra_list[$extra_id.'_'.$extra_name][] = array(
					'value_id'	=>$value->value,
					'value_name'=>$value->name
				);
			}
		}
        return $extra_list;
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
}
