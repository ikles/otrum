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

class MinitekWallViewDashboard extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$utilities = new MinitekWallHelperUtilities();
		
		$this->navbar = $utilities->getNavbarHTML();
		$this->sidebar = $utilities->getSideMenuHTML();
				
		$this->addTitle();
		
		parent::display($tpl);
	}

	protected function addTitle()
	{
		JToolbarHelper::title(JText::_('COM_MINITEKWALL_DASHBOARD_CREATE_WIDGET'), '');
	}

}
