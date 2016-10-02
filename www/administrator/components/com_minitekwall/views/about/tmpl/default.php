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

				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					
					<tr>
						<td>	
							<h3 style="font-size:16px;"><?php echo JText::_('COM_MINITEKWALL_ABOUT_TITLE'); ?></h3>
							<p style="font-size:14px;">
								<?php echo JText::_('COM_MINITEKWALL_ABOUT_DESC'); ?><br />
							</p>
							<p style="font-size:14px;"><a class="btn btn-default" href="http://www.minitek.gr/joomla-extensions/minitek-wall">Learn more</a></p>
						</td>
					</tr>
					<tr>
						<td>		
							<h3 style="font-size:16px;"><?php echo JText::_('COM_MINITEKWALL_VERSION'); ?></h3>
							<?php 
							$xml = JFactory::getXML(JPATH_ADMINISTRATOR .'/components/com_minitekwall/minitekwall.xml');
							$version = (string)$xml->version;
							?>
				
							<p style="font-size:14px;"><?php echo JText::_('COM_MINITEKWALL_VERSION_MSG').': '.$version; ?></p>
						</td>
					</tr>
					<tr>
						<td>		
							<h3 style="font-size:16px;">Copyright</h3>
							<p style="font-size:14px;">
							© 2011 - <?php echo date("Y"); ?> Minitek
							</p>
						</td>
					</tr>
					<tr>
						<td>		
							<h3 style="font-size:16px;">Licence</h3>
							<p style="font-size:14px;">
							<a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GPLv3</a>
							</p>
						</td>
					</tr>
					
				</table>

			</div><!-- End page-content -->
			
		</div><!-- End main-content -->

	</div><!-- End mw-main-container -->
	
</div><!-- End Main Container -->