<?php
/**
 * @version    2.7.x
 * @package    K2
 * @author     JoomlaWorks http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

// includes placehold
$yt_temp = JFactory::getApplication()->getTemplate();
include (JPATH_BASE . '/templates/'.$yt_temp.'/includes/placehold.php');
?>

<!-- Start K2 Tag Layout -->
<div id="k2Container" class="tagView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')): ?>
		<!-- Page title -->
		<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</div>
	<?php endif; ?>

	<?php if($this->params->get('tagFeedIcon',1)): ?>
		<!-- RSS feed icon -->
		<div class="k2FeedIcon">
			<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
				<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
			</a>
			<div class="clr"></div>
		</div>
	<?php endif; ?>

	<?php if(count($this->items)): ?>
		<div class="tagItemList row">
			<?php $i = 0; ?>
			<?php foreach($this->items as $item): ?>
				<?php $i++; ?>
				<!-- Start K2 Item Layout -->
				<div class="col-md-6 <?php if($i == 3){ echo"tagitems"; $i = 1; } ?>">
					<div class="tagItemView">

						<?php if($item->params->get('tagItemImage',1)): ?>
							<!-- Item Image -->
							<div class="tagItemImageBlock">
								<span class="tagItemImage">
									<a href="<?php echo $item->link; ?>" title="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>">
										<?php 
                           				 //Create placeholder items images
										$src = isset($item->imageGeneric)? $item->imageGeneric : '';
										if (!empty( $src)) {								
											$thumb_img = '<img src="'.$src.'" alt="'.K2HelperUtilities::cleanHtml($item->title).'" style="width:'.$item->params->get('itemImageGeneric').'px; height:auto;" />';
										} else if ($is_placehold) {					
											$thumb_img = yt_placehold($placehold_size['k2_item'],$item->title,$item->title);
										}	
										echo $thumb_img;
										?>

									</a>
								</span>
								<div class="clr"></div>
							</div>
						<?php endif; ?>

						<div class="k2-info-left">
							<?php if($item->params->get('tagItemDateCreated',1)): ?>
								<!-- Date created -->
								<span class="tagItemDateCreated">
									<?php echo JHTML::_('date', $item->created , JText::_('DATE_FORMAT_LC3')); ?>
								</span>
							<?php endif; ?> 
							<div class="tagItemCommentsLink">
								<?php if(!empty($item->event->K2CommentsCounter)): ?>
									<!-- K2 Plugins: K2CommentsCounter -->
									<?php echo $item->event->K2CommentsCounter; ?>
								<?php else: ?>
									<p><i class="fa fa-comments-o"></i></p>
									<?php if($item->numOfComments > 0): ?>
										<a href="<?php echo $item->link; ?>#itemCommentsAnchor">
											<?php echo $item->numOfComments; ?> <?php echo ($item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
										</a>
									<?php else: ?>
										<a href="<?php echo $item->link; ?>#itemCommentsAnchor">
											<?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?>
										</a>
									<?php endif; ?>
								<?php endif; ?>
							</div>   
						</div>
						<div class="k2-info-right">
							<div class="tagItemHeader">
								<?php if($item->params->get('tagItemTitle',1)): ?>
									<!-- Item title -->
									<h2 class="tagItemTitle">
										<?php if ($item->params->get('tagItemTitleLinked',1)): ?>
											<a href="<?php echo $item->link; ?>">
												<?php echo limit_text($item->title,4); ?>
											</a>
										<?php else: ?>
											<?php echo $item->title; ?>
										<?php endif; ?>
									</h2>
								<?php endif; ?>

							</div>

							<div class="tagItemBody">

								<?php if($item->params->get('tagItemIntroText',1)): ?>
									<!-- Item introtext -->
									<div class="tagItemIntroText">
										<?php echo limit_text($item->introtext,20); ?>
									</div>
								<?php endif; ?>

								<div class="clr"></div>
							</div>

							<div class="clr"></div>

							<?php if($item->params->get('tagItemExtraFields',0) && count($item->extra_fields)): ?>
								<!-- Item extra fields -->
								<div class="tagItemExtraFields">
									<h4><?php echo JText::_('K2_ADDITIONAL_INFO'); ?></h4>
									<ul>
										<?php foreach ($item->extra_fields as $key=>$extraField): ?>
											<?php if($extraField->value != ''): ?>
												<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
													<?php if($extraField->type == 'header'): ?>
														<h4 class="tagItemExtraFieldsHeader"><?php echo $extraField->name; ?></h4>
													<?php else: ?>
														<span class="tagItemExtraFieldsLabel"><?php echo $extraField->name; ?></span>
														<span class="tagItemExtraFieldsValue"><?php echo $extraField->value; ?></span>
													<?php endif; ?>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
									<div class="clr"></div>
								</div>
							<?php endif; ?>
							<span class="tagItemAuthor">
								<?php echo K2HelperUtilities::writtenBy($item->author->profile->gender); ?>
								<?php if(isset($item->author->link) && $item->author->link): ?>
									<a rel="author" href="<?php echo $item->author->link; ?>"><?php echo $item->author->name; ?></a>
								<?php else: ?>
									<?php echo $item->author->name; ?>
								<?php endif; ?>
							</span>
							<?php if($item->params->get('tagItemCategory')): ?>
								<!-- Item category name -->
								<div class="tagItemCategory">
									<span><?php echo JText::_('K2_PUBLISHED_IN'); ?></span>
									<a href="<?php echo $item->category->link; ?>"><?php echo $item->category->name; ?></a>
								</div>
							<?php endif; ?>

							<?php if ($item->params->get('tagItemReadMore')): ?>
								<!-- Item "read more..." link -->
								<div class="tagItemReadMore">
									<a class="k2ReadMore" href="<?php echo $item->link; ?>">
										<?php echo JText::_('K2_READ_MORE'); ?>
									</a>
								</div>
							<?php endif; ?>

							<div class="clr"></div>

						</div>
					</div>
				</div>
				<!-- End K2 Item Layout -->

			<?php endforeach; ?>
		</div>

		<!-- Pagination -->
		<?php if($this->pagination->getPagesLinks()): ?>
			<div class="k2Pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
				<div class="clr"></div>
				<?php echo $this->pagination->getPagesCounter(); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

</div>
<!-- End K2 Tag Layout -->
