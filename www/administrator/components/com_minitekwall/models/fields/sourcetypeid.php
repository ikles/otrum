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

class JFormFieldSourceTypeId extends JFormFieldList
{
	public $type = 'SourceTypeId';

	public function getOptions()
	{
		
		$options = Array( 
			Array(
				'value' => '',
				'text' => JText::_('COM_MINITEKWALL_SELECT_SOURCE_TYPE_ID')
			),
			Array(
				'value' => 'dynamic',
				'text' => JText::_('COM_MINITEKWALL_DYNAMIC')
			),
			Array(
				'value' => 'custom',
				'text' => JText::_('COM_MINITEKWALL_CUSTOM')
			)
		); 
		
		return $options;
	}
	
}
