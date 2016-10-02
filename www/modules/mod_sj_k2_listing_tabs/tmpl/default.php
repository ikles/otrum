<?php
/**
 * @package SJ Listing Tabs For K2
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2016 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
if (!empty($list)) {
	$instance = rand() . time();
	JHtml::stylesheet('modules/' . $module->module . '/assets/css/sj-listing-tabs.css');
	if($params->get('type_show') != 'responsive'){
		
		JHtml::stylesheet('modules/' . $module->module . '/assets/css/animate.css');
		if (!defined ('OWL_CAROUSEL'))
		{
			JHtml::stylesheet('modules/' . $module->module . '/assets/css/owl.carousel.css');
			JHtml::script('modules/' . $module->module . '/assets/js/owl.carousel.js');
			define( 'OWL_CAROUSEL', 1 );
		}
	}else{
		JHtml::script('modules/'.$module->module.'/assets/js/jquery.isotope.min.js');
		JHtml::script('modules/'.$module->module.'/assets/js/jquery.infinitescroll.min.js');
		JHtml::script('modules/'.$module->module.'/assets/js/jquery.fancybox.js');
		JHtml::stylesheet('modules/'.$module->module.'/assets/css/jquery.fancybox.css');
		JHtml::stylesheet('modules/'.$module->module.'/assets/css/rescontent.css');
		$uri=JURI::getInstance();
		$uri->setVar('page', '2');
		$uri->setVar('module_id',$module->id);
		$uri->setVar('sj_class_responsive',$instance);
	}
    $tag_id = 'sj_listing_tabs_'.$instance;
    $options = $params->toObject();
    $class_ltabs = 'ltabs00-' . $params->get('nb-column1', 6) . ' ltabs01-' . $params->get('nb-column1', 6) . ' ltabs02-' . $params->get('nb-column2', 4) . ' ltabs03-' . $params->get('nb-column3', 2) . ' ltabs04-' . $params->get('nb-column4', 1)
    ?>
    <!--[if lt IE 9]>
    <div id="<?php echo $tag_id; ?>" class="sj-listing-tabs msie lt-ie9 first-load"><![endif]-->
    <!--[if IE 9]>
    <div id="<?php echo $tag_id; ?>" class="sj-listing-tabs msie first-load"><![endif]-->
    <!--[if gt IE 9]><!-->
    <div id="<?php echo $tag_id; ?>" class="sj-listing-tabs first-load"><!--<![endif]-->
        <?php if (!empty($options->pretext)) { ?>
            <div class="pre-text"><?php echo $options->pretext; ?></div>
        <?php } ?>
        <div class="ltabs-wrap ">

            <!--Begin Tabs-->
            <div class="ltabs-tabs-container" data-delay="<?php echo $params->get('delay', 300); ?>"
                 data-duration="<?php echo $params->get('duration', 600); ?>"
                 data-effect="<?php echo $params->get('effect'); ?>"
                 data-ajaxurl="<?php echo (string)JURI::current(); ?>" data-modid="<?php echo $module->id; ?>">
                <?php require JModuleHelper::getLayoutPath($module->module, $layout . '_tabs'); ?>
            </div>
            <!-- End Tabs-->

            <div class="ltabs-items-container"><!--Begin Items-->
                <?php foreach ($tabs as $index => $items) {
				
				$index = $items->id;
                $child_items = isset($index) ? $index : '';
                $cls_device = $class_ltabs;
                $cls_animate = $params->get('effect');
                $cls = (isset($items->sel) && $items->sel == "sel") ? ' ltabs-items-selected ltabs-items-loaded' : '';
                $cls .= ($index == "*") ? ' items-category-all' : ' items-category-' . $index;
                ?>
				 
                <div class="ltabs-items <?php echo $cls; ?>">
					<?php if ($params->get('type_show') == 'loadmore'){ ?>
                    <div class="ltabs-items-inner <?php echo $cls_device . ' ';
					echo $cls_animate; ?>">
					<?php }else if($params->get('type_show') == 'slider'){ ?>				
					<div class="ltabs-items-inner owl2-carousel ltabs-slider ">
					<?php }else if($params->get('type_show') == 'responsive'){?>
						<?php $class_respl= 'sj-respl01-'.$params->get('nb-column1',6).' sj-respl02-'.$params->get('nb-column2',4).' sj-respl03-'.$params->get('nb-column3',2).' sj-respl04-'.$params->get('nb-column4',1) ?>
						<div data-field="<?php echo $items->id;?>"	 class="ltabs-items-inner sj-k2-responsive-content <?php echo $class_respl?> <?php echo ($params->get('loadmore_type') == 0)?'loadmore-click':'';?>">
					<?php foreach($list  as $item){
						$img = K2ListingTabsHelper::getK2Image($item, $params);
					?>
	<div class="responsive-content-box">
		<div class="responsive-content-box-inner">
			<div class="responsive-content-box-bg">
				<div class="item <?php echo "id".$item->id?>">
					<?php
			        	$img = K2ListingTabsHelper::getK2Image($item, $params);
			        	if($img){
			        	?>
		       		<div class="item-img">
			        		<img class="responsive-loadimage" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"  src="<?php echo ImageHelper::init($img)->src(); ?>"  style="display:none;" />
			        		<?php 	echo K2ListingTabsHelper::imageTag($img); ?>
			        	<?php if($params->get('itemDateCreated', 1) == 1 || $params->get('itemHits',1) == 1 || $params->get('itemCommentsCounter',1) == 1) {?>
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
						<div class="item-img-mask"></div>
						<div class="item-spacer"></div>
			  		</div>
			  		<?php }?>
			  		<?php if($params->get('itemTitle',1) == 1){?>
		    		<h4 class="item-title">
		            	<?php echo K2ListingTabsHelper::truncate($item->title, $params->get('itemTitleWordLimit',25)); ?>
		         	</h4>
		         	<?php } ?>
		         	<?php if($params->get('itemIntroText', 1) == 1 && $item->displayIntrotext !='') {?>
		       		<div class="item-desc">
		       			<?php echo K2ListingTabsHelper::truncate($item->displayIntrotext, $params->get('itemIntroTextWordLimit',200)); ?>
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
					<a class="mask-img fancybox fancybox.iframe <?php echo ($img)?'':'item-img-mask'?>" data-fancybox-group="gallery" href="<?php  echo $link; ?>" title="<?php echo $item->title;?> "></a>
				<?php } else {?>
					<a class="mask-img <?php echo ($img)?'':'item-img-mask'?>" href="<?php echo $item->link ?>" <?php echo K2ListingTabsHelper::parseTarget($options->item_link_target);?> title="<?php echo $item->title?>" ></a>
				<?php }?>
			</div>
    	</div>
   	</div>
<?php } ?>
					
					
					<?php } ?>
                    
                    <?php if (!empty($list) && $params->get('type_show')  != 'responsive') {
                        require JModuleHelper::getLayoutPath($module->module, $layout . '_items');
                    } else {
                        ?>
                        <div class="ltabs-loading"></div>
                    <?php } ?>
                </div>
                <?php
				if($params->get('type_show')=='loadmore'){
                $classloaded = ($params->get('itemCount', 2) >= $items->countI || $params->get('itemCount') == 0) ? 'loaded' : ''; ?>
                <div class="ltabs-loadmore"
                     data-active-content=".items-category-<?php echo ($index == "*") ? 'all' : $index; ?>"
                     data-categoryid="<?php echo $index; ?>"
                     data-rl_start="<?php echo $params->get('itemCount', 2) ?>"
                     data-rl_total="<?php echo $items->countI; ?>"
                     data-rl_allready="<?php echo JText::_('ALL_READY_LABEL'); ?>"
                     data-ajaxurl="<?php echo (string)JURI::current(); ?>" data-modid="<?php echo $module->id; ?>"
                     data-rl_load="<?php echo $params->get('itemCount', 2) ?>"
                     data-rl-field_order="<?php echo ($params->get('filter_type') == "filter_categories") ? $items->field_order : $index; ?>">
                    <div class="ltabs-loadmore-btn <?php echo $classloaded ?>"
                         data-label="<?php echo ($classloaded) ? JText::_('ALL_READY_LABEL') : JText::_('LOAD_MORE_LABEL'); ?>">
                        <span class="ltabs-image-loading"></span>
                        <img class="add-loadmore" alt="Load More"
                             src="<?php echo JURI::base(); ?>/modules/<?php echo $module->module; ?>/assets/images/add.png">
                    </div>
                </div>
				<?php }?>
				<?php if($items->id == '*'){
					$id_cls = 0;
				}else{
					$id_cls = $items->id;
				} ?>
				<?php
				 if($params->get('type_show') == 'responsive'){
				 $uri->setVar('field_responsive',$id_cls);
				 }
				?>
				<?php if($params->get('type_show') == 'responsive' &&  ($params->get('loadmore_type') == 1 || $params->get('loadmore_type') == 2)){ ?>
					<nav id="page_nav_<?php echo $instance .'_'. $id_cls;?>" style="clear: both;">
						<a class="respl-button" href="<?php echo (string)$uri; ?>"></a>
					</nav>
					<?php  } 

					if($params->get('type_show') == 'responsive' && ($params->get('loadmore_type') == 0 || $params->get('loadmore_type') == 2)){ ?>
					<?php if($params->get('loadmore_type') == 2){
						$page = $params->get('type_all') + 1;
						//$uri->setVar('page',6);
					}
					?>
					<nav id="responsive_loadmore_<?php echo $instance .'_'. $id_cls;?>" style="margin-top:30px;" class="responsive-content-loadmore">
						<a class="resp-content-button" id="resp_content_button_<?php echo $instance .'_'. $id_cls;?>" href="<?php echo (string)$uri; ?>">
							<span class="loader-image"></span>
							<span class="loader-label" >Load More</span>
						</a>
					</nav>
					<?php } ?>
            </div>
			
            <?php } ?>
        </div>
        <!--End Items-->
    </div>
    <?php if (!empty($options->posttext)) { ?>
        <div class="post-text"><?php echo $options->posttext; ?></div>
    <?php } ?>
    </div>
<?php
} else {
    echo JText::_('Has no item to show!');
} ?>



