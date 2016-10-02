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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JLoader::register('K2Parameter', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2parameter.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_k2store/library/prices.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_k2store/library/popup.php');
$listOrder	= $this->lists['order'];
$listDirn	= $this->lists['order_Dir'];
$saveOrder	= $listOrder == 'p.ordering';
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		console.log(task);
		if (task == 'add')
		{
			window.open('index.php?option=com_k2&view=item&task=add');
		//	Joomla.submitform(task, document.getElementById('item-form'));
		}
		if (task == 'edit')
		{
			window.open('index.php?option=com_k2&view=item&task=edit');
		//	Joomla.submitform(task, document.getElementById('item-form'));
		}

		if (task == 'saveAll')
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}


	/* Method to Enable single Product using
	 * params html
	 * return value of the html
	 */

	Joomla.enableMe=function(meHtml)
	{
		if(meHtml.checked==true)
		{
			meHtml.value=1;
			console.log(meHtml.value);
		}else{
			meHtml.value=0;
			console.log(meHtml.value);
			}
	}

Joomla.enableAll=function(value)
{
	var master = document.getElementById('enableCheckAll').checked;
	var CheckBoxes = document.getElementsByClassName('enableAll');
	if(master==true){
		for (var i = 0; i < CheckBoxes.length; i++) {
		    CheckBoxes[i].checked = true;
		    CheckBoxes[i].value=1;
		}
	}else if(master==false)
	{
		for (var i = 0; i < CheckBoxes.length; i++) {
		    CheckBoxes[i].checked = false;
		    CheckBoxes[i].value=0;
		}
	}
}


</script>
<?php
$this->lists['search'];
?>
<div class="k2store">
	<div class="alert alert-block alert-info">
	<strong>
		<?php echo JText::_('K2STORE_PRODUCTS_HELP_TEXT'); ?>
	</strong>
	</div>

	<form action="index.php?option=com_k2store&view=products" method="post"
	name="adminForm" id="adminForm">

	 	<input type="hidden" name="task" value="" />
	 	<input type="hidden" name="option" value="com_k2store" />
	 	<input type="hidden" name="view" value="products" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	<div class="row-fluid">
	<table class="adminlist table span9">
		<tr>
			<td width="2px">
				<?php echo JText::_( 'K2STORE_FILTER_SEARCH' ); ?>
			</td>
			<td width="1px">
				<input type="text" name="search" id="search" value="<?php echo  htmlspecialchars($this->lists['search']);?>"
					class="text_area" onchange="document.adminForm.submit();" />
			</td>
			<td>
				<button class="btn btn-success" onclick="this.form.submit();"><?php echo JText::_( 'K2STORE_FILTER_GO' ); ?></button>
			</td>
			<td>
				<button class="btn btn-inverse" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'K2STORE_FILTER_RESET' ); ?></button>
			</td>
			<td><?php echo $this->category_options; ?></td>
			<td colspan="3">
				<?php  echo $this->page->getLimitBox();?>
			</td>

		</tr>
	</table>
	<div class="span3">
		<input type="button" class="btn btn-large btn-success" onclick="Joomla.submitbutton('saveAll')" value="<?php echo JText::_('K2STORE_PRODUCTS_SAVE_ALL')?>" />

	</div>
	</div>
	<div id="j-main-container">

	<h3><?php echo JText::_('K2STORE_PRODUCTS');?></h3>
	<table id="productsList" class="adminlist table table-striped table-bordered">
			<!-- <table class="adminlist table table-striped table-bordered"> -->
			<thead>
				<th width="1px">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />

				</th>

				<th>
					<?php echo JHtml::_('grid.sort',  'K2STORE_PRODUCT_ID', 'p.id', $this->lists['order_Dir'],$this->lists['order']);?>
				</th>

				<th>
					<?php echo JHtml::_('grid.sort','K2STORE_SKU', 'price.item_sku', $this->lists['order_Dir'],$this->lists['order']);?>
				</th>

				<th>
				<?php echo JHtml::_('grid.sort',  'K2STORE_PRODUCT_NAME', 'p.title', $this->lists['order_Dir'],$this->lists['order']);?>
				</th>

				<th>
					<?php // echo JHtml::_('grid.sort','K2STORE_PRODUCT_PRICE', 'price.item_price', $this->lists['order_Dir'],$this->lists['order']);?>
					<?php echo JText::_('K2STORE_PRODUCT_PRICE');?>
				</th>

				<th>
					<?php echo JText::_('K2STORE_PRODUCT_SPECIAL_PRICE');?>
				</th>
				<?php if($this->params->get('enable_inventory', 0)):?>
				<th>
					<?php echo JHtml::_('grid.sort','K2STORE_PRODUCT_QUANTITY', 'qty.quantity', $this->lists['order_Dir'],$this->lists['order']);?>
				</th>
				<?php endif; ?>
				<th>
					<input type="checkbox" id="enableCheckAll" name="enableall-toggle"  title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.enableAll(this.checked)" />
					<?php //echo JHtml::_('grid.sort','K2STORE_PRODUCT_ENABLE_CART', 'qty.quantity', $this->lists['order_Dir'],$this->lists['order']);?>
					<?php echo JText::_('K2STORE_PRODUCT_ENABLE_CART');?>
				 </th>
			</thead>
			<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php $i = 0; $pluginName = 'k2store'; $plugin = JPluginHelper::getPlugin('k2', $pluginName); ?>
			<?php foreach ($this->items as $product):
				//legacy compatibility
				if(!isset($product->product_id)) {
				unset($plugins);
				$plugins = new K2Parameter($product->plugins, '', $pluginName);
				$product->item_enabled = $plugins->get('item_enabled');
				$product->item_sku = $plugins->get('item_sku');
				$product->item_price = $plugins->get('item_price');
				$product->special_price = $plugins->get('special_price');
				//check if we have them stored in the plugins field.

				}

			?>

				<tr class="row<?php echo $i%2;?>">
					<td><?php echo JHtml::_('grid.id',$i,$product->id); ?> </td>
					<td>
						<?php echo $product->id;?>
						<input type="hidden" name="product[<?php echo $product->id?>][product_id]" value="<?php echo $product->id; ?>" />
						<input type="hidden" name="product[<?php echo $product->id?>][p_id]" value="<?php echo $product->p_id; ?>" />
						<input type="hidden" name="product[<?php echo $product->id?>][manage_stock]" value="<?php echo $product->manage_stock; ?>" />

					</td>
					<td><input type="text" class="input-mini" name="product[<?php echo $product->id;?>][item_sku]" value="<?php echo $product->item_sku;?>" /> </td>
					<td>
						<span  class="editlinktip hasTip" title="<?php echo JText::_('K2STORE_PRODUCT_NAME_TOOL_TIP_EDIT_DETAILS');?>">

							<a style="text-decoration:none;" href="<?php echo JRoute::_('index.php?option=com_k2&view=item&task=edit&cid='.$product->id);?>" onClick="window.open(this.href);return false;">
								<!-- 	<a href="<?php echo JRoute::_('index.php?option=com_k2&view=item&task=edit&cid='.$product->id);?>" targert="_blank" > -->
								<?php  echo $product->title?>
							</a>
						</span>
					</td>
					<td>
						<div class="input-prepend">
							<span class="add-on">
							<?php
								$currency=K2StoreFactory::getCurrencyObject();
								echo $currency->getSymbol();
							 ?></span>
							<input type="text" class="input-mini" name="product[<?php echo $product->id;?>][item_price]" value="<?php echo $product->item_price;?>" />
						</div>
					</td>

					<td>
						<div class="input-prepend">
							<span class="add-on">
							<?php
								$currency=K2StoreFactory::getCurrencyObject();
								echo $currency->getSymbol();
							 ?></span>
						<input type="text" class="input-mini" name="product[<?php echo $product->id;?>][special_price]" value="<?php echo $product->special_price;?>" /> </td>
					</div>
					<?php if($this->params->get('enable_inventory', 0)):?>

					<td>
						<?php if($product->manage_stock): ?>
							<?php echo K2StorePopup::popup("index.php?option=com_k2store&view=products&task=setquantities&tmpl=component&product_id={$product->id}", JText::_('K2STORE_SET_QUANTITY'), array(0)); ?>
						<?php else: ?>
							<small>
							<?php echo JText::_('K2STORE_PRODUCTS_STOCK_DISABLED'); ?>
							</small>
						<?php endif; ?>
					</td>
					<?php endif; ?>
					<td>
						<input onclick="Joomla.enableMe(this)" class="enableAll"
							value="<?php echo $product->item_enabled?>"
						    type="checkbox" name="product[<?php echo $product->id?>][item_enabled]"
						  	id="item_enabled<?php echo $product->id?>"  <?php echo ($product->item_enabled) ? "checked" : ""  ; ?>/>

					</td>
				</tr>

			<?php $i++; ?>
			<?php  endforeach;?>
			</tbody>
			</table>


	</form>
</div>