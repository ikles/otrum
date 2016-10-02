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
$step = JRequest::getInt('step');

$type_id = $this->type_id;
$source_id = $this->source_id;

if (!$type_id) {
	$type_id = $this->item->type_id;
}

if (!$source_id) {
	$source_id = $this->item->source_id;
}

$type_active = '';
$type_disabled = '';
$type_return_false = '';
$source_active = '';
$source_disabled = '';
$source_return_false = '';
$settings_active = '';
$settings_disabled = '';
$settings_return_false = '';

if ($isNew) {
	if ($step == 1) {
		$type_active = 'mw-active';
		$type_disabled = 'disabled';
		$type_return_false = 'onclick="return false;"';
		if (!$type_id)
		{
			$source_disabled = 'disabled';
			$source_return_false = 'onclick="return false;"';
			$settings_disabled = 'disabled';
			$settings_return_false = 'onclick="return false;"';	
		}
		if (!$source_id) 
		{
			$settings_disabled = 'disabled';
			$settings_return_false = 'onclick="return false;"';	
		}
	}
	if ($step == 2) {
		$source_active = 'mw-active';
		$source_disabled = 'disabled';
		$source_return_false = 'onclick="return false;"';
		if (!$source_id)
		{
			$settings_disabled = 'disabled';
			$settings_return_false = 'onclick="return false;"';	
		}
	}
	if (!$step || $step == 3) {
		$settings_active = 'mw-active';
		$settings_disabled = 'disabled';
		$settings_return_false = 'onclick="return false;"';	
	}
} else {
	if ($step == 1) {
		$type_active = 'mw-active';
		$type_disabled = 'disabled';
		$type_return_false = 'onclick="return false;"';
	}
	if ($step == 2) {
		$source_active = 'mw-active';
		$source_disabled = 'disabled';
		$source_return_false = 'onclick="return false;"';
	}
	if (!$step || $step == 3) {
		$settings_active = 'mw-active';
		$settings_disabled = 'disabled';
		$settings_return_false = 'onclick="return false;"';
	}
}
?>								
									
<?php if ($isNew) { ?>
			
	<div class="span4 <?php echo $type_active; ?>">
		<a href="index.php?option=com_minitekwall&view=widget&layout=edit&step=1" class="<?php echo $type_disabled; ?>" <?php echo $type_return_false; ?>>
			<?php if ($type_id) { ?>
				<?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_TYPE'); ?>
				<strong>: <?php echo $type_id; ?></strong> <i class="fa fa-edit"></i> 
			<?php } else { ?>
				1. <?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_SELECT_TYPE'); ?>
			<?php } ?>
		</a>
	</div>
	<div class="span4 <?php echo $source_active; ?>">
		<a href="index.php?option=com_minitekwall&view=widget&layout=edit&step=2" class="<?php echo $source_disabled; ?>" <?php echo $source_return_false; ?>>
			<?php if ($source_id) { ?>
				<?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_SOURCE'); ?>
				<strong>: <?php echo $source_id; ?></strong>  <i class="fa fa-edit" data-toggle="tooltip" data-placement="bottom" title="Change"></i>
			<?php } else { ?>
				2. <?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_SELECT_SOURCE'); ?>
			<?php } ?>
		</a>
	</div>
	<div class="span4 <?php echo $settings_active; ?>">
		<a href="index.php?option=com_minitekwall&view=widget&layout=edit&step=3" class="<?php echo $settings_disabled; ?>" <?php echo $settings_return_false; ?>>
			<?php if ($source_id) { ?>
				<i class="fa fa-gear"></i> <?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_SETTINGS'); ?>
			<?php } else { ?>
				3. <?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_SETTINGS'); ?>
			<?php } ?>
		</a>
	</div>

<?php } else { ?>

	<div class="span4 <?php echo $type_active; ?>">
		<a href="index.php?option=com_minitekwall&view=widget&layout=edit&step=1&id=<?php echo $this->item->id; ?>" class="<?php echo $type_disabled; ?>" <?php echo $type_return_false; ?>>
			<?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_TYPE'); ?><strong>: <?php echo $type_id; ?></strong> <i class="fa fa-edit"></i>
		</a>
	</div>
	<div class="span4 <?php echo $source_active; ?>">
		<a href="index.php?option=com_minitekwall&view=widget&layout=edit&step=2&id=<?php echo $this->item->id; ?>" class="<?php echo $source_disabled; ?>" <?php echo $source_return_false; ?>>
			<?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_SOURCE'); ?><strong>: <?php echo $source_id; ?></strong> <i class="fa fa-edit"></i>
		</a>
	</div>
	<div class="span4 <?php echo $settings_active; ?>">
		<a href="index.php?option=com_minitekwall&view=widget&layout=edit&step=3&id=<?php echo $this->item->id; ?>" class="<?php echo $settings_disabled; ?>" <?php echo $settings_return_false; ?>>
			<i class="fa fa-gear"></i> <?php echo JText::_('COM_MINITEKWALL_WIDGET_PAGINATION_SETTINGS'); ?>
		</a>
	</div>

<?php } ?>