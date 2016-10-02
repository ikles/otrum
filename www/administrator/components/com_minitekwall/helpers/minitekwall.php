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

class MinitekWallHelper
{
	public static $extension = 'com_minitekwall';

	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_MINITEKWALL_SUBMENU_DASHBOARD'),
			'index.php?option=com_minitekwall&view=dashboard',
			$vName == 'dashboard'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_MINITEKWALL_SUBMENU_MODULE_INSTANCES'),
			'index.php?option=com_minitekwall&view=instances',
			$vName == 'instances'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_MINITEKWALL_SUBMENU_MODULES'),
			'index.php?option=com_minitekwall&view=modules',
			$vName == 'modules'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_MINITEKWALL_SUBMENU_ABOUT'),
			'index.php?option=com_minitekwall&view=about',
			$vName == 'about'
		);
	}

	public static function getActions($categoryId = 0, $articleId = 0)
	{
		// Reverted a change for version 2.5.6
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($articleId) && empty($categoryId))
		{
			$assetName = 'com_minitekwall';
		}
		elseif (empty($articleId))
		{
			$assetName = 'com_minitekwall.category.'.(int) $categoryId;
		}
		else
		{
			$assetName = 'com_minitekwall.instance.'.(int) $articleId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	public static function filterText($text)
	{
		JLog::add('MinitekWallHelper::filterText() is deprecated. Use JComponentHelper::filterText() instead.', JLog::WARNING, 'deprecated');

		return JComponentHelper::filterText($text);
	}
}
