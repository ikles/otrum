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


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once(JPATH_ADMINISTRATOR.'/components/com_k2store/library/k2item.php');
class K2StoreControllerProducts extends K2StoreController {

	function __construct()
	{
		parent::__construct();
		$this->registerTask('unsetDefault', 'setDefault');
		$this->registerTask('setDefault', 'setDefault');

	}

	function saveAll()
	{
		$app=JFactory::getApplication();
		$post=$app->input->getArray($_POST);
		//$this->get
		$model=$this->getModel('products');
		if(!$model->saveAll($post))
		{
			$msg=$model->getError();
		}else{
			$msg=JText::_('K2STORE_ALL_CHANGES_SAVED');
		}
		$this->setRedirect('index.php?option=com_k2store&view=products', $msg);
	}



	function createattribute() {
		$app = JFactory::getApplication();
		$model  = $this->getModel( 'productattributes' );
		$row = $model->getTable();
		$row->product_id = $app->input->getInt( 'id' );
		$row->productattribute_name = $app->input->getString( 'productattribute_name' );
		$row->productattribute_display_type = $app->input->getString( 'productattribute_display_type', 'select' );
		$row->productattribute_required = $app->input->getInt( 'productattribute_required', 0 );
		$row->ordering = '99';
		//  $post=JRequest::get('post');

		if ( !$row->save() )
		{
			$messagetype = 'notice';
			$message = JText::_( 'K2STORE_SAVE_FAILED' )." - ".$row->getError();
		}

		$redirect = "index.php?option=com_k2store&view=products&task=setattributes&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $message, $messagetype );

	}


	function setattributes()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productattributes');
		$ns = 'com_k2store.productattributes';

		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$id = $app->input->getInt('id', 0);

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
		$row = JTable::getInstance('K2Item', 'Table');
		$row->load($id);

		$items = $model->getData();
		$total		= $model->getTotal();
		$pagination = $model->getPagination();

		$view   = $this->getView( 'productattributes', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_k2store&view=products&task=setattributes&tmpl=component&id=".$id);
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $items );
		$view->assign( 'total', $total );
		$view->assign( 'lists', $lists );
		$view->assign( 'pagination', $pagination );
		$view->assign( 'product_id', $id );
		$view->setLayout( 'default' );
		$view->display();
	}


	function saveattributes()
	{
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';
		$app = JFactory::getApplication();
		$model = $this->getModel('productattributes');
		$row = $model->getTable();

		$id = $app->input->getInt('id', 0);
		$cids = $app->input->post->get('cid', array(0), 'ARRAY');
		$name = $app->input->post->get('name', array(0), 'ARRAY');
		$display_type = $app->input->post->get('display_type', array(0), 'ARRAY');
		$pa_required = $app->input->post->get('pa_required', array(0), 'ARRAY');
		$ordering = $app->input->post->get('ordering', array(0), 'ARRAY');

		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->productattribute_name = $name[$cid];
			$row->productattribute_display_type = $display_type[$cid];
			$row->productattribute_required = $pa_required[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_k2store&view=products&task=setattributes&id={$id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	function deleteattributes()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$product_id = $app->input->getInt('product_id');
		if (!isset($this->redirect)) {
			$this->redirect = $app->input->getString('return' )
			? base64_decode( $app->input->getString( 'return' ) )
			: 'index.php?option=com_k2store&view=products&task=setattributes&id='.$product_id.'&tmpl=component';
			$this->redirect = JRoute::_( $this->redirect, false );
		}

		$model = $this->getModel('productattributes');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array (0),'ARRAY');
		foreach (@$cids as $cid)
		{
			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('K2STORE_ITEMS_DELETED');
		}

		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );
	}

	function setattributeoptions()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productattributeoptions');
		$ns = 'com_k2store.productattributeoptions';
		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$id = $app->input->getInt('id', 0);

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2store'.DS.'tables');
		$row = JTable::getInstance('ProductAttributes', 'Table');
		$row->load($model->getId());

		$items = $model->getData();
		$total		= $model->getTotal();
		$pagination = $model->getPagination();

		$view   = $this->getView( 'productattributeoptions', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_k2store&view=products&task=setattributeoptions&tmpl=component&id=".$model->getId());
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $items );
		$view->assign( 'total', $total );
		$view->assign( 'lists', $lists );
		$view->assign( 'pagination', $pagination );
		$view->setLayout( 'default' );
		$view->display();
	}


	function createattributeoption()
	{
		$app = JFactory::getApplication();
		$model  = $this->getModel( 'productattributeoptions' );
		$row = $model->getTable();
		$row->productattribute_id = $app->input->getInt('id', 0);
		$row->productattributeoption_name = $app->input->getString('productattributeoption_name' );
		$row->productattributeoption_price = $app->input->getInt( 'productattributeoption_price' );
		$row->productattributeoption_code = $app->input->getString( 'productattributeoption_code' );
		$row->productattributeoption_prefix = $app->input->getString( 'productattributeoption_prefix' );
		$row->ordering = '99';

		if (!$row->save() )
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_( 'K2STORE_SAVE_FAILED' )." - ".$row->getError();
		}

		$redirect = "index.php?option=com_k2store&view=products&task=setattributeoptions&id={$row->productattribute_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	function saveattributeoptions()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';
		$model = $this->getModel('productattributeoptions');
		$row = $model->getTable();

		$productattribute_id = $app->input->get('id', 0);
		$cids = $app->input->post->get('cid', array(0), 'ARRAY');
		$name = $app->input->post->get('name', array(0), 'ARRAY');
		$prefix = $app->input->post->get('prefix', array(0), 'ARRAY');
		$price = $app->input->post->get('price', array(0), 'ARRAY');
		$code = $app->input->post->get('code', array(0), 'ARRAY');
		$ordering = $app->input->post->get('ordering', array(0), 'ARRAY');

		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->productattributeoption_name = $name[$cid];
			$row->productattributeoption_prefix = $prefix[$cid];
			$row->productattributeoption_price = $price[$cid];
			$row->productattributeoption_code = $code[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_k2store&view=products&task=setattributeoptions&id={$productattribute_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	function deleteattributeoptions()
	{
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$app = JFactory::getApplication();
		$productattribute_id = $app->input->get( 'pa_id' );
		if (!isset($this->redirect)) {
			$this->redirect = $app->input->getString( 'return' )
			? base64_decode( $app->input->getString( 'return' ) )
			: 'index.php?option=com_k2store&view=products&task=setattributeoptions&id='.$productattribute_id.'&tmpl=component';
			$this->redirect = JRoute::_( $this->redirect, false );
		}

		$model = $this->getModel('productattributeoptions');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array (0), 'ARRAY');
		foreach (@$cids as $cid)
		{
			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('K2STORE_ITEMS_DELETED');
		}

		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );
	}

//product attributie option extra
	function setpaoextra()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productoptionvalues');

		$id = $app->input->getInt('pov_id', 0);

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2store/tables');
		$row = JTable::getInstance('ProductOptionValues', 'Table');
		$row->load($id);

		$view   = $this->getView( 'paoextra', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_k2store&view=products&task=setpaoextra&tmpl=component&pov_id=".$model->getId());
		$view->setModel( $model, true );
		$view->assign( 'row', $row );
		$view->setLayout( 'default' );
		$view->display();
	}


	function savepaoextra()
	{
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';
		$app = JFactory::getApplication();
		$model = $this->getModel('productoptionvalues');
		$row = $model->getTable();
		$data = $app->input->get('extra', array(), 'ARRAY');

		$pov_id = $app->input->getInt('pov_id', 0);
		$row->load($pov_id);
		if($row->product_optionvalue_id == $pov_id) {
			$row->bind($data);

				if (!$row->check() || !$row->store())
				{
					$this->message .= $row->getError();
					$this->messagetype = 'notice';
					$error = true;
				}
		}
		else {
			$this->message .= JText::_('K2STORE_PAO_VALUE_RECORD_NOT_FOUND');
			$error = true;
		}

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}
		$redirect = "index.php?option=com_k2store&view=products&task=setpaoextra&pov_id={$pov_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	//product attributie option extra
	function setpaimport()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('paimport');
		$ns = 'com_k2store.paimport';
		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'p.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$product_id = JRequest::getVar('id', 0, 'get', 'int');

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
		$row = JTable::getInstance('K2Item', 'Table');
		$row->load($model->getId());

		$items = $model->getData();
		$total		= $model->getTotal();
		$pagination = $model->getPagination();

		$view   = $this->getView( 'paimport', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_k2store&view=products&task=setpaimport&tmpl=component&product_id=".$model->getId());
		$view->setModel( $model, true );
		$view->assign('model', $model);
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row);
		$view->assign( 'items', $items );
		$view->assign( 'total', $total );
		$view->assign( 'lists', $lists );
		$view->assign( 'pagination', $pagination );
		$view->assign('product_id', $product_id);
		$view->setLayout( 'default' );
		$view->display();
	}

	function importattributes() {
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';
		$cids = $app->input->get('cid', array(), 'array');
		$product_id = $app->input->getInt('product_id', 0);
		if(empty($cids) || count($cids) < 1) {
			$error = true;
			$this->message .= JText::_('K2STORE_PAI_SELECT_PRODUCT_TO_IMPORT');
			$this->messagetype = 'notice';
		} else {
			//get the model
			$model = $this->getModel('paimport');
			foreach($cids as $cid) {
				if(!$model->importAttributeFromProduct($cid, $product_id)){
					$this->message .= $model->getError();
					$this->messagetype = 'error';
				}
			}

		}
		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('K2STORE_PAI_SELECT_ATTRIBUTES_IMPORTED');
			$this->messageType = 'message';
		}
		if($product_id) K2StoreItem::doProductQuantitiesReconciliation($product_id);
		$redirect = "index.php?option=com_k2store&view=products&task=setpaimport&product_id={$product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );

	}

	/*
	 * PA options section
	 */

	function createproductoptionvalue() {
		$app = JFactory::getApplication();
		$model  = $this->getModel( 'productoptionvalues' );
		$row = $model->getTable();
		$row->product_option_id = $app->input->getInt( 'product_option_id' );
		$row->option_id = $app->input->getInt( 'option_id' );
		$row->product_id = $app->input->getInt( 'product_id' );
		$row->optionvalue_id = $app->input->getInt( 'productoptionvalue_id' );
		$row->product_optionvalue_price = $app->input->get( 'product_optionvalue_price');
		$row->product_optionvalue_prefix = JFactory::getDbo()->escape($app->input->getString( 'product_optionvalue_prefix'));
		$row->product_optionvalue_weight = $app->input->get( 'product_optionvalue_weight');
		$row->product_optionvalue_weight_prefix = JFactory::getDbo()->escape($app->input->getString( 'product_optionvalue_weight_prefix'));
		$row->product_optionvalue_sku = $app->input->getString( 'product_optionvalue_sku');

		$row->ordering = '99';
		//  $post=JRequest::get('post');

		if ( !$row->save() )
		{
			$messagetype = 'notice';
			$message = JText::_( 'K2STORE_SAVE_FAILED' )." - ".$row->getError();
		}
		if($row->product_id) K2StoreItem::doProductQuantitiesReconciliation($row->product_id);
		$redirect = "index.php?option=com_k2store&view=products&task=setproductoptionvalues&product_option_id={$row->product_option_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $message, $messagetype );

	}


	function setproductoptionvalues()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productoptionvalues');
		$ns = 'com_k2store.productoptionvalues';

		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$product_option_id = $app->input->getInt('product_option_id', 0);

		//load the product options table joining general options tables
		$product_options = $model->getProductOptions();

		//load the general option values
		$lists['option_values'] = null;
		$option_values = array();
		$option_values = $model->getOptionValues($product_options->option_id);
		if(count($option_values)) {
			foreach($option_values as $option_value) {
				$options[] = JHtml::_('select.option', $option_value->optionvalue_id, $option_value->optionvalue_name);
			}
			$attribs = array('class' => 'inputbox', 'size'=>'1', 'title'=>JText::_('K2STORE_SELECT_AN_OPTION'));
			$lists['option_values'] = JHtml::_('select.genericlist', $options, 'productoptionvalue_id', $attribs, 'value', 'text', '', 'productoptionvalue_id');
		}

		$items = $model->getData();
		$total		= $model->getTotal();
		$pagination = $model->getPagination();

		$view   = $this->getView( 'productoptionvalues', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_k2store&view=products&task=setproductoptionvalues&tmpl=component&product_option_id=".$product_option_id);
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $product_options );
		$view->assign( 'items', $items );
		$view->assign( 'total', $total );
		$view->assign( 'lists', $lists );
		$view->assign( 'pagination', $pagination );
		$view->assign( 'product_option_id', $product_option_id);
		$view->setLayout( 'default' );
		$view->display();
	}


	function saveproductoptionvalues()
	{
		$error = false;
		$this->messageType  = '';
		$this->message      = '';
		$app = JFactory::getApplication();
		$model = $this->getModel('productoptionvalues');
		$row = $model->getTable();

		$product_option_id = $app->input->getInt('product_option_id', 0);
		$product_id = $app->input->getInt('product_id', 0);
		$option_id = $app->input->getInt('option_id', 0);
		$redirect = "index.php?option=com_k2store&view=products&task=setproductoptionvalues&product_option_id={$product_option_id}&tmpl=component";
		if(!$product_option_id || !$product_id || !$option_id ) {
			$this->messageType  = 'notice';
			$this->message      = JText::_('K2STORE_OPTIONVALUES_MISSING_VALUES');
			$app->redirect($redirect);
		}

		$cids = $app->input->post->get('cid', array(0), 'ARRAY');
		$optionvalue_ids = $app->input->post->get('optionvalue_id', array(0), 'ARRAY');
		$prefix = $app->input->post->get('prefix', array(0), 'ARRAY');
		$price = $app->input->post->get('price', array(0), 'ARRAY');
		$ordering = $app->input->post->get('ordering', array(0), 'ARRAY');
		$weight_prefix = $app->input->post->get('weight_prefix', array(0), 'ARRAY');
		$weight = $app->input->post->get('weight', array(0), 'ARRAY');
		$sku = $app->input->post->get('sku', array(0), 'ARRAY');

		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->optionvalue_id = $optionvalue_ids[$cid];
			$row->product_optionvalue_prefix = $prefix[$cid];
			$row->product_optionvalue_price = $price[$cid];
			$row->product_optionvalue_weight_prefix = $weight_prefix[$cid];
			$row->product_optionvalue_weight = $weight[$cid];
			$row->product_optionvalue_sku = $sku[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	function deleteproductoptionvalues()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$product_option_id = $app->input->getInt('po_id');
		if (!isset($this->redirect)) {
			$this->redirect = $app->input->getString('return' )
			? base64_decode( $app->input->getString( 'return' ) )
			: 'index.php?option=com_k2store&view=products&task=setproductoptionvalues&id='.$product_option_id.'&tmpl=component';
			$this->redirect = JRoute::_( $this->redirect, false );
		}

		$model = $this->getModel('productoptionvalues');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array (0),'ARRAY');
		foreach (@$cids as $cid)
		{
			$row->load($cid);
			$product_id = $row->product_id;
			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
			if($product_id) K2StoreItem::doProductQuantitiesReconciliation($product_id);
		}

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('K2STORE_ITEMS_DELETED');
		}

		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );
	}

	function setDefault() {
		$app = JFactory::getApplication();
		$cid = $app->input->get('cid', array(), 'array');
		$task = $app->input->getString('task');
		$pov_id = $cid[0];
		$product_id = $app->input->getInt('product_id');
		$product_option_id = $app->input->getInt('product_option_id');

		if($product_id && $product_option_id && $pov_id) {

			$model = $this->getModel('productoptionvalues');

			//first query others and set them not default
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)->update('#__k2store_product_optionvalues')->set('product_optionvalue_default=0')
			->where('product_id='.$db->q($product_id))
			->where('product_option_id='.$db->q($product_option_id))
			;
			$db->setQuery($query)->execute();

			$row = $model->getTable();
			$row->load($pov_id);
			if($task == 'unsetDefault') {
				$row->product_optionvalue_default=0;
			} else {
				$row->product_optionvalue_default=1;
			}

			$row->store();
		}
		$redirect = "index.php?option=com_k2store&view=products&task=setproductoptionvalues&product_option_id={$row->product_option_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $message, $messagetype );
	}

	public static function removeProductOption() {

		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2store/tables');
		$result = array();
		$id = $app->input->post->get('pao_id');
		$table =  JTable::getInstance('ProductOptions', 'Table');
		$table->load($id);
		$product_id = $table->product_id;
		if($table->delete($id)){
			$result['success'] = 1;

			//now remove option values associated with this
			$query = $db->getQuery(true);
			$query->delete('#__k2store_product_optionvalues')->where('product_option_id='.$id);
			try {
				$db->query();
			} catch (Exception $e) {
				//failed... dont worry about it
			}

			//reconcile stock quantity
			if($product_id) {
				K2StoreItem::doProductQuantitiesReconciliation($product_id);
			}

		} else {
			$result['success'] = 0;
		}

		echo json_encode($result);
		$app->close();
	}


	/*
	 *
	* Price range */

	/*-------------------------------------------------------------------------------*/

	function createpricerange() {
		$app = JFactory::getApplication();
		$model  = $this->getModel( 'productprices' );
		$row = $model->getTable();
		$row->product_id = $app->input->getInt( 'id' );
		$row->quantity_start = $app->input->getString( 'pricerange_quantity_start' );
		$row->condition = $app->input->getString( 'pricerange_condition', 'above' );
		$row->pricetype = $app->input->getString( 'pricerange_price_type', 0 );
		$row->price = $app->input->getFloat( 'pricerange_price', 0 );
		$row->ordering = '99';

		//$post=JRequest::get('post');

		if ( !$row->save() )
		{
			$messagetype = 'notice';
			$message = JText::_( 'K2STORE_SAVE_FAILED' )." - ".$row->getError();
		}

		$redirect = "index.php?option=com_k2store&view=products&task=setpricerange&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $message, $messagetype );

	}


	function setpricerange()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productprices');
		$ns = 'com_k2store.productprices';

		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$id = $app->input->getInt('id', 0);
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
		$row = JTable::getInstance('K2Item','Table');
		$row->load($id);

		$items = $model->getData();
		$total		=  $model->getTotal();
		$pagination =  $model->getPagination();

		$view   = $this->getView( 'productprices', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_k2store&view=products&task=setpricerange&tmpl=component&id=".$id);
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $items );
		$view->assign( 'total', $total );
		$view->assign( 'lists', $lists );
		$view->assign( 'pagination', $pagination );
		$view->assign( 'product_id', $id );
		$view->setLayout( 'default' );
		$view->display();
	}


	function savepricerange()
	{
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';
		$app = JFactory::getApplication();
		$model = $this->getModel('productprices');
		$row = $model->getTable();

		$id = $app->input->getInt('id', 0);
		$cids = $app->input->post->get('cid', array(0), 'ARRAY');
		$quantity_start = $app->input->post->get('quantity_start', array(0), 'ARRAY');
		$condition = $app->input->post->get('condition', array(0), 'ARRAY');
		$price = $app->input->post->get('price', array(0), 'ARRAY');
		$pricetype = $app->input->post->get('pricetype', array(0), 'ARRAY');
		$ordering = $app->input->post->get('ordering', array(0), 'ARRAY');

		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->quantity_start = $quantity_start[$cid];
			$row->condition = $condition[$cid];
			$row->price = (float) $price[$cid];
			$row->pricetype = $pricetype[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_k2store&view=products&task=setpricerange&id={$id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	function deletepricerange()
	{
		$app = JFactory::getApplication();
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$product_id = $app->input->getInt('product_id');
		if (!isset($this->redirect)) {
			$this->redirect = JRequest::getVar( 'return' )
			? base64_decode( JRequest::getVar( 'return' ) )
			: 'index.php?option=com_k2store&view=products&task=setpricerange&id='.$product_id.'&tmpl=component';
			$this->redirect = JRoute::_( $this->redirect, false );
		}

		$model = $this->getModel('productprices');
		$row = $model->getTable();

		$cids = JRequest::getVar('cid', array (0), 'request', 'array');
		foreach (@$cids as $cid)
		{
			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('K2STORE_ITEMS_DELETED');
		}

		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );
	}

	function setquantities() {

		$app = JFactory::getApplication();
		$product_id = $app->input->getInt('product_id');
		$model = $this->getModel('productquantities');
		$model->setState('filter_productid', $product_id);
		$items = $model->getAll();

		$row = K2StoreItem::_getK2Item($product_id);
		K2StoreItem::doProductQuantitiesReconciliation( $row->id);
		$ns = 'com_k2store.productquantities.setquantities';

		$filter_order		= $app->getUserStateFromRequest( $ns .'filter_order',		'filter_order',		'tbl.productquantity_id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns .'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$model->setState('direction', $filter_order_Dir);
		$model->setState('order', $filter_order);

		$view   = $this->getView( 'productquantities', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'productquantities' );
		$view->set( '_action', "index.php?option=com_k2store&view=products&task=setquantities&product_id={$row->id}&tmpl=component" );
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $model->getList() );

		$view->setLayout( 'setquantities' );
		$view->display();
	}


	/**
	 * Saves the quantities for all product attributes in list
	 *
	 * @return unknown_type
	 */
	function savequantities()
	{
		$error = false;
		$app = JFactory::getApplication();
		$this->messagetype  = '';
		$this->message      = '';
		$model = $this->getModel('productquantities');
		$row = $model->getTable();

		$cids = $app->input->get('cid', array(0), 'array');
		$quantities = $app->input->get('quantity', array(0), 'array');

		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->quantity = $quantities[$cid];

			if (!$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('K2STORE_ERROR') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('K2STORE_ALL_CHANGES_SAVED');
		}

		$redirect = "index.php?option=com_k2store&view=products&task=setquantities&product_id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}



}
