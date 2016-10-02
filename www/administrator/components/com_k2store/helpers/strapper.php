<?php
/*------------------------------------------------------------------------
 # com_k2store - K2 Store
# ------------------------------------------------------------------------
# author    Ramesh Elamathi - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/

class K2StoreStrapper {

	public static function addJS() {
		$mainframe = JFactory::getApplication();
		$k2storeparams = JComponentHelper::getParams('com_k2store');
		$document =JFactory::getDocument();
		$option = $mainframe->input->getCmd('option');
		$view = $mainframe->input->getCmd('view');

		if($mainframe->isAdmin() && $option == 'com_k2' && $view =='item') {
			//TODO:: write a method that solves conflicts.
		} else {
			if (!version_compare(JVERSION, '3.0', 'ge'))
			{
				if($k2storeparams->get('load_jquery', 1)) {
					$document->addScript(JURI::root(true).'/media/k2store/js/k2storejq.js');
				}
			} else {

				JHtml::_('jquery.framework');
				JHtml::_('bootstrap.framework');
			}
		}

		$document->addScript(JURI::root(true).'/media/k2store/js/k2store-noconflict.js');
		$ui_location = $k2storeparams->get ( 'load_jquery_ui', 3 );
		switch ($ui_location) {
		
			case '0' :
				// load nothing
				break;
			case '1':
				if ($mainframe->isSite ()) {
					$document->addScript ( JURI::root ( true ) . '/media/k2store/js/k2storejqui.js' );
				}
				break;
		
			case '2' :
				if ($mainframe->isAdmin ()) {
					$document->addScript ( JURI::root ( true ) . '/media/k2store/js/k2storejqui.js' );
				}
				break;
		
			case '3' :
			default :
				$document->addScript ( JURI::root ( true ) . '/media/k2store/js/k2storejqui.js' );
				break;
		}

		if (!version_compare(JVERSION, '3.0', 'ge'))
		{
			$document->addScript(JURI::root(true).'/media/k2store/js/bootstrap.min.js');
		}

		if($mainframe->isAdmin()) {
			$document->addScript(JURI::root(true).'/media/k2store/js/jquery.validate.min.js');
			//add additional css if it is version 2.5
			$document->addScript(JURI::root(true).'/media/k2store/js/k2store_admin.js');
		}
		else {
			$document->addScript(JUri::root(true).'/media/k2store/js/jquery-ui-timepicker-addon.js');
			self::loadTimepickerScript($document);
			$document->addScript(JURI::root(true).'/media/k2store/js/k2store.js');
		}

	}

	public static function addCSS() {
		$mainframe = JFactory::getApplication();
		$k2storeparams = JComponentHelper::getParams('com_k2store');
		$document =JFactory::getDocument();

		if (!version_compare(JVERSION, '3.0', 'ge'))
		{
			if($mainframe->isAdmin()) {
				//always load bootstrap for J 2.5 admin side
				$document->addStyleSheet(JURI::root(true).'/media/k2store/css/bootstrap.min.css');
			}			
		}
		
		//for site side, check if the param is enabled.
		if($mainframe->isSite() && $k2storeparams->get('load_bootstrap', 1)) {
			$document->addStyleSheet(JURI::root(true).'/media/k2store/css/bootstrap.min.css');
		}
		
		if($mainframe->isAdmin()) {
			$document->addStyleSheet(JURI::root(true).'/media/k2store/css/k2store_admin.css');
		}
		else {
			$document->addStyleSheet(JURI::root(true).'/media/k2store/css/jquery-ui-custom.css');
			// Add related CSS to the <head>
			if ($document->getType() == 'html' && $k2storeparams->get('k2store_enable_css')) {

				$db = JFactory::getDBO();
				$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home=1";
				$db->setQuery( $query );
				$template = $db->loadResult();

				jimport('joomla.filesystem.file');
				// k2store.css
				if(JFile::exists(JPATH_SITE.'/templates/'.$template .'/css/k2store.css'))
					$document->addStyleSheet(JURI::root(true).'/templates/'.$template .'/css/k2store.css');
				else
					$document->addStyleSheet(JURI::root(true).'/media/k2store/css/k2store.css');

			} else {
				$document->addStyleSheet(JURI::root(true).'/media/k2store/css/k2store.css');
			}
	}

	}

	public static function loadTimepickerScript($document) {
		static $sets;

		if ( !is_array( $sets ) )
		{
			$sets = array( );
		}
		$id = 1;
		if(!isset($sets[$id])) {
			$document->addScriptDeclaration(self::getTimePickerScript());
			$sets[$id] = true;
		}
	}

	public static function getTimePickerScript($date_format='', $time_format='', $prefix='k2store', $isAdmin=false) {

		//initialise the date/time picker
		if($isAdmin) {
			$document =JFactory::getDocument();
			$document->addScript(JUri::root(true).'/media/k2store/js/jquery-ui-timepicker-addon.js');
			$document->addStyleSheet(JURI::root(true).'/media/k2store/css/jquery-ui-custom.css');
		}

		if(empty($date_format)) {
			$date_format = 'yy-mm-dd';
		}

		if(empty($time_format)) {
			$time_format = 'HH:mm';
		}

		$element_date = $prefix.'_date';
		$element_time = $prefix.'_time';
		$element_datetime = $prefix.'_datetime';

		//localisation
		$currentText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_CURRENT_TEXT'));
		$closeText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_CLOSE_TEXT'));
		$timeOnlyText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_CHOOSE_TIME'));
		$timeText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_TIME'));
		$hourText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_HOUR'));
		$minuteText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_MINUTE'));
		$secondText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_SECOND'));
		$millisecondText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_MILLISECOND'));
		$timezoneText = addslashes(JText::_('K2STORE_TIMEPICKER_JS_TIMEZONE'));

		$localisation ="
		currentText: '$currentText',
		closeText: '$closeText',
		timeOnlyTitle: '$timeOnlyText',
		timeText: '$timeText',
		hourText: '$hourText',
		minuteText: '$minuteText',
		secondText: '$secondText',
		millisecText: '$millisecondText',
		timezoneText: '$timezoneText'
		";

		$timepicker_script ="
		if(typeof(k2store) == 'undefined') {
		var k2store = {};
		}

		if(typeof(jQuery) != 'undefined') {
		jQuery.noConflict();
		}

		if(typeof(k2store.jQuery) == 'undefined') {
		k2store.jQuery = jQuery.noConflict();
		}

		if(typeof(k2store.jQuery) != 'undefined') {

		(function($) {
		$(document).ready(function(){
		//date, time, datetime
		if ($.browser.msie && $.browser.version == 6) {
		$('.$element_date, .$element_datetime, .$element_time').bgIframe();
		}

		$('.$element_date').datepicker({dateFormat: '$date_format'});
		$('.$element_datetime').datetimepicker({
		dateFormat: '$date_format',
		timeFormat: '$time_format',
		$localisation
		});

		$('.$element_time').timepicker({timeFormat: '$time_format', $localisation});

		});
		})(k2store.jQuery);
		}
		";

		return $timepicker_script;

	}

}