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

// import Joomla controller library
jimport('joomla.application.component.controller');

// Add libraries prefix
JLoader::registerPrefix('MinitekWallLib', JPATH_SITE .DS. 'components' .DS. 'com_minitekwall' .DS. 'libraries');

class MinitekWallController extends JControllerLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
  	function display($cachable = false, $urlparams = false) 
	{
        if( !JRequest::getVar( 'view' )) {
			$error = JText::_('COM_MINITEKWALL_VIEW_NOT_FOUND');
            JError::raiseError(403, $error);
        }
		if( JRequest::getVar( 'view' ) && !JRequest::getVar( 'widget_id' )) {
			$error = JText::_('COM_MINITEKWALL_WIDGET_NOT_FOUND');
            JError::raiseError(403, $error);
        }
		
        parent::display();
    }

}