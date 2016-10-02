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
JHtml::stylesheet(JUri::base() . '/templates/'.$yt_temp.'/asset/owl.carousel/owl.carousel.css','text/css',"screen");
JHtml::_('jquery.framework');
JHtml::script(JUri::base() . '/templates/'.$yt_temp.'/asset/owl.carousel/owl.carousel.min.js');

?>

<?php if(JRequest::getInt('print')==1): ?>
	<!-- Print button at the top of the print page only -->
	<a class="itemPrintThisPage" rel="nofollow" href="#" onclick="window.print();return false;">
		<span><?php echo JText::_('K2_PRINT_THIS_PAGE'); ?></span>
	</a>
<?php endif; ?>

<!-- Start K2 Item Layout -->
<span id="startOfPageId<?php echo JRequest::getInt('id'); ?>"></span>

<div id="k2Container" class="itemView<?php echo ($this->item->featured) ? ' itemIsFeatured' : ''; ?><?php if($this->item->params->get('pageclass_sfx')) echo ' '.$this->item->params->get('pageclass_sfx'); ?> item-courses">

	<!-- Plugins: BeforeDisplay -->
	<?php echo $this->item->event->BeforeDisplay; ?>

	<!-- K2 Plugins: K2BeforeDisplay -->
	<?php echo $this->item->event->K2BeforeDisplay; ?>

	<div class="headerTop">
		<?php if($this->item->params->get('itemImage') ): ?>
			<!-- Item Image -->
			<div class="itemImageBlock col-md-3 col-sm-3 col-sx-12">
				<span class="itemImage">
					<a data-k2-modal="image" href="<?php echo $this->item->imageXLarge; ?>" title="<?php echo JText::_('K2_CLICK_TO_PREVIEW_IMAGE'); ?>">
						<?php 
					//Create placeholder items images

						$src = $this->item->image;
						if (!empty( $src)) {								
							$thumb_img = '<img src="'.$src.'" alt="'.$this->item->title.'" />';
						} else if ($is_placehold) {					
							$thumb_img = yt_placehold($placehold_size['k2_courses_itemrelated'],$this->item->title,$this->item->title);
						}	
						echo $thumb_img;
						?>
					</a>
				</span>

				<?php if($this->item->params->get('itemImageMainCaption') && !empty($this->item->image_caption)): ?>
					<!-- Image caption -->
					<span class="itemImageCaption"><?php echo $this->item->image_caption; ?></span>
				<?php endif; ?>

				<?php if($this->item->params->get('itemImageMainCredits') && !empty($this->item->image_credits)): ?>
					<!-- Image credits -->
					<span class="itemImageCredits"><?php echo $this->item->image_credits; ?></span>
				<?php endif; ?>

				<div class="clr"></div>
			</div>
		<?php endif; ?>
		<div class="headerTopRight col-md-9 col-sm-9 col-xs-12">
			<?php if($this->item->params->get('itemTitle')): ?>
				<!-- Item title -->
				<h2 class="itemTitle">
					<?php if(isset($this->item->editLink)): ?>
						<!-- Item edit link -->
						<span class="itemEditLink">
							<a data-k2-modal="edit" href="<?php echo $this->item->editLink; ?>"><?php echo JText::_('K2_EDIT_ITEM'); ?></a>
						</span>
					<?php endif; ?>

					<?php echo $this->item->title; ?>

					<?php if($this->item->params->get('itemFeaturedNotice') && $this->item->featured): ?>
						<!-- Featured flag -->
						<span>
							<sup>
								<?php echo JText::_('K2_FEATURED'); ?>
							</sup>
						</span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if($this->item->params->get('itemIntroText')): ?>
				<!-- Item introtext -->
				<div class="itemIntroText">
					<?php echo $this->item->introtext; ?>
				</div>
			<?php endif; ?>

			<?php if($this->item->params->get('itemExtraFields') && count($this->item->extra_fields)): ?>
				<!-- Item extra fields -->
				<div class="itemExtraFields">
					<ul>
						<?php
						$countClass = "extraIcon1";
						foreach ($this->item->extra_fields as $key=>$extraField): ?>
						<?php if($extraField->value != ''): ?>
							<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
								<?php if($extraField->type == 'header'): ?>
									<h4 class="itemExtraFieldsHeader"><?php echo $extraField->name; ?></h4>
								<?php else: ?>
									<i class="fa <?php echo $countClass; $countClass ++;?>"></i>
									<span class="itemExtraFieldsValue"><?php echo $extraField->value; ?></span>
								<?php endif; ?>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<div class="clr"></div>
			</div>
		<?php endif; ?>

		<!-- K2 Plugins: K2AfterDisplay -->
		<?php echo $this->item->event->K2AfterDisplay; ?>
	</div>
</div>

<!-- Plugins: AfterDisplayTitle -->
<?php echo $this->item->event->AfterDisplayTitle; ?>

<!-- K2 Plugins: K2AfterDisplayTitle -->
<?php echo $this->item->event->K2AfterDisplayTitle; ?>

<!-- Plugins: AfterDisplay -->
<?php echo $this->item->event->AfterDisplay; ?>

<div class="itemRelCom row">
	<?php if($this->item->params->get('itemRelated') && isset($this->relatedItems)) {
		$clasComx = "col-md-12";
	}
	else {
		$clasComx = "col-md-12";
	}
	?>
	<div class="<?php echo $clasComx; ?>">
		<?php if(
			$this->item->params->get('itemComments') &&
			(($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1'))
			): ?>
			<!-- K2 Plugins: K2CommentsBlock -->
			<?php echo $this->item->event->K2CommentsBlock; ?>
		<?php endif; ?>
		<div class="item-details">
			<?php if($this->item->params->get('commentsFormPosition')=='above' && $this->item->params->get('itemComments') && !JRequest::getInt('print') && ($this->item->params->get('comments') == '1' || ($this->item->params->get('comments') == '2' && K2HelperPermissions::canAddComment($this->item->catid)))): ?>
				<!-- Item comments form -->
				<div class="itemCommentsForm">
					<?php echo $this->loadTemplate('comments_form'); ?>
				</div>
			<?php endif; ?>

			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a class="item-tab" href="#tab-detail" aria-controls="tab-detail" role="tab" data-toggle="tab"><?php echo JText::_('K2_COURSES_INFORMATION') ?></a>
				</li>
				<!--
				<li role="presentation"><a class="item-tab" href="#tab-lessons" aria-controls="tab-lessons" role="tab" data-toggle="tab"><?php echo JText::_('K2_LESSONS') ?></a></li>
				-->
				<li role="presentation"><a class="item-tab" href="#tab-reviews" aria-controls="tab-reviews" role="tab" data-toggle="tab"><?php echo JText::_('K2_REVIEWS') ?></a></li>
				<li role="presentation"><a class="item-tab" href="#tab-trailer" aria-controls="tab-trailer" role="tab" data-toggle="tab"><?php echo JText::_('K2_TRAILER') ?></a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="tab-detail">
					<?php if(
						$this->item->params->get('itemAuthor') ||
						$this->item->params->get('itemDateCreated') ||
						$this->item->params->get('itemFontResizer') ||
						$this->item->params->get('itemPrintButton') ||
						$this->item->params->get('itemEmailButton') ||
						$this->item->params->get('itemRating') ||
						($this->item->params->get('itemVideoAnchor') && !empty($this->item->video)) ||
						($this->item->params->get('itemImageGalleryAnchor') && !empty($this->item->gallery)) ||
						($this->item->params->get('itemCommentsAnchor') && $this->item->params->get('itemComments') && $this->item->params->get('comments'))
						): ?>
						<div class="itemToolbar row">
							<ul class="itemToolbar-left col-md-4">
								<?php if($this->item->params->get('itemAuthor')): ?>
									<!-- Item Author -->
									<li class="itemAuthor">
										<?php if($this->item->params->get('itemAuthorImage') && !empty($this->item->author->avatar)): ?>
											<span class="itemAuthorAvatar"><img src="<?php echo $this->item->author->avatar; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($this->item->author->name); ?>" /></span>
										<?php endif; ?>
										<?php echo K2HelperUtilities::writtenBy($this->item->author->profile->gender); ?>
										<?php if(empty($this->item->created_by_alias)): ?>
											<a rel="author" href="<?php echo $this->item->author->link; ?>"><?php echo $this->item->author->name; ?></a>
										<?php else: ?>
											<?php echo $this->item->author->name; ?>
										<?php endif; ?>
									</li>
								<?php endif; ?>
								<?php if($this->item->params->get('itemDateCreated')): ?>
									<!-- Date created -->
									<li class="itemDateCreated">
										<?php echo JText::_('K2_CUSTOM_UPDATE'); ?> <?php echo JHTML::_('date', $this->item->created , JText::_('d/m/Y')); ?>
									</li>
								<?php endif; ?>
							</ul>
							<ul class="itemToolbar-right col-md-8">
								<?php if($this->item->params->get('itemFontResizer')): ?>
									<!-- Font Resizer -->
									<li>
										<span class="itemTextResizerTitle"><?php echo JText::_('K2_FONT_SIZE'); ?></span>
										<a href="#" id="fontDecrease">
											<span><?php echo JText::_('K2_DECREASE_FONT_SIZE'); ?></span>
										</a>
										<a href="#" id="fontIncrease">
											<span><?php echo JText::_('K2_INCREASE_FONT_SIZE'); ?></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if($this->item->params->get('itemPrintButton') && !JRequest::getInt('print')): ?>
									<!-- Print Button -->
									<li>
										<a class="itemPrintLink" rel="nofollow" href="<?php echo $this->item->printLink; ?>" onclick="window.open(this.href,'printWindow','width=900,height=600,location=no,menubar=no,resizable=yes,scrollbars=yes'); return false;">
											<span><?php echo JText::_('K2_PRINT'); ?></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if($this->item->params->get('itemEmailButton') && !JRequest::getInt('print')): ?>
									<!-- Email Button -->
									<li>
										<a class="itemEmailLink" rel="nofollow" href="<?php echo $this->item->emailLink; ?>" onclick="window.open(this.href,'emailWindow','width=400,height=350,location=no,menubar=no,resizable=no,scrollbars=no'); return false;">
											<span><?php echo JText::_('K2_EMAIL'); ?></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if($this->item->params->get('itemRating')): ?>
									<!-- Item Rating -->
									<li>
										<div class="itemRatingBlock">
											<span><?php echo JText::_('K2_RATE_THIS_ITEM'); ?></span>
											<div class="itemRatingForm">
												<ul class="itemRatingList">
													<li class="itemCurrentRating" id="itemCurrentRating<?php echo $this->item->id; ?>" style="width:<?php echo $this->item->votingPercentage; ?>%;"></li>
													<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_1_STAR_OUT_OF_5'); ?>" class="one-star">1</a></li>
													<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_2_STARS_OUT_OF_5'); ?>" class="two-stars">2</a></li>
													<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_3_STARS_OUT_OF_5'); ?>" class="three-stars">3</a></li>
													<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_4_STARS_OUT_OF_5'); ?>" class="four-stars">4</a></li>
													<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_5_STARS_OUT_OF_5'); ?>" class="five-stars">5</a></li>
												</ul>
												<div id="itemRatingLog<?php echo $this->item->id; ?>" class="itemRatingLog"><?php echo $this->item->numOfvotes; ?></div>
												<div class="clr"></div>
											</div>
											<div class="clr"></div>
										</div>
									</li>
								<?php endif; ?>

								<?php if($this->item->params->get('itemVideoAnchor') && !empty($this->item->video)): ?>
									<!-- Anchor link to item video below - if it exists -->
									<li>
										<a class="itemVideoLink k2Anchor" href="<?php echo $this->item->link; ?>#itemVideoAnchor"><?php echo JText::_('K2_MEDIA'); ?></a>
									</li>
								<?php endif; ?>

								<?php if($this->item->params->get('itemImageGalleryAnchor') && !empty($this->item->gallery)): ?>
									<!-- Anchor link to item image gallery below - if it exists -->
									<li>
										<a class="itemImageGalleryLink k2Anchor" href="<?php echo $this->item->link; ?>#itemImageGalleryAnchor"><?php echo JText::_('K2_IMAGE_GALLERY'); ?></a>
									</li>
								<?php endif; ?>

								<?php if($this->item->params->get('itemCommentsAnchor') && $this->item->params->get('itemComments') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1')) ): ?>
									<!-- Anchor link to comments below - if enabled -->
									<li class="icomments">
										<?php if(!empty($this->item->event->K2CommentsCounter)): ?>
											<!-- K2 Plugins: K2CommentsCounter -->
											<?php echo $this->item->event->K2CommentsCounter; ?>
										<?php else: ?>
											<?php if($this->item->numOfComments > 0): ?>
												<i class="fa fa-comments-o"></i>
												<a class="itemCommentsLink k2Anchor" href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
													<span><?php echo $this->item->numOfComments; ?></span> <?php echo ($this->item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
												</a>
											<?php else: ?>
												<i class="fa fa-comments-o"></i>
												<a class="itemCommentsLink k2Anchor" href="<?php echo $this->item->link; ?>#itemCommentsAnchor"><?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?></a>
											<?php endif; ?>
										<?php endif; ?>
									</li>
								<?php endif; ?>
							</ul>
							<div class="clr"></div>
						</div>
					<?php endif; ?>
					<div class="itemBody">

						<!-- Plugins: BeforeDisplayContent -->
						<?php echo $this->item->event->BeforeDisplayContent; ?>

						<!-- K2 Plugins: K2BeforeDisplayContent -->
						<?php echo $this->item->event->K2BeforeDisplayContent; ?>

						<?php if(!empty($this->item->fulltext)): ?>

							<?php if($this->item->params->get('itemFullText')): ?>
								<!-- Item fulltext -->
								<div class="itemFullText">
									<?php echo $this->item->fulltext; ?>
								</div>
							<?php endif; ?>

						<?php else: ?>

							<!-- Item text -->
							<div class="itemFullText">
								<?php echo $this->item->introtext; ?>
							</div>

						<?php endif; ?>

						<div class="clr"></div>

						<?php if($this->item->params->get('itemHits') || ($this->item->params->get('itemDateModified') && intval($this->item->modified)!=0)): ?>
							<div class="itemContentFooter">

								<?php if($this->item->params->get('itemHits')): ?>
									<!-- Item Hits -->
									<span class="itemHits">
										<?php echo JText::_('K2_READ'); ?> <b><?php echo $this->item->hits; ?></b> <?php echo JText::_('K2_TIMES'); ?>
									</span>
								<?php endif; ?>

								<?php if($this->item->params->get('itemDateModified') && intval($this->item->modified)!=0): ?>
									<!-- Item date modified -->
									<span class="itemDateModified">
										<?php echo JText::_('K2_LAST_MODIFIED_ON'); ?> <?php echo JHTML::_('date', $this->item->modified, JText::_('K2_DATE_FORMAT_LC2')); ?>
									</span>
								<?php endif; ?>

								<div class="clr"></div>
							</div>
						<?php endif; ?>

						<?php if(
							$this->item->params->get('itemCategory') ||
							$this->item->params->get('itemTags') ||
							$this->item->params->get('itemAttachments')
							): ?>
							<div class="itemLinks">

								<?php if($this->item->params->get('itemCategory')): ?>
									<!-- Item category -->
									<div class="itemCategory">
										<span><?php echo JText::_('K2_PUBLISHED_IN'); ?></span>
										<a href="<?php echo $this->item->category->link; ?>"><?php echo $this->item->category->name; ?></a>
									</div>
								<?php endif; ?>

								<?php if($this->item->params->get('itemAttachments') && count($this->item->attachments)): ?>
									<!-- Item attachments -->
									<div class="itemAttachmentsBlock">
										<span><?php echo JText::_('K2_DOWNLOAD_ATTACHMENTS'); ?></span>
										<ul class="itemAttachments">
											<?php foreach ($this->item->attachments as $attachment): ?>
												<li>
													<a title="<?php echo K2HelperUtilities::cleanHtml($attachment->titleAttribute); ?>" href="<?php echo $attachment->link; ?>"><?php echo $attachment->title; ?></a>
													<?php if($this->item->params->get('itemAttachmentsCounter')): ?>
														<span>(<?php echo $attachment->hits; ?> <?php echo ($attachment->hits==1) ? JText::_('K2_DOWNLOAD') : JText::_('K2_DOWNLOADS'); ?>)</span>
													<?php endif; ?>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>

								<div class="clr"></div>
							</div>

							<?php if($this->item->params->get('itemTags') && count($this->item->tags)): ?>
								<!-- Item tags -->
								<div class="itemTagsBlock">
									<span><?php echo JText::_('K2_TAGGED_UNDER'); ?></span>
									<ul class="itemTags">
										<?php foreach ($this->item->tags as $tag): ?>
											<li><a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a></li>
										<?php endforeach; ?>
									</ul>
									<div class="clr"></div>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						<!-- Go to www.addthis.com/dashboard to customize your tools -->
						<?php if($this->item->params->get('itemSocialButton') && !is_null($this->item->params->get('socialButtonCode', NULL))): ?>
							<!-- Item Social Button -->
							<div class="itemSocialBlock">
								<strong>Share:</strong> <?php echo $this->item->params->get('socialButtonCode'); ?>
							</div>
						<?php endif; ?>

						<?php if($this->item->params->get('itemAuthorLatest') && empty($this->item->created_by_alias) && isset($this->authorLatestItems)): ?>
							<!-- Latest items from author -->
							<div class="itemAuthorLatest">
								<h3><?php echo JText::_('K2_LATEST_FROM'); ?> <?php echo $this->item->author->name; ?></h3>
								<ul>
									<?php foreach($this->authorLatestItems as $key=>$item): ?>
										<li class="<?php echo ($key%2) ? "odd" : "even"; ?>">
											<a href="<?php echo $item->link ?>"><?php echo $item->title; ?></a>
										</li>
									<?php endforeach; ?>
								</ul>
								<div class="clr"></div>
							</div>
						<?php endif; ?>

						<div class="clr"></div>

						<?php if($this->item->params->get('itemImageGallery') && !empty($this->item->gallery)): ?>
							<!-- Item image gallery -->
							<a name="itemImageGalleryAnchor" id="itemImageGalleryAnchor"></a>
							<div class="itemImageGallery">
								<h3><?php echo JText::_('K2_IMAGE_GALLERY'); ?></h3>
								<?php echo $this->item->gallery; ?>
							</div>
						<?php endif; ?>

						<?php if($this->item->params->get('itemNavigation') && !JRequest::getCmd('print') && (isset($this->item->nextLink) || isset($this->item->previousLink))): ?>
							<!-- Item navigation -->
							<div class="itemNavigation">
								<span class="itemNavigationTitle"><?php echo JText::_('K2_MORE_IN_THIS_CATEGORY'); ?></span>

								<?php if(isset($this->item->previousLink)): ?>
									<a class="itemPrevious" href="<?php echo $this->item->previousLink; ?>">&laquo; <?php echo $this->item->previousTitle; ?></a>
								<?php endif; ?>

								<?php if(isset($this->item->nextLink)): ?>
									<a class="itemNext" href="<?php echo $this->item->nextLink; ?>"><?php echo $this->item->nextTitle; ?> &raquo;</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<!-- Plugins: AfterDisplayContent -->
						<?php echo $this->item->event->AfterDisplayContent; ?>

						<!-- K2 Plugins: K2AfterDisplayContent -->
						<?php echo $this->item->event->K2AfterDisplayContent; ?>

						<div class="clr"></div>
					</div>						
				</div>
				<!--
				<div role="tabpanel" class="tab-pane" id="tab-lessons">
					
				</div>
				-->
				<div role="tabpanel" class="tab-pane" id="tab-reviews">
					<?php if($this->item->params->get('itemAuthorBlock') && empty($this->item->created_by_alias)): ?>
						<!-- Author Block -->
						<div class="itemAuthorBlock">
							<?php if($this->item->params->get('itemAuthorImage') && !empty($this->item->author->avatar)): ?>
								<img class="itemAuthorAvatar" src="<?php echo $this->item->author->avatar; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($this->item->author->name); ?>" />
							<?php endif; ?>

							<div class="itemAuthorDetails">
								<h3 class="itemAuthorName">
									<a rel="author" href="<?php echo $this->item->author->link; ?>"><?php echo $this->item->author->name; ?></a>
								</h3>

								<?php if($this->item->params->get('itemAuthorDescription') && !empty($this->item->author->profile->description)): ?>
									<?php echo $this->item->author->profile->description; ?>
								<?php endif; ?>

								<?php if($this->item->params->get('itemAuthorURL') && !empty($this->item->author->profile->url)): ?>
									<span class="itemAuthorUrl"><i class="k2icon-globe"></i> <a rel="me" href="<?php echo $this->item->author->profile->url; ?>" target="_blank"><?php echo str_replace('http://','',$this->item->author->profile->url); ?></a></span>
								<?php endif; ?>

								<?php if($this->item->params->get('itemAuthorURL') && !empty($this->item->author->profile->url) && $this->item->params->get('itemAuthorEmail')): ?>
									<span class="k2HorizontalSep">|</span>
								<?php endif; ?>

								<?php if($this->item->params->get('itemAuthorEmail')): ?>
									<span class="itemAuthorEmail"><i class="k2icon-envelope"></i> <?php echo JHTML::_('Email.cloak', $this->item->author->email); ?></span>
								<?php endif; ?>

								<div class="clr"></div>

								<!-- K2 Plugins: K2UserDisplay -->
								<?php echo $this->item->event->K2UserDisplay; ?>

								<div class="clr"></div>
							</div>
							<div class="clr"></div>
						</div>
					<?php endif; ?>
					<ul class="itemCommentsList">
						<?php foreach ($this->item->comments as $key=>$comment): ?>
							<li class="<?php echo ($key%2) ? "odd" : "even"; echo (!$this->item->created_by_alias && $comment->userID==$this->item->created_by) ? " authorResponse" : ""; echo($comment->published) ? '':' unpublishedComment'; ?>">
								<div class="c-avatar">
									<?php if($comment->userImage): ?>
										<img src="<?php echo $comment->userImage; ?>" alt="<?php echo JFilterOutput::cleanText($comment->userName); ?>" width="<?php echo $this->item->params->get('commenterImgWidth'); ?>" />
									<?php endif; ?>
								</div>

								<div class="c-content">
									<span class="commentAuthorName">
										<?php if(!empty($comment->userLink)): ?>
											<a href="<?php echo JFilterOutput::cleanText($comment->userLink); ?>" title="<?php echo JFilterOutput::cleanText($comment->userName); ?>" target="_blank" rel="nofollow"><?php echo $comment->userName; ?></a>
										<?php else: ?>
											<?php echo $comment->userName; ?>
										<?php endif; ?>
									</span>

									<span class="commentDate"><?php echo JHTML::_('date', $comment->commentDate, JText::_('K2_DATE_FORMAT_LC2')); ?></span>

									<p><?php echo $comment->commentText; ?></p>

									<?php if(
										$this->inlineCommentsModeration ||
										($comment->published && ($this->params->get('commentsReporting')=='1' || ($this->params->get('commentsReporting')=='2' && !$this->user->guest)))
										): ?>
										<span class="commentToolbar">
											<?php if($this->inlineCommentsModeration): ?>
												<?php if(!$comment->published): ?>
													<a class="commentApproveLink" href="<?php echo JRoute::_('index.php?option=com_k2&view=comments&task=publish&commentID='.$comment->id.'&format=raw')?>"><?php echo JText::_('K2_APPROVE')?></a>
												<?php endif; ?>

												<a class="commentRemoveLink" href="<?php echo JRoute::_('index.php?option=com_k2&view=comments&task=remove&commentID='.$comment->id.'&format=raw')?>"><?php echo JText::_('K2_REMOVE')?></a>
											<?php endif; ?>

											<?php if($comment->published && ($this->params->get('commentsReporting')=='1' || ($this->params->get('commentsReporting')=='2' && !$this->user->guest))): ?>
												<a data-k2-modal="iframe" href="<?php echo JRoute::_('index.php?option=com_k2&view=comments&task=report&commentID='.$comment->id)?>"><?php echo JText::_('K2_REPORT')?></a>
											<?php endif; ?>

											<?php if($comment->reportUserLink): ?>
												<a class="k2ReportUserButton" href="<?php echo $comment->reportUserLink; ?>"><?php echo JText::_('K2_FLAG_AS_SPAMMER'); ?></a>
											<?php endif; ?>
										</span>
									<?php endif; ?>
								</div>
								<div class="clr"></div>
							</li>
						<?php endforeach; ?>
					</ul>

					<!-- Comments Pagination -->
					<div class="itemCommentsPagination">
						<?php echo $this->pagination->getPagesLinks(); ?>
						<div class="clr"></div>
					</div>
					<?php if(
						$this->item->params->get('commentsFormPosition')=='below' &&
						$this->item->params->get('itemComments') &&
						!JRequest::getInt('print') &&
						($this->item->params->get('comments') == '1' || ($this->item->params->get('comments') == '2' && K2HelperPermissions::canAddComment($this->item->catid)))
						): ?>
						<!-- Item comments form -->
						<div class="itemCommentsForm">
							<?php echo $this->loadTemplate('comments_form'); ?>
						</div>
					<?php endif; ?>

					<?php $user = JFactory::getUser(); if($this->item->params->get('comments') == '2' && $user->guest): ?>
					<div class="itemCommentsLoginFirst"><?php echo JText::_('K2_LOGIN_TO_POST_COMMENTS'); ?></div>
				<?php endif; ?>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-trailer">
				<?php if($this->item->params->get('itemVideo') && !empty($this->item->video)): ?>
					<!-- Item video -->
					<a name="itemVideoAnchor" id="itemVideoAnchor"></a>
					<div class="itemVideoBlock">
						<h3><?php echo JText::_('K2_MEDIA'); ?></h3>

						<?php if($this->item->videoType=='embedded'): ?>
							<div class="itemVideoEmbedded">
								<?php echo $this->item->video; ?>
							</div>
						<?php else: ?>
							<span class="itemVideo"><?php echo $this->item->video; ?></span>
						<?php endif; ?>

						<?php if($this->item->params->get('itemVideoCaption') && !empty($this->item->video_caption)): ?>
							<span class="itemVideoCaption"><?php echo $this->item->video_caption; ?></span>
						<?php endif; ?>

						<?php if($this->item->params->get('itemVideoCredits') && !empty($this->item->video_credits)): ?>
							<span class="itemVideoCredits"><?php echo $this->item->video_credits; ?></span>
						<?php endif; ?>

						<div class="clr"></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php 
//echo "<pre>";
//var_dump(isset($this->relatedItems));die;
//echo "</pre>";
if($this->item->params->get('itemRelated') && isset($this->relatedItems)): ?>
<div class="col-md-12">
	<!-- Related items by tag -->
	<div class="itemRelated">
		<h3><?php echo JText::_("K2_RELATED_ITEMS_BY_TAG"); ?></h3>
		<div class="related-items">
			<?php foreach($this->relatedItems as $key=>$item): ?>
				<div class="related-item <?php echo ($key%2) ? "odd" : "even"; ?>">

					<?php if($this->item->params->get('itemRelatedImageSize')): ?>

						<?php 
                                //Create placeholder items images

						$src = $item->image;
						if (!empty( $src)) {								
							$thumb_img = '<img class="itemRelImg" src="'.$src.'" alt="'.K2HelperUtilities::cleanHtml($item->title).'" />';
						} else if ($is_placehold) {					
							$thumb_img = yt_placehold($placehold_size['k2_courses_itemrelated'],$this->item->title,$this->item->title);
						}	
						echo $thumb_img;
						?>

					<?php endif; ?>

					<div class="detail-bottom">
						<?php if($this->item->params->get('itemRelatedTitle', 1)): ?>
							<a class="itemRelTitle" href="<?php echo $item->link ?>"><?php echo limit_text($item->title,7); ?></a>
						<?php endif; ?>

						<?php if($this->item->params->get('itemRelatedIntrotext')): ?>
							<div class="itemRelIntrotext"><?php echo limit_text($item->introtext, 17); ?></div>
						<?php endif; ?>

						<?php if($this->item->params->get('itemRelatedFulltext')): ?>
							<div class="itemRelFulltext"><?php echo $item->fulltext; ?></div>
						<?php endif; ?>

						<p class="readItemTitle">
							<a class="readmore-itemtitle" href="<?php echo $item->link ?>">.</a>
						</p>
					</div>
				</div>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
	</div>  
</div>
<?php endif; ?>
</div>
<div class="clr"></div>

</div>
<!-- End K2 Item Layout -->

<!-- Related Items -->
<script>// <![CDATA[
jQuery(document).ready(function($) {
		$('.related-items').owlCarousel({
			pagination: false,
			center: false,
			nav: true,
			dots: false,
			loop: false,
			margin: 26,
			//rtl: true,
			slideBy: 1,
			autoplay: false,
			autoplayTimeout: 2500,
			autoplayHoverPause: true,
			autoplaySpeed: 800,
			startPosition: 0,
            navText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
			responsive:{
				0:{
					items:1
				},
				480:{
					items:1
				},
				768:{
					items:2
				},
				1200:{
					items:4
				}
			}
		});	  
	});
// ]]>
</script>