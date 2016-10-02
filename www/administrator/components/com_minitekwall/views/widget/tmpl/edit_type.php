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
?>								
									
<form action="<?php echo JRoute::_('index.php?option=com_minitekwall&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="widget-form" class="form-validate">
										
	<div class="span6">
	
		<div class="thumbnail">
			<i class="mwicons mwicons-bricks10"></i>
			<div class="thumbnail-title">
				<span><?php echo JText::_('COM_MINITEKWALL_MASONRY'); ?></span>
			</div>
			<p><?php echo JText::_('COM_MINITEKWALL_MASONRY_DESCRIPTION'); ?></p>
			
			<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectMasonry')">
				<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
			</button>
				
		</div>
	 
	</div>
	
	<?php /*
	<div class="span4">
								
		<div class="thumbnail">
			<i class="mwicons mwicons-web63"></i>
			<div class="thumbnail-title">
				<span><?php echo JText::_('COM_MINITEKWALL_SLIDER'); ?></span>
			</div>
			<p><?php echo JText::_('COM_MINITEKWALL_SLIDER_DESCRIPTION'); ?></p>
			
			<button class="btn btn-info disabled" onclick="return false;">
				<?php echo JText::_('COM_MINITEKWALL_UNDER_CONSTRUCTION'); ?>
			</button>
			
		</div>
		
	</div>
	*/ ?>
	
	<div class="span6">
								
		<div class="thumbnail">
			<i class="mwicons mwicons-three168"></i>
			<div class="thumbnail-title">
				<span><?php echo JText::_('COM_MINITEKWALL_SCROLLER'); ?></span>
			</div>
			<p><?php echo JText::_('COM_MINITEKWALL_SCROLLER_DESCRIPTION'); ?></p>
			
			<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectScroller')">
				<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
			</button>
			
		</div>
		
	</div>
							
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
</form>