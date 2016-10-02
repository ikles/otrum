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
									
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_layout', JText::_('COM_MINITEKWALL_FIELD_WIDGET_LAYOUT_PARAMS', true)); ?>
											
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_layout') as $field): ?>
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

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_image_settings', JText::_('COM_MINITEKWALL_FIELD_WIDGET_IMAGES_PARAMS', true)); ?>
	
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_image_settings') as $field): ?>
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

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_detailbox', JText::_('COM_MINITEKWALL_FIELD_WIDGET_DETAIL_BOX_PARAMS', true)); ?>
	
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_detailbox') as $field): ?>
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

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_hoverbox', JText::_('COM_MINITEKWALL_FIELD_WIDGET_HOVER_BOX_PARAMS', true)); ?>
	
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_hoverbox') as $field): ?>
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
																
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_pagination', JText::_('COM_MINITEKWALL_FIELD_WIDGET_PAGINATION_PARAMS', true)); ?>
	
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_pagination') as $field): ?>
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

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_filters', JText::_('COM_MINITEKWALL_FIELD_WIDGET_FILTERS_PARAMS', true)); ?>
	
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_filters') as $field): ?>
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
								
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_effects', JText::_('COM_MINITEKWALL_FIELD_WIDGET_EFFECTS_PARAMS', true)); ?>
	
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_effects') as $field): ?>
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

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'masonry_responsive_settings', JText::_('COM_MINITEKWALL_FIELD_WIDGET_RESPONSIVE_PARAMS', true)); ?>
	
	<div class="row-fluid">
		<div class="span12">
			<?php foreach ($this->masonryform->getFieldset('masonry_responsive_settings') as $field): ?>
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