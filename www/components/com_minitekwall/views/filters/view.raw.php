<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2015 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MinitekWallViewFilters extends JViewLegacy
{

	// Overwriting JView display method
	function display($tpl = null) 
	{		
    	$document = JFactory::getDocument();
		$this->model = JModelLegacy::getInstance('masonry', 'MinitekWallModel', $config = array());
		$utilities = $this->model->utilities;
		$params = $utilities->getParams('com_minitekwall');
		$jinput = JFactory::getApplication()->input;
		$widgetID = $jinput->get('widget_id');
		
		// Get masonry parameters
		$masonry_params = $utilities->getMasonryParams($widgetID);
		
		// Get Wall		
		$wall = $this->model->getAllResultsAjax($widgetID);
		
		// Create display params
		$detailBoxParams = array();
		$detailBoxParams['images'] = null;
		$detailBoxParams['crop_images'] = null;
		$detailBoxParams['image_width'] = null;
		$detailBoxParams['image_height'] = null;
		$detailBoxParams['detailBoxTitleLimit'] = null;
		$detailBoxParams['detailBoxIntrotextLimit'] = null;
		$detailBoxParams['detailBoxStripTags'] = null;
		$detailBoxParams['detailBoxDateFormat'] = null;
		
		$hoverBoxParams = array();
		$hoverBoxParams['hoverBox'] = null;
		$hoverBoxParams['hoverBoxTitle'] = null;
		$hoverBoxParams['hoverBoxTitleLimit'] = null;
		$hoverBoxParams['hoverBoxIntrotext'] = null;
		$hoverBoxParams['hoverBoxIntrotextLimit'] = null;
		$hoverBoxParams['hoverBoxStripTags'] = null;
		$hoverBoxParams['hoverBoxDate'] = null;
		$hoverBoxParams['hoverBoxDateFormat'] = null;
		
		$options = $this->model->options;
		$wall = $options->getWidgetDisplayOptions($widgetID, $wall, $detailBoxParams, $hoverBoxParams);
		
		// Get Filters
		$filters = '-'; // =0 for javascript purposes
		if ($masonry_params['mas_category_filters'] || 
			$masonry_params['mas_tag_filters'] ||
			$masonry_params['mas_location_filters'] ||
			$masonry_params['mas_date_filters'])
		{
			$masonry_filters = $this->model->masonry_filters;
			$filters = $masonry_filters->getFilters($wall, $masonry_params);
		}
		
		// Assign data to the view	
		$this->assignRef('filters', $filters);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Set raw type
		$document->setType('raw');
		
		// Set Layout
		$this->setLayout('default');
		
		// Display the view
		parent::display($tpl);
	}
	
}