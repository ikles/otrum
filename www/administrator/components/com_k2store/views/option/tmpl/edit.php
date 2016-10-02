<?php
/*------------------------------------------------------------------------
# com_k2store - K2Store
# ------------------------------------------------------------------------
# author    Ramesh Elamathi - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
require_once (JPATH_ADMINISTRATOR.'/components/com_k2store/version.php');
 $action = JRoute::_('index.php?option=com_k2store&view=options');
 $document = JFactory::getDocument();
 $document->addScript(JUri::root(true).'/media/k2store/js/k2store.namespace.js');
 $document->addScriptDeclaration("
 Joomla.submitbutton = function(pressbutton){
 		if (pressbutton == 'cancel') {
 		submitform( pressbutton );
 		return;
 		}
 		if (K2Store.trim(K2Store('#option_unique_name').val()) == '') {
 			alert( '".JText::_('K2STORE_OPTION_UNIQUE_NAME_MUST_HAVE_A_TITLE', true)."' );
 		} else if(K2Store.trim(K2Store('#option_name').val()) == '') {
 			alert( '".JText::_('K2STORE_OPTION_NAME_MUST_HAVE_A_TITLE', true)."' );
 		}else {
 		submitform( pressbutton );

 }
 }
 ");

?>
<div class="k2store">
<form action="index.php?option=com_k2store&view=options" method="post" name="adminForm" id="adminForm">
<fieldset>
	<legend><?php echo JText::_('K2STORE_OPTION_DETAILS'); ?> </legend>

	<table class="admintable">
			<tr>
			<td width="100" align="right" class="key">
				<label for="option_unique_name">
					<?php echo JText::_( 'K2STORE_OPTION_UNIQUE_NAME' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="option_unique_name" id="option_unique_name" class="required" value="<?php echo $this->data->option_unique_name;?>" />
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
				<label for="option_name">
					<?php echo JText::_( 'K2STORE_OPTION_DISPLAY_NAME' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="option_name" id="option_name" class="required" value="<?php echo $this->data->option_name;?>" />
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
				<label for="type">
					<?php echo JText::_( 'K2STORE_OPTION_TYPE' ); ?>:
				</label>
			</td>
			<td>
				<select name="type" id="option-type">
                <optgroup label="<?php echo JText::_( 'K2STORE_OPTION_OPTGROUP_LABEL_CHOOSE' ); ?>">
                                <option <?php echo ($this->data->type=='select')? 'selected="selected"':''?> value="select"><?php echo JText::_( 'K2STORE_OPTION_DROPDOWN' ); ?></option>
                               <option  <?php echo ($this->data->type=='radio')? 'selected="selected"':''?> value="radio"><?php echo JText::_( 'K2STORE_RADIO' ); ?></option>
                               <?php if(K2STORE_PRO == 1):?>
                               	<option  <?php echo ($this->data->type=='checkbox')? 'selected="selected"':''?> value="checkbox"><?php echo JText::_( 'K2STORE_CHECKBOX' ); ?></option>
                               <?php endif; ?>
                   </optgroup>
                   <?php if(K2STORE_PRO == 1):?>
                		<optgroup label="<?php echo JText::_( 'K2STORE_OPTION_OPTGROUP_LABEL_INPUT' ); ?>">
                                <option  <?php echo ($this->data->type=='text')? 'selected="selected"':''?> value="text"><?php echo JText::_( 'K2STORE_TEXT' ); ?></option>
                   				<option  <?php echo ($this->data->type=='textarea')? 'selected="selected"':''?> value="textarea"><?php echo JText::_( 'K2STORE_TEXTAREA' ); ?></option>
                     	</optgroup>

                <optgroup label="<?php echo JText::_( 'K2STORE_OPTION_OPTGROUP_LABEL_DATE' ); ?>">
                                <option  <?php echo ($this->data->type=='date')? 'selected="selected"':''?> value="date"><?php echo JText::_( 'K2STORE_DATE' ); ?></option>
                				<option  <?php echo ($this->data->type=='time')? 'selected="selected"':''?> value="time"><?php echo JText::_( 'K2STORE_TIME' ); ?></option>
                                <option <?php echo ($this->data->type=='datetime')? 'selected="selected"':''?> value="datetime"><?php echo JText::_( 'K2STORE_DATETIME' ); ?></option>
				  </optgroup>
				  <?php endif; ?>
              </select>
			</td>
		</tr>

		<tr>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'K2STORE_OPTION_STATE' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>

</fieldset>
<fieldset id="option-value">
	<legend><h3><?php echo JText::_('K2STORE_OV_ADD_NEW_OPTION_VALUES');?></h3></legend>

	<table class="list table table-bordered table-stripped">

          <thead>
            <tr>
              <td class="left"><span class="required">*</span> <?php echo JText::_('K2STORE_OPTION_VALUE_NAME'); ?></td>
              <td class="right"><?php echo JText::_('JGRID_HEADING_ORDERING'); ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $option_value_row = 0; ?>
          <?php foreach ($this->data->optionvalues as $option_value) { ?>
          <tbody id="option-value-row<?php echo $option_value_row; ?>">
            <tr>
              <td class="left"><input type="hidden" name="option_value[<?php echo $option_value_row; ?>][optionvalue_id]" value="<?php echo $option_value->optionvalue_id; ?>" />

                <input type="text" name="option_value[<?php echo $option_value_row; ?>][optionvalue_name]" value="<?php echo isset($option_value->optionvalue_name) ? $this->escape($option_value->optionvalue_name): ''; ?>" />
                <br />
               </td>
              <td class="right"><input type="text" name="option_value[<?php echo $option_value_row; ?>][ordering]" value="<?php echo $option_value->ordering; ?>" size="1" /></td>
              <td class="left"><a onclick="DeleteOptionValue(<?php echo $option_value->optionvalue_id; ?>,<?php echo $option_value_row; ?>)" class="button"><?php echo JText::_('K2STORE_REMOVE'); ?></a></td>
            </tr>
          </tbody>
          <?php $option_value_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="2"></td>
              <td class="left"><a href="javascript:void(0)" onclick="k2storeAddOptionValue();" class="btn btn-primary"><?php echo JText::_('K2STORE_OPTION_VALUE_ADD'); ?></a></td>
            </tr>
          </tfoot>
        </table>
</fieldset>

    <input type="hidden" name="option" value="com_k2store" />
	<input type="hidden" name="view" value="options" />
	<input type="hidden" name="option_id" value="<?php echo $this->data->option_id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->data->option_id; ?>" />
	<input type="hidden" name="task" id="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>

<script type="text/javascript"><!--
(function($) {
$('select[name=\'type\']').bind('change', function() {
	if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox' || this.value == 'image') {
		$('#option-value').show();
	} else {
		$('#option-value').hide();
	}
});
$('select[name=\'type\']').trigger('change');
})(k2store.jQuery);
var option_value_row = <?php echo $option_value_row; ?>;

function k2storeAddOptionValue(){
	(function($) {
	html  = '<tbody id="option-value-row' + option_value_row + '">';
	html += '  <tr>';
    html += '    <td class="left"><input type="hidden" name="option_value[' + option_value_row + '][optionvalue_id]" value="" />';
	html += '<input type="text" name="option_value[' + option_value_row + '][optionvalue_name]" value="" /> <br />';
	html += '    </td>';
	html += '    <td class="right"><input type="text" name="option_value[' + option_value_row + '][ordering]" value="" size="1" /></td>';
	html += '    <td class="left"><a onclick="k2store.jQuery(\'#option-value-row' + option_value_row + '\').remove();" class="button"><?php echo JText::_('K2STORE_REMOVE'); ?></a></td>';
	html += '  </tr>';
    html += '</tbody>';

	$('#option-value tfoot').before(html);

	option_value_row++;
	})(k2store.jQuery);
}

function DeleteOptionValue(optionvalue_id , option_value_row){
	(function($) {
				$.ajax({
				url :'index.php?option=com_k2store&view=options&task=deleteoptionvalue&optionvalue_id='+optionvalue_id,
				type: 'post',
				success: function(response){
					if(response){
						$("#system-message-container").html(
								'<div class="alert alert-success">'+
								'<h4 class="alert-heading">'+ response + '</h4>'+
								'</div>'
								);

						$('#option-value-row'+option_value_row).remove();
					}
				}

		});
	})(k2store.jQuery);
}
//--></script>
</div>