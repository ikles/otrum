jQuery.noConflict();																	

jQuery(document).ready(function(){
	
	// Global variables
	var token = window.mnwvars.token + "=1";
	var site_path = window.mnwvars.site_path;
	var itemid = window.mnwvars.itemid;
	var $container = jQuery('#mnwall_container');
	var gridType = window.mnwvars2.gridType;
	gridType = parseInt(gridType);
	if (gridType == 99) {
		gridLayout = 'vertical';
	} else {
		gridLayout = 'packery';
	}
	var themeType = window.mnwvars2.themeType;
	var hoverBox = window.mnwvars2.hoverBox;
	var fancybox = window.mnwvars2.fancybox;
	// Fancybox
	if (fancybox == 1) {
		$container.find('.mnwall-item-fancy-icon').fancybox();
	}
		
	// Colors
	var detailBoxBackground = window.mnwvars2.detailBoxBackground;
	var detailBoxBackgroundOpacity = window.mnwvars2.detailBoxBackgroundOpacity;
	
	// Hover effects
	var hoverBoxEffect = window.mnwvars2.hoverBoxEffect;
	var hoverBoxContentEffect = window.mnwvars2.hoverBoxContentEffect;
	if (hoverBoxContentEffect == 1) {
		var hoverContentEffectClass = 'hoverFadeIn';
	} else {
		var hoverContentEffectClass = 'no-effect';
	}
	
	// Hex to RGB
	function hexToRgb(hex) {
		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
		hex = hex.replace(shorthandRegex, function(m, r, g, b) {
			return r + r + g + g + b + b;
		});
	
		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		return result ? {
			r: parseInt(result[1], 16),
			g: parseInt(result[2], 16),
			b: parseInt(result[3], 16)
		} : null;
	}
	
	// RGB Color
	var ColorR = function ColorR(color) {
		var color_r = hexToRgb(color).r;
		return color_r;
	}
	var ColorG = function ColorG(color) {
		var color_g = hexToRgb(color).g;
		return color_g;
	}
	var ColorB = function ColorB(color) {
		var color_b = hexToRgb(color).b;
		return color_b;
	}
					
	// Initialize isotope	
	var $wall = jQuery('#mnwall_iso_container').imagesLoaded( function() 
	{
		// Isotope
		$wall.isotope({
			// General
			itemSelector: '.mnwall-item',
			layoutMode: gridLayout,
			// Vertical list
			vertical: {
				horizontalAlignment: 0
			},
			getSortData: {
			  	title: '[data-title]',
				category: '[data-category]',
				author: '[data-author]',
				date: '[data-date]'
			},
			sortBy: 'original-order'
		});
	});
	
	// Fix classes
	if (gridType != 99 && gridType != 98) 
	{
		// Add classes to items
		$container.find('.mnwall-item').each(function(index){
			mnw_mas_index = index + 1;
			if (mnw_mas_index > gridType) {
				mnw_mas_index = recurseMasItemIndex(mnw_mas_index);
			}
			jQuery(this).removeClass (function (class_index, css) {
				return (css.match (/(^|\s)mnwitem\S+/g) || []).join(' ');
			}).addClass('mnwitem'+mnw_mas_index);
		});
	}
	
	// Hover box trigger
	if (hoverBox == '1') {
		
		// Center hover content
		var centerHoverContent = function centerHoverContent() {
			jQuery(window).load(function(){
				$container.find('.mnwall-hover-box-content').each(function(index){
					if (gridType == 99 || gridType == 98) {
						jQuery(this).css( "top", ((jQuery(this).parents('.mnwall-img-div').height() - jQuery(this).outerHeight() ) / 2) + "px" );
					}
					if (gridType != 99 && gridType != 98) {
						jQuery(this).css( "top", ((jQuery(this).parents('.mnwall-item').height() - jQuery(this).outerHeight() ) / 2) + "px" );
					}
				});
			});	
		}
		centerHoverContent();
		
		// Center Hover content Dynamic
		var centerHoverContentDynamic = function centerHoverContentDynamic() {
			$container.find('.mnwall-hover-box-content').each(function(index){
				if (gridType == 99 || gridType == 98) {
					jQuery(this).css( "top", ((jQuery(this).parents('.mnwall-img-div').height() - jQuery(this).outerHeight() ) / 2) + "px" );
				}
				if (gridType != 99 && gridType != 98) {
					jQuery(this).css( "top", ((jQuery(this).parents('.mnwall-item').height() - jQuery(this).outerHeight() ) / 2) + "px" );
				}
			});
		}
			
		var triggerHoverBox = function triggerHoverBox() {
						
			if (gridType == 99 || gridType == 98) {
				// Hover effects
				jQuery('#mnwall_container .mnwall-item').find('.mnwall-item-inner-cont')
				.mouseenter(function(e) {        
					
					if (hoverBoxEffect == 'no') {
						jQuery(this).find('.mnwall-hover-box').stop().addClass('hoverShow');
						jQuery(this).find('.mnwall-hover-box-content').addClass(hoverContentEffectClass);
					}
					if (hoverBoxEffect == '1') {
						jQuery(this).find('.mnwall-hover-box').stop().addClass('hoverFadeIn')
						.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){  
							jQuery(this).find('.mnwall-hover-box-content').addClass(hoverContentEffectClass);
						});
					}
									
				}).mouseleave(function (e) {   
					
					if (hoverBoxEffect == 'no') {
						jQuery(this).find('.mnwall-hover-box').stop().removeClass('hoverShow');
						jQuery(this).find('.mnwall-hover-box-content').removeClass(hoverContentEffectClass);
					}
					if (hoverBoxEffect == '1') {
						jQuery(this).find('.mnwall-hover-box').stop().removeClass('hoverFadeIn')
						.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){  
							jQuery(this).find('.mnwall-hover-box-content').removeClass(hoverContentEffectClass);
						});
					}
					
				});
			}
			
			if (gridType != 98 && gridType != 99) {
				// Hover effects
				jQuery('#mnwall_container .mnwall-item')
				.mouseenter(function(e) {        
					
					if (hoverBoxEffect == 'no') {
						jQuery(this).find('.mnwall-hover-box').stop().addClass('hoverShow');
						jQuery(this).find('.mnwall-hover-box-content').addClass(hoverContentEffectClass);
					}
					if (hoverBoxEffect == '1') {
						jQuery(this).find('.mnwall-hover-box').stop().addClass('hoverFadeIn')
						.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){  
							jQuery(this).find('.mnwall-hover-box-content').addClass(hoverContentEffectClass);
						});
					}
									
				}).mouseleave(function (e) {   
					
					if (hoverBoxEffect == 'no') {
						jQuery(this).find('.mnwall-hover-box').stop().removeClass('hoverShow');
						jQuery(this).find('.mnwall-hover-box-content').removeClass(hoverContentEffectClass);
					}
					if (hoverBoxEffect == '1') {
						jQuery(this).find('.mnwall-hover-box').stop().removeClass('hoverFadeIn')
						.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){  
							jQuery(this).find('.mnwall-hover-box-content').removeClass(hoverContentEffectClass);
						});
					}
					
				});
			}
		}
		triggerHoverBox();
	}
		
	// Add detail-box background-color
	var backgroundColor = function backgroundColor() {
		$container.find('.mnwall-item').each(function(index){
			if (gridType != 99) {
				if (themeType == 1) {
					if (gridType == 98) {
						jQuery(this).find('.mnwall-item-inner-cont').css({"background":"rgba("+ColorR(detailBoxBackground)+", "+ColorG(detailBoxBackground)+", "+ColorB(detailBoxBackground)+", "+detailBoxBackgroundOpacity+")"});
					} else {
						jQuery(this).find('.mnwall-item-inner').css({"background":"rgba("+ColorR(detailBoxBackground)+", "+ColorG(detailBoxBackground)+", "+ColorB(detailBoxBackground)+", "+detailBoxBackgroundOpacity+")"});
					}
				}
				if (themeType == 2) {
					jQuery(this).find('.mnwall-detail-box-inner').css({"background":"rgba("+ColorR(detailBoxBackground)+", "+ColorG(detailBoxBackground)+", "+ColorB(detailBoxBackground)+", "+detailBoxBackgroundOpacity+")"});
				}
			}
			if (gridType == 99) {
				jQuery(this).find('.mnwall-item-inner-cont').css({"background":"rgba("+ColorR(detailBoxBackground)+", "+ColorG(detailBoxBackground)+", "+ColorB(detailBoxBackground)+", "+detailBoxBackgroundOpacity+")"});
			}
		});
	};
	backgroundColor();
		
	// Recursive function for masonry items classes
	function recurseMasItemIndex(mnw_mas_index) 
	{
		mnw_mas_index = mnw_mas_index - gridType;
		if (mnw_mas_index > gridType) {
			mnw_mas_index = recurseMasItemIndex(mnw_mas_index);
		}
		
		return mnw_mas_index;
	}
			
	// Filters
	var filters = {};
	jQuery('#mnwall_iso_filters').on( 'click', '.mnwall-filter', function(event) 
	{
		event.preventDefault();
		var $this = jQuery(this);
		// get group key
		var $buttonGroup = $this.parents('.button-group');
		var filterGroup = $buttonGroup.attr('data-filter-group');
		// set filter for group
		filters[ filterGroup ] = $this.attr('data-filter');
		// combine filters
		var filterValue = '';
		for ( var prop in filters ) {
			filterValue += filters[ prop ];
		}
		// set filter for Isotope
		$wall.isotope({ filter: filterValue });
		// Fix background image size
		if (gridType != 99 && gridType != 98) 
		{
			backgroundSize();
		}
		// Center hover content
		if (hoverBox == '1') {
			centerHoverContentDynamic();
		}
	});
	
	// Change active class on filter buttons
	var active_Filters = function active_Filters() {
		var $activeFilters = jQuery('#mnwall_container .button-group').each( function( i, buttonGroup ) {
			var $buttonGroup = jQuery( buttonGroup );
			$buttonGroup.on( 'click', 'a', function(event) {
				event.preventDefault();
				$buttonGroup.find('.mnw_filter_active').removeClass('mnw_filter_active');
				jQuery( this ).addClass('mnw_filter_active');
			});
		});
	};
	active_Filters();
	
	// Dropdown filter list
	var dropdown_Filters = function dropdown_Filters() {
		var $dropdownFilters = jQuery('#mnwall_container .mnwall_iso_filters .mnwall_iso_dropdown').each( function( i, dropdownGroup ) {
			var $dropdownGroup = jQuery( dropdownGroup );
			$dropdownGroup.on( 'click', '.dropdown-label', function(event) {
				event.preventDefault();
				$dropdownGroup.toggleClass('expanded');
			});
		});
		jQuery(document).mouseup(function (e)
		{
			var $dropdowncontainer = jQuery('#mnwall_container .mnwall_iso_dropdown');
		
			if (!$dropdowncontainer.is(e.target)
				&& $dropdowncontainer.has(e.target).length === 0
				) 
			{
				$dropdowncontainer.removeClass("expanded");
			}
		});
	};
	dropdown_Filters();
		
	// Bind sort button click
  	jQuery('#mnwall_container .sorting-group-filters').on( 'click', '.mnwall-filter', function(event) {
		event.preventDefault();
    	var sortValue = jQuery(this).attr('data-sort-value');
    	// set filter for Isotope
		$wall.isotope({ 
			sortBy: sortValue
		});
  	});
	
	// Change active class on sorting filters
	jQuery('#mnwall_container .sorting-group-filters').each( function( i, sortingGroup ) {
		var $sortingGroup = jQuery( sortingGroup );
		$sortingGroup.on( 'click', '.mnwall-filter', function() {
	  		$sortingGroup.find('.mnw_filter_active').removeClass('mnw_filter_active');
	  		jQuery( this ).addClass('mnw_filter_active');
		});
	});
	
	// Bind sorting direction button click
  	jQuery('#mnwall_container .sorting-group-direction').on( 'click', '.mnwall-filter', function(event) {
		event.preventDefault();
    	var sortDirection = jQuery(this).attr('data-sort-value');
		if (sortDirection == 'asc') {
			sort_Direction = true;
		} else {
			sort_Direction = false;
		}
    	// set direction
		$wall.isotope({ 
			sortAscending: sort_Direction
		});
  	});
	
	// Change active class on sorting direction
	jQuery('#mnwall_container .sorting-group-direction').each( function( i, sortingDirection ) {
		var $sortingDirection = jQuery( sortingDirection );
		$sortingDirection.on( 'click', '.mnwall-filter', function() {
	  		$sortingDirection.find('.mnw_filter_active').removeClass('mnw_filter_active');
	  		jQuery( this ).addClass('mnw_filter_active');
		});
	});
	
	// Dropdown sorting list
	var dropdown_Sortings = function dropdown_Sortings() {
		var $dropdownSortings = jQuery('#mnwall_container .mnwall_iso_sortings .mnwall_iso_dropdown').each( function( i, dropdownSorting ) {
			var $dropdownSorting = jQuery( dropdownSorting );
			$dropdownSorting.on( 'click', '.dropdown-label', function(event) {
				event.preventDefault();
				$dropdownSorting.toggleClass('expanded');
			});
		});
	};
	dropdown_Sortings();
	
	// Fix background image size
	if (gridType != 99 && gridType != 98) 
	{
		var backgroundSize = function backgroundSize() {
			$container.find('.mnwall-item').each(function(){
				var $lp = jQuery(this).attr('data-lp');
				var $child_cont = jQuery(this).find('.mnwall-item-inner-cont');
				var $child_cont_width = $child_cont.width();
				var $child_cont_height = $child_cont.height();
				var $lk_raw = $child_cont_width / $child_cont_height;
				var $lk = $lk_raw.toFixed(2);
				// 1.
				if ($lk <= 1 && $lp >= 1) {
					jQuery(this).find('.mnwall-item-inner-cont').css({"background-size":"auto 100%"});
				}
				// 2.
				if ($lk < 1 && $lp < 1) {
					// 2a.
					if ($lp > $lk) {
						jQuery(this).find('.mnwall-item-inner-cont').css({"background-size":"auto 100%"});
					}
					// 2b.
					if ($lp < $lk) {
						jQuery(this).find('.mnwall-item-inner-cont').css({"background-size":"100% auto"});
					}
				}
				// 3.
				if ($lk >= 1 && $lp <= 1) {
					jQuery(this).find('.mnwall-item-inner-cont').css({"background-size":"100% auto"});
				}
				// 4.
				if ($lk > 1 && $lp > 1) {
					// 4a.
					if ($lp > $lk) {
						jQuery(this).find('.mnwall-item-inner-cont').css({"background-size":"auto 100%"});
					}
					// 4b.
					if ($lp < $lk) {
						jQuery(this).find('.mnwall-item-inner-cont').css({"background-size":"100% auto"});
					}
				}
				
			});
		};
		backgroundSize();
	}
					  	
	jQuery(window).resize(function() {
		
		// Center hover content
		if (hoverBox == '1') {
			centerHoverContentDynamic();
		}
				
		// Fix background image size
		if (gridType != 99 && gridType != 98) 
		{
			backgroundSize();
		}
		
	});
			
});	