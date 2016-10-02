<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2014 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

jimport( 'joomla.application.component.helper' );
$componentParams  = JComponentHelper::getParams('com_minitekwall');
$document = JFactory::getDocument();
if ($componentParams->get('load_fontawesome')) {
	$document->addStyleSheet('https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');
}

$widget_id = $params->get('widget_id');

// Get widget type
$db = JFactory::getDBO();
$query = ' SELECT * '
	. ' FROM '. $db->quoteName('#__minitek_wall_widgets') . ' '
	. ' WHERE '.$db->quoteName('id').' = ' . $db->Quote($widget_id);
$db->setQuery($query);
$widget_type = $db->loadObject()->type_id;
$document->addStyleSheet(JURI::base().'components/com_minitekwall/assets/css/'.$widget_type.'.css');

// Load page
$jinput = JFactory::getApplication()->input;
$option = $jinput->getCmd('option', NULL);
$view = $jinput->getCmd('view', NULL);
$layout = $jinput->getCmd('layout', NULL);
$task = $jinput->getCmd('task', NULL);
JRequest::setVar('option', 'com_minitekwall');
JRequest::setVar('view', $widget_type);
JRequest::setVar('widget_id', $widget_id);

// Load language
$lang = JFactory::getLanguage();
$lang->load('com_minitekwall', JPATH_SITE);

if (!class_exists('MinitekWallController')) 
{
	require_once (JPATH_SITE .DS. 'components' .DS. 'com_minitekwall' .DS. 'controller.php');
	require_once (JPATH_SITE .DS. 'components' .DS. 'com_minitekwall' .DS. 'models' .DS. 'masonry.php');
	require_once (JPATH_SITE .DS. 'components' .DS. 'com_minitekwall' .DS. 'models' .DS. 'scroller.php');
}

// Load controller
$controller = new MinitekWallController();
$controller->setProperties(array(
	'basePath' => JPATH_SITE .DS. 'components' .DS. 'com_minitekwall',
	'paths' => array(
		'view' => array(
			JPATH_SITE .DS. 'components' .DS. 'com_minitekwall' .DS. 'views'
		),
		'model' => array(
			JPATH_SITE .DS. 'components' .DS. 'com_minitekwall' .DS. 'models'
		)
	)
));
$controller->execute('display');

if ($option != null)
{
	JRequest::setVar('option', $option);
}

if ($view != null)
{
	JRequest::setVar('view', $view);
}

if ($layout != null)
{
	JRequest::setVar('layout', $layout);
}

if ($task != null)
{
	JRequest::setVar('task', $task);
}