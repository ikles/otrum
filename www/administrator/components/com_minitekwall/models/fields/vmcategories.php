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

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

$vm = JPATH_ROOT.DS.'components'.DS.'com_virtuemart';	
if (file_exists($vm.DS.'virtuemart.php')) 
{
	
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

if (!class_exists('ShopFunctions'))
	require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('TableCategories'))
	require(VMPATH_ADMIN . DS . 'tables' . DS . 'categories.php');
jimport('joomla.form.formfield');

/*
 * This element is used by the menu manager
 * Should be that way
 */
class JFormFieldVmcategories extends JFormField {

	var $type = 'vmcategories';

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
				
				jQuery('#jform_virtuemart_source_vmp_catfilter0').click(function(){
					jQuery('#jform_virtuemart_source_vmp_category_id').attr('disabled', 'disabled');
					jQuery('#jform_virtuemart_source_vmp_category_id option').each(function() {
						jQuery(this).attr('selected', 'selected');
					});
					jQuery('#jform_virtuemart_source_vmp_category_id').trigger('liszt:updated');
				});
				
				jQuery('#jform_virtuemart_source_vmp_catfilter1').click(function(){
					jQuery('#jform_virtuemart_source_vmp_category_id').removeAttr('disabled');
					jQuery('#jform_virtuemart_source_vmp_category_id option').each(function() {
						jQuery(this).removeAttr('selected');
					});
					jQuery('#jform_virtuemart_source_vmp_category_id').trigger('liszt:updated');
				});
				
				if (jQuery('#jform_virtuemart_source_vmp_catfilter0').attr('checked')) {
					jQuery('#jform_virtuemart_source_vmp_category_id').attr('disabled', 'disabled');
					jQuery('#jform_virtuemart_source_vmp_category_id option').each(function() {
						jQuery(this).attr('selected', 'selected');
					});
					jQuery('#jform_virtuemart_source_vmp_category_id').trigger('liszt:updated');
				}
				
				if (jQuery('#jform_virtuemart_source_vmp_catfilter1').attr('checked')) {
					jQuery('#jform_virtuemart_source_vmp_category_id').removeAttr('disabled');
					jQuery('#jform_virtuemart_source_vmp_category_id').trigger('liszt:updated');
				}
				
			});
		";
		$doc->addScriptDeclaration($js);
		
		if (!$value)
		{
			$value = array();
		}
		
		$app = JFactory::getApplication ();
		$isSite = $app->isSite();
		$vendor = VmConfig::isSuperVendor();
		$vmlang = VmConfig::setdbLanguageTag();
		$category_tree = ShopFunctions::categoryListTree($value, $cid = 0, $level = 0, $disabledFields = array(), $isSite, $vendor, $vmlang);
		
		$html = '<select class="inputbox" id="' . $this->id . '" name="' . $this->name . '" multiple="multiple"  size="10">';
		$html .= '<option value="">'.vmText::_('COM_MINITEKWALL_FIELD_UNCATEGORIZED').'</option>';
		$html .= $category_tree;
		$html .= '</select>';
		
        return $html;
       
    }

}

}