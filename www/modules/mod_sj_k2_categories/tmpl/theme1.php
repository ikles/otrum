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

$uniqued='sj_k2_categories_'.rand().time();
$options=$params->toObject();?>
		<div id="<?php echo $uniqued; ?>" class="sj_k2_categories <?php echo $options->deviceclass_sfx; ?>">
    	<?php if(!empty($options->pretext)){?>
    		<p class="intro_text"><?php echo $options->pretext; ?></p>
    	<?php }?>
            <div class="cat-wrap theme1">
                <?php $i = 0;
                	foreach ($list as $key=>$items){
                	$i++;
					$cat_child = $items->categoriesChild;?>
			  	<div class="content-box">
					<?php if ($options->cat_title_display == 1){ ?>
					<div class="cat-title">
						<a style="font-weight: bold" href="<?php echo $items->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> >
							<?php echo  $items->title;?>
						</a>
					</div>
					<?php } ?>
					<div class="child-cat">
					<?php if(!empty($cat_child)) {
						foreach ($cat_child as $key1=>$item) {
							$item->articlesCount = count(SjK2CategoriesHelper::getItems($item->id, $params));
						?>
					<?php if ($options->cat_sub_title_display == 1){ ?>
						<div class="child-cat-title">
							<a href="<?php echo $item->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?>>
								<?php echo SjK2CategoriesHelper::truncate($item->title, $options->cat_title_max_characs);?>
							</a>
							<?php if ($options->cat_all_article ==1) {?>
							<span class="num_items"><?php echo '('.$item->articlesCount.')';?></span>
							<?php } ?>
						</div>
						
					<?php }}}  else { echo JText::_('No sub-categories to show!'); } ?>
					</div>
		    	</div> <!-- END sub_content -->
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

