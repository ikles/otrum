<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2014 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JFormFieldModuleInstance extends JFormField
{

    function getInput()
    {
        JHtml::_('behavior.framework');
		
        $db = JFactory::getDBO();
        $query = 'SELECT m.* FROM #__minitek_wall_modules m WHERE published = 1 ORDER BY name';
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        if ($items)
        {
			$list = array();
            foreach ($items as $v)
            {
                $pt = $v->name;
                array_push($list, $v);
            }
			
			$items = array();
		
			foreach ($list as $item)
			{
				$item->name = JString::str_ireplace('&#160;', '- ', $item->name);
				$items[] = JHTML::_('select.option', $item->id, $item->name);
			}
	
			$output = JHTML::_('select.genericlist', $items, $this->name, 'class="inputbox" size="10"', 'value', 'text', $this->value, $this->id);
        
		} else {
			$output = JText::_('COM_MINITEKWALL_NO_MODULES_FOUND');
		}
		
		return $output;
		
    }

}
