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

$user  = JFactory::getUser();
$token = JSession::getFormToken();
?>

<div id="mw-cpanel"><!-- Main Container -->
	
	<?php echo $this->navbar; ?>	
	
	<div id="mw-main-container" class="main-container container-fluid">
	
		<a id="menu-toggler" class="menu-toggler" href="#">
			<span class="menu-text"></span>
		</a>

		<div id="mw-sidebar" class="sidebar">
		
			<?php echo $this->sidebar; ?>
		
		</div>
		
		<div class="main-content">
			
			<div class="page-header clearfix"> </div>
			
			<div class="page-content mw-dashboard">
				
				<div class="row-fluid">
				
					<div class="span12">
					
						<div class="thumbnail">
							<i class="mwicons mwicons-refresh57"></i>
							<div class="thumbnail-title">
								<span><?php echo JText::_('COM_MINITEKWALL_DYNAMIC_WIDGET'); ?></span>
							</div>
							<p><?php echo JText::_('COM_MINITEKWALL_DYNAMIC_WIDGET_DESCRIPTION'); ?></p>
							<a href="index.php?option=com_minitekwall&view=widget&layout=edit&step=1" class="btn btn-info"><?php echo JText::_('COM_MINITEKWALL_CREATE'); ?></a>
						</div>
					 
					</div>
					
					<?php /*
					<div class="span6">
												
						<div class="thumbnail">
							<i class="mwicons mwicons-images11"></i>
							<div class="thumbnail-title">
								<span><?php echo JText::_('COM_MINITEKWALL_CUSTOM_WIDGET'); ?></span>
							</div>
							<p><?php echo JText::_('COM_MINITEKWALL_CUSTOM_WIDGET_DESCRIPTION'); ?></p>
							<a href="#" class="btn btn-default disabled" onclick="return false;"><?php echo JText::_('COM_MINITEKWALL_UNDER_CONSTRUCTION'); ?></a>
						</div>
						
					</div>		
					*/ ?>			
					
				</div>
		
			</div><!-- End page-content -->
			
		</div><!-- End main-content -->

	</div><!-- End mw-main-container -->
	
</div><!-- End Main Container -->