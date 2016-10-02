<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2015 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class MinitekWallLibBaseMasonryJavascript
{
	
	public function loadMasonryJavascript($masonry_params, $widgetID, $totalCount, $fancybox)
	{
		$document = JFactory::getDocument();

		$this->loadJavascriptVars($masonry_params, $widgetID, $totalCount, $fancybox);				
		
		$javascript = "jQuery(function(){";
		
			$javascript .= $this->loadJavascriptVars($masonry_params, $widgetID, $totalCount, $fancybox);
					
			$javascript .= $this->initializeWall($masonry_params, $widgetID);
			
			if ($masonry_params['mas_hb']) 
			{		
				$javascript .= $this->initializeHoverBox($masonry_params['mas_hb_effect']);
			}
					
			$javascript .= $this->initializeFiltersSortings($widgetID);
			
			if ($masonry_params['mas_pagination']) 
			{		
				$javascript .= $this->initializePagination();
			}
			
			if ($masonry_params['mas_pagination'] == '1') 
			{
				$javascript .= $this->initializeAppendPagination($widgetID);
			} 
			else if ($masonry_params['mas_pagination'] == '2') 
			{
				$javascript .= $this->initializeArrowsPagination($widgetID);
			} 
			else if ($masonry_params['mas_pagination'] == '3') 
			{
				$javascript .= $this->initializePagesPagination($widgetID);
			}
			else if ($masonry_params['mas_pagination'] == '4') 
			{
				$javascript .= $this->initializeInfinitePagination($widgetID);
			} 
				
		$javascript .= "});";
				
		//$document->addScriptDeclaration($javascript);
		$document->addCustomTag('<script type="text/javascript">'.$javascript.'</script>');
	
	}
	
	public function loadJavascriptVars($masonry_params, $widgetID, $totalCount, $fancybox)
	{
		$token = JSession::getFormToken();
		$site_path = JURI::root();
		$itemid = JRequest::getVar('Itemid');	
		$pagination = $masonry_params['mas_pagination'];
		$startLimit = $masonry_params['mas_starting_limit'];
		$pageLimit = $masonry_params['mas_page_limit'];
		$globalLimit = $masonry_params['mas_global_limit'];
		if ($startLimit > $globalLimit) 
		{
			$startLimit = $globalLimit;
		}
		if ($totalCount < $startLimit) 
		{
			$startLimit = $totalCount;
		}
		$lastPage = ceil(($totalCount - $startLimit) / $pageLimit);		
		$gridType = $masonry_params['mas_grid'];
		$hoverBox = $masonry_params['mas_hb'];
		$custom_scrollbar = $masonry_params['mas_db_scrollbar'];
		
		$javascript = "
		
			// Global variables
			var token = '".$token."=1';
			var site_path = '".$site_path."';
			var itemid = '".$itemid."';
			var pageLimit = '".$pageLimit."';
			var lastPage = '".$lastPage."';
			var endPage = parseInt(lastPage) + 2;
			var pagination = '".$pagination."';
			var _container = jQuery('#mnwall_container_".$widgetID."');
			var gridType = '".$gridType."';
			gridType = parseInt(gridType);
			if (gridType == 99) {
				gridLayout = 'vertical';
			} else {
				gridLayout = 'packery';
			}
			var hoverBox = '".$hoverBox."';
			var custom_scrollbar = '".$custom_scrollbar."';
			var fancybox = '".$fancybox."';
			
			// Fancybox
			if (fancybox == 1) {
				_container.find('.mnwall-item-fancy-icon').fancybox();
			}
			
			// Custom scrollbar
			if (custom_scrollbar == 1) {
				_container.find('.mnwall-item-inner').mCustomScrollbar();
			}
			
		";
		
		return $javascript;
	}
	
	public function initializeWall($masonry_params, $widgetID)
	{	
		$mas_effects = $masonry_params['mas_effects'];
		
		$hiddenStyle = 'opacity: 0';
		$visibleStyle = 'opacity: 1';
		
		if ($mas_effects == 'fade')
		{
			$effect = "
				hiddenStyle: {
					".$hiddenStyle."
				},
				visibleStyle: {
					".$visibleStyle."
				}
			";
		} else {
			$effect = '';
		}
						
		$javascript = "
		
			// Initialize wall	
			var _wall = jQuery('#mnwall_iso_container_".$widgetID."').imagesLoaded( function() 
			{
				// Isotope
				_wall.isotope({
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
					sortBy: 'original-order',
					".$effect."
				});
				
				jQuery('.mnwall_container').show();
				
				_wall.isotope();
				
				if (pagination == '4') {
					if(_container.find('.mnwall_more_results').visible())
					{
						infiniteWall();
					}
				}
			});
						
		";
		
		$javascript .= "
			
			var wall_id;
			jQuery(window).resize(function(){
				
				clearTimeout(wall_id);
    			wall_id = setTimeout(doneBrowserResizing, 500);
			});
			
			function doneBrowserResizing(){
  				_wall.isotope();
			}
		
		";
		
		return $javascript;
	}
	
	public function initializeHoverBox($hoverBoxEffect)
	{		
		$javascript = "
			
			// Hover effects
			var hoverBoxEffect = '".$hoverBoxEffect."';
			
			// Hover box trigger
			if (hoverBox == '1') {
					
				var triggerHoverBox = function triggerHoverBox() {
								
					if (gridType == 99 || gridType == 98) {
						// Hover effects
						_container.find('.mnwall-item-inner-cont')
						.mouseenter(function(e) {        
							
							if (hoverBoxEffect == 'no') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().addClass('hoverShow');
							}
							if (hoverBoxEffect == '1') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().addClass('hoverFadeIn');
							}
							if (hoverBoxEffect == '2') {
								jQuery(this).closest('.mnwall-item').find('.mnwall-cover').stop().addClass('perspective');
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-img-div').stop().addClass('flip flipY hoverFlipY');
							}
							if (hoverBoxEffect == '3') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-cover').stop().addClass('perspective');
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-img-div').stop().addClass('flip flipX hoverFlipX');
							}
							if (hoverBoxEffect == '4') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().addClass('slideInRight');
							}
							if (hoverBoxEffect == '5') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().addClass('slideInLeft');
							}
							if (hoverBoxEffect == '6') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().addClass('slideInTop');
							}
							if (hoverBoxEffect == '7') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().addClass('slideInBottom');
							}
							if (hoverBoxEffect == '8') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().addClass('mnwzoomIn');
							}
											
						}).mouseleave(function (e) {   
							
							if (hoverBoxEffect == 'no') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().removeClass('hoverShow');
							}
							if (hoverBoxEffect == '1') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().removeClass('hoverFadeIn');
							}
							if (hoverBoxEffect == '2') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-img-div').stop().removeClass('hoverFlipY');
							}
							if (hoverBoxEffect == '3') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-img-div').stop().removeClass('hoverFlipX');
							}
							if (hoverBoxEffect == '4') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().removeClass('slideInRight');
							}
							if (hoverBoxEffect == '5') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().removeClass('slideInLeft');
							}
							if (hoverBoxEffect == '6') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().removeClass('slideInTop');
							}
							if (hoverBoxEffect == '7') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().removeClass('slideInBottom');
							}
							if (hoverBoxEffect == '8') {
								jQuery(this).closest('.mnwall-item-outer-cont').find('.mnwall-hover-box').stop().removeClass('mnwzoomIn');
							}
							
						});
					}
					
					if (gridType != 98 && gridType != 99) {
						// Hover effects
						_container.find('.mnwall-item')
						.mouseenter(function(e) {        
							
							if (hoverBoxEffect == 'no') {
								jQuery(this).find('.mnwall-hover-box').stop().addClass('hoverShow');
							}
							if (hoverBoxEffect == '1') {
								jQuery(this).find('.mnwall-hover-box').stop().addClass('hoverFadeIn');
							}
							if (hoverBoxEffect == '2') {
								jQuery(this).stop().addClass('perspective');
								jQuery(this).find('.mnwall-item-outer-cont').stop().addClass('flip flipY hoverFlipY');
							}
							if (hoverBoxEffect == '3') {
								jQuery(this).stop().addClass('perspective');
								jQuery(this).find('.mnwall-item-outer-cont').stop().addClass('flip flipX hoverFlipX');
							}
							if (hoverBoxEffect == '4') {
								jQuery(this).find('.mnwall-hover-box').stop().addClass('animated slideInRight');
							}
							if (hoverBoxEffect == '5') {
								jQuery(this).find('.mnwall-hover-box').stop().addClass('animated slideInLeft');
							}
							if (hoverBoxEffect == '6') {
								jQuery(this).find('.mnwall-hover-box').stop().addClass('animated slideInTop');
							}
							if (hoverBoxEffect == '7') {
								jQuery(this).find('.mnwall-hover-box').stop().addClass('animated slideInBottom');
							}
							if (hoverBoxEffect == '8') {
								jQuery(this).find('.mnwall-hover-box').stop().addClass('animated mnwzoomIn');
							}
											
						}).mouseleave(function (e) {   
							
							if (hoverBoxEffect == 'no') {
								jQuery(this).find('.mnwall-hover-box').stop().removeClass('hoverShow');
							}
							if (hoverBoxEffect == '1') {
								jQuery(this).find('.mnwall-hover-box').stop().removeClass('hoverFadeIn');
							}
							if (hoverBoxEffect == '2') {
								jQuery(this).find('.mnwall-item-outer-cont').stop().removeClass('hoverFlipY');
							}
							if (hoverBoxEffect == '3') {
								jQuery(this).find('.mnwall-item-outer-cont').stop().removeClass('hoverFlipX');
							}
							if (hoverBoxEffect == '4') {
								jQuery(this).find('.mnwall-hover-box').stop().removeClass('slideInRight');
							}
							if (hoverBoxEffect == '5') {
								jQuery(this).find('.mnwall-hover-box').stop().removeClass('slideInLeft');
							}
							if (hoverBoxEffect == '6') {
								jQuery(this).find('.mnwall-hover-box').stop().removeClass('slideInTop');
							}
							if (hoverBoxEffect == '7') {
								jQuery(this).find('.mnwall-hover-box').stop().removeClass('slideInBottom');
							}
							if (hoverBoxEffect == '8') {
								jQuery(this).find('.mnwall-hover-box').stop().removeClass('mnwzoomIn');
							}
							
						});
					}
				}
				triggerHoverBox();
			}
			
		";
		
		return $javascript;
	}
	
	public function initializeFiltersSortings($widgetID)
	{		
		$javascript = "
		
			// Filters
			var filters = {};
			jQuery('#mnwall_iso_filters_".$widgetID."').on( 'click', '.mnwall-filter', function(event) 
			{
				event.preventDefault();
				var \$this = jQuery(this);
				// get group key
				var \$buttonGroup = \$this.parents('.button-group');
				var filterGroup = \$buttonGroup.attr('data-filter-group');
				// set filter for group
				filters[ filterGroup ] = \$this.attr('data-filter');
				// combine filters
				var filterValue = '';
				for ( var prop in filters ) {
					filterValue += filters[ prop ];
				}
				// set filter for Isotope
				_wall.isotope({ 
					filter: filterValue 
				});
			});
			
			// Change active class on filter buttons
			var active_Filters = function active_Filters() {
				var \$activeFilters = _container.find('.button-group').each( function( i, buttonGroup ) {
					var \$buttonGroup = jQuery( buttonGroup );
					\$buttonGroup.on( 'click', 'a', function(event) {
						event.preventDefault();
						\$buttonGroup.find('.mnw_filter_active').removeClass('mnw_filter_active');
						jQuery( this ).addClass('mnw_filter_active');
					});
				});
			};
			active_Filters();
			
			// Dropdown filter list
			var dropdown_Filters = function dropdown_Filters() {
				var \$dropdownFilters = _container.find('.mnwall_iso_filters .mnwall_iso_dropdown').each( function( i, dropdownGroup ) {
					var \$dropdownGroup = jQuery( dropdownGroup );
					\$dropdownGroup.on( 'click', '.dropdown-label', function(event) {
						event.preventDefault();
						\$dropdownGroup.toggleClass('expanded');
					});
				});
				jQuery(document).mouseup(function (e)
				{
					var \$dropdowncontainer = _container.find('.mnwall_iso_dropdown');
				
					if (!\$dropdowncontainer.is(e.target)
						&& \$dropdowncontainer.has(e.target).length === 0
						) 
					{
						\$dropdowncontainer.removeClass('expanded');
					}
				});
			};
			dropdown_Filters();
				
			// Bind sort button click
			_container.find('.sorting-group-filters').on( 'click', '.mnwall-filter', function(event) {
				event.preventDefault();
				var sortValue = jQuery(this).attr('data-sort-value');
				// set filter for Isotope
				_wall.isotope({ 
					sortBy: sortValue
				});
			});
			
			// Change active class on sorting filters
			_container.find('.sorting-group-filters').each( function( i, sortingGroup ) {
				var \$sortingGroup = jQuery( sortingGroup );
				\$sortingGroup.on( 'click', '.mnwall-filter', function() {
					\$sortingGroup.find('.mnw_filter_active').removeClass('mnw_filter_active');
					jQuery( this ).addClass('mnw_filter_active');
				});
			});
			
			// Bind sorting direction button click
			_container.find('.sorting-group-direction').on( 'click', '.mnwall-filter', function(event) {
				event.preventDefault();
				var sortDirection = jQuery(this).attr('data-sort-value');
				if (sortDirection == 'asc') {
					sort_Direction = true;
				} else {
					sort_Direction = false;
				}
				// set direction
				_wall.isotope({ 
					sortAscending: sort_Direction
				});
			});
			
			// Change active class on sorting direction
			_container.find('.sorting-group-direction').each( function( i, sortingDirection ) {
				var \$sortingDirection = jQuery( sortingDirection );
				\$sortingDirection.on( 'click', '.mnwall-filter', function() {
					\$sortingDirection.find('.mnw_filter_active').removeClass('mnw_filter_active');
					jQuery( this ).addClass('mnw_filter_active');
				});
			});
			
			// Dropdown sorting list
			var dropdown_Sortings = function dropdown_Sortings() {
				var \$dropdownSortings = _container.find('.mnwall_iso_sortings .mnwall_iso_dropdown').each( function( i, dropdownSorting ) {
					var \$dropdownSorting = jQuery( dropdownSorting );
					\$dropdownSorting.on( 'click', '.dropdown-label', function(event) {
						event.preventDefault();
						\$dropdownSorting.toggleClass('expanded');
					});
				});
			};
			dropdown_Sortings();
			
			// Reset Filters and sortings
			jQuery('#mnwall_reset_".$widgetID."').on( 'click', '', function(event) 
			{
				var \$resetFilters = _container.find('.button-group').each( function( i, buttonGroup ) {
					var \$buttonGroup = jQuery( buttonGroup );
					\$buttonGroup.find('.mnw_filter_active').removeClass('mnw_filter_active');
					\$buttonGroup.find('li:first-child a').addClass('mnw_filter_active');
					
					// Reset filters
					var filterGroup = \$buttonGroup.attr('data-filter-group');
					filters[ filterGroup ] = '';
					var filterValue = '';
					// set filter for Isotope
					_wall.isotope({ 
						filter: filterValue,
						sortBy: 'original-order',
						sortAscending: true
					});		
					
				});
				var \$resetSortings = _container.find('.sorting-group-filters').each( function( i, sortingGroup ) {
					var \$sortingGroup = jQuery( sortingGroup );
					\$sortingGroup.find('.mnw_filter_active').removeClass('mnw_filter_active');
					\$sortingGroup.find('li:first-child a').addClass('mnw_filter_active');
				});
				var \$resetSortingDirection = _container.find('.sorting-group-direction').each( function( i, sortingGroupDirection ) {
					var \$sortingGroupDirection = jQuery( sortingGroupDirection );
					\$sortingGroupDirection.find('.mnw_filter_active').removeClass('mnw_filter_active');
					\$sortingGroupDirection.find('li:first-child a').addClass('mnw_filter_active');
				});
				
			});
			
		";
		
		return $javascript;
	}
	
	public function initializePagination()
	{		
		$javascript = "
		
			// Last page
			if (_container.find('.more-results.mnw-all').attr('data-page') == endPage) {
				_container.find('.more-results.mnw-all').addClass('disabled');
				_container.find('.more-results.mnw-all span.more-results').hide();
				_container.find('.more-results.mnw-all span.no-results').show();
				_container.find('.more-results.mnw-all img').hide();
			}
			
			// Create spinner
			var opts = {
			  lines: 9,
			  length: 4,
			  width: 3,
			  radius: 3,
			  corners: 1,
			  rotate: 0,
			  direction: 1,
			  color: '#000',
			  speed: 1,
			  trail: 52,
			  shadow: false,
			  hwaccel: false,
			  className: 'spinner',
			  zIndex: 2e9,
			  top: '50%',
			  left: '50%'
			};
			_container.find('.mas_loader').append(new Spinner(opts).spin().el);
			
		";
		
		return $javascript;
	}
	
	public function initializeAppendPagination($widgetID)
	{		
		$javascript = "
		
			// Load more (Append) pagination
			_container.find('.more-results.mnw-all').on( 'click', function(event)
			{
				event.preventDefault();
				
				if (jQuery(this).hasClass('disabled')) {
					return false;
				}
								
				// Find page
				var dataPage = jQuery(this).attr('data-page');
				page = parseInt(dataPage);
				new_page = page + 1;
				
				// Increment page in data-page
				jQuery(this).attr('data-page', new_page);
				
				// Show loader
				_container.find('.more-results').addClass('mnwall-loading');
				_container.find('.more-results span.more-results').hide();
				_container.find('.mnwall_append_loader').show();
						
				// Ajax request			
				jQuery.ajax({
					type: 'POST',
					url: site_path+'index.php?option=com_minitekwall&view=masonry&widget_id=".$widgetID."&format=raw&page=' + page + '&Itemid=' + itemid + '&' + token,
					success: function(msg) 
					{
						if (msg.length > 3) 
						{
							// Append items
							newItems = jQuery(msg).appendTo(_wall);
							newItems.css({'visibility':'hidden'});
							imagesLoaded( _wall, function() {
								newItems.css({'visibility':'visible'});
								_wall.isotope( 'appended', newItems );
								_wall.isotope('updateSortData').isotope();
							});
																													
							// Hover box trigger
							if (hoverBox == '1') {
								triggerHoverBox();
							}
							
							// Store active button
							var _activeButtonCategory = _container.find('.button-group-category').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonTag = _container.find('.button-group-tag').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonLocation = _container.find('.button-group-location').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonDate = _container.find('.button-group-date').find('.mnw_filter_active').attr('data-filter');
							
							// Update filters
							jQuery.ajax({
								type: 'POST',
								url: site_path+'index.php?option=com_minitekwall&view=filters&widget_id=".$widgetID."&format=raw&page=' + page + '&pagination=' + pagination + '&Itemid=' + itemid + '&' + token,
								success: function(msg) 
								{
									if (msg.length > 3) 
									{
										// Add new filters
										_container.find('.mnwall_iso_filters').html(msg);
										// Restore active button after success
										_container.find('.button-group-category').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-category').find('[data-filter=\'' + _activeButtonCategory + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-tag').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-tag').find('[data-filter=\'' + _activeButtonTag + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-location').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-location').find('[data-filter=\'' + _activeButtonLocation + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-date').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-date').find('[data-filter=\'' + _activeButtonDate + '\']').addClass('mnw_filter_active');
									} 	
									
									active_Filters();
									dropdown_Filters();					
								}
							});
							
							// Hide loader
							_container.find('.more-results').removeClass('mnwall-loading');
							_container.find('.more-results span.more-results').show();
							_container.find('.mnwall_append_loader').hide();
							_container.find('.more-results').blur();
							
							// Deduct remaining items number in button
							_remaining = _container.find('.mnw-total-items').text();
							remaining = parseInt(_remaining) - parseInt(pageLimit);
							_container.find('.mnw-total-items').html(remaining);
							
							// Last page
							if (_container.find('.more-results').attr('data-page') == endPage) {
								_container.find('.more-results').addClass('disabled');
								_container.find('.mnw-total-items').html('0');
								_container.find('.more-results span.more-results').hide();
								_container.find('.more-results span.no-results').show();
								_container.find('.more-results img').hide();
							}
							
							// Fancybox
							if (fancybox == 1) {
								_container.find('.mnwall-item-fancy-icon').fancybox();
							}
			
						} 
						else 
						{
							_container.find('.more-results').addClass('disabled');
							_container.find('.more-results span.more-results').hide();
							_container.find('.more-results span.no-results').show();
							_container.find('.more-results img').hide();
						}
					}
				});
				
			});
			
		";
		
		return $javascript;
	}
	
	public function initializeInfinitePagination($widgetID)
	{		
		$javascript = "
			
			_container.find('.more-results.mnw-all').bind('inview', function(event, isInView, visiblePartX, visiblePartY) {
				if (isInView) {
					// element is now visible in the viewport
					if (visiblePartY == 'top') {} else if (visiblePartY == 'bottom') {
					} else {
						infiniteWall();
					}
				}
			});
					
			// Infinite pagination
			function infiniteWall()
			{			
				\$this = _container.find('.more-results.mnw-all');
				
				if (\$this.hasClass('disabled')) {
					return false;
				}
								
				// Find page
				var dataPage = \$this.attr('data-page');
				page = parseInt(dataPage);
				new_page = page + 1;
				
				// Check if there is a pending ajax request
				if (typeof ajax_request !== 'undefined') {
					ajax_request.abort();
					_container.find('.more-results span.more-results').show();
					_container.find('.mnwall_append_loader').hide();
				}
				
				// Show loader
				_container.find('.more-results').addClass('mnwall-loading');
				_container.find('.more-results span.more-results').hide();
				_container.find('.mnwall_append_loader').show();
						
				// Ajax request			
				ajax_request = jQuery.ajax({
					type: 'POST',
					url: site_path+'index.php?option=com_minitekwall&view=masonry&widget_id=".$widgetID."&format=raw&page=' + page + '&Itemid=' + itemid + '&' + token,
					success: function(msg) 
					{
						if (msg.length > 3) 
						{
							// Increment page in data-page
							\$this.attr('data-page', new_page);
				
							// Append items
							newItems = jQuery(msg).appendTo(_wall);
							newItems.css({'visibility':'hidden'});
							imagesLoaded( _wall, function() {
								newItems.css({'visibility':'visible'});
								_wall.isotope( 'appended', newItems );
								_wall.isotope('updateSortData').isotope();
							});
																													
							// Hover box trigger
							if (hoverBox == '1') {
								triggerHoverBox();
							}
							
							// Store active button
							var _activeButtonCategory = _container.find('.button-group-category').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonTag = _container.find('.button-group-tag').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonLocation = _container.find('.button-group-location').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonDate = _container.find('.button-group-date').find('.mnw_filter_active').attr('data-filter');
							
							// Update filters
							jQuery.ajax({
								type: 'POST',
								url: site_path+'index.php?option=com_minitekwall&view=filters&widget_id=".$widgetID."&format=raw&page=' + page + '&pagination=' + pagination + '&Itemid=' + itemid + '&' + token,
								success: function(msg) 
								{
									if (msg.length > 3) 
									{
										// Add new filters
										_container.find('.mnwall_iso_filters').html(msg);
										// Restore active button after success
										_container.find('.button-group-category').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-category').find('[data-filter=\'' + _activeButtonCategory + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-tag').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-tag').find('[data-filter=\'' + _activeButtonTag + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-location').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-location').find('[data-filter=\'' + _activeButtonLocation + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-date').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-date').find('[data-filter=\'' + _activeButtonDate + '\']').addClass('mnw_filter_active');
									} 	
									
									active_Filters();
									dropdown_Filters();					
								}
							});
							
							// Hide loader
							_container.find('.more-results').removeClass('mnwall-loading');
							_container.find('.more-results span.more-results').show();
							_container.find('.mnwall_append_loader').hide();
							
							// Last page
							if (_container.find('.more-results').attr('data-page') == endPage) {
								_container.find('.more-results').addClass('disabled');
								_container.find('.more-results span.more-results').hide();
								_container.find('.more-results span.no-results').show();
								_container.find('.more-results img').hide();
							}
							
							// Fancybox
							if (fancybox == 1) {
								_container.find('.mnwall-item-fancy-icon').fancybox();
							}
							
							// Run function again until load more button is out of viewport
							if(_container.find('.mnwall_more_results').visible())
							{
								infiniteWall();
							}
						} 
						else 
						{
							_container.find('.more-results').addClass('disabled');
							_container.find('.more-results span.more-results').hide();
							_container.find('.more-results span.no-results').show();
							_container.find('.more-results img').hide();
						}
					}
				});
			
			}
			
		";
		
		return $javascript;
	}
	
	public function initializeArrowsPagination($widgetID)
	{		
		$javascript = "
		
			// Previous arrow pagination
			_container.find('.mnwall_arrow_prev').on( 'click', function(event)
			{
				event.preventDefault();
				
				var current = jQuery(this);
				
				if (jQuery(this).hasClass('disabled')) {
					return false;
				}
										
				// Find page
				var dataPage = jQuery(this).attr('data-page');
				page = parseInt(dataPage);
				new_page = page - 1;
				next_page = page + 1;
				
				// Check if there is a pending ajax request
				if (typeof ajax_request !== 'undefined') {
					ajax_request.abort();
					_container.find('.mnwall_arrow_next').removeClass('mnwall-loading');
					_container.find('.more-results').show();
					_container.find('.mnwall_arrow_loader').hide();	
				}
				
				// Show loader
				jQuery(this).addClass('mnwall-loading');
				current.find('.more-results').hide();
				current.find('.mnwall_arrow_loader').show();
				
				// Ajax request			
				ajax_request = jQuery.ajax({
					type: 'POST',
					url: site_path+'index.php?option=com_minitekwall&view=masonry&widget_id=".$widgetID."&format=raw&page=' + page + '&Itemid=' + itemid + '&' + token,
					success: function(msg) 
					{
						if (msg.length > 3) 
						{
							// Decrease page in link id
							_container.find('.mnwall_arrow_prev').attr('data-page', new_page);
							_container.find('.mnwall_arrow_next').attr('data-page', next_page);
				
							// Append items
							var elems = _wall.isotope('getItemElements');
							newItems = jQuery(msg).appendTo(_wall);
							newItems.css({'visibility':'hidden'});
							imagesLoaded( _wall, function() {
								_wall.isotope( 'remove', elems );
								newItems.css({'visibility':'visible'});
								_wall.isotope( 'insert', newItems );
								_wall.isotope('updateSortData').isotope();
							});
																														
							// Hover box trigger
							if (hoverBox == '1') {
								triggerHoverBox();
							}
							
							// Store active button
							var _activeButtonCategory = _container.find('.button-group-category').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonTag = _container.find('.button-group-tag').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonLocation = _container.find('.button-group-location').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonDate = _container.find('.button-group-date').find('.mnw_filter_active').attr('data-filter');
							
							// Update filters
							jQuery.ajax({
								type: 'POST',
								url: site_path+'index.php?option=com_minitekwall&view=filters&widget_id=".$widgetID."&format=raw&page=' + page + '&pagination=' + pagination + '&Itemid=' + itemid + '&' + token,
								success: function(msg) 
								{
									if (msg.length > 3) 
									{
										// Add new filters
										jQuery('#mnwall_container .mnwall_iso_filters').html(msg);
										// Restore active button after success
										_container.find('.button-group-category').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-category').find('[data-filter=\'' + _activeButtonCategory + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-tag').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-tag').find('[data-filter=\'' + _activeButtonTag + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-location').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-location').find('[data-filter=\'' + _activeButtonLocation + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-date').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-date').find('[data-filter=\'' + _activeButtonDate + '\']').addClass('mnw_filter_active');
									} 
									
									active_Filters();
									dropdown_Filters();
								}
							});
							
							// Hide loader
							_container.find('.mnwall_arrow_prev').removeClass('mnwall-loading');
							current.find('.more-results').show();
							current.find('.mnwall_arrow_loader').hide();
							
							// Enable next button
							_container.find('.mnwall_arrow_next').removeClass('disabled');
							
							// Disable previous button on 1st page
							if (new_page <= 0) 
							{
								if (new_page < 0) 
								{
									_container.find('.mnwall_arrow_prev').attr('data-page', 0);
									_container.find('.mnwall_arrow_next').attr('data-page', 2);
								}
								
								// Disable previous button
								_container.find('.mnwall_arrow_prev').addClass('disabled');
							}
							
							// Fancybox
							if (fancybox == 1) {
								_container.find('.mnwall-item-fancy-icon').fancybox();
							}
							
						} 
						else 
						{
							// Disable previous button / Hide loader
							_container.find('.mnwall_arrow_prev').addClass('disabled');
							_container.find('.mnwall_arrow_loader').hide();
						}
					}
				});
				
			});
			
			// Next arrow pagination
			_container.find('.mnwall_arrow_next').on( 'click', function(event)
			{
				event.preventDefault();
				
				var current = jQuery(this);
				
				if (jQuery(this).hasClass('disabled')) {
					return false;
				}
										
				// Find page
				var dataPage = jQuery(this).attr('data-page');
				page = parseInt(dataPage);
				next_page = page + 1;
				prev_page = page - 1;
				end_page_next = next_page - 1;
				end_page_prev = next_page - 3;
				
				// Check if there is a pending ajax request
				if (typeof ajax_request !== 'undefined') {
					ajax_request.abort();
					_container.find('.mnwall_arrow_prev').removeClass('mnwall-loading');
					_container.find('.more-results').show();
					_container.find('.mnwall_arrow_loader').hide();	
				}
				
				// Show loader
				jQuery(this).addClass('mnwall-loading');
				current.find('.more-results').hide();
				current.find('.mnwall_arrow_loader').show();
								
				// Ajax request			
				ajax_request = jQuery.ajax({
					type: 'POST',
					url: site_path+'index.php?option=com_minitekwall&view=masonry&widget_id=".$widgetID."&format=raw&page=' + page + '&Itemid=' + itemid + '&' + token,
					success: function(msg) 
					{
						if (msg.length > 3) 
						{
							// Increment page in link id
							_container.find('.mnwall_arrow_next').attr('data-page', next_page);
							_container.find('.mnwall_arrow_prev').attr('data-page', prev_page);
				
							// Append items
							var elems = _wall.isotope('getItemElements');
							newItems = jQuery(msg).appendTo(_wall);
							newItems.css({'visibility':'hidden'});
							imagesLoaded( _wall, function() {
								_wall.isotope( 'remove', elems );
								newItems.css({'visibility':'visible'});
								_wall.isotope( 'insert', newItems );
								_wall.isotope('updateSortData').isotope();
							});
														
							// Hover box trigger
							if (hoverBox == '1') {
								triggerHoverBox();
							}
							
							// Store active button
							var _activeButtonCategory = _container.find('.button-group-category').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonTag = _container.find('.button-group-tag').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonLocation = _container.find('.button-group-location').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonDate = _container.find('.button-group-date').find('.mnw_filter_active').attr('data-filter');
							// Update filters
							jQuery.ajax({
								type: 'POST',
								url: site_path+'index.php?option=com_minitekwall&view=filters&widget_id=".$widgetID."&format=raw&page=' + page + '&pagination=' + pagination + '&Itemid=' + itemid + '&' + token,
								success: function(msg) 
								{
									if (msg.length > 3) 
									{
										// Add new filters
										_container.find('.mnwall_iso_filters').html(msg);
										// Restore active button after success
										_container.find('.button-group-category').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-category').find('[data-filter=\'' + _activeButtonCategory + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-tag').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-tag').find('[data-filter=\'' + _activeButtonTag + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-location').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-location').find('[data-filter=\'' + _activeButtonLocation + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-date').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-date').find('[data-filter=\'' + _activeButtonDate + '\']').addClass('mnw_filter_active');
									} 
									
									active_Filters();
									dropdown_Filters();
								}
							});
							
							// Hide loader
							_container.find('.mnwall_arrow_next').removeClass('mnwall-loading');
							current.find('.more-results').show();
							current.find('.mnwall_arrow_loader').hide();
							
							// Enable previous button
							_container.find('.mnwall_arrow_prev').removeClass('disabled');
							
							// Last page
							if (_container.find('.mnwall_arrow_next').attr('data-page') == endPage) {
								_container.find('.mnwall_arrow_next').addClass('disabled');
							}
							
							// Fancybox
							if (fancybox == 1) {
								_container.find('.mnwall-item-fancy-icon').fancybox();
							}
							
						} 
						else 
						{
							// Disable next button / Hide loader
							_container.find('.mnwall_arrow_next').addClass('disabled');
							_container.find('.mnwall_arrow_loader').hide();
							_container.find('.mnwall_arrow_prev').attr('data-page', end_page_prev);
							_container.find('.mnwall_arrow_next').attr('data-page', end_page_next);
						}
					}
				});
				
			});
		
		";
		
		return $javascript;
	}
	
	public function initializePagesPagination($widgetID)
	{		
		$javascript = "
		
			// Pages pagination
			_container.find('.mnwall_page').on( 'click', function(event)
			{
				var current = jQuery(this);
				_container.find('.mnwall_page').removeClass('mnw_active');
				
				event.preventDefault();
										
				// Find page
				var dataPage = jQuery(this).attr('data-page');
				page = parseInt(dataPage);
				
				// Check if there is a pending ajax request
				if (typeof ajax_request !== 'undefined') {
					ajax_request.abort();
					_container.find('.page-number').show();
					_container.find('.mnwall_page_loader').hide();	
				}
				
				// Show loader
				current.find('.page-number').hide();
				current.find('.mnwall_page_loader').show();
								
				// Ajax request			
				ajax_request = jQuery.ajax({
					type: 'POST',
					url: site_path+'index.php?option=com_minitekwall&view=masonry&widget_id=".$widgetID."&format=raw&page=' + page + '&Itemid=' + itemid + '&' + token,
					success: function(msg) 
					{
						if (msg.length > 3) 
						{
							// Append items
							var elems = _wall.isotope('getItemElements');
							newItems = jQuery(msg).appendTo(_wall);
							newItems.css({'visibility':'hidden'});
							imagesLoaded( _wall, function() {
								_wall.isotope( 'remove', elems );
								newItems.css({'visibility':'visible'});
								_wall.isotope( 'insert', newItems );
								_wall.isotope('updateSortData').isotope();
							});
																														
							// Hover box trigger
							if (hoverBox == '1') {
								triggerHoverBox();
							}
							
							// Store active button
							var _activeButtonCategory = _container.find('.button-group-category').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonTag = _container.find('.button-group-tag').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonLocation = _container.find('.button-group-location').find('.mnw_filter_active').attr('data-filter');
							var _activeButtonDate = _container.find('.button-group-date').find('.mnw_filter_active').attr('data-filter');
							// Update filters
							jQuery.ajax({
								type: 'POST',
								url: site_path+'index.php?option=com_minitekwall&view=filters&widget_id=".$widgetID."&format=raw&page=' + page + '&pagination=' + pagination + '&Itemid=' + itemid + '&' + token,
								success: function(msg) 
								{
									if (msg.length > 3) 
									{
										// Add new filters
										_container.find('.mnwall_iso_filters').html(msg);
										// Restore active button after success
										_container.find('.button-group-category').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-category').find('[data-filter=\'' + _activeButtonCategory + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-tag').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-tag').find('[data-filter=\'' + _activeButtonTag + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-location').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-location').find('[data-filter=\'' + _activeButtonLocation + '\']').addClass('mnw_filter_active');
										_container.find('.button-group-date').find('.mnw_filter_active').removeClass('mnw_filter_active');
										_container.find('.button-group-date').find('[data-filter=\'' + _activeButtonDate + '\']').addClass('mnw_filter_active');
									} 
									
									active_Filters();
									dropdown_Filters();
								}
							});
							
							// Hide loader
							current.find('.page-number').show();
							current.find('.mnwall_page_loader').hide();
							
							// Remove active class
							if (!jQuery(current).hasClass('mnw_active')) {
								jQuery(current).addClass('mnw_active');
							}
							
							// Fancybox
							if (fancybox == 1) {
								_container.find('.mnwall-item-fancy-icon').fancybox();
							}
							
						} 
						else 
						{
							// Hide loader
							_container.find('.mnwall_page_loader').hide();
						}
					}
				});
				
			});
		
		";
		
		return $javascript;
	}
	
}