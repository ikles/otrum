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

class JFormFieldSourceId extends JFormFieldList
{
	public $type = 'SourceId';

	public function getOptions()
	{
		
		$options = Array( 
			Array(
				'value' => '',
				'text' => JText::_('COM_MINITEKWALL_SELECT_SOURCE_ID')
			),
			Array(
				'value' => 'joomla',
				'text' => JText::_('COM_MINITEKWALL_JOOMLA')
			),
			Array(
				'value' => 'k2',
				'text' => JText::_('COM_MINITEKWALL_K2')
			),
			Array(
				'value' => 'virtuemart',
				'text' => JText::_('COM_MINITEKWALL_VIRTUEMART')
			),
			Array(
				'value' => 'jomsocial',
				'text' => JText::_('COM_MINITEKWALL_JOMSOCIAL')
			),
			Array(
				'value' => 'easyblog',
				'text' => JText::_('COM_MINITEKWALL_EASYBLOG')
			),
			Array(
				'value' => 'folder',
				'text' => JText::_('COM_MINITEKWALL_IMAGE_FOLDER')
			),
		); 
		
		return $options;
	}
	
}
