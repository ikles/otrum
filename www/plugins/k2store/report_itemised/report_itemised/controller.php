<?php
/*------------------------------------------------------------------------
# com_k2store - K2Store
# ------------------------------------------------------------------------
# author    Gokila Priya - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR.'/components/com_k2store/library/reportcontroller.php');

class K2StoreControllerReportItemised extends K2StoreControllerReportPlugin
{
	var $_element   = 'report_itemised';

	/**
	 * constructor
	 */
	function __construct()
	{

		parent::__construct();

		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6+ code
			JModelLegacy::addIncludePath(JPATH_SITE.'/plugins/k2store/report_ritemised/report_itemised/models');
			JTable::addIncludePath(JPATH_SITE.'/plugins/k2store/report_itemised/report_itemised/tables');
		}
		else {
			JModelLegacy::addIncludePath(JPATH_SITE.'/plugins/k2store/report_itemised/report_itemised/models');
			JTable::addIncludePath(JPATH_SITE.'/plugins/k2store/report_itemised/report_itemised/tables');
		}
	}

	function export(){
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id',0);
		JModelLegacy::addIncludePath(JPATH_SITE.'/plugins/k2store/report_itemised/report_itemised/models');
		$model = JModelLegacy::getInstance('ReportItemised', 'K2StoreModel');
		$data = $model->getData();
		$filename = $model->export($data);
		$url = "index.php?option=com_k2store&view=report&task=view&id=".$id;
		if($filename){
			$msg = JText::_('PLG_K2STORE_REPORT_ITEMISED_EXPORT_SUCCESS');
			$mtype="Message";
		}else{
			$msg = JText::_('PLG_K2STORE_REPORT_ITEMISED_EXPORT_FAILED');
			$mtype="Warning";
		}
		$app->redirect($url,$msg,$mtype);
	}



}
