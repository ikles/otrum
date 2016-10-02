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

JHtml::stylesheet('modules/'.$module->module.'/assets/css/mod_sj_k2_categories.css');
JHtml::script('modules/'.$module->module.'/assets/js/jquery.sj_accordion.js');
JHtml::script('modules/'.$module->module.'/assets/js/jquery.imagesloaded.js');

ImageHelper::setDefault($params);
$uniqued='sj_k2_categories_'.rand().time();
$options=$params->toObject();?>

    <div id="<?php echo $uniqued; ?>" class="sj_k2_categories <?php echo $options->deviceclass_sfx; ?>">
    <?php if(!empty($options->pretext)){?>
    	<p class="intro_text"><?php echo $options->pretext; ?></p>
    <?php }?>
            <div class="cat-wrap theme2">
                <?php $i = 0;
                	foreach ($list as $key=>$items){
					$i ++;
                	$cat_child = $items->categoriesChild;?>
				<div class="content-box">
			       	<div class="parent-cat">
						<?php if ($options->cat_title_display == 1){ ?>
						<div class="cat-title">
							<a href="<?php echo $items->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> >
								<?php echo  $items->title;?>
							</a>
						</div>
						<?php }	?>
						<?php
						$img = SjK2CategoriesHelper::getK2CImage($items, $params);
						if ($img){ ?>
						<div class="image_cat">
							<a href="<?php echo $items->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> >
								<img class="categories-loadimage" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"  src="<?php echo ImageHelper::init($img)->src(); ?>"  style="display:none;" />
								<?php echo SjK2CategoriesHelper::imageTag($img);?>
							</a>
						</div>
						<?php } ?>
					</div>	
					<div class="child-cat">
						<?php if (!empty($cat_child)) {
						foreach ($cat_child as $key1=>$item) {
							$item->articlesCount = count(SjK2CategoriesHelper::getItems($item->id, $params));
						?>
						<?php if ($options->cat_sub_title_display == 1){ ?>
						<div class="arrow"></div>
						<div class="child-cat-title">
							<a href="<?php echo $item->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?>>
							<?php echo SjK2CategoriesHelper::truncate($item->title, $options->cat_title_max_characs);?><?php if ($options->cat_all_article ==1) {echo '&nbsp;&nbsp;('.$item->articlesCount.')';}?>
							</a>
						</div>
						<?php }}} else {?>
						<p style="padding:0; margin: 0; display:block;width:100%;float:left;">
						<?php echo JText::_('No sub-categories to show!'); }  ?>
						</p>
			 		</div>
				</div>
		    	<?php
		    		$clear = 'clr1';
		    		if ($i % 2 == 0) $clear .= ' clr2';
		    		if ($i % 3 == 0) $clear .= ' clr3';
		    		if ($i % 4 == 0) $clear .= ' clr4';
		    		if ($i % 5 == 0) $clear .= ' clr5';
		    		if ($i % 6 == 0) $clear .= ' clr6';
		    	?>
		    	<div class="<?php echo $clear; ?>"></div>				
			<?php }?>
			
			</div>
        <?php if(!empty($options->posttext)){?>
        	<p class="footer_text"><?php echo $options->posttext; ?></p>
   		<?php }?>
    </div>
 
 