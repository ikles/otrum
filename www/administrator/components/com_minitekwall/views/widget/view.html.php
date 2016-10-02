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

class MinitekWallViewWidget extends JViewLegacy
{
	protected $form;
	protected $masonryform;
	protected $scrollerform;
	protected $sliderform;
	protected $item;
	protected $state;
	protected $canDo;

	public function display($tpl = null)
	{
		$this->form			= $this->get('Form');
		$this->masonryform	= $this->get('MasonryForm');
		$this->scrollerform	= $this->get('ScrollerForm');
		$this->sliderform	= $this->get('SliderForm');
		$this->item			= $this->get('Item');
		$this->state		= $this->get('State');
		$this->canDo		= $this->canDo	= MinitekWallHelperUtilities::getActions();
		
		$app = JFactory::getApplication();
		$this->type_id = $app->getUserState( 'com_minitekwall.type_id', '' );
		$this->source_id = $app->getUserState( 'com_minitekwall.source_id', '' );

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}
				
		// Get Navbar & Sidebar
		$utilities = new MinitekWallHelperUtilities();
		$this->navbar = $utilities->getNavbarHTML();
		$this->sidebar = $utilities->getSideMenuHTML();
		
		// Check if module is installed
		$this->checkModuleIsInstalled = $utilities->checkModuleIsInstalled();
		
		$this->addToolbar();
		
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user		= JFactory::getUser();
		$app = JFactory::getApplication(); 
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= $this->canDo;
		
		$step = JRequest::getInt('step');
		$layout = JRequest::getVar('layout');
		$type_id = $app->getUserState( 'com_minitekwall.type_id', '' );
		$source_id = $app->getUserState( 'com_minitekwall.source_id', '' );
		
		if ($isNew)
		{
			// No type
			if (!$type_id) 
			{
				// Redirect to step 1
				if (!$step || $step > 1) {
					if ($layout == 'edit') 
					{
						$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit&step=1');
					}
					if ($layout == 'edit_custom') 
					{
						$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit_custom&step=1');
					}
				}
			} else
			// With type
			if ($type_id) 
			{	
				// No source - Redirect to Step 2
				if (!$source_id)
				{
					if ($step > 2) {
						if ($layout == 'edit') 
						{
							$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit&step=2');
						}
						if ($layout == 'edit_custom') 
						{
							$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit_custom&step=2');
						}
					}
				}
			}
			
			// Titles
			if ($step == 1)
			{
				if ($layout == 'edit') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_NEW_DYNAMIC_WIDGET_SELECT_TYPE'), '');
				}
				if ($layout == 'edit_custom') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_NEW_CUSTOM_WIDGET_SELECT_TYPE'), '');
				}
			} 
			else if ($step == 2)
			{
				if ($layout == 'edit') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_NEW_DYNAMIC_WIDGET_SELECT_SOURCE'), '');
				}
				if ($layout == 'edit_custom') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_NEW_CUSTOM_WIDGET_SELECT_SOURCE'), '');
				}
			} 
			else if (!$step || $step == 3)
			{
				if ($layout == 'edit') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_NEW_DYNAMIC_WIDGET_SETTINGS'), '');
				}
				if ($layout == 'edit_custom') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_NEW_CUSTOM_WIDGET_SETTINGS'), '');
				}
			}
			
		}
		else
		{
			// Titles
			if ($step == 1)
			{
				if ($layout == 'edit') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_EDIT_DYNAMIC_WIDGET_SELECT_TYPE'), '');
				}
				if ($layout == 'edit_custom') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_EDIT_CUSTOM_WIDGET_SELECT_TYPE'), '');
				}
			} 
			else if ($step == 2)
			{
				if ($layout == 'edit') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_EDIT_DYNAMIC_WIDGET_SELECT_SOURCE'), '');
				}
				if ($layout == 'edit_custom') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_EDIT_CUSTOM_WIDGET_SELECT_SOURCE'), '');
				}
			} 
			else if (!$step || $step == 3)
			{
				if ($layout == 'edit') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_EDIT_DYNAMIC_WIDGET_SETTINGS'), '');
				}
				if ($layout == 'edit_custom') {
					JToolbarHelper::title(JText::_('COM_MINITEKWALL_MANAGER_EDIT_CUSTOM_WIDGET_SETTINGS'), '');
				}
			}
		}
		
		// Redirect steps
		if ($step > 3)
		{
			if ($isNew)
			{
				if ($layout == 'edit') 
				{
					$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit&step=3');
				}
				if ($layout == 'edit_custom') 
				{
					$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit_custom&step=3');
				}
			}
			else
			{
				if ($layout == 'edit') 
				{
					$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit&step=3&id='.$this->item->id);
				}
				if ($layout == 'edit_custom') 
				{
					$app->redirect('index.php?option=com_minitekwall&view=widget&layout=edit_custom&step=3&id='.$this->item->id);
				}
			}
		}
		
		// Step buttons
		if ($step == 1)
		{
			if ($type_id || !$isNew)
			{
				JToolbarHelper::custom('widget.dynamicStep2', 'next.png', 'next_f2.png', 'COM_MINITEKWALL_STEP_2', false);
			}
		}
		if ($step == 2)
		{
			JToolbarHelper::custom('widget.dynamicStep1', 'previous.png', 'previous_f2.png', 'COM_MINITEKWALL_STEP_1', false);
			if ($source_id || !$isNew)
			{
				JToolbarHelper::custom('widget.dynamicStep3', 'next.png', 'next_f2.png', 'COM_MINITEKWALL_STEP_3', false);
			}
		}
		if (!$step || $step == 3)
		{
			JToolbarHelper::custom('widget.dynamicStep2', 'previous.png', 'previous_f2.png', 'COM_MINITEKWALL_STEP_2', false);
		}
				
		// Save
		if ((!$step || $step == 3) && (($type_id && $source_id) || !$isNew)) {
			if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create')))
			{
				JToolbarHelper::apply('widget.apply');
				JToolbarHelper::save('widget.save');
			}
		}
		
		// Save and new
		if ((!$step || $step == 3) && (($type_id && $source_id) || !$isNew)) {
			if (!$checkedOut && $canDo->get('core.create'))
			{
				JToolbarHelper::save2new('widget.save2new');
			}
		}
		
		// Save as copy
		if ((!$step || $step == 3) && (($type_id && $source_id) || !$isNew)) {
			if (!$isNew && $canDo->get('core.create'))
			{
				JToolbarHelper::save2copy('widget.save2copy');
			}
		}
		
		// Cancel
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('widget.cancel');
		}
		else
		{
			JToolbarHelper::cancel('widget.cancel', 'JTOOLBAR_CLOSE');
		}
		
		// Preview
		//JToolbarHelper::preview( $url = '', $updateEditors = false);
	}
}
