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

class MinitekWallViewAbout extends JViewLegacy
{
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
		JToolbarHelper::title(JText::_('COM_MINITEKWALL_ABOUT'), '');
	}

}
