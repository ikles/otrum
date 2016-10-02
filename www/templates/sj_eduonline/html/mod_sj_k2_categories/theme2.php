<?php
/**
 * @package Sj K2 Categories
 * @version 2.5
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;
$doc = JFactory::getDocument();
JHtml::stylesheet('modules/'.$module->module.'/assets/css/mod_sj_k2_categories.css');
JHtml::script('modules/'.$module->module.'/assets/js/jquery.sj_accordion.js');
JHtml::script('modules/'.$module->module.'/assets/js/jquery.imagesloaded.js');
$yt_temp = JFactory::getApplication()->getTemplate();
JHtml::stylesheet(JUri::base() . '/templates/'.$yt_temp.'/asset/owl.carousel/owl.carousel.css','text/css',"screen");
JHtml::_('jquery.framework');
JHtml::script(JUri::base() . '/templates/'.$yt_temp.'/asset/owl.carousel/owl.carousel.min.js');

ImageHelper::setDefault($params);
$uniqued='sj_k2_categories_'.rand().time();
$options=$params->toObject();?>

<div id="<?php echo $uniqued; ?>" class="sj_k2_categories <?php echo $options->deviceclass_sfx; ?>">
<?php if(!empty($options->pretext)){?>
	<p class="intro_text"><?php echo $options->pretext; ?></p>
<?php }?>
        <div class="cat-wrap theme2">
            <?php $i = 0;
				$items = array_shift($list);
            	//foreach ($list as $key=>$items){
				//$i ++;
            	$cat_child = $items->categoriesChild;?>
				<div class="cat-title">
					<?php echo  $items->title;?>
				</div>
				<div class="items-child">
					<?php if (!empty($cat_child)) {
						foreach ($cat_child as $key1=>$item) {
								$item->articlesCount = count(SjK2CategoriesHelper::getItems($item->id, $params));
								$imgs = SjK2CategoriesHelper::getK2CImage($item, $params);
								$i ++;
							?>
							<?php if ($options->cat_sub_title_display == 1){ ?>
								<?php if ($i % 2 != 0) {?>
								<div class="ct-item"> 
								<?php } ?>
								<div class="item-child">
									<div class="child-image">
										<?php echo SjK2CategoriesHelper::imageTag($imgs);?>
									</div>
									<a class="cat-itemtitle" href="<?php echo $item->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?>>
									<?php echo SjK2CategoriesHelper::truncate($item->title, $options->cat_title_max_characs);?>
									</a>
									<div class="toltal-readmore">
										<?php if ($options->cat_all_article ==1) { ?>
										<span class="toltal-count">
											<?php echo $item->articlesCount; echo '&nbsp;'.JText::_('TEMPLATE_COURSES_CATEGORY'); ?>
										</span>
										<?php }?>
										<a class="cat-readmore" href="<?php echo $item->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?>>
										<?php echo JText::_('CONTENT_READ_MORE'); ?>
										</a>
									</div>
								</div>
								<?php if ($i % 2 == 0) {echo "</div>";} ?>
							<?php }
						}
					}
					else {?>
					<p style="padding:0; margin: 0; display:block;width:100%;float:left;">
					<?php echo $items->title.'&nbsp;'; echo JText::_('no sub-categories to show!');?>
					</p>
					<?php } ?>
					
							
				<?php //}?>
			
				</div>
			<div class="cat-all">
				<a href="<?php echo $items->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> >
					<?php echo JText::_('TEMPLATE_BROWSE_ALL_CATEGORY'); ?>
				</a>
			</div>
			<?php if(!empty($options->posttext)){?>
			<p class="footer_text"><?php echo $options->posttext; ?></p>
			<?php }?>
		</div>
 </div>
 <!-- Items -->
<script>// <![CDATA[
jQuery(document).ready(function($) {
		$('.items-child').owlCarousel({
			pagination: false,
			center: false,
			nav: true,
			dots: false,
			loop: false,
			margin: 0,
			<?php if($doc->direction == 'rtl') {
				echo "rtl: true,";
			} ?>
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
					items:3
				},
				1200:{
					items:4
				}
			}
		});	  
	});
// ]]>
</script>