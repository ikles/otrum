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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('bootstrap.framework');

$user       = JFactory::getUser();
$canCreate  = $user->authorise('core.create', 'com_minitekwall');

// Add page leave verification
$document = JFactory::getDocument();
$document->addScript(JURI::root().'/administrator/components/com_minitekwall/assets/js/pageleave.js');

$uri = JFactory::getURI();
$new_url = $uri->toString();
$isNew	= ($this->item->id == 0);

$step = JRequest::getInt('step');
$type_id = $this->type_id;
$source_id = $this->source_id;

if (!$type_id) {
	$type_id = $this->item->type_id;
}
if (!$source_id) {
	$source_id = $this->item->source_id;
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'widget.cancel' || document.formvalidator.isValid(document.id('widget-form')))
		{
			Joomla.submitform(task, document.getElementById('widget-form'));
		}
	}
</script>

<div id="mw-cpanel"><!-- Main Container -->

	<?php echo $this->navbar; ?>
	
	<div id="mw-main-container" class="main-container container-fluid">
	
		<a id="menu-toggler" class="menu-toggler" href="#">
			<span class="menu-text"></span>
		</a>

		<div id="mw-sidebar" class="sidebar">
						
			<?php //echo $this->sidebar; ?>
			<ul class="nav nav-list">
				<li class="">
					<a href="#" class="" onclick="Joomla.submitbutton('widget.cancel')">
						<i class="fa fa-reply"></i> <span class="menu-text"><?php echo JText::_('COM_MINITEKWALL_BACK_TO_WIDGETS'); ?></span>
					</a>
				</li>
				<li class="open">
					<span class="widget-info">
						<i class="fa fa-refresh"></i> <span class="menu-text"><?php echo JText::_('COM_MINITEKWALL_DYNAMIC_WIDGET'); ?></span>
					</span>
				</li>
				<?php if ($this->item->name) { ?>
				<li class="open">
					<span class="widget-info">
						<span class="menu-text"><strong><?php echo $this->item->name; ?></strong></span>
					</span>
				</li>
				<?php } ?>
			</ul>
			
			<div class="sidebar-collapse" id="sidebar-collapse">
		  		<i class="fa fa-angle-double-left"></i>
			</div>
					
		</div>
		
		<div class="main-content widget-content">
			
			<div class="page-header clearfix"> 
				
				<?php if ($canCreate && (!$step || $step == 3)) { ?>
					
					<!-- Create module button - Module not installed -->
					<div id="toolbar-new-module" class="btn-wrapper">
						
						<button class="btn btn-small btn-success" data-toggle="modal" data-target="#newModuleModal">
							<span class="icon-play icon-white"></span>
							<?php echo JText::_('COM_MINITEKWALL_PUBLISH_WIDGET'); ?>
						</button>
						
						<?php echo $this->loadTemplate('module'); ?>
						
					</div>
									
				<?php } ?>
			
			</div>
			
			<div class="mw-pagination clearfix">
			
				<?php echo $this->loadTemplate('pagination'); ?>
			
			</div>

			<?php // Step 1
			if ($step == 1) { ?>	
			
				<div class="page-content mw-dashboard">
					
					<div class="row-fluid"> </div>
							
					<div class="row-fluid">
						
						<?php echo $this->loadTemplate('type'); ?>
						
					</div>
																		
				</div>
			
			<?php // Step 2	
			} else if ($step == 2) { ?>
			
				<div class="page-content mw-dashboard">
					
					<div class="row-fluid"> </div>
					
					<?php echo $this->loadTemplate('source'); ?>
												
				</div>
									
			<?php // Step 3
			} else if (!$step || $step == 3) { ?>
			
			<div class="page-content">
	
				<form action="<?php echo JRoute::_('index.php?option=com_minitekwall&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="widget-form" class="form-validate">
										
					<div class="row-fluid">
					
						<div class="span12 form-horizontal">
							
							<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'basic')); ?>
								
								<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'basic', JText::_('COM_MINITEKWALL_FIELD_WIDGET_GENERAL_PARAMS', true)); ?>
									
									<div class="row-fluid">
										<div class="span12">
											<?php foreach ($this->form->getFieldset('basic') as $field): ?>
											<div class="control-group form-inline">
												<div class="control-label">
													<?php echo $field->label; ?>
												</div>
												<div class="controls">
													<?php echo $field->input; ?>
												</div>
											</div>
										<?php endforeach; ?>
										</div>
									</div>     
																								
								<?php echo JHtml::_('bootstrap.endTab'); ?>
								
								<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'datasource', JText::_('COM_MINITEKWALL_FIELD_WIDGET_DATA_SOURCE_PARAMS', true)); ?>
									
									<?php if ($source_id == 'joomla') { ?>
									
										<div class="row-fluid">
											<div class="span12">
												<?php echo $this->form->getControlGroup('joomla_source'); ?>
												<?php foreach ($this->form->getGroup('joomla_source') as $field) : ?>
													<?php echo $field->getControlGroup(); ?>
												<?php endforeach; ?>
											</div>
										</div>  
									
									<?php } else if ($source_id == 'k2') { ?>  
									 
										<div class="row-fluid">
											<div class="span12">
												<?php echo $this->form->getControlGroup('k2_source'); ?>
												<?php foreach ($this->form->getGroup('k2_source') as $field) : ?>
													<?php echo $field->getControlGroup(); ?>
												<?php endforeach; ?>
											</div>
										</div>  
									
									<?php } else if ($source_id == 'virtuemart') { ?>   
									
										<div class="row-fluid">
											<div class="span12">
												<?php echo $this->form->getControlGroup('virtuemart_source'); ?>
												<?php foreach ($this->form->getGroup('virtuemart_source') as $field) : ?>
													<?php echo $field->getControlGroup(); ?>
												<?php endforeach; ?>
											</div>
										</div>  
									
									<?php } else if ($source_id == 'jomsocial') { ?>  
									 
										<div class="row-fluid">
											<div class="span12">
												<?php echo $this->form->getControlGroup('jomsocial_source'); ?>
												<?php foreach ($this->form->getGroup('jomsocial_source') as $field) : ?>
													<?php echo $field->getControlGroup(); ?>
												<?php endforeach; ?>
											</div>
										</div>  
										
									<?php } else if ($source_id == 'easyblog') { ?>  
									 
										<div class="row-fluid">
											<div class="span12">
												<?php echo $this->form->getControlGroup('easyblog_source'); ?>
												<?php foreach ($this->form->getGroup('easyblog_source') as $field) : ?>
													<?php echo $field->getControlGroup(); ?>
												<?php endforeach; ?>
											</div>
										</div>  
									
									<?php } else if ($source_id == 'folder') { ?>  
									 
										<div class="row-fluid">
											<div class="span12">
												<?php echo $this->form->getControlGroup('folder_source'); ?>
												<?php foreach ($this->form->getGroup('folder_source') as $field) : ?>
													<?php echo $field->getControlGroup(); ?>
												<?php endforeach; ?>
											</div>
										</div>  
									
									<?php } ?>    
																								
								<?php echo JHtml::_('bootstrap.endTab'); ?>
								
								<?php if ($type_id == 'masonry') { ?>
									
									<?php echo $this->loadTemplate('masonry'); ?>
									
								<?php } ?>
								
								<?php if ($type_id == 'scroller') { ?>
									
									<?php echo $this->loadTemplate('scroller'); ?>
									
								<?php } ?>
								
							</div>
						
						<?php echo JHtml::_('bootstrap.endTabSet'); ?>
				
						<input type="hidden" name="task" value="" />
						<input type="hidden" id="jform_type_id" name="jform[type_id]" value="<?php echo $type_id; ?>" />
						<input type="hidden" id="jform_source_id" name="jform[source_id]" value="<?php echo $source_id; ?>" />
						<input type="hidden" id="jform_source_type_id" name="jform[source_type_id]" value="dynamic" />
					
						<?php echo JHtml::_('form.token'); ?>
						
					</div>
					
				</form>
				
			</div><!-- End page-content -->
			
			<?php } ?>
			
		</div><!-- End main-content -->
	
	</div><!-- End mw-main-container -->
	
</div><!-- End Main Container -->	
