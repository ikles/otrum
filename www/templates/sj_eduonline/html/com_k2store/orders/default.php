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
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/library/prices.php');
//print_r($this->state);
//print_r($this->pagination);
?>
<div class="k2store">
<h3><?php echo JText::_('K2STORE_ORDER_HISTORY'); ?></h3>

<form action="<?php echo JRoute::_('index.php')?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">


	<table class="userTable table table-striped table-bordered table-hover">
	<thead>
				<tr class="jorder_rowhead">
					<th width="1%">
						<?php echo JText::_('K2STORE_NO'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('K2STORE_ORDER_DATE'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('K2STORE_INVOICE_NO'); ?>
					</th>
					<!--
					<th width="15%">
						<?php echo JText::_('K2STORE_ORDER_ID'); ?>
					</th>
					 -->
					<th width="10%">
						<?php echo JText::_('K2STORE_ORDER_TOTAL'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('K2STORE_ORDER_STATUS'); ?>
					</th>

				</tr>
			</thead>

			<tfoot>
				<tr>
					<td colspan="6" class="jorder_row">
							<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
							<div class="pagination pagination-toolbar" ><?php echo $this->pagination->getPagesLinks(); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>

	<?php
			$k = 0;
		for($i=0; $i<count($this->orders); $i++) {
			$row = $this->orders[$i];
			if(isset($row->invoice_number) && $row->invoice_number > 0) {
				$invoice_number = $row->invoice_prefix.$row->invoice_number;
			}else {
				$invoice_number = $row->id;
			}
			$link = JRoute::_('index.php?option=com_k2store&view=orders&task=view&id='.$row->id);
		?>

	<tr class="k2store_order_<?php echo "row$k"; ?>">
					<td>
					<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td>
					<?php echo JHTML::_('date', $row->created_date, 'd-m-Y'); ?>
					</td>
					<td>
					<a href='<?php echo $link; ?>'>
					<?php echo $invoice_number; ?>
					</a>
					</td>
					<!--
					<td>
					<a href='<?php echo $link; ?>'> <?php echo $row->order_id; ?></a>
					</td>
					 -->
					<td>
					<?php echo K2StorePrices::number( $row->orderpayment_amount, $row->currency_code, $row->currency_value ); ?>
					</td>
					<td>
						<?php
							if($row->order_state_id == 1) {
								$label_class='success';
							} elseif($row->order_state_id == 3 || $row->order_state_id == 5 || $row->order_state_id == '') {
								$label_class='important';
							} elseif($row->order_state_id == 4) {
								$label_class='warning';
							 }
						?>

						<span class="label label-<?php echo $label_class;?> order-state-label">
							<?php
							if(JString::strlen($row->order_state) > 0) {
								echo JText::_($row->order_state);
							} else {
								echo JText::_('K2STORE_PAYSTATUS_INCOMPLETE');
							}
							?>
						</span>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
		</table>


	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="option" value="com_k2store" />
	<input type="hidden" name="view" value="orders" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$this->state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$this->state->direction; ?>" />
</form>
</div>