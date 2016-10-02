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

class JFormFieldPhotosAlbums extends JFormField
{

    function getInput()
    {
        $document = JFactory::getDocument();
        JHtml::_('behavior.framework');
		
        $db = JFactory::getDBO();
        $query = 'SELECT p.* FROM #__community_photos_albums p ORDER BY name, id';
        $db->setQuery($query);
        $list = $db->loadObjectList();
        
        $pitems = array();
		//$pitems[] = JHTML::_('select.option', '0', JText::_('JALL'));
		
        foreach ($list as $item)
        {
            $pitems[] = JHTML::_('select.option', $item->id, $item->name);
        }
        
        $output = JHTML::_('select.genericlist', $pitems, $this->name, 'class="inputbox" multiple="true" size="10"', 'value', 'text', $this->value, $this->id);
		
		return $output;
    }

}