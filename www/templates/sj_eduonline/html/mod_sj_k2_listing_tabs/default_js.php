<?php
/**
 * @package SJ Listing Tabs For K2
 * @version 1.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
$tag_id = 'sj_listing_tabs_'.$instance;
$doc     = JFactory::getDocument();
?>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function ($) {
    ;
    (function (element) {
		
        var $element = $(element),
            $tab = $('.ltabs-tab', $element),
            $tab_label = $('.ltabs-tab-label', $tab),
            $tabs = $('.ltabs-tabs', $element),
            ajax_url = $tabs.parents('.ltabs-tabs-container').attr('data-ajaxurl'),
            effect = $tabs.parents('.ltabs-tabs-container').attr('data-effect'),
            delay = $tabs.parents('.ltabs-tabs-container').attr('data-delay'),
            duration = $tabs.parents('.ltabs-tabs-container').attr('data-duration'),
            rl_moduleid = $tabs.parents('.ltabs-tabs-container').attr('data-modid'),
            $items_content = $('.ltabs-items', $element),
            $items_inner = $('.ltabs-items-inner', $items_content),
            $items_first_active = $('.ltabs-items-selected', $element),
            $load_more = $('.ltabs-loadmore', $element),
            $btn_loadmore = $('.ltabs-loadmore-btn', $load_more),
            $select_box = $('.ltabs-selectbox', $element),
            $tab_label_select = $('.ltabs-tab-selected', $element);

        enableSelectBoxes();
        function enableSelectBoxes() {
            $tab_wrap = $('.ltabs-tabs-wrap', $element),
                $tab_label_select.html($('.ltabs-tab', $element).filter('.tab-sel').children('.ltabs-tab-label').html());
            if ($(window).innerWidth() <= 479) {
                $tab_wrap.addClass('ltabs-selectbox');
            } else {
                $tab_wrap.removeClass('ltabs-selectbox');
            }
        }

        $('span.ltabs-tab-selected, span.ltabs-tab-arrow', $element).click(function () {
            if ($('.ltabs-tabs', $element).hasClass('ltabs-open')) {
                $('.ltabs-tabs', $element).removeClass('ltabs-open');
            } else {
                $('.ltabs-tabs', $element).addClass('ltabs-open');
            }
        });

        $(window).resize(function () {
            if ($(window).innerWidth() <= 479) {
                $('.ltabs-tabs-wrap', $element).addClass('ltabs-selectbox');
            } else {
                $('.ltabs-tabs-wrap', $element).removeClass('ltabs-selectbox');
            }
        });

        function showAnimateItems(el) {
            var $_items = $('.new-ltabs-item', el), nub = 0;
            $('.ltabs-loadmore-btn', el).fadeOut('fast');
            $_items.each(function (i) {
                nub++;
                switch (effect) {
                    case 'none' :
                        $(this).css({'opacity': '1', 'filter': 'alpha(opacity = 100)'});
                        break;
                    default:
                        animatesItems($(this), nub * delay, i, el);
                }
                if (i == $_items.length - 1) {
                    $('.ltabs-loadmore-btn', el).fadeIn(delay);
                }
                $(this).removeClass('new-ltabs-item');
            });
        }

        function animatesItems($this, fdelay, i, el) {
            var $_items = $('.ltabs-item', el);
            $this.attr("style",
                "-webkit-animation:" + effect + " " + duration + "ms;"
                + "-moz-animation:" + effect + " " + duration + "ms;"
                + "-o-animation:" + effect + " " + duration + "ms;"
                + "-moz-animation-delay:" + fdelay + "ms;"
                + "-webkit-animation-delay:" + fdelay + "ms;"
                + "-o-animation-delay:" + fdelay + "ms;"
                + "animation-delay:" + fdelay + "ms;").delay(fdelay).animate({
                    opacity: 1,
                    filter: 'alpha(opacity = 100)'
                }, {
                    delay: 100
                });
            if (i == ($_items.length - 1)) {
                $(".ltabs-items-inner").addClass("play");
            }
        }
		
        showAnimateItems($items_first_active);
        $tab.on('click.tab', function () {
            var $this = $(this);
            if ($this.hasClass('tab-sel')) return false;
            if ($this.parents('.ltabs-tabs').hasClass('ltabs-open')) {
                $this.parents('.ltabs-tabs').removeClass('ltabs-open');
            }
            $tab.removeClass('tab-sel');
            $this.addClass('tab-sel');
            var items_active = $this.attr('data-active-content');
            var _items_active = $(items_active, $element);
			<?php if($params->get('type_show') == 'responsive'){?>
				$element.find('.ltabs-items-selected .<?php echo 'sj-k2-responsive-content';?>').infinitescroll('pause');
			<?php } ?>
			
            $items_content.removeClass('ltabs-items-selected');
            _items_active.addClass('ltabs-items-selected');
            $tab_label_select.html($tab.filter('.tab-sel').children('.ltabs-tab-label').html());
            var $loading = $('.ltabs-loading', _items_active);
            var loaded = _items_active.hasClass('ltabs-items-loaded');
            if (!loaded && !_items_active.hasClass('ltabs-process')) {
                _items_active.addClass('ltabs-process');
                var category_id = $this.attr('data-category-id');
                var field_order = $this.attr('data-field_order');
				$('#<?php echo $tag_id; ?> .ltabs-item').css('opacity','0');
				$('#<?php echo $tag_id; ?> .ltabs-item').css('display','none');
				
                $loading.show();
                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data: {
                        listing_tabs_moduleid: rl_moduleid,
                        is_ajax_listing_tabs: 1,
                        ajax_reslisting_start: 0,
                        categoryid: category_id,
                        time_temp: '<?php echo time().rand(); ?>',
                        field_order: field_order,
						limitstart:$this.parent().attr('data-rl_start'),
						sj_class_responsive: '<?php echo $instance;?>',
						field_responsive :category_id,
						<?php if($params->get('type_show') == 'responsive')echo 'type_js:2';?>
                    },
                    success: function (data) {
						
						datas = data.split(':123::::123');
						data = datas[1];
						data = $.parseJSON(data);
						
                        if (data.items_markup != '') {
                            $('.ltabs-items-inner', _items_active).html(data.items_markup);
                            _items_active.addClass('ltabs-items-loaded').removeClass('ltabs-process');
                            $loading.remove();
                            showAnimateItems(_items_active);
                            updateStatus(_items_active);  
							<?php if ($params->get('type_show') == 'slider') { ?>
									var total_product = <?php echo $params->get('itemCount', 5); ?>;
									var nb_column1 = <?php echo $params->get('nb-column1'); ?>,
										  nb_column2 = <?php echo $params->get('nb-column2'); ?>,
										  nb_column3 = <?php echo $params->get('nb-column3'); ?>,
										  nb_column4 = <?php echo $params->get('nb-column4'); ?>;
										  $('#<?php echo $tag_id; ?> .ltabs-item').css('display','block');
										  $('#<?php echo $tag_id; ?> .ltabs-item').css('opacity','1');
										  $('#<?php echo $tag_id; ?> .ltabs-items-selected .owl2-carousel').owlCarousel({
												nav: <?php echo $params->get('display_nav') ; ?>,
												dots: false,
												margin: 30,
												loop:  <?php echo $params->get('display_loop') ; ?>,
												autoplay: <?php echo $params->get('autoplay'); ?>,
												autoplayHoverPause: <?php echo $params->get('pausehover') ; ?>,
												autoplayTimeout: <?php echo $params->get('autoplay_timeout') ; ?>,
												autoplaySpeed: <?php echo $params->get('autoplay_speed') ; ?>,
												mouseDrag: <?php echo  $params->get('mousedrag'); ?>,
												touchDrag: <?php echo $params->get('touchdrag'); ?>,
												navRewind: true,
												<?php if($doc->direction == 'rtl') {
													echo "rtl: true,";
												} ?>
												navText: [ '', '' ],
												
												responsive: {
													0: {
														items: nb_column4,
														nav: total_product <= nb_column4 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
													},
													480: {
														items: nb_column3,
														nav: total_product <= nb_column3 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
													},
													768: {
														items: nb_column2,
														nav: total_product <= nb_column2 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
													},
													1200: {
														items: nb_column1,
														nav: total_product <= nb_column1 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
													},
												}
										 });
							<?php }?>
							<?php if ($params->get('type_show') == 'responsive') { ?>
								scrollResponsive();
						    <?php } ?>
                        }
                    },
                    dataType: 'text'
                });

            } else {
				<?php if ($params->get('type_show') == 'slider') { ?>
				 $('.ltabs-item', $items_content).removeAttr('style').addClass('new-ltabs-item').css('opacity', 1);
				<?php }else{ ?>
				$('.ltabs-item', $items_content).removeAttr('style').addClass('new-ltabs-item').css('opacity', 0);
                showAnimateItems(_items_active);
				<?php } ?>
				<?php if ($params->get('type_show') == 'responsive') { ?>
					$element.find('.ltabs-items-selected .<?php echo 'sj-k2-responsive-content';?>').infinitescroll('resume');
				<?php } ?>
				
            }
        });

        function updateStatus($el) {
            $('.ltabs-loadmore-btn', $el).removeClass('loading');
            var countitem = $('.ltabs-item', $el).length;
            $('.ltabs-image-loading', $el).css({display: 'none'});
            $('.ltabs-loadmore-btn', $el).parent().attr('data-rl_start', countitem);
            var rl_total = $('.ltabs-loadmore-btn', $el).parent().attr('data-rl_total');
            var rl_load = $('.ltabs-loadmore-btn', $el).parent().attr('data-rl_load');
            var rl_allready = $('.ltabs-loadmore-btn', $el).parent().attr('data-rl_allready');

            if (countitem >= rl_total) {
                $('.ltabs-loadmore-btn', $el).addClass('loaded');
                $('.ltabs-image-loading', $el).css({display: 'none'});
                $('.ltabs-loadmore-btn', $el).attr('data-label', rl_allready);
                $('.ltabs-loadmore-btn', $el).removeClass('loading');
            }
        }
		
        $btn_loadmore.on('click.loadmore', function () {
            var $this = $(this);
            if ($this.hasClass('loaded') || $this.hasClass('loading')) {
                return false;
            } else {
                $this.addClass('loading');
                $('.ltabs-image-loading', $this).css({display: 'inline-block'});
                var rl_start = $this.parent().attr('data-rl_start'),
                    rl_moduleid = $this.parent().attr('data-modid'),
                    rl_ajaxurl = $this.parent().attr('data-ajaxurl'),
                    effect = $this.parent().attr('data-effect'),
                    category_id = $this.parent().attr('data-categoryid'),
                    items_active = $this.parent().attr('data-active-content');
					field_order = $this.parent().attr('data-rl-field_order');
					var _items_active = $(items_active, $element);
					var start = $this.parent().attr('data-rl_start');
					$.ajax({
						type: 'POST',
						url: rl_ajaxurl,
						data: {
							listing_tabs_moduleid: rl_moduleid,
							is_ajax_listing_tabs: 1,
							ajax_reslisting_start: rl_start,
							categoryid: category_id,
							time_temp: '<?php echo time().rand(); ?>',
							field_order: field_order,
							sj_class_responsive: '<?php echo $instance;?>',
							limitstart:$this.parent().attr('data-rl_start')
						},
						success: function (data) {
							if (data.items_markup != '') {
								$(data.items_markup).insertAfter($('.ltabs-item', _items_active).nextAll().last());
								$('.ltabs-image-loading', $this).css({display: 'none'});
								showAnimateItems(_items_active);
								updateStatus(_items_active);
							}
						}, dataType: 'json'
					});
            }
            return false;
        });
		<?php if($params->get('loadmore_type') == 2) { ?>
		$('#<?php echo $tag_id; ?> .responsive-content-loadmore').css('display','none');
		<?php } ?>
		<?php if($params->get('type_show') == 'responsive'){?>
		function scrollResponsive(){
		var field = $('#<?php echo $tag_id; ?> .tab-sel').attr('data-category-id');
		if(field == '*'){
			field = 0;
		}
		var element = '#<?php echo $tag_id; ?> .ltabs-items-selected .<?php echo 'sj-k2-responsive-content';?>';
		var time = 0, inter = null;
		var $container = $(element), test = function(){ if (time>2000 && inter){clearInterval(inter); time=0;} time+=10; console.log('At '+time+': '+$container.height()); },
			is = element + ' .responsive-content-box',
			scroll = function(){
				<?php if($params->get('loadmore_type') == 1) { ?>
					$container.infinitescroll('scroll');
				<?php } ?>
			},
			start = function(){
				$container.isotope({
					itemSelector: is
				});

				if ( $.browser.msie  && parseInt($.browser.version, 10) <= 8){
					//nood
				}else{
					$(window).resize(function() {
						$container.isotope('reLayout');
					});
			    }
				
				<?php if($params->get('loadmore_type') == 1 || $params->get('loadmore_type') == 2) { ?>
					$(window).bind('scroll');
					var index_page = 0;
					function scrollAjax(e){
						e.infinitescroll(
						{							
							navSelector : '#page_nav_<?php echo $instance;?>_'+field, // selector for the paged navigation
							nextSelector : '#page_nav_<?php echo $instance;?>_'+field+' a', // selector for the NEXT link (to page 2)
							itemSelector : is, // selector for all items you'll retrieve
							debug		 : false,
							loading: {
								finishedMsg: 'No more pages to load.',
								img: 'http://i.imgur.com/qkKy8.gif'
							},
							 infid:'<?php echo $instance; ?>'
						},
						// call Isotope as a callback
						
						function( newElements ) {
							var $newElements = $( newElements ).css({ opacity: 0 });
							$newElements.imagesLoaded( function(){
								$newElements.animate({ opacity: 1 });
								e.isotope( 'appended', $newElements );
								index_page++;
								var type = 1;
								<?php if($params->get('loadmore_type') == 2) {?>
								type = 0;
								if(index_page % parseInt(<?php echo $params->get('type_all');?>) == 0){
									$('#<?php echo $tag_id; ?> .responsive-content-loadmore').css('display','block');
									e.infinitescroll('pause');
									responsiveLoadmore(e);
								}
								<?php }?>
								
							});
							
						}
					);
					}
					scrollAjax($container);
				<?php } else {?>
					responsiveLoadmore($container);
						
				<?php }?>
				
				function responsiveLoadmore($container){
					var field = $('#<?php echo $tag_id; ?> .tab-sel').attr('data-category-id');
					if(field == '*'){
						field = 0;
					}
					var  loading_state = function(){
       						 $('.loader-image','#resp_content_button_<?php echo $instance;?>_'+field).css('display','inline-block');
       						 $('.loader-label','#resp_content_button_<?php echo $instance;?>_'+field).html('Loading...');
       					} 
					var  loadmore_state = function(){
       						 $('.loader-image','#resp_content_button_<?php echo $instance;?>_'+field).css('display','none');
       						 $('.loader-label','#resp_content_button_<?php echo $instance;?>_'+field).html('Load More');
       					} 

						$container.infinitescroll(
								{
									navSelector : "a#resp_content_button_<?php echo $instance;?>_"+field+":last", // selector for the paged navigation
									nextSelector : "a#resp_content_button_<?php echo $instance;?>_"+field+":last", // selector for the NEXT link (to page 2)
									itemSelector : is, // selector for all items you'll retrieve
									debug		 : false,
									loading: {
										finishedMsg: 'No more pages to load.',
										img: 'http://i.imgur.com/qkKy8.gif'
									},
									errorCallback: function(){
										$('#responsive_loadmore_<?php echo $instance;?>_'+field).remove();
										
									},
									 animate      : true,  
									 localMode    : true,
									infid:'<?php echo $instance; ?>'
								},
							// call Isotope as a callback
							function( newElements ) {
								var $newElements = $( newElements ).css({ opacity: 0 });
								$newElements.imagesLoaded( function(){
									$newElements.animate({ opacity: 1 });
									$container.isotope( 'appended', $newElements );
									loadmore_state();
									
								});
							}
						);
						
						
					
					$(window).unbind('.infscr');
					$('#resp_content_button_<?php echo $instance;?>_'+field,'#responsive_loadmore_<?php echo $instance;?>_'+field).click(function(e){
							loading_state();	
							e.preventDefault();
							$container.infinitescroll('retrieve');
							loadmore_state();
						 return false;;
						});
					}
			};
			
			
		$container.imagesLoaded(function(){
			start();
			scroll();
		});
		
		// fancybox
		$('.fancybox').fancybox({
			prevEffect : 'none',
			nextEffect : 'none',
			width     :600,
			heidth    :200,
			maxWight  :800,
			maxHeight :400,
			autoSize  : false
		});
		}
		 scrollResponsive();
		<?php } ?>
	 
		$('.ltabs-loading').css('display','none');
    })('#<?php echo $tag_id; ?>');
	
	
});
//-->
</script>
<?php if ($params->get('type_show') == 'slider') { ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
			var total_product = <?php echo $params->get('itemCount', 5); ?>;
            var $tag_id = $('#<?php echo $tag_id; ?>'),
                parent_active = 	$('<?php echo $tag_id; ?>', $tag_id),
                total_product = parent_active.data('total'),
                tab_active = $('.owl2-carousel',parent_active),
                nb_column1 = <?php echo $params->get('nb-column1'); ?>,
                nb_column2 = <?php echo $params->get('nb-column2'); ?>,
                nb_column3 = <?php echo $params->get('nb-column3'); ?>,
                nb_column4 = <?php echo $params->get('nb-column4'); ?>;
				$('#<?php echo $tag_id; ?> .ltabs-item').css('opacity','1');

				$('#<?php echo $tag_id; ?> .ltabs-items-selected .owl2-carousel').owlCarousel({
                nav: <?php echo $params->get('display_nav') ; ?>,
                dots: false,
                margin: 30,
                loop:  <?php echo $params->get('display_loop') ; ?>,
                autoplay: <?php echo $params->get('autoplay'); ?>,
                autoplayHoverPause: <?php echo $params->get('pausehover') ; ?>,
                autoplayTimeout: <?php echo $params->get('autoplay_timeout') ; ?>,
                autoplaySpeed: <?php echo $params->get('autoplay_speed') ; ?>,
                mouseDrag: <?php echo  $params->get('mousedrag'); ?>,
                touchDrag: <?php echo $params->get('touchdrag'); ?>,
                navRewind: true,
                <?php if($doc->direction == 'rtl') {
					echo "rtl: true,";
				} ?>
            	navText: [ '', '' ],
                responsive: {
                    0: {
                        items: nb_column4,
                        nav: total_product <= nb_column4 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
                    },
                    480: {
                        items: nb_column3,
                        nav: total_product <= nb_column3 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
                    },
                    768: {
                        items: nb_column2,
                        nav: total_product <= nb_column2 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
                    },
                    1200: {
                        items: nb_column1,
                        nav: total_product <= nb_column1 ? false : ((<?php echo $params->get('display_nav') ; ?>) ? true: false),
                    },
                }
            });

        });
    </script>
<?php } ?>