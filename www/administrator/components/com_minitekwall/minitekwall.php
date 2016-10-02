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

// Include dependancies
jimport('joomla.application.component.controller');

// Check component access
if (!JFactory::getUser()->authorise('core.manage', 'com_minitekwall'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include utilities helper
JLoader::register('MinitekWallHelperUtilities', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/utilities.php');

$controller	= JControllerLegacy::getInstance('MinitekWall');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

$document = JFactory::getDocument();

// Add font
$font_tag = '<link href="http://fonts.googleapis.com/css?family=Roboto:400,300,500,300italic,400italic,500italic,700,700italic,900" rel="stylesheet" type="text/css">';
$document->addCustomTag($font_tag);

// Add stylesheet
$document->addStyleSheet(JURI::root().'administrator/components/com_minitekwall/assets/css/style.css?v=3.2.0');
$document->addStyleSheet(JURI::root().'administrator/components/com_minitekwall/assets/mwicons/mwicons.css'); 
$document->addStyleSheet('https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');

// Add js
JHtml::_('bootstrap.framework');
$document->addScript(JURI::root().'administrator/components/com_minitekwall/assets/js/script.js?v=3.2.0');

// Clear state variables if view != widget
$view = JRequest::getVar('view');
if ($view !== 'widget')
{
	$app = JFactory::getApplication();		
	$app->setUserState('com_minitekwall.type_id', '');	
	$app->setUserState('com_minitekwall.source_id', '');	
}