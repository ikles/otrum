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
					
	<div class="row-fluid">
	
		<div class="span4">
	
			<div class="thumbnail">
				<img src="components/com_minitekwall/assets/images/icon-48-joomla.png" alt="Joomla" />
				<div class="thumbnail-title">
					<span><?php echo JText::_('COM_MINITEKWALL_JOOMLA'); ?></span>
				</div>
				
				<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectSourceJoomla')">
					<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
				</button>
				
			</div>
		 
		</div>
		
		<div class="span4">
									
			<div class="thumbnail">
				<img src="components/com_minitekwall/assets/images/icon-48-k2.png" alt="K2" />
				<div class="thumbnail-title">
					<span><?php echo JText::_('COM_MINITEKWALL_K2'); ?></span>
				</div>
				
				<?php 
				$k2 = JPATH_ROOT.DS.'components'.DS.'com_k2';	
				if (file_exists($k2.DS.'k2.php')) { ?>
					<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectSourceK2')">
						<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
					</button>
				<?php } else { ?>
					<button class="btn btn-info disabled" onclick="return false;">
						<?php echo JText::_('COM_MINITEKWALL_K2_NOT_INSTALLED'); ?>
					</button>
				<?php } ?>
				
			</div>
			
		</div>
		
		<div class="span4">
									
			<div class="thumbnail">
				<img src="components/com_minitekwall/assets/images/icon-48-virtuemart.png" alt="Virtuemart" />
				<div class="thumbnail-title">
					<span><?php echo JText::_('COM_MINITEKWALL_VIRTUEMART'); ?></span>
				</div>
				
				<?php 
				$vm = JPATH_ROOT.DS.'components'.DS.'com_virtuemart';	
				if (file_exists($vm.DS.'virtuemart.php')) { ?>
					<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectSourceVirtuemart')">
						<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
					</button>
				<?php } else { ?>
					<button class="btn btn-info disabled" onclick="return false;">
						<?php echo JText::_('COM_MINITEKWALL_VIRTUEMART_NOT_INSTALLED'); ?>
					</button>
				<?php } ?>
				
			</div>
			
		</div>
	
	</div>

	<div class="row-fluid">
						
		<div class="span4">
	
			<div class="thumbnail">
				<img src="components/com_minitekwall/assets/images/icon-48-jomsocial.png" alt="Jomsocial" />
				<div class="thumbnail-title">
					<span><?php echo JText::_('COM_MINITEKWALL_JOMSOCIAL'); ?></span>
				</div>
				
				<?php 
				$js = JPATH_ROOT.DS.'components'.DS.'com_community';	
				if (file_exists($js.DS.'community.php')) { ?>
					<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectSourceJomsocial')">
						<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
					</button>
				<?php } else { ?>
					<button class="btn btn-info disabled" onclick="return false;">
						<?php echo JText::_('COM_MINITEKWALL_JOMSOCIAL_NOT_INSTALLED'); ?>
					</button>
				<?php } ?>
				
			</div>
		 
		</div>
		
		<div class="span4">
									
			<div class="thumbnail">
				<img src="components/com_minitekwall/assets/images/icon-48-easyblog.png" alt="Easyblog" />
				<div class="thumbnail-title">
					<span><?php echo JText::_('COM_MINITEKWALL_EASYBLOG'); ?></span>
				</div>
				
				<?php 
				$easyblog = JPATH_ROOT.DS.'components'.DS.'com_easyblog';	
				if (file_exists($easyblog.DS.'easyblog.php')) { ?>
					<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectSourceEasyblog')">
						<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
					</button>
				<?php } else { ?>
					<button class="btn btn-info disabled" onclick="return false;">
						<?php echo JText::_('COM_MINITEKWALL_EASYBLOG_NOT_INSTALLED'); ?>
					</button>
				<?php } ?>
				
			</div>
			
		</div>
		
		<div class="span4">
									
			<div class="thumbnail">
				<img src="components/com_minitekwall/assets/images/icon-48-images-folder.png" alt="Images folder" />
				<div class="thumbnail-title">
					<span><?php echo JText::_('COM_MINITEKWALL_IMAGE_FOLDER'); ?></span>
				</div>
				
				<button class="btn btn-info" onclick="Joomla.submitbutton('widget.selectSourceFolder')">
					<?php echo JText::_('COM_MINITEKWALL_SELECT'); ?>
				</button>
				
			</div>
			
		</div>
	
	</div>
	
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>