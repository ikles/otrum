<?php
/**
 * @package SJ Listing Tabs For K2
 * @version 1.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2016 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */


defined('_JEXEC') or die;
ImageHelper::setDefault($params);
$tag_id = 'sj_listing_tabs_'.$instance;
//$tag_id = 'sj_listing_tabs_123';
$options = $params->toObject();
	foreach($list  as $item){ 
		$img = K2ListingTabsHelper::getK2Image($item, $params);
	?>
	
	<?php if(JRequest::getInt('type_js', 1) != 2){?>
	<div id="<?php echo $tag_id; ?>">
	<div class="ltabs-items-selected">
	<div class="sj-k2-responsive-content">
	<?php }?>
	
		<div class="responsive-content-box">
			<div class="responsive-content-box-inner">
				<div class="responsive-content-box-bg">
					<div class="item">
					<?php
			        	$img = K2ListingTabsHelper::getK2Image($item, $params);
			        	if($img){
			        	?>
			       		<div class="item-img">
							<img class="responsive-loadimage" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"  src="<?php echo ImageHelper::init($img)->src(); ?>"  style="display:none;" />		
							<?php echo K2ListingTabsHelper::imageTag($img);?>
				        	<?php if($params->get('itemDateCreated', 1)== 1 || $params->get('itemHits',1) == 1 ) {?>
				      		<div class="item-caption">
				      			<?php if($params->get('itemDateCreated',1) == 1) {?>
				           		<span class="item-date">
				                	<?php echo  JHTML::_('date', $item->created,JText::_('DATE_FORMAT_LC3')) ?>
				             	</span>
				             	<?php }?>
				             	<?php if($params->get('itemHits',1) == 1 || $params->get('itemCommentsCounter',1) == 1) {?>
			             		<span class="item-hit-comment">
					             	<?php if($params->get('itemHits',1) == 1) {?>
					              	<span class="item-hit">
					                	<?php if ((int)$item->hits>1){ ?>
					                		<?php echo $item->hits ?> hits
					                	<?php } else {?>
					                		<?php echo $item->hits ?> hit
					                	<?php }?>
					           		</span>
					           		<?php }?>
					           		
			             		</span>
			             		<?php }?>
							</div>
							<?php } ?>
							<div class="item-img-mask">	</div>
							<div class="item-spacer"></div>
				  		</div>
				  		<?php } ?>
				  		<?php if($params->get('itemTitle',1) == 1){?>
			    		<h4 class="item-title">
			            	<?php echo K2ListingTabsHelper::truncate($item->title, $params->get('itemTitleWordLimit',25)); ?>			
			         	</h4>
			         	<?php } ?>
			         	<?php if($params->get('itemIntroText', 1) == 1 && $item->displayIntrotext !='') {?>
			       		<div class="item-desc">
			       			<?php
								echo K2ListingTabsHelper::truncate($item->displayIntrotext, $params->get('itemIntroTextWordLimit',200));
							?>
			            </div>
			           <?php }?>
			           <?php if($params->get('item_readmore_display', 0) == 1){?>
							<div class="item-readmore">
								<a href="<?php echo $item->link ?>" <?php echo K2ListingTabsHelper::parseTarget($options->item_link_target);?> title="<?php echo $item->title?>" >
									 <?php echo $params->get('item_readmore_text','read more..') ?>
								</a>
							</div>
						<?php } ?>
					</div>
					<div class="responsive-content-box-mask">	
					</div>
					<?php if ($options->item_link_target=='_windowopen'){
						$link = $item->link;
						$link .= (strpos($item->link,'?'))?'&tmpl=component':'?tmpl=component';
						?>
						<a class="mask-img fancybox fancybox.iframe <?php echo ($img)?'':'item-img-mask'?>" data-fancybox-group="gallery" href="<?php echo $link; ?>" title="<?php echo $item->title;?> "></a>
					<?php } else {?>
						<a class="mask-img <?php echo ($img)?'':'item-img-mask'?>" href="<?php echo $item->link ?>" <?php echo K2ListingTabsHelper::parseTarget($options->item_link_target);?> title="<?php echo $item->title?>" ></a>	
					<?php }?>
				</div>
	    	</div>
	    </div>
	<?php if(JRequest::getInt('type_js', 1) != 2){?>
	</div>
	</div>
	</div>
	<?php }?>
	<?php 
	} ?>
