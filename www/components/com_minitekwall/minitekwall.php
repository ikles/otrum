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

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

$controller	= JControllerLegacy::getInstance('MinitekWall');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

$document = JFactory::getDocument();

// Add params
jimport( 'joomla.application.component.helper' );
$params  = JComponentHelper::getParams('com_minitekwall');

// jQuery
if ($params->get('load_jquery')) {
	JHtml::_('jquery.framework');	
}

// Font Awesome
if ($params->get('load_fontawesome')) {
	$document->addStyleSheet('https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');
}