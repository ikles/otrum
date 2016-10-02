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
	<div id="<?php echo $uniqued ;?>" class="sj_k2_categories <?php echo $options->deviceclass_sfx; ?>">
    <?php if(!empty($options->pretext)){?>
    	<p class="intro_text"><?php echo $options->pretext; ?></p>
    <?php }?>    
            <div class="cat-wrap theme3">
                <?php $j = 0;
	                foreach ($list as $key=>$items){
					$j++; 
	                $cat_child = $items->categoriesChild;?>						 
				<div class="content-box">						
					<?php $img = SjK2CategoriesHelper::getK2CImage($items, $params);
					if ($img){ ?>
					<div class="image-cat">
						<a href="<?php echo $items->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> > 
							<?php echo SjK2CategoriesHelper::imageTag($img);?>
						</a>                                      									
					</div>
					<?php } ?>	
					<?php if ($options->cat_title_display == 1){ ?>
					<div class="cat-title"> 
						<a href="<?php echo $items->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> > 
							<?php echo  $items->title;?> 
						</a>                                    									
					</div>
					<?php }	?>							    
					<div class="child-cat">
					<?php if(!empty($cat_child)){ 
						if ($options->cat_sub_title_display == 1){									
						$i=1; 
						foreach ($cat_child as $key1=>$item) { 
							$count = count($cat_child);?>									
							<div class="child-cat-title">
							<a href="<?php echo $item->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> > 
							<?php if(($i >=1) && ($i < $count)){
								echo SjK2CategoriesHelper::truncate($item->title, $options->cat_title_max_characs).','.'&nbsp;'; 
							}else { echo SjK2CategoriesHelper::truncate($item->title, $options->cat_title_max_characs); }?>
							</a>                                     									
							
							</div>
					<?php $i++; }}}  else {echo JText::_('No sub-categories to show!'); }  ?>
					</div>
				</div>
		    	<?php
		    		$clear = 'clr1';
		    		if ($j % 2 == 0) $clear .= ' clr2';
		    		if ($j % 3 == 0) $clear .= ' clr3';
		    		if ($j % 4 == 0) $clear .= ' clr4';
		    		if ($j % 5 == 0) $clear .= ' clr5';
		    		if ($j % 6 == 0) $clear .= ' clr6';
		    	?>
		    	<div class="<?php echo $clear; ?>"></div>				
				<?php }?>
			</div>
        <?php if(!empty($options->posttext)){?>
        	<p class="footer_text"><?php echo $options->posttext; ?></p>
   		<?php }?>
    </div>
 
 