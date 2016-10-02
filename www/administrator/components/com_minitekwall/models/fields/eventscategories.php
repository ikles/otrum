<?php
/**
* @title		  	Minitek Wall
* @version   		3.x
* @copyright   		Copyright (C) 2011-2015 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die ;

class JFormFieldEventsCategories extends JFormField
{

    function getInput()
    {
        $document = JFactory::getDocument();
        JHtml::_('behavior.framework');
		
        $db = JFactory::getDBO();
        $query = 'SELECT e.* FROM #__community_events_category e ORDER BY parent, id';
        $db->setQuery($query);
        $eitems = $db->loadObjectList();
        $children = array();
        if ($eitems)
        {
            foreach ($eitems as $v)
            {
                $v->title = $v->name;
                $v->parent_id = $v->parent;
				
                $pt = $v->parent;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        $eitems = array();
		//$eitems[] = JHTML::_('select.option', '0', JText::_('JALL'));
		
        foreach ($list as $item)
        {
            $item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
            $eitems[] = JHTML::_('select.option', $item->id, $item->treename);
        }
        
        $output = JHTML::_('select.genericlist', $eitems, $this->name, 'class="inputbox" multiple="true" size="10"', 'value', 'text', $this->value, $this->id);
		
		return $output;
    }

}