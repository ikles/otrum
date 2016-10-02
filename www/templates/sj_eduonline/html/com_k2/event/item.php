<?php
/**
 * @version		2.6.x
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// includes placehold
$yt_temp = JFactory::getApplication()->getTemplate();
include (JPATH_BASE . '/templates/'.$yt_temp.'/includes/placehold.php');
/*includes countdown
$doc 	= JFactory::getDocument();
$doc->addScript($this->baseurl .'/templates/'.$yt_temp.'/js/jquery.plugin.js');
$doc->addScript($this->baseurl .'/templates/'.$yt_temp.'/js/jquery.countdown.js');
*/
?>

<?php if(JRequest::getInt('print')==1): ?>
<!-- Print button at the top of the print page only -->
<a class="itemPrintThisPage" rel="nofollow" href="#" onclick="window.print();return false;">
	<span><?php echo JText::_('K2_PRINT_THIS_PAGE'); ?></span>
</a>
<?php endif; ?>

<!-- Start K2 Item Layout -->
<span id="startOfPageId<?php echo JRequest::getInt('id'); ?>"></span>

<div id="k2Container" class="itemView item-event<?php if($this->item->params->get('pageclass_sfx')) echo ' '.$this->item->params->get('pageclass_sfx'); ?>">

	<!-- Plugins: BeforeDisplay -->
	<?php echo $this->item->event->BeforeDisplay; ?>

	<!-- K2 Plugins: K2BeforeDisplay -->
	<?php echo $this->item->event->K2BeforeDisplay; ?>
	<?php if(
		
		$this->item->params->get('itemSocialButton') ||
		$this->item->params->get('itemVideoAnchor') ||
		$this->item->params->get('itemImageGalleryAnchor') ||
		$this->item->params->get('itemCommentsAnchor')
	): ?>

	<div class="date-vote-thor">
		<div class="action">
			<?php if($this->item->params->get('itemDateCreated')): ?>
			<!-- Date created -->
			<div class="itemDateCreated me-inline">
				<i class="fa fa-clock-o"></i> <?php echo JHTML::_('date', $this->item->created , JText::_('d F Y')); ?>
			</div>
			<?php endif; ?>
			<?php if($this->item->params->get('itemRating')): ?>
			<!-- Item Rating -->
			<div class="itemRatingBlock me-inline">
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
			<?php endif; ?>
			<?php if($this->item->params->get('itemAuthor')): ?>
			<!-- Item Author -->
			<div class="itemAuthor me-inline">
				<?php echo JText::_('K2_ITEM_AUTHOR'); ?>&nbsp;
				<?php if(empty($this->item->created_by_alias)): ?>
				<a rel="author" href="<?php echo $this->item->author->link; ?>"><?php echo $this->item->author->name; ?></a>
				<?php else: ?>
				<?php echo $this->item->author->name; ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			
		</div>	
		
	</div>
	<?php endif; ?>
	
		
	<?php if($this->item->params->get('itemVideo') && !empty($this->item->video)): ?>
	<!-- Item video -->
	<div class="itemVideoBlock">
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
	<?php else: ?>

	<?php if($this->item->params->get('itemImage')): ?>
	<!-- Item Image -->
	<div class="itemImageBlock">
		<span class="itemImage">
            <a data-k2-modal="image" href="<?php echo $this->item->imageXLarge; ?>" title="<?php echo JText::_('K2_CLICK_TO_PREVIEW_IMAGE'); ?>">
                <?php 
					//Create placeholder items images
				
					$src = $this->item->image;
					if (!empty( $src)) {								
						$thumb_img = '<img src="'.$src.'" alt="'.$this->item->title.'" />';
					} else if ($is_placehold) {					
						$thumb_img = yt_placehold($placehold_size['k2_event_item'],$this->item->title,$this->item->title);
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
	<?php endif; ?>
	
  <!-- Plugins: AfterDisplayTitle -->
  <?php echo $this->item->event->AfterDisplayTitle; ?>

  <!-- K2 Plugins: K2AfterDisplayTitle -->
  <?php echo $this->item->event->K2AfterDisplayTitle; ?>
  
<div class="itemBody">

	  <!-- Plugins: BeforeDisplayContent -->
	  <?php echo $this->item->event->BeforeDisplayContent; ?>

	  <!-- K2 Plugins: K2BeforeDisplayContent -->
	  <?php echo $this->item->event->K2BeforeDisplayContent; ?>
	<div class="itemHeader">
	<?php if($this->item->params->get('itemTitle')): ?>
	<!-- Item title -->
	<div class="itemTitle">
			<?php if(isset($this->item->editLink)): ?>
			<!-- Item edit link -->
			<span class="itemEditLink">
				<a class="modal no-modal" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $this->item->editLink; ?>">
					<?php echo JText::_('K2_EDIT_ITEM'); ?>
				</a>
			</span>
			<?php endif; ?>
		<h3 class="me-inline">
	  	<?php echo $this->item->title; ?>
		</h3>
	  	<?php if($this->item->params->get('itemFeaturedNotice') && $this->item->featured): ?>
	  	<!-- Featured flag -->
	  	<span>
		  	<sup>
		  		<?php echo JText::_('K2_FEATURED'); ?>
		  	</sup>
	  	</span>
	  	<?php endif; ?>
		<?php if(
		$this->item->params->get('itemFontResizer') ||
		$this->item->params->get('itemPrintButton') ||
		$this->item->params->get('itemEmailButton') 
		): ?>
		<div class="itemToolbar me-inline">
			
			<ul class="icon-item size-mail">
				<?php if($this->item->params->get('itemFontResizer')): ?>
				<!-- Font Resizer -->
				<li class="fontsize">
					<a href="#" id="fontIncrease">
						&#43;
	
					</a>
					<a href="#" id="fontDecrease">
						&ndash;
					</a>
				</li>
				<?php endif; ?>
				<!-- Email Button -->
				<?php if($this->item->params->get('itemEmailButton')): ?>
				<li class="emailbutton">
					<a class="itemEmailLink" rel="nofollow" href="<?php echo $this->item->emailLink; ?>" onclick="window.open(this.href,'emailWindow','width=400,height=350,location=no,menubar=no,resizable=no,scrollbars=no'); return false;">
						<i class="fa fa-envelope-o"></i>
					</a>
				</li>
				<?php endif; ?>
				<?php endif; ?>
	
				<?php if($this->item->params->get('itemPrintButton') && !JRequest::getInt('print')): ?>
				<!-- Print Button -->
				<li class="printbutton">
					<a class="itemPrintLink" rel="nofollow" href="<?php echo $this->item->printLink; ?>" onclick="window.open(this.href,'printWindow','width=900,height=600,location=no,menubar=no,resizable=yes,scrollbars=yes'); return false;">
						<i class="fa fa-print"></i>
					</a>
				</li>
				<?php endif; ?>
			</ul>
			<ul style="text-align: inherit;">
				<?php if($this->item->params->get('itemEmailButton') && !JRequest::getInt('print')): ?>
				
				<?php if($this->item->params->get('itemSocialButton') && !is_null($this->item->params->get('socialButtonCode', NULL))): ?>
				<!-- Item Social Button -->
				<!--<li>
					<?php echo $this->item->params->get('socialButtonCode'); ?>
				</li>-->
				<?php endif; ?>
	
				<?php //if($this->item->params->get('itemVideoAnchor') && !empty($this->item->video)): ?>
				<!-- Anchor link to item video below - if it exists 
				<li>
					<a class="itemVideoLink k2Anchor" href="<?php //echo $this->item->link; ?>#itemVideoAnchor"><?php //echo JText::_('K2_MEDIA'); ?></a>
				</li>
				-->
				<?php //endif; ?>
	
				<?php if($this->item->params->get('itemImageGalleryAnchor') && !empty($this->item->gallery)): ?>
				<!-- Anchor link to item image gallery below - if it exists -->
				<li>
					<a class="itemImageGalleryLink k2Anchor" href="<?php echo $this->item->link; ?>#itemImageGalleryAnchor"><?php echo JText::_('K2_IMAGE_GALLERY'); ?></a>
				</li>
				<?php endif; ?>
	
				
			</ul>
			<div class="clr"></div>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	</div>
	<?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields)): 
				$metas = $this->item->extra_fields;
				
			  ?>
			  <?php if( (isset($metas[0]) && ($metas[0]->value !='')) || (isset($metas[1]) && ($metas[1]->value !=''))):?>
				<ul class="timevent-address">
					<?php if( (isset($metas[0]) && ($metas[0]->value !=''))):?>
					<li class="time-of-event"><i class="fa fa-clock-o"></i><?php echo $metas[0]->value; ?></li>
					<?php endif; ?>
					<?php if( (isset($metas[1]) && ($metas[1]->value !=''))):?>
					<li class="address"> <i class="fa fa-map-marker"></i><?php echo $metas[1]->value; ?></li>
					<?php endif; ?>
					<?php if( (isset($metas[2]) && ($metas[2]->value !=''))):?>
					<li class="phone"><i class="fa fa-phone"></i><?php echo $metas[2]->value; ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
			 <?php if(isset($metas[4]) && ($metas[4]->value !='')):
					$full_date = JHTML::_('date', $metas[4]->value, 'j-m-Y');
					$event_date = explode("-", $full_date);
					$year_end = $event_date[2];
					$month_end = $event_date[1];
					$day_end = $event_date[0];
					
			      ?>
			      <script type="text/javascript"><!--
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
				//--></script>
				<div id="countdown-<?php echo $this->item->id;?>" class="countdown-event"></div>
			<?php endif; ?>
		<?php endif; ?>
	<?php if($this->item->params->get('itemIntroText')): ?>
	  <!-- Item introtext -->
	  <div class="itemIntroText">
	  	<?php echo $this->item->introtext; ?>
	  </div>
	  <?php endif; ?>
	  <?php if($this->item->params->get('itemFullText')) : ?>
	  <!-- Item fulltext -->
	  <div class="itemFullText">
	  	<?php echo $this->item->fulltext; ?>
	  </div>
	  <?php endif; ?>
	<ul class="addthis-resgis">

		<?php if($this->item->params->get('itemSocialButton') && !is_null($this->item->params->get('socialButtonCode', NULL))): ?>
		<!-- Item Social Button -->
		<li class="itemSocialBlock">
		<strong>Share:</strong> <?php echo $this->item->params->get('socialButtonCode'); ?>
		</li>
		<?php endif; ?>

		<?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields)): 
		$metas = $this->item->extra_fields;
		?>
			<?php if( (isset($metas[3]) && ($metas[3]->value !=''))):?>
			<li class="register-btn"><?php echo $metas[3]->value; ?></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	  <!-- Plugins: AfterDisplayContent -->
	  <?php echo $this->item->event->AfterDisplayContent; ?>

	  <!-- K2 Plugins: K2AfterDisplayContent -->
	  <?php echo $this->item->event->K2AfterDisplayContent; ?>

	  <div class="clr"></div>
  </div>

		
  
  <?php if($this->item->params->get('itemAuthorBlock') && empty($this->item->created_by_alias)): ?>
  <!-- Author Block -->
  <div class="itemAuthorBlock">
	<div class="header-author">
		<?php if($this->item->params->get('itemCommentsAnchor') && $this->item->params->get('itemComments') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1')) ): ?>
		<span class="itemcomment me-inline">
		<!-- Anchor link to comments below - if enabled -->
			<?php if(!empty($this->item->event->K2CommentsCounter)): ?>
			<!-- K2 Plugins: K2CommentsCounter -->
			<?php echo $this->item->event->K2CommentsCounter; ?>
			<?php else: ?>
				<?php if($this->item->numOfComments > 0): ?>
				<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
					 <?php echo $this->item->numOfComments; ?> <?php echo ($this->item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
				</a>
				<?php else: ?>
				<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
					<?php echo JText::_('K2_THE_FIRST_TO_COMMENT'); ?>
				</a>
				<?php endif; ?>
			<?php endif; ?>
		</span>
		<?php endif; ?>
		<?php if($this->item->params->get('itemHits')): ?>
		<!-- Item Hits -->
		
		<span class="itemHits me-inline">
			<?php if($this->item->hits< 2){
				echo $this->item->hits; ?>&nbsp;<?php echo JText::_('K2_VIEW'); 
			}else{
				echo $this->item->hits; ?>&nbsp;<?php echo JText::_('K2_VIEWS'); 
			}
			?>
			
		</span>
		<?php endif; ?>
		<?php if($this->item->params->get('itemTags') && count($this->item->tags)): ?>
		<!-- Item tags -->
		<div class="itemTagsBlock me-inline">
			<span><i class="fa fa-tags"></i><?php echo JText::_('K2_TAGGED'); ?></span>
			<ul class="itemTags">
			  <?php foreach ($this->item->tags as $tag): ?>
			  <li><a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a></li>
			  <?php endforeach; ?>
			</ul>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

	</div>
	<div class="line"></div>
  	<?php if($this->item->params->get('itemAuthorImage') && !empty($this->item->author->avatar)): ?>
	<div class="itemAuthorAvatar">
		<img src="<?php echo $this->item->author->avatar; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($this->item->author->name); ?>" />
  	</div>
	<?php endif; ?>
	

    <div class="itemAuthorDetails">
      <h3 class="itemAuthorName">
      	<a rel="author" href="<?php echo $this->item->author->link; ?>"><?php echo $this->item->author->name; ?></a>
      </h3>

      <?php if($this->item->params->get('itemAuthorDescription') && !empty($this->item->author->profile->description)): ?>
      <p><?php echo $this->item->author->profile->description; ?></p>
      <?php endif; ?>

      <?php if($this->item->params->get('itemAuthorURL') && !empty($this->item->author->profile->url)): ?>
      <span class="itemAuthorUrl"><?php echo JText::_('K2_WEBSITE'); ?> <a rel="me" href="<?php echo $this->item->author->profile->url; ?>" target="_blank"><?php echo str_replace('http://','',$this->item->author->profile->url); ?></a></span>
      <?php endif; ?>

      <?php if($this->item->params->get('itemAuthorEmail')): ?>
      <span class="itemAuthorEmail"><?php echo JText::_('K2_EMAIL'); ?> <?php echo JHTML::_('Email.cloak', $this->item->author->email); ?></span>
      <?php endif; ?>

			<div class="clr"></div>

			<!-- K2 Plugins: K2UserDisplay -->
			<?php echo $this->item->event->K2UserDisplay; ?>

    </div>
    <div class="clr"></div>
  </div>
  <?php endif; ?>
 <?php if($this->item->params->get('itemAuthorLatest') && empty($this->item->created_by_alias) && isset($this->authorLatestItems)): ?>
  <!-- Latest items from author -->
	<div class="itemAuthorLatest">
		<h3><?php echo JText::_('K2_LATEST_FROM'); ?>&nbsp;<?php echo $this->item->author->name; ?></h3>
		<div class="line"></div>
		<ul>
			<?php foreach($this->authorLatestItems as $key=>$item): ?>
			<li class="item">
				<i class="fa fa-angle-right"></i><a href="<?php echo $item->link ?>"><?php echo $item->title; ?></a>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="clr"></div>
	</div>
	<?php endif; ?>
	<?php
	/*
	Note regarding 'Related Items'!
	If you add:
	- the CSS rule 'overflow-x:scroll;' in the element div.itemRelated {…} in the k2.css
	- the class 'k2Scroller' to the ul element below
	- the classes 'k2ScrollerElement' and 'k2EqualHeights' to the li element inside the foreach loop below
	- the style attribute 'style="width:<?php echo $item->imageWidth; ?>px;"' to the li element inside the foreach loop below
	...then your Related Items will be transformed into a vertical-scrolling block, inside which, all items have the same height (equal column heights). This can be very useful if you want to show your related articles or products with title/author/category/image etc., which would take a significant amount of space in the classic list-style display.
	*/
	?>

  <?php if($this->item->params->get('itemRelated') && isset($this->relatedItems)): ?>
  <!-- Related items by tag -->
	<div class="itemRelated">
		<h3 class="title-related"><?php echo JText::_("K2_RELATED_ITEMS_BY_TAG"); ?></h3>
		<ul class="items">
			<?php foreach($this->relatedItems as $key=>$item): ?>
			<li class="item">
				<div class="item-images">
				<?php
					
					$src_rela = $item->imageLarge;
					$title_rela= K2HelperUtilities::cleanHtml($item->title);
					if($this->item->params->get('itemRelatedImageSize')):
					//Create placeholder items images
					if (!empty( $src_rela)) {								
					  $thumb_img = '<img src="'.$src_rela.'" alt="'.$title_rela.'" />';
					} else if ($is_placehold) {					
					  $thumb_img = yt_placehold($placehold_size['related_items'],$this->item->title);
					}	
					echo $thumb_img;
					endif;
				?>
				</div>
				<?php if($this->item->params->get('itemRelatedTitle', 1)): ?>
				<h3 class="itemRelTitle" ><a href="<?php echo $item->link ?>"><?php echo $item->title; ?></a></h3>
				<?php endif; ?>
				<?php if( $this->item->params->get('itemRelatedCategory') || $this->item->params->get('itemRelatedAuthor') || $this->item->params->get('itemRelatedIntrotext')
				|| $this->item->params->get('itemRelatedFulltext') || $this->item->params->get('itemRelatedMedia') || $this->item->params->get('itemRelatedImageGallery')): ?>
				<aside class="article-aside">
					<dl class="article-info  muted">
						<dt></dt>
						<!-- Date created -->
						<?php if($this->item->params->get('catItemDateCreated')): ?>
						<dd class="create"><i class="fa fa-clock-o"></i><?php echo JHTML::_('date', $this->item->created , 'j M Y'); ?></dd>		
						<?php endif; ?>
						<?php if($this->item->params->get('itemRelatedCategory')): ?>
						<dd class="catItemCategory"><a href="<?php echo $item->category->link ?>"><i class="fa fa-folder"></i><?php echo $item->category->name; ?></a></dd>
						<?php endif; ?>
						<?php if($this->item->params->get('itemRelatedAuthor')): ?>
						<dd class="catItemAuthor"> <a rel="author" href="<?php echo $item->author->link; ?>"><i class="fa fa-user"></i><?php echo $item->author->name; ?></a></dd>
						<?php endif; ?>
						
					</dl>
				</aside>
				<?php endif; ?>
				<?php if($this->item->params->get('itemRelatedIntrotext')): ?>
				<dd class="itemRelIntrotext"><?php echo $item->introtext; ?></dd>
				<?php endif; ?>

				<?php if($this->item->params->get('itemRelatedFulltext')): ?>
				<dd class="itemRelFulltext"><?php echo $item->fulltext; ?></dd>
				<?php endif; ?>

				<?php if($this->item->params->get('itemRelatedMedia')): ?>
				<?php if($item->videoType=='embedded'): ?>
				<dd class="itemRelMediaEmbedded"><?php echo $item->video; ?></dd>
				<?php else: ?>
				<dd class="itemRelMedia"><?php echo $item->video; ?></dd>
				<?php endif; ?>
				<?php endif; ?>

				<?php if($this->item->params->get('itemRelatedImageGallery')): ?>
				<dd class="itemRelImageGallery"><?php echo $item->gallery; ?></dd>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
			<li class="clr"></li>
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

  <!-- Plugins: AfterDisplay -->
  <?php echo $this->item->event->AfterDisplay; ?>

  <!-- K2 Plugins: K2AfterDisplay -->
  <?php echo $this->item->event->K2AfterDisplay; ?>

  <?php if($this->item->params->get('itemComments') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1'))): ?>
  <!-- K2 Plugins: K2CommentsBlock -->
  <?php echo $this->item->event->K2CommentsBlock; ?>
  <?php endif; ?>

 <?php if($this->item->params->get('itemComments') && ($this->item->params->get('comments') == '1' || ($this->item->params->get('comments') == '2')) && empty($this->item->event->K2CommentsBlock)): ?>
  <!-- Item comments -->
  <a name="itemCommentsAnchor" id="itemCommentsAnchor"></a>

  <div class="itemComments">

	  <?php if($this->item->params->get('commentsFormPosition')=='above' && $this->item->params->get('itemComments') && !JRequest::getInt('print') && ($this->item->params->get('comments') == '1' || ($this->item->params->get('comments') == '2' && K2HelperPermissions::canAddComment($this->item->catid)))): ?>
	  <!-- Item comments form -->
	  <div class="itemCommentsForm">
	  	<?php echo $this->loadTemplate('comments_form'); ?>
	  </div>
	  <?php endif; ?>

	  <?php if($this->item->numOfComments>0 && $this->item->params->get('itemComments') && ($this->item->params->get('comments') == '1' || ($this->item->params->get('comments') == '2'))): ?>
	  <!-- Item user comments -->
	  <h3 class="itemCommentsCounter">
	  	<?php echo JText::_('K2_COMMENT_ITEM'); ?>
	  </h3>
	  <div class="line"></div>

	  <ul class="itemCommentsList">
	    <?php foreach ($this->item->comments as $key=>$comment): ?>
	    <li class="<?php echo ($key%2) ? "odd" : "even"; echo (!$this->item->created_by_alias && $comment->userID==$this->item->created_by) ? " authorResponse" : ""; echo($comment->published) ? '':' unpublishedComment'; ?>">

	    	<span class="commentLink">
		    	<a href="<?php echo $this->item->link; ?>#comment<?php echo $comment->id; ?>" name="comment<?php echo $comment->id; ?>" id="comment<?php echo $comment->id; ?>">
		    		<?php //echo JText::_('K2_COMMENT_LINK'); ?>
		    	</a>
		    </span>
			<?php if($comment->userImage): ?>
			
			<img src="<?php echo $comment->userImage; ?>" alt="<?php echo JFilterOutput::cleanText($comment->userName); ?>" width="<?php echo $this->item->params->get('commenterImgWidth'); ?>" />
			<?php endif;  ?>
			<span class="commentAuthorName">
			   
			    <?php if(!empty($comment->userLink)): ?>
			    <a href="<?php echo JFilterOutput::cleanText($comment->userLink); ?>" title="<?php echo JFilterOutput::cleanText($comment->userName); ?>" target="_blank" rel="nofollow">
			    	<?php echo $comment->userName; ?>
			    </a>
			    <?php else: ?>
			    <?php echo $comment->userName; ?>
			    <?php endif; ?>
			    
			</span>
			<span class="commentDate">
		    	 <?php echo JHTML::_('date', $this->item->created , JText::_('DATE_FORMAT_LC1')); ?>
			</span>

		    

		    <p><?php echo $comment->commentText; ?></p>

				<?php if($this->inlineCommentsModeration || ($comment->published && ($this->params->get('commentsReporting')=='1' || ($this->params->get('commentsReporting')=='2' && !$this->user->guest)))): ?>
				<span class="commentToolbar">
					<?php if($this->inlineCommentsModeration): ?>
					<?php if(!$comment->published): ?>
					<a class="commentApproveLink" href="<?php echo JRoute::_('index.php?option=com_k2&view=comments&task=publish&commentID='.$comment->id.'&format=raw')?>"><?php echo JText::_('K2_APPROVE')?></a>
					<?php endif; ?>

					<a class="commentRemoveLink" href="<?php echo JRoute::_('index.php?option=com_k2&view=comments&task=remove&commentID='.$comment->id.'&format=raw')?>"><?php echo JText::_('K2_REMOVE')?></a>
					<?php endif; ?>

					<?php if($comment->published && ($this->params->get('commentsReporting')=='1' || ($this->params->get('commentsReporting')=='2' && !$this->user->guest))): ?>
					<a class="modal" rel="{handler:'iframe',size:{x:560,y:480}}" href="<?php echo JRoute::_('index.php?option=com_k2&view=comments&task=report&commentID='.$comment->id)?>"><?php echo JText::_('K2_REPORT')?></a>
					<?php endif; ?>

					<?php if($comment->reportUserLink): ?>
					<a class="k2ReportUserButton" href="<?php echo $comment->reportUserLink; ?>"><?php echo JText::_('K2_FLAG_AS_SPAMMER'); ?></a>
					<?php endif; ?>

				</span>
				<?php endif; ?>

				<div class="clr"></div>
	    </li>
	    <?php endforeach; ?>
	  </ul>

	  <div class="itemCommentsPagination">
	  	<?php echo $this->pagination->getPagesLinks(); ?>
	  	<div class="clr"></div>
	  </div>
		<?php endif; ?>

		<?php if($this->item->params->get('commentsFormPosition')=='below' && $this->item->params->get('itemComments') && !JRequest::getInt('print') && ($this->item->params->get('comments') == '1' || ($this->item->params->get('comments') == '2' && K2HelperPermissions::canAddComment($this->item->catid)))): ?>
	  <!-- Item comments form -->
	  <div class="itemCommentsForm">
	  	<?php echo $this->loadTemplate('comments_form'); ?>
	  </div>
	  <?php endif; ?>

	  <?php $user = JFactory::getUser(); if ($this->item->params->get('comments') == '2' && $user->guest): ?>
	  		<div><?php echo JText::_('K2_LOGIN_TO_POST_COMMENTS'); ?></div>
	  <?php endif; ?>

  </div>
  <?php endif; ?>

	<?php if(!JRequest::getCmd('print')): ?>
	<div class="itemBackToTop">
		<a class="k2Anchor" href="<?php echo $this->item->link; ?>#startOfPageId<?php echo JRequest::getInt('id'); ?>">
			<?php //echo JText::_('K2_BACK_TO_TOP'); ?>
		</a>
	</div>
	<?php endif; ?>

	<div class="clr"></div>
</div>
<div class="page-event-map">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4292.512958841368!2d-71.10096637871857!3d42.34557468094433!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89e379f63dc43ccb%3A0xa15d5aa87d0f0c12!2s4+Yawkey+Way%2C+Boston%2C+MA+02215!5e0!3m2!1svi!2s!4v1465376613865" width="1170" height="450" style="border:0" allowfullscreen></iframe>
</div>
<!-- End K2 Item Layout -->

