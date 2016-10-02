<?php
/*------------------------------------------------------------------------
 # com_k2store - K2Store
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$action = JRoute::_('index.php?option=com_k2store&view=taxprofile');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
?>
<div class="k2store">
<div class="k2storehelp alert alert-info">
	<?php echo JText::_('K2STORE_TAXPROFILES_HELP_TEXT');?>
</div>

<form action="<?php echo $action; ?>" method="post" name="adminForm"
	id="adminForm" class="form-validate">

	<div id="taxprofile_edit">
		<fieldset class="fieldset">
			<legend>
				<?php echo JText::_('K2STORE_TAXPROFILE'); ?>
			</legend>
			<table>
				<tr>
					<td><?php echo $this->form->getLabel('taxprofile_name'); ?>
					</td>
					<td><?php echo $this->form->getInput('taxprofile_name'); ?> <?php echo $this->form->getInput('taxprofile_id'); ?>
					</td>
				</tr>
				<tr>
					<td><?php echo $this->form->getLabel('state'); ?>
					</td>
					<td><?php echo $this->form->getInput('state'); ?>
					</td>
				</tr>

			</table>


			<fieldset>
				<legend>
					<?php echo JText::_('K2STORE_TAXPROFILE_TAXRATES'); ?>
					<small><?php echo JText::_('K2STORE_TAXPROFILE_TAXRATE_MAP_HELP'); ?></small>
				</legend>
			</fieldset>


	</div>

	<table id="taxprofile_rule_table" class="table table-stripped table-bordered">
	<h4><?php echo JText::_('K2STORE_GEOZONE_COUNTRIES_AND_ZONES');?></h4>
		<thead>
			<tr>
				<th><?php echo JText::_('K2STORE_NUM'); ?>
				</th>
				<th colspan="1"><?php echo JText::_('K2STORE_TAXPROFILE_TAXRATE'); ?>
					</th>
			<th><?php echo JText::_('K2STORE_TAXPROFILE_ADDRESS'); ?>
			</th>
							<th></th>
						</tr>
		</thead>
		<?php $taxrule_row = 0;
			$tax_address=array();
		?>
		 <?php foreach ($this->taxrules as  $i => $taxrule):
		 		$tax_address [$taxrule_row] =$taxrule->address;
		 ?>
          <tbody id="tax-to-taxrule-row<?php echo $taxrule_row; ?>">
            <tr>
				<td><?php echo $i+1; ?>
				</td>
					<td>
					<?php
					$option =array();
					$option [] = JHtml::_('select.option',0,JText::_('K2STORE_SELECT_OPTION'));
						foreach($this->lists['taxrate'] as $taxrate){
						$option [] = JHtml::_('select.option',$taxrate->taxrate_id,$taxrate->taxrate_name.'&nbsp;'.floatval($taxrate->tax_percent).'%');
						}
						echo JHTML::_('select.genericlist', $option, 'tax-to-taxrule-row['.$taxrule_row.'][taxrate_id]',null, 'value', 'text',$taxrule->taxrate_id);
					?>
				 </td>
				  <td>
					<?php
						//$address_array= $this->model->checkAddressExist($this->item->taxprofile_id,$taxrule->taxrate_id,$tax_address [$taxrule_row]);
						$address_options=array();
						$address_options[''] =JText::_('K2STORE_SELECT_ADDRESS');
						$address_options['shipping'] =JText::_('K2STORE_SHIPPING_ADDRESS');
						$address_options['billing']=JText::_('K2STORE_BILLING_ADDRESS');
						$address_options['store'] = JText::_('K2STORE_STORE_ADDRESS');
						echo JHTML::_('select.genericlist', $address_options, 'tax-to-taxrule-row['.$taxrule_row.'][address]',null, 'value', 'text',$taxrule->address);
					?>
                </td>
              	<input type="hidden" name ="tax-to-taxrule-row[<?php echo $taxrule_row; ?>][taxrule_id]"value="<?php echo $taxrule->id; ?>"/>
              	<td class="left"><a onclick="k2storeRemoveTax(<?php echo $taxrule->id; ?>, <?php echo $taxrule_row; ?>);" class="button"><?php echo JText::_('K2STORE_REMOVE'); ?></a></td>
            </tr>
          </tbody>
          <?php $taxrule_row++; ?>
          <?php endforeach; ?>
		<tfoot>
            <tr>
            <td colspan="3"></td>
              <td><a class="btn btn-primary" onclick="k2storeAddTaxProfile();" class="button"><?php echo JText::_('K2STORE_ADD_TAXPROFILE'); ?></a></td>
            </tr>
          </tfoot>
	</table>


	</div>
	<input type="hidden" name="option" value="com_k2store">

	 <input type="hidden" name="taxprofile_id"	value="<?php echo $this->item->taxprofile_id; ?>">
	 <input type="hidden"	name="task" value="">
	<?php echo JHTML::_( 'form.token' ); ?>

</div>
</form>



<script type="text/javascript"><!--

var geozone_name;
Joomla.submitbutton=function (task){


	if(task == 'taxprofile.save' || task =='taxprofile.apply'){

		geozone_name = document.getElementById('jform_taxprofile_name').value;
			if(geozone_name){
				Joomla.submitform(task);
                return true;
			}else{
				alert('<?php echo JText::_('K2STORE_TAXPROFILE_NAME'); ?> <?php echo JText::_('K2STORE_CONF_REQUIRED');?>');
				return false;
			}
	}else if(task == 'taxprofile.cancel'){
			Joomla.submitform(task);
	}

};



var taxrule_row = <?php echo $taxrule_row; ?>;

function k2storeAddTaxProfile() {

	(function($) {
	html  = '<tbody id="tax-to-taxrule-row' + taxrule_row + '">';
	html += '<tr>';
	html +='<td></td>';
	html += '<td class="left"><select name="tax-to-taxrule-row[' + taxrule_row + '][taxrate_id]" id="taxprofile' + taxrule_row + '">';
	<?php
		if($this->lists['taxrate']){
		foreach ($this->lists['taxrate'] as $key=>$value) { ?>
	html += '<option value="<?php echo $value->taxrate_id; ?>"><?php  echo addslashes($value->taxrate_name).'&nbsp;'.floatval($value->tax_percent).'%'; ?></option>';
	<?php }} ?>
	html += '</select></td>';
	html += '<td class="left"><select name="tax-to-taxrule-row[' + taxrule_row + '][address]" id="zone' + taxrule_row + '">';
	<?php  foreach ($this->lists['address'] as $key=>$value) { ?>
	html += '<option value="<?php echo $value->value; ?>"><?php  echo addslashes($value->text); ?></option>';
	<?php } ?>

	html +='</select>';

	html += '<input type="hidden" name="tax-to-taxrule-row['+ taxrule_row + '][taxrule_id]" value="" /></td>';
	html += '<td class="left"><a onclick="k2store.jQuery(\'#tax-to-taxrule-row' + taxrule_row + '\').remove();" class="button"><?php echo JText::_('K2STORE_REMOVE'); ?></a></td>';
	html += '</tr>';
	html += '</tbody>';

	$('#taxprofile_rule_table > tfoot').before(html);

	//$('#zone' + taxrule_row).load('index.php?option=com_k2store&view=geozones&task=geozone.getZone&country_id=' + $('#country' + taxrule_row).attr('value') + '&zone_id=0');
//html += '<td class="left"><select name="zone_to_geo_zone[' + taxrule_row + '][country_id]" id="country' + taxrule_row + '" onchange="k2store.jQuery(\'#zone' + taxrule_row + '\').load(\'index.php?option=com_k2store&view=geozone&task=geozone.getZone&country_id=\' + this.value + \'&zone_id=0\');">';
	taxrule_row++;
	})(k2store.jQuery);

}


function k2storeRemoveTax(rule_id, taxrule_row) {
	(function($) {
		$('.k2storealert').remove();
		$.ajax({
			method:'post',
			url:'index.php?option=com_k2store&view=taxprofile&task=taxprofile.deleteTaxRule',
			data:{'taxrule_id':rule_id},
			dataType:'json'
		}).done(function(response) {
			if(response.success) {
				$('#tax-to-taxrule-row'+taxrule_row).remove();
				$('#taxprofile_rule_table').before('<div class="k2storealert alert alert-block">'+response.success+'</div>');
			} else {
				$('#taxprofile_rule_table').before('<div class="k2storealert alert alert-block">'+response.error+'</div>');
			}

		});
	})(k2store.jQuery);
}



//--></script>

</div>











