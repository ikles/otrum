<?php
/**
* @title		    Minitek Wall
* @version   		3.x
* @copyright   		Copyright (C) 2011-2015 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/
defined('_JEXEC') or die();

$vm = JPATH_ROOT.DS.'components'.DS.'com_virtuemart';	
if (file_exists($vm.DS.'virtuemart.php')) 
{
	
jimport('joomla.form.formfield');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

if (!class_exists('ShopFunctions'))
require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('VirtueMartModelManufacturer'))
JLoader::import('manufacturer', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models');


if(!class_exists('TableManufacturers')) require(VMPATH_ADMIN.DS.'tables'.DS.'manufacturers.php');
if (!class_exists( 'VirtueMartModelManufacturer' ))
JLoader::import( 'manufacturer', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );

/**
 * Supports a modal Manufacturer picker.
 *
 *
 */
 
class JFormFieldManufacturer extends JFormField
{

    var $type = 'manufacturer';

	function getInput()
    {
    	return self::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
    }
	
	function fetchElement($name, $value, &$node, $control_name)
    {		
		$doc = JFactory::getDocument();
		$js = "
		    jQuery.noConflict();
			jQuery(document).ready(function(){
				
				jQuery('#jform_virtuemart_source_vmp_manufacturerfilter0').click(function(){
					jQuery('#jform_virtuemart_source_vmp_manufacturers').attr('disabled', 'disabled');
					jQuery('#jform_virtuemart_source_vmp_manufacturers option').each(function() {
						jQuery(this).attr('selected', 'selected');
					});
					jQuery('#jform_virtuemart_source_vmp_manufacturers').trigger('liszt:updated');
				});
				
				jQuery('#jform_virtuemart_source_vmp_manufacturerfilter1').click(function(){
					jQuery('#jform_virtuemart_source_vmp_manufacturers').removeAttr('disabled');
					jQuery('#jform_virtuemart_source_vmp_manufacturers option').each(function() {
						jQuery(this).removeAttr('selected');
					});
					jQuery('#jform_virtuemart_source_vmp_manufacturers').trigger('liszt:updated');
				});
				
				if (jQuery('#jform_virtuemart_source_vmp_manufacturerfilter0').attr('checked')) {
					jQuery('#jform_virtuemart_source_vmp_manufacturers').attr('disabled', 'disabled');
					jQuery('#jform_virtuemart_source_vmp_manufacturers option').each(function() {
						jQuery(this).attr('selected', 'selected');
					});
					jQuery('#jform_virtuemart_source_vmp_manufacturers').trigger('liszt:updated');
				}
				
				if (jQuery('#jform_virtuemart_source_vmp_manufacturerfilter1').attr('checked')) {
					jQuery('#jform_virtuemart_source_vmp_manufacturers').removeAttr('disabled');
					jQuery('#jform_virtuemart_source_vmp_manufacturers').trigger('liszt:updated');
				}
				
			});
		";
		$doc->addScriptDeclaration($js);
	
		$key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
		$val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
		$model = VmModel::getModel('Manufacturer');
		$manufacturers = $model->getManufacturers(true, true, false);
		//$emptyOption = JHtml::_ ('select.option', '', vmText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION'), 'virtuemart_manufacturer_id', 'mf_name');
		//array_unshift ($manufacturers, $emptyOption);
		return JHtml::_('select.genericlist', $manufacturers, $this->name, 'class="inputbox"  size="1" multiple="multiple"', 'virtuemart_manufacturer_id', 'mf_name', $this->value, $this->id);
    }

}

}