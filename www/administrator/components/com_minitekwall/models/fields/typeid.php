<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2015 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldTypeId extends JFormFieldList
{
	public $type = 'TypeId';

	public function getOptions()
	{
		
		$options = Array( 
			Array(
				'value' => '',
				'text' => JText::_('COM_MINITEKWALL_SELECT_TYPE_ID')
			),
			Array(
				'value' => 'masonry',
				'text' => JText::_('COM_MINITEKWALL_MASONRY')
			),
			Array(
				'value' => 'slider',
				'text' => JText::_('COM_MINITEKWALL_SLIDER')
			),
			Array(
				'value' => 'scroller',
				'text' => JText::_('COM_MINITEKWALL_SCROLLER')
			)
		); 
		
		return $options;
	}
	
}
