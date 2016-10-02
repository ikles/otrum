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

class MinitekWallViewWidgets extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		$utilities = new MinitekWallHelperUtilities();
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		// Get Navbar & Sidebar
		$this->navbar = $utilities->getNavbarHTML();
		$this->sidebar = $utilities->getSideMenuHTML();
		
		// Get Toolbar
		$this->addToolbar();
		
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$canDo = MinitekWallHelperUtilities::getActions();
		if (!JFactory::getUser()->authorise('core.manage', 'com_minitekwall'))
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_MINITEKWALL_WIDGETS'), 'widget.png');
		
		if (($canDo->get('core.edit')))
		{
			JToolbarHelper::editList('widget.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('widgets.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('widgets.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('widgets.archive');
			JToolbarHelper::checkin('widgets.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'widgets.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('widgets.trash');
		}
		
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::custom('widgets.purgeCache', 'refresh.png', 'refresh_f2.png', 'COM_MINITEKWALL_PURGE_CACHE', false);
		}		
		JHtmlSidebar::setAction('index.php?option=com_minitekwall&view=widgets');	
	}
}
