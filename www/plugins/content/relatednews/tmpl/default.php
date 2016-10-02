<?php
/**
 * @package Content - Related News
 * @version 3.3.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;

// includes placehold
$yt_temp = JFactory::getApplication()->getTemplate();
include (JPATH_BASE . '/templates/'.$yt_temp.'/includes/placehold.php');
?>
<?php /*<div class="addthis">
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-55f39cd53f56cee0"></script>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<div class="addthis_sharing_toolbox"></div>
        </div>*/ ?>
<?php
ImageHelper::setDefault($_params);

if (count($items)){
    if ((int)$this->params->get('usecss', 1)){
        $css_url = JURI::root() . 'plugins/content/relatednews/assets/css/relatednews.css';
		
        $document = JFactory::getDocument();
        $document->addStyleSheet($css_url);
		JHtml::stylesheet(JUri::base()."plugins/content/relatednews/assets/owl.carousel/owl.carousel.css",'text/css',"screen");
		JHtml::_('jquery.framework');
		JHtml::script(JUri::base()."plugins/content/relatednews/assets/owl.carousel/owl.carousel.min.js");
		
    }

    if ($this->params->get('title')){
        echo '<h3 class="related-title">';
        echo $this->params->get('title');
        echo '</h3>';
    }
	
    echo '<ul class="related-items row">';
    foreach ($items as $id => $item) {
        if($item->id != $article_id){?>
            <li class="col-sm-12">
            <?php
            if ((int)$this->params->get('item_image_display', 1)){ ?>
                <div class="img-fulltext">

                    <?php
                        $img = RelatedBaseHelper::getArticleImage($item, $_params);
                        //Create placeholder images
                        if (isset($img['src'])) $src = $img['src'];
                        if (file_exists($src )) {
                            echo RelatedBaseHelper::imageTag($img);
                        } else if ($is_placehold) {
                            echo yt_placehold($placehold_size['related_items'] );
                        }
                    ?>
                </div>
            <?php }?>
            <div class="content">
            <?php
            if ((int)$_params->get('item_date_display', 1) == 1): ?>
                <span class="related-item-date"><?php echo JHtml::date($item->created, 'd mm Y'); ?></span>

            <?php
            endif;
            ?>
                <h3 class="related-item-title">
                    <a href="<?php echo $item->link; ?>" <?php echo RelatedBaseHelper::parseTarget($_params->get('item_link_target'));?> >
                    <?php echo limit_text($item->title,6); ?>
                    </a>
                </h3>
                <div class="related-item-info">
            <?php
            if ((int)$_params->get('item_date_display', 1) == 2): ?>
                <div class="related-item-date"><?php echo JHtml::date($item->created, 'M d, Y'); ?></div>
                <div class="related-item-author"> <span>by</span> <?php echo JText::sprintf($item->author); ?> </div>
            <?php endif; ?>
                </div>
            <?php echo limit_text($item->introtext,25);?>
            </div>
            </li>
    <?php
    }}
    echo '</ul>';
}

?>

<script>// <![CDATA[
jQuery(document).ready(function($) {
		$('.related-items').owlCarousel({
			pagination: false,
			center: false,
			nav: true,
			dots: false,
			loop: false,
			margin: 0,
			//rtl: true,
			slideBy: 1,
			autoplay: false,
			autoplayTimeout: 2500,
			autoplayHoverPause: true,
			autoplaySpeed: 800,
			startPosition: 0,
            navText: ["<i class='fa fa-long-arrow-left'></i>","<i class='fa fa-long-arrow-right'></i>"],
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
					items:4,
                    nav:false,
				}
			}
		});	  
	});
// ]]></script>
