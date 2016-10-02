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
$options=$params->toObject();
$uniqued='sj_k2_categories_'.rand().time();?> 

<script type="text/javascript">

//<![CDATA[
jQuery(document).ready(function($) {
	$(window).load(function(){
		$('#<?php echo $uniqued;?>').imagesLoaded( function(){});
		
	});
	$('#<?php echo $uniqued;?>').imagesLoaded( function(){
		
		$('#<?php echo $uniqued;?>').sj_accordion({
			items : '.sj_k2_categories-inner',
			heading : '.sj_k2_categories-heading',
			content : '.sj_k2_categories-content',
			active_class : 'selected',
			event : '<?php echo $params->get('accmouseenter','click'); ?>',
			delay : 300,
			duration : 500,
			active : '1'
		}); 
		
		var height_content = function(){	
			
		  	$('.sj_k2_categories-inner', '#<?php echo $uniqued;?>').each(function(){
		        var $inner = $('.sj_k2_categories-content', $(this).filter('.selected'));
	            if($inner.length){
	                var inner_height = $inner.height();
	                var inner_width = $inner.width();
	                $('.cat-title','#<?php echo $uniqued;?>').css('max-width',inner_width-40);
	                $inner.css('height',inner_height);
	            }
		   });
		}
		if ( $.browser.msie  && parseInt($.browser.version, 10) <= 8){
		//nood
		}else{
      		  $(window).resize(function() {
        		height_content();
        	
       		 });
		}
	}); 
	
}); 
//]]>	

</script>
    <div id="<?php echo $uniqued?>" class="sj_k2_categories">
    <?php if(!empty($options->pretext)){?>
    	<p class="intro_text"><?php echo $options->pretext; ?></p>
    <?php }?>
		<div class="cat-wrap theme4">
	 	<?php $i = 0;
	 		foreach ($list as $key=>$items){ 
			$i++;               
			$cat_child = $items->categoriesChild;
			//var_dump($cat_child);
			?>	
			<div class="sj_k2_categories-inner">
		   		<div class="sj_k2_categories-heading">
					<div class="icon_left"></div>
					<div class="cat-title">
					<?php echo $items->title;?>
					</div>
					<div class="icon_right"></div>
				</div>
				<div class="sj_k2_categories-content cf"> 
			    <?php if (!empty($cat_child)){
			    	$k =0;
				foreach ($cat_child as $key1=>$item) {
					$k++;
						$count = count($cat_child);
					$item->articlesCount = count(SjK2CategoriesHelper::getItems($item->id, $params));
				?>
				<div class="sj_k2_categories-content-inner">
					<div class="child-cat <?php echo ($k == $count)?'cat-lastitem':''; ?>">								
						<div class="child-cat-info">
							<?php $img = SjK2CategoriesHelper::getK2CImage($item, $params);
							if ($img){ ?>
							<div class="image-cat">
								<a href="<?php echo $item->link; ?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> >
									<?php echo SjK2CategoriesHelper::imageTag($img);?>
								</a>
							</div>
							<?php }?>
							<div class="child-cat-desc">										
								<?php if ($options->cat_sub_title_display == 1){ ?>                                  
									<div class="child-cat-title">                                         
										<a href="<?php echo $item->link;?>" <?php echo SjK2CategoriesHelper::parseTarget($options->target);?> >
											<?php echo SjK2CategoriesHelper::truncate($item->title, $options->cat_title_max_characs);?>
										</a>                                                                       
									</div>
								<?php }?>
								<?php if ($options->cat_all_article ==1) {?>
									<div class="num_items" style="float:left;color: #737373;">
								<?php echo '('.$item->articlesCount.')';?>
								</div>  
								<?php }?>        
							</div>
						</div>								
					</div>	
				</div>	
				<?php }}else {?>
					<div class="sj_k2_categories-content-inner">
						<div class="child-cat subcat-empty">								
							<div class="child-cat-info">
						<?php echo JText::_('No sub-categories to show!');?>
							</div>
						</div>
					</div>
					<?php }?>
				</div>
			</div>
		<?php } ?>
		</div>
        <?php if(!empty($options->posttext)){?>
        	<p class="footer_text"><?php echo $options->posttext; ?></p>
   		<?php }?>        
    </div>

 