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

class MinitekWallControllerWidgets extends JControllerAdmin
{
	protected $text_prefix = 'COM_MINITEKWALL_WIDGETS';

	public function getModel($name = 'Widget', $prefix = 'MinitekWallModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
	
	public function clearWidgetStateVariables()
	{
		$app = JFactory::getApplication();
		
		$app->setUserState('com_minitekwall.type_id', '');	
		$app->setUserState('com_minitekwall.source_id', '');	
	}
	
	public function purgeCache()
	{
		// Delete images folder
		jimport('joomla.filesystem.folder');
		JSession::checkToken('request') or jexit('Invalid token');		
		$app = JFactory::getApplication();
		
		$tmppath =  JPATH_SITE.DS.'images'.DS.'mnwallimages'.DS;
		if (file_exists($tmppath)) 
		{
			JFolder::delete($tmppath);
			$message = JText::_('COM_MINITEKWALL_IMAGES_PURGED');
			$link = JRoute::_('index.php?option=com_minitekwall&view=widgets');
			$app->redirect(str_replace('&amp;', '&', $link), $message, 'message');	
		} 
		else 
		{
			$message = JText::_('COM_MINITEKWALL_IMAGES_PURGED_ALREADY');
			$link = JRoute::_('index.php?option=com_minitekwall&view=widgets');
			$app->redirect(str_replace('&amp;', '&', $link), $message, 'notice');	
		}
	}
	
}
