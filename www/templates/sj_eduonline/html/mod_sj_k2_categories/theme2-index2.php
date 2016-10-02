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
            <?php $i = 0; $j = 0;
				$items = array_shift($list);
            	//foreach ($list as $key=>$items){
				//$i ++;
            	$cat_child = $items->categoriesChild;?>
			<div class="items-child">
				<?php if (!empty($cat_child)) {
				foreach ($cat_child as $key1=>$item) {
					$item->articlesCount = count(SjK2CategoriesHelper::getItems($item->id, $params));
					$imgs = SjK2CategoriesHelper::getK2CImage($item, $params);
					$i ++;
				?>
				<?php if ($options->cat_sub_title_display == 1){ ?>
				<?php if ($i % 2 != 0) { $j ++; $m = ($j % 2 != 0) ? 'old'.'':'event'; ?>
					<div class="ct-item col-sm-3 ct-<?php echo $m; ?>"> 
				<?php } ?>
				<div class="item-child">
					<div class="child-image">
						<?php echo SjK2CategoriesHelper::imageTag($imgs);?>
					</div>
					<a class="cat-itemtitle" href="<?php echo $item->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?>>
					<?php echo SjK2CategoriesHelper::truncate($item->title, $options->cat_title_max_characs);?>
					</a>
					<?php if ($options->cat_all_article ==1) { ?>
					<div class="toltal-count">
						<?php echo $item->articlesCount; echo '&nbsp;'.JText::_('TEMPLATE_COURSES_CATEGORY'); ?>
					</div>
					<?php }?>
				</div>
				<?php if ($i % 2 == 0) {echo "</div>";} ?>
				<?php }}} else {?>
				<p style="padding:0; margin: 0; display:block;width:100%;float:left;">
				<?php echo $items->title.'&nbsp;'; echo JText::_('no sub-categories to show!'); ?>
				</p>
				<?php }  ?>
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
		<?php //}?>
		
		</div>
    <?php if(!empty($options->posttext)){?>
    	<p class="footer_text"><?php echo $options->posttext; ?></p>
		<?php }?>
</div>