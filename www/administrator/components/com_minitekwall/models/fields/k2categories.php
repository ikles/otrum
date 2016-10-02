<?php
/**
* @title		  	Joomfolio for K2
* @version   		3.x
* @copyright   		Copyright (C) 2011-2015 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die ;

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
$k2 = JPATH_ROOT.DS.'components'.DS.'com_k2';	
if (file_exists($k2.DS.'k2.php')) 
{
require_once (JPATH_ADMINISTRATOR.'/components/com_k2/elements/base.php');

class K2ElementK2Categories extends K2Element
{

    function fetchElement($name, $value, &$node, $control_name)
    {
        $params = JComponentHelper::getParams('com_k2');
        if (version_compare(JVERSION, '1.6.0', 'ge'))
        {
            JHtml::_('behavior.framework');
        }
        else
        {
            JHTML::_('behavior.mootools');
        }
        K2HelperHTML::loadjQuery();

        $db = JFactory::getDBO();
        $query = 'SELECT m.* FROM #__k2_categories m WHERE trash = 0 ORDER BY parent, ordering';
        $db->setQuery($query);
        $mitems = $db->loadObjectList();
        $children = array();
        if ($mitems)
        {
            foreach ($mitems as $v)
            {
                if (K2_JVERSION != '15')
                {
                    $v->title = $v->name;
                    $v->parent_id = $v->parent;
                }
                $pt = $v->parent;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        $mitems = array();

        foreach ($list as $item)
        {
            $item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
            $mitems[] = JHTML::_('select.option', $item->id, '   '.$item->treename);
        }

        if (K2_JVERSION != '15')
        {
            $fieldName = $name.'[]';
        }
        else
        {
            $fieldName = $control_name.'['.$name.'][]';
        }

        $output = JHTML::_('select.genericlist', $mitems, $fieldName, 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $value);
        return $output;
    }

}

class JFormFieldK2Categories extends K2ElementK2Categories
{
    var $type = 'k2categories';
}

class JElementK2Categories extends K2ElementK2Categories
{
    var $_name = 'k2categories';
}

}