<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_SITE . '/components/com_finder/helpers/html');

JHtml::_('jquery.framework');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('bootstrap.tooltip');

// Load the smart search component language file.
$lang = JFactory::getLanguage();
$lang->load('com_finder', JPATH_SITE);

$suffix = $params->get('moduleclass_sfx');
$output = '<input type="text" name="q" id="mod-finder-searchword" class="search-query input-medium" size="'
	. $params->get('field_size', 20) . '" value="' . htmlspecialchars(JFactory::getApplication()->input->get('q', '', 'string')) . '"'
	. ' placeholder="' . JText::_('MOD_FINDER_SEARCH_VALUE') . '"/>';

$showLabel  = $params->get('show_label', 1);
$labelClass = (!$showLabel ? 'element-invisible ' : '') . 'finder' . $suffix;

if ($params->get('show_button'))
{
	$button = '<button class="btn ' . $suffix . ' finder' . $suffix . '" type="submit" title="' . JText::_('MOD_FINDER_SEARCH_BUTTON') . '"><i class="fa fa-search icon-white"></i></button>';

	switch ($params->get('button_pos', 'left'))
	{
		case 'top' :
			$output = $button . '<br />' . $output;
			break;

		case 'bottom' :
			$output .= '<br />' . $button;
			break;

		case 'right' :
			$output .= $button;
			break;

		case 'left' :
		default :
			$output = $button . $output;
			break;
	}
}

JHtml::_('stylesheet', 'com_finder/finder.css', false, true, false);

$script = "
jQuery(document).ready(function() {
	var value, searchword = jQuery('#mod-finder-searchword');
		
		// Begin: Show hide mod-finder-searchform 
		var ua = navigator.userAgent;
		event = (ua.match(/iPad/i)) ? 'touchstart' : 'click';
		jQuery('.btn-finder').bind(event, function() {
			jQuery('#top1').animate({opacity:'0'});
			jQuery('#top2').animate({opacity:'0'});
			jQuery('#yt_mainmenu').animate({opacity:'0'});
			jQuery(this).animate({opacity:'0'},function(){
				jQuery('.dropdown-search').animate({top:'0px'},300);
			  });
		});
		jQuery('.mod-finder-close').bind(event, function() {
			jQuery('.dropdown-search').animate({top:'-100%'},200,function(){
				jQuery('.btn-finder').animate({opacity:'1'},200);
				jQuery('#top1').animate({opacity:'1'},200);
				jQuery('#top2').animate({opacity:'1'},200);
				jQuery('#yt_mainmenu').animate({opacity:'1'},200);
			  });
		});
		
		/*jQuery('.mod-finder-close').bind(event, function() {
		
			jQuery('.dropdown-search').animate({top:-100%},300,function(){
				jQuery('.btn-finder').animate({opacity:'1'},850);
			});
		});*/
		
		// Get the current value.
		value = searchword.val();

		// If the current value equals the default value, clear it.
		searchword.on('focus', function ()
		{
			var el = jQuery(this);

			if (el.val() === '" . JText::_('MOD_FINDER_SEARCH_VALUE', true) . "')
			{
				el.val('');
			}
		});

		// If the current value is empty, set the previous value.
		searchword.on('blur', function ()
		{
			var el = jQuery(this);

			if (!el.val())
			{
				el.val(value);
			}
		});

		jQuery('#mod-finder-searchform').on('submit', function (e)
		{
			e.stopPropagation();
			var advanced = jQuery('#mod-finder-advanced');

			// Disable select boxes with no value selected.
			if (advanced.length)
			{
				advanced.find('select').each(function (index, el)
				{
					var el = jQuery(el);

					if (!el.val())
					{
						el.attr('disabled', 'disabled');
					}
				});
			}
		});";
/*
 * This segment of code sets up the autocompleter.
 */
if ($params->get('show_autosuggest', 1))
{
	JHtml::_('script', 'media/jui/js/jquery.autocomplete.min.js', false, false, false, false, true);

	$script .= "
	var suggest = jQuery('#mod-finder-searchword').autocomplete({
		serviceUrl: '" . JRoute::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component') . "',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});";
}

$script .= "});";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<div class="dropdown <?php echo $suffix; ?>">
  	<button type="button" class="btn-finder" ><i class="fa fa-search" aria-hidden="true"></i></button>
	<div class="dropdown-search" >
		<form id="mod-finder-searchform" action="<?php echo JRoute::_($route); ?>" method="get" class="form-search">
			<div class="finder<?php echo $suffix; ?>">
				<div class="button mod-finder-close "><i class='fa fa-times button'></i></div>
				<?php
				// Show the form fields.
				echo $output;
				?>

				<?php $show_advanced = $params->get('show_advanced'); ?>
				<?php if ($show_advanced == 2) : ?>
					<br />
					<a href="<?php echo JRoute::_($route); ?>"><?php echo JText::_('COM_FINDER_ADVANCED_SEARCH'); ?></a>
				<?php elseif ($show_advanced == 1) : ?>
					<div id="mod-finder-advanced">
						<?php echo JHtml::_('filter.select', $query, $params); ?>
					</div>
				<?php endif; ?>
				<?php echo modFinderHelper::getGetFields($route, (int) $params->get('set_itemid')); ?>
			</div>
		</form>
	</div>
</div>