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
	
	<div class="col-md-6 col-sm-12 event-left">
		<!--Image Item-->
		<div class="catItemImageBlock  <?php if($this->item->params->get('catItemVideo') && !empty($this->item->video)): ?> me-video <?php endif; ?> ">

			<a href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">

				<?php 
				//Create placeholder items images
				
				$src = $this->item->image;
				$src_pre= $this->item->imageLarge;
				if (!empty( $src)) {								
					$thumb_img = '<img src="'.$src.'" alt="'.$this->item->title.'" />';
				} else if ($is_placehold) {					
					$thumb_img = yt_placehold($placehold_size['k2_event'],$this->item->title,$this->item->title);
				}	
				echo $thumb_img;
				?>

			</a>

			<?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields)): 
			$metas = $this->item->extra_fields;
			?>
			<?php if( (isset($metas[3]) && ($metas[3]->value !=''))):?>
				<div class="register-btn" ><?php echo $metas[3]->value; ?></div>
			<?php endif; ?>
		<?php endif; ?>

	</div>
	<!--End Images Item-->

	<div class="main-item">
		<?php if($this->item->params->get('catItemTitle')): ?>
			<!-- Item title -->
			<h3 class="catItemTitle">
				
				<?php if ($this->item->params->get('catItemTitleLinked')): ?>
					<a href="<?php echo $this->item->link; ?>">
						<?php echo $this->item->title; ?>
					</a>
				<?php else: ?>
					<?php echo $this->item->title; ?>
				<?php endif;
				?> 
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
		<?php if( (isset($metas[0]) && ($metas[0]->value !='')) || (isset($metas[1]) && ($metas[1]->value !=''))):?>
			<ul class="timevent-address">
				<?php if( (isset($metas[0]) && ($metas[0]->value !=''))):?>
					<li class="time-of-event"><?php echo $metas[0]->value; ?></li>
				<?php endif; ?>
				<?php if( (isset($metas[1]) && ($metas[1]->value !=''))):?>
					<li class="address"><?php echo $metas[1]->value; ?></li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
		<?php endif; ?>
		<?php 
		
		if(isset($metas[4]) && ($metas[4]->value !='')):
		$full_date = $metas[4]->value;
		$event_date = explode("-", $full_date);
		$year_end = $event_date[2];
		$month_end = $event_date[1];
		$day_end = $event_date[0];
		?>
		<script type="text/javascript">
			jQuery(function () {
				var austDay = new Date(<?php echo $year_end; ?>, <?php echo $month_end; ?>-1 , <?php echo $day_end; ?>);
				jQuery('#countdown-<?php echo $this->item->id; ?>').countdown(austDay) 
				.on('update.countdown', function(event) {
					jQuery(this).html(event.strftime(
						'<div class="countdown-section time-day"><div class="countdown-amount">%D</div><div class="countdown-period"><?php echo JText::_('DAYS'); ?> </div></div>'
						+ '<div class="countdown-section time-hour"><div class="countdown-amount">%H</div><div class="countdown-period"><?php echo JText::_('HOURS'); ?></div></div>'
						+ '<div class="countdown-section time-min"><div class="countdown-amount">%M</div><div class="countdown-period"><?php echo JText::_('MIN'); ?></div></div>'
						+ '<div class="countdown-section time-sec"><div class="countdown-amount">%S</div><div class="countdown-period"><?php echo JText::_('SEC'); ?> </div></div>'));

				})
				.on('finish.countdown', function(event) {
					jQuery(this).html('<h4>This offer has expired!</h4>');
				});;
			});
		</script>
		<div id="countdown-<?php echo $this->item->id; ?>" class="countdown-event"></div>

		
		<?php endif; ?>
	</div>
<!-- End Main Item -->
</div>
<div class="col-md-6 col-sm-12 event-right">
	<?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields) && (isset($metas[2]) && ($metas[2]->value !=''))): ?>
		<div class="phone"><i class="fa fa-phone"></i> <?php echo $metas[2]->value; ?></div>
	<?php endif; ?>
	
	<!--
	<ul class="icon-item size-mail">

		<?php if($this->item->params->get('itemEmailButton')): ?>
			<li class="emailbutton">
				<a class="itemEmailLink" rel="nofollow" href="<?php echo $this->item->emailLink; ?>" onclick="window.open(this.href,'emailWindow','width=400,height=350,location=no,menubar=no,resizable=no,scrollbars=no'); return false;">
					<i class="fa fa-envelope-o"></i>
				</a>
			</li>
		<?php endif; ?>
		
		<?php if($this->item->params->get('itemPrintButton') && !JRequest::getInt('print')): ?>
			<li class="printbutton">
				<a class="itemPrintLink" rel="nofollow" href="<?php echo $this->item->printLink; ?>" onclick="window.open(this.href,'printWindow','width=900,height=600,location=no,menubar=no,resizable=yes,scrollbars=yes'); return false;">
					<i class="fa fa-print"></i>
				</a>
			</li>
		<?php endif; ?>
	</ul>
	-->

	<?php if($this->item->params->get('catItemIntroText')): ?>
		<!-- Item introtext -->
		<div class="catItemIntroText">
			<?php echo $this->item->introtext; ?>
		</div>
	<?php endif; ?>
	<!-- Go to www.addthis.com/dashboard to customize your tools -->
	<?php if($this->item->params->get('itemSocialButton') && !is_null($this->item->params->get('socialButtonCode', NULL))): ?>
		<!-- Item Social Button -->
		<div class="itemSocialBlock col-md-6">
			<strong>Share:</strong> <?php echo $this->item->params->get('socialButtonCode'); ?>
		</div>
	<?php endif; ?>
</div>

<!-- Plugins: AfterDisplay -->
<?php echo $this->item->event->AfterDisplay; ?>

<!-- K2 Plugins: K2AfterDisplay -->
<?php echo $this->item->event->K2AfterDisplay; ?>


</div>
<!-- End K2 Item Layout -->
