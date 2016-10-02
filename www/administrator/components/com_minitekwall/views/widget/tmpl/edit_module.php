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

$isNew	= ($this->item->id == 0);
$moduleIsInstalled = $this->checkModuleIsInstalled; 
?>								
									
<div class="modal fade" id="newModuleModal" tabindex="-1" role="dialog" aria-labelledby="newModuleModalLabel" aria-hidden="true">
								
	<div class="modal-dialog">
		
		<div class="modal-content">
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo JText::_('COM_MINITEKWALL_CLOSE_MODAL_WINDOW'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
				<h3 class="modal-title" id="newModuleModalLabel">
					<?php echo JText::_('COM_MINITEKWALL_CREATE_WIDGET_MODULE'); ?>
				</h3>
			</div>
			
			<div class="modal-body">
			
				<div class="page-content mw-dashboard">

					<div class="row-fluid">
						
						<?php if (!$moduleIsInstalled) { ?>
						
						<div class="span12">
							
							<div class="thumbnail">
								
								<div class="thumbnail-title">
									<span><?php echo JText::_('COM_MINITEKWALL_MODULE_NOT_FOUND'); ?></span>
								</div>
								<p><?php echo JText::_('COM_MINITEKWALL_MODULE_NOT_FOUND_DESC'); ?></p>
								
							</div>
						 
						</div>
						
						<?php } else if ($moduleIsInstalled && !$isNew) { ?>
						
						<div class="span4">
							
							<div class="thumbnail">
								
								<div class="thumbnail-title">
									<span><?php echo JText::_('COM_MINITEKWALL_MODULE_IN_MODULE_POSITION'); ?></span>
								</div>
								<p><?php echo JText::_('COM_MINITEKWALL_MODULE_IN_MODULE_POSITION_DESC'); ?></p>
		
								<button class="btn btn-small btn-success" data-toggle="modal" data-target="#newModuleModal" onclick="Joomla.submitbutton('widget.createModule')">
									<span class="icon-new icon-white"></span>
									<?php echo JText::_('COM_MINITEKWALL_CREATE_MODULE'); ?>
								</button>
							
							</div>
							
						</div>
						
						<div class="span4">
							
							<div class="thumbnail">
							
								<div class="thumbnail-title">
									<span><?php echo JText::_('COM_MINITEKWALL_MODULE_LOAD_POSITION_PLUGIN'); ?></span>
								</div>
								<p><?php echo JText::_('COM_MINITEKWALL_MODULE_LOAD_POSITION_PLUGIN_DESC'); ?></p>
								
								<button class="btn btn-small btn-success" data-toggle="modal" data-target="#newModuleModal" onclick="Joomla.submitbutton('widget.createModuleforPlugin')">
									<span class="icon-new icon-white"></span>
									<?php echo JText::_('COM_MINITEKWALL_CREATE_MODULE'); ?>
								</button>
								
								<div class="alert alert-warning" role="alert">
									<p><small><?php echo JText::_('COM_MINITEKWALL_MODULE_SYNTAX'); ?></small></p>
									<p class="lead">&#123;loadposition minitekwall-<?php echo $this->item->id; ?>&#125;</p>
								</div>
								
							</div>
							
						</div>
						
						<div class="span4">
							
							<div class="thumbnail">
							
								<div class="thumbnail-title">
									<span><?php echo JText::_('COM_MINITEKWALL_MODULE_MODULES_ANYWHERE_PLUGIN'); ?></span>
								</div>
								<p><?php echo JText::_('COM_MINITEKWALL_MODULE_MODULES_ANYWHERE_PLUGIN_DESC'); ?></p>
								
								<button class="btn btn-small btn-success" data-toggle="modal" data-target="#newModuleModal" onclick="Joomla.submitbutton('widget.createModuleforPlugin')">
									<span class="icon-new icon-white"></span>
									<?php echo JText::_('COM_MINITEKWALL_CREATE_MODULE'); ?>
								</button>
								
								<div class="alert alert-warning" role="alert">
									<p><small><?php echo JText::_('COM_MINITEKWALL_MODULE_SYNTAX'); ?></small></p>
									<p class="lead">&#123;modulepos minitekwall-<?php echo $this->item->id; ?>&#125;</p>
								</div>
								
							</div>
							
						</div>
						
						<?php } else if ($moduleIsInstalled && $isNew) { ?>
						
						<div class="span12">
							
							<div class="thumbnail">
								
								<div class="thumbnail-title">
									<span><?php echo JText::_('COM_MINITEKWALL_WIDGET_NOT_SAVED'); ?></span>
								</div>
								<p><?php echo JText::_('COM_MINITEKWALL_WIDGET_NOT_SAVED_DESC'); ?></p>
								
							</div>
						 
						</div>
							
						<?php } ?>
						
					</div>
			
				</div>
				
			</div>
											
		</div>
		
	</div>
	
</div>