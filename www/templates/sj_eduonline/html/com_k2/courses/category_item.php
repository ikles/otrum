<?php
/**
 * @version		2.6.x
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;
// includes placehold

$yt_temp = JFactory::getApplication()->getTemplate();
include (JPATH_BASE . '/templates/'.$yt_temp.'/includes/placehold.php');

// Define default image size (do not change)
K2HelperUtilities::setDefaultImage($this->item, 'itemlist', $this->params);

?>

<!-- Start K2 Item Layout -->
<div class="catItemView group<?php echo ucfirst($this->item->itemGroup); ?><?php if($this->item->params->get('pageclass_sfx')) echo ' '.$this->item->params->get('pageclass_sfx'); ?>">

	<!-- Plugins: BeforeDisplay -->
	<?php echo $this->item->event->BeforeDisplay; ?>

	<!-- K2 Plugins: K2BeforeDisplay -->
	<?php echo $this->item->event->K2BeforeDisplay; ?>
	
	<div class="col-md-3 col-sm-3 col-sx-12 courses-left">
		<!--Image Item-->
		<div class="catItemImageBlock <?php if($this->item->params->get('catItemVideo') && !empty($this->item->video)): ?> me-video <?php endif; ?> ">

			<a href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">

				<?php 
				//Create placeholder items images
				
				$src = $this->item->image;
				$src_pre= $this->item->imageLarge;
				if (!empty( $src)) {								
					$thumb_img = '<img src="'.$src.'" alt="'.$this->item->title.'" />';
				} else if ($is_placehold) {					
					$thumb_img = yt_placehold($placehold_size['k2_courses'],$this->item->title,$this->item->title);
				}	
				echo $thumb_img;
				?>

			</a>

			<?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields)): 
			$metas = $this->item->extra_fields;
			?>
			
		<?php endif; ?>

	</div>
	<!--End Images Item-->
</div>
<div class="col-md-9 col-sm-9 col-xs-12 courses-right">
	<div class="main-item">
		<?php if($this->item->params->get('catItemTitle')): ?>
			<!-- Item title -->
			<h3 class="courItemTitle">
				<?php if ($this->item->params->get('catItemTitleLinked')): ?>
					<a href="<?php echo $this->item->link; ?>">
						<?php echo $this->item->title; ?>
					</a>
				<?php else: ?>
					<?php echo $this->item->title; ?>
				<?php endif; ?> 
			</h3>
		<?php endif; ?>
		<!-- End Item title -->
		<!-- Date created -->
		<?php if($this->item->params->get('catItemDateCreated')): ?>
			<span class="create"><?php echo JHTML::_('date', $this->item->created , 'j F Y'); ?></span>		
		<?php endif; ?>
		<?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields)): 
		$metas = $this->item->extra_fields;
		?>
		
	<?php endif; ?>
</div>

<?php if($this->item->params->get('catItemIntroText')): ?>
	<!-- Item introtext -->
	<div class="catItemIntroText">
		<?php echo $this->item->introtext; ?>
	</div>
	<!-- K2 Plugins: K2AfterDisplay -->
	<?php echo $this->item->event->K2AfterDisplay; ?>
	<?php if( (isset($metas[1]) && ($metas[1]->value !='')) || (isset($metas[2]) && ($metas[2]->value !='')) || (isset($metas[3]) && ($metas[3]->value !='')) ):?>
		<ul class="timecourses-address">
			<?php if( (isset($metas[1]) && ($metas[1]->value !=''))):?>
				<li class="time-of-courses"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo JHTML::_('date', $metas[1]->value, JText::_('TEMPLATE_DATE_FORMAT_LC3')); ?></li>
			<?php endif; ?>
			<?php if( (isset($metas[2]) && ($metas[2]->value !=''))):?>
				<li class="address"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo $metas[2]->value; ?></li>
			<?php endif; ?>
			<?php if( (isset($metas[3]) && ($metas[3]->value !=''))):?>
				<li class="address"><i class="fa fa-hourglass-half" aria-hidden="true"></i> <?php echo $metas[3]->value; ?></li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
<?php endif; ?>
<?php if($this->item->params->get('catItemTitle')): ?>
	<!-- Item title -->
	<p class="readItemTitle">
		<a href="<?php echo $this->item->link; ?>">.</a>
	</p>
<?php endif; ?>
<!-- Go to www.addthis.com/dashboard to customize your tools 
<?php if($this->item->params->get('itemSocialButton') && !is_null($this->item->params->get('socialButtonCode', NULL))): ?>
	<div class="itemSocialBlock col-md-6">
		<strong>Share:</strong> <?php echo $this->item->params->get('socialButtonCode'); ?>
	</div>
<?php endif; ?>
-->
</div>

<!-- Plugins: AfterDisplay -->
<?php echo $this->item->event->AfterDisplay; ?>



</div>
<!-- End K2 Item Layout -->
