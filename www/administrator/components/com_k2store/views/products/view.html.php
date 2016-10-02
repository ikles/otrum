<?php
/*------------------------------------------------------------------------
 # com_k2store - K2Store
# ------------------------------------------------------------------------
# author   priya bose - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/



// no direct access
defined('_JEXEC') or die('Restricted access');


class K2StoreViewProducts extends K2StoreView
{
	function display($tpl=null)
	{
		// initialize the appllication
		$app = JFactory::getApplication();
		$model=$this->getModel('products');

		// get the Layout
		$layout=$this->getLayout();

		$option = 'com_k2store';
		$ns='com_k2store.products';
		// Initialize the DB connection
		$db=JFactory::getDbo();
		$uri	=JFactory::getURI();
		$params = JComponentHelper::getParams('com_k2store');

		$javascript 	= 'onchange="document.adminForm.submit();"';

		// Order option based on the user request
		$filter_articles	= $app->getUserStateFromRequest( $ns.'filter_article_type',		'filter_article_type',		'',	'cmd' );
		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'p.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'ASC',				'word' );
		$filter_orderstate	= $app->getUserStateFromRequest( $ns.'filter_orderstate',	'filter_orderstate',	'', 'string' );
		// Get the  user Reguested String

		$search				= $app->getUserStateFromRequest( $ns.'search',			'search',			'',				'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		// search filter
		$lists['search']= $search;


		$this->lists=$lists;

		require_once JPATH_ADMINISTRATOR.'/components/com_k2/models/categories.php';
		$categoriesModel = K2Model::getInstance('Categories', 'K2Model');
		$categories_option= array(''=>JText::_('JOPTION_SELECT_CATEGORY'));
		$categories = $categoriesModel->categoriesTree(NULL, true, false);
		$categories_options = @array_merge($categories_option, $categories);

		$ordering = (($this->lists['order'] == 'p.ordering' ));
		$this->assignRef('ordering', $ordering);

		// Joomla! 3.0 drag-n-drop sorting variables
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JHtml::_('bootstrap.tooltip');
			if ($ordering)
			{
				JHtml::_('sortablelist.sortable', 'productsList', 'adminForm', strtolower($this->lists['order_Dir']), 'index.php?option=com_k2store&view=products&task=saveorder&format=raw');
			}
			$document = JFactory::getDocument();
			$document->addScriptDeclaration('
					Joomla.orderTable = function() {
					table = document.getElementById("sortTable");
					direction = document.getElementById("directionTable");
					order = table.options[table.selectedIndex].value;
					if (order != \''.$this->lists['order'].'\') {
					dirn = \'asc\';
		} else {
					dirn = direction.options[direction.selectedIndex].value;
		}
					Joomla.tableOrdering(order, dirn, "");
		}');
		}

		// Get data from the model
		$this->items		=  $this->get( 'Data');
		$this->total		=  $this->get( 'Total');
		$this->state		= $this->get('State');
		$this->pagination	=  $this->get( 'Pagination' );

		$this->page=$model->getPagination();

		$article_options	= array();
		$article_options[]	= JHtml::_('select.option', '0', JText::_('K2STORE_PRODUCT_SHOW_ALL_ARTICLES'));
		$article_options[]	= JHtml::_('select.option', '1', JText::_('K2STORE_PRODUCT_SHOW_K2STORE_ARTICLES'));

		$this->f_levels = $article_options;

		$javascript 	= 'onchange="document.adminForm.submit();"';
		$this->category_options = JHTML::_('select.genericlist', $categories_options, 'filter_category_id', $javascript, 'value', 'text', $this->state->get('filter.category_id'));
		$this->params = $params;

		$this->addToolBar();
		$toolbar = new K2StoreToolBar();
		$toolbar->renderLinkbar();
		parent::display($tpl);
		}

	function addToolBar()
		{
			JToolBarHelper::title(JText::_('K2STORE_PRODUCTS'),'k2store-logo');
			JToolBarHelper::addNew();

		}

}
