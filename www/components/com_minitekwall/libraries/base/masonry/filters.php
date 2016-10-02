<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2014 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

class MinitekWallLibBaseMasonryFilters
{
	var $utilities = null;
	
	function __construct()
	{
		$this->utilities = new MinitekWallLibUtilities;		
		
		return;	
	}
	
	public function getFilters($wall, $masonry_params)
	{
		// Start output
		$output = '';
		
		// Category Filters
		if ($masonry_params['mas_category_filters'])
		{
			// Create cat filters 
			$cat_array = array();		
			foreach($wall as $key=>$wall_item) 
			{	  
				if (isset($wall_item->itemCategoryRaw)) 
				{					
					array_push($cat_array, $wall_item->itemCategoryRaw);	
				}
				else if (isset($wall_item->itemCategoriesRaw)) 
				{				
					foreach ($wall_item->itemCategoriesRaw as $key=>$itemCategory) 
					{
						if (is_array($itemCategory))
						{
							array_push($cat_array, $itemCategory['category_name']);	
						}
						else
						{
							// For Easyblog categories
							array_push($cat_array, $itemCategory->getTitle());	
						}
					}
				}
			}
			$cat_array = array_unique($cat_array);
			asort($cat_array);
			$cat_array = array_values($cat_array);	
			
			if ($masonry_params['mas_filter_type'] == '1') 
			{
				// Inline filters
				$output .= '<div class="button-group button-group-category mnwall_iso_buttons" data-filter-group="category">';
				if ($masonry_params['mas_category_filters_label'])
				{
					$output .= '<span>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_category_filters_label']).'</span>';
				}
				$output .= '<ul>';
					$output .= '<li>';
					$output .= '<a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a>';
					$output .= '</li>';
					foreach ($cat_array as $category) 
					{
						$cat_name_fixed = $this->utilities->cleanName($category);
						$category = htmlspecialchars($category);
						$output .= '<li>';
						$output .= '<a href="#" data-filter=".cat-'.$cat_name_fixed.'" class="mnwall-filter">'.$category.'</a>';
						$output .= '</li>';
					}	
				$output .= '</ul>';	
				$output .= '</div>';	
			}
			
			if ($masonry_params['mas_filter_type'] == '2') 
			{
				// Dropdown filters
				$output .= '<div class="mnwall_iso_dropdown">';
					$output .= '<div class="dropdown-label cat-label">';
						$output .= '<span data-label="'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_category_filters_label']).'">';
						$output .= '<i class="fa fa-angle-down"></i>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_category_filters_label']);
						$output .= '</span>';
					$output .= '</div>';
					$output .= '<ul class="button-group button-group-category" data-filter-group="category">';
						$output .= '<li><a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a></li>';
						foreach ($cat_array as $category) 
						{
							$cat_name_fixed = $this->utilities->cleanName($category);
							$category = htmlspecialchars($category);
							$output .= '<li><a href="#" data-filter=".cat-'.$cat_name_fixed.'" class="mnwall-filter">'.$category.'</a></li>';
						}	
					$output .= '</ul>';
				$output .= '</div>';
			}
			
		}
		
		// Tag Filters
		if ($masonry_params['mas_tag_filters'])
		{
			// Create tag filters 
			$tag_array = array();		
			foreach($wall as $key=>$wall_item) 
			{	
				if (isset($wall_item->itemTags)) 
				{		
					foreach($wall_item->itemTags as $key=>$itemTag) 
					{
						array_push($tag_array, $itemTag->title);	
					}
				}
				else if (isset($wall_item->itemAuthorsRaw))
				{
					$manufacturerModel = VmModel::getModel('Manufacturer');
					foreach($wall_item->itemAuthorsRaw as $key=>$itemTag) 
					{
						$manufacturer = $manufacturerModel->getManufacturer((int)$itemTag);						
						array_push($tag_array, $manufacturer->mf_name);	
					}
				}
			}
			$tag_array = array_unique($tag_array);
			asort($tag_array);
			$tag_array = array_values($tag_array);	
			
			if ($masonry_params['mas_filter_type'] == '1') 
			{
				// Inline filters
				$output .= '<div class="button-group button-group-tag mnwall_iso_buttons" data-filter-group="tag">';
				if ($masonry_params['mas_tag_filters_label'])
				{
					$output .= '<span>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_tag_filters_label']).'</span>';
				}
				$output .= '<ul>';
					$output .= '<li>';
					$output .= '<a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a>';
					$output .= '</li>';
					foreach ($tag_array as $tagName) 
					{ 
						$tag_name_fixed = $this->utilities->cleanName($tagName);
						$tag = htmlspecialchars($tagName);
						$output .= '<li>';
						$output .= '<a href="#" data-filter=".tag-'.$tag_name_fixed.'" class="mnwall-filter">'.$tag.'</a>';
						$output .= '</li>';
					}
				$output .= '</ul>';		
				$output .= '</div>';	
			}
			
			if ($masonry_params['mas_filter_type'] == '2') 
			{
				// Dropdown filters
				$output .= '<div class="mnwall_iso_dropdown">';
					$output .= '<div class="dropdown-label tag-label">';
						$output .= '<span data-label="'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_tag_filters_label']).'">';
						$output .= '<i class="fa fa-angle-down"></i>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_tag_filters_label']); 
						$output .= '</span>';
					$output .= '</div>';
					$output .= '<ul class="button-group button-group-tag" data-filter-group="tag">';
						$output .= '<li><a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a></li>';
						foreach ($tag_array as $tagName) 
						{
							$tag_name_fixed = $this->utilities->cleanName($tagName);
							$tag = htmlspecialchars($tagName);
							$output .= '<li><a href="#" data-filter=".tag-'.$tag_name_fixed.'" class="mnwall-filter">'.$tag.'</a></li>';
						}	
					$output .= '</ul>';
				$output .= '</div>';
			}
			
		}
		
		// Location Filters
		if ($masonry_params['mas_location_filters'])
		{
			// Create location filters 
			$location_array = array();		
			foreach($wall as $key=>$wall_item) 
			{	  
				if (isset($wall_item->itemLocationRaw))
				{					
					array_push($location_array, $wall_item->itemLocationRaw);	
				}
			}
			$location_array = array_filter($location_array);
			$location_array = array_unique($location_array);
			asort($location_array);
			$location_array = array_values($location_array);
			
			if ($masonry_params['mas_filter_type'] == '1') 
			{
				// Inline filters	
				$output .= '<div class="button-group button-group-location mnwall_iso_buttons" data-filter-group="location">';
				if ($masonry_params['mas_location_filters_label'])
				{
					$output .= '<span>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_location_filters_label']).'</span>';
				}
				$output .= '<ul>';
					$output .= '<li>';
					$output .= '<a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a>';
					$output .= '</li>';
					foreach ($location_array as $location) 
					{
						$location_name_fixed = $this->utilities->cleanName($location);
						$location = htmlspecialchars($location);
						$output .= '<li>';
						$output .= '<a href="#" data-filter=".location-'.$location_name_fixed.'" class="mnwall-filter">'.$location.'</a>';
						$output .= '</li>';
					}		
				$output .= '</ul>';
				$output .= '</div>';
			}
			
			if ($masonry_params['mas_filter_type'] == '2') 
			{
				// Dropdown filters
				$output .= '<div class="mnwall_iso_dropdown">';
					$output .= '<div class="dropdown-label location-label">';
						$output .= '<span data-label="'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_location_filters_label']).'">';
						$output .= '<i class="fa fa-angle-down"></i>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_location_filters_label']);
						$output .= '</span>';
					$output .= '</div>';
					$output .= '<ul class="button-group button-group-location" data-filter-group="location">';
						$output .= '<li><a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a></li>';
						foreach ($location_array as $location) 
						{
							$location_name_fixed = $this->utilities->cleanName($location);
							$location = htmlspecialchars($location);
							$output .= '<li><a href="#" data-filter=".location-'.$location_name_fixed.'" class="mnwall-filter">'.$location.'</a></li>';
						}	
					$output .= '</ul>';
				$output .= '</div>';
			}
			
		}
		
		// Date Filters
		if ($masonry_params['mas_date_filters'])
		{
			// Create date filters 
			$date_array = array();		
			foreach($wall as $key=>$wall_item) 
			{	  
				if (isset($wall_item->itemDateRaw))
				{					
					array_push($date_array, JHTML::_('date', $wall_item->itemDateRaw, 'Y-m'));	
				}
			}
			$date_array = array_unique($date_array);
			rsort($date_array);
			$date_array = array_values($date_array);	
			
			if ($masonry_params['mas_filter_type'] == '1') 
			{
				// Inline filters		
				$output .= '<div class="button-group button-group-date mnwall_iso_buttons" data-filter-group="date">';
				if ($masonry_params['mas_date_filters_label'])
				{
					$output .= '<span>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_date_filters_label']).'</span>';
				}
				$output .= '<ul>';
					$output .= '<li>';
					$output .= '<a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a>';
					$output .= '</li>';
					foreach ($date_array as $date) 
					{
						$date_name_fixed = $this->utilities->cleanName($date);
						$date = JHTML::_('date', $date, 'M Y');
						$output .= '<li>';
						$output .= '<a href="#" data-filter=".date-'.$date_name_fixed.'" class="mnwall-filter">'.$date.'</a>';
						$output .= '</li>';
					}
				$output .= '</ul>';		
				$output .= '</div>';
			}
			
			if ($masonry_params['mas_filter_type'] == '2') 
			{
				// Dropdown filters
				$output .= '<div class="mnwall_iso_dropdown">';
					$output .= '<div class="dropdown-label date-label">';
						$output .= '<span data-label="'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_date_filters_label']).'">';
						$output .= '<i class="fa fa-angle-down"></i>'.JText::_('COM_MINITEKWALL_'.$masonry_params['mas_date_filters_label']); 
						$output .= '</span>';
					$output .= '</div>';
					$output .= '<ul class="button-group button-group-date" data-filter-group="date">';
						$output .= '<li><a href="#" data-filter="" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_SHOW_ALL').'</a></li>';
						foreach ($date_array as $date) 
						{
							$date_name_fixed = $this->utilities->cleanName($date);
							$date = JHTML::_('date', $date, 'M Y');
							
							$output .= '<li><a href="#" data-filter=".date-'.$date_name_fixed.'" class="mnwall-filter">'.$date.'</a></li>';
						}	
					$output .= '</ul>';
				$output .= '</div>';
			}
			
		}
						
		return $output;
                                                   
	}
	
	public function getSortings($masonry_params)
	{
		// Start output
		$output = '';
					
			if ($masonry_params['mas_sorting_type'] == 1) 
			{ 
				// Inline sortings
				$output .= '<div class="sorting-group sorting-group-filters mnwall_iso_buttons">';
					$output .= '<span>'.JText::_('COM_MINITEKWALL_SORT_BY').'</span>';
					$output .= '<ul>';
						$output .= '<li>';
							$output .= '<a href="#" data-sort-value="original-order" class="mnwall-filter mnw_filter_active">';
								$output .= JText::_('COM_MINITEKWALL_DEFAULT_ORDER');
							$output .= '</a>';
						$output .= '</li>';
						if ($masonry_params['mas_title_sorting']) {
							$output .= '<li>';
							$output .= '<a href="#" data-sort-value="title" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_TITLE').'</a>';
							$output .= '</li>';
						}
						if ($masonry_params['mas_category_sorting']) {
							$output .= '<li>';
							$output .= '<a href="#" data-sort-value="category" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_CATEGORY').'</a>';
							$output .= '</li>';
						}
						if ($masonry_params['mas_author_sorting']) {
							$output .= '<li>';
							$output .= '<a href="#" data-sort-value="author" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_AUTHOR').'</a>';
							$output .= '</li>';
						}
						if ($masonry_params['mas_date_sorting']) {
							$output .= '<li>';
							$output .= '<a href="#" data-sort-value="date" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_DATE').'</a>';
							$output .= '</li>';
						}
					$output .= '</ul>';
				$output .= '</div>';
			
				if ($masonry_params['mas_sorting_direction']) {
				// Inline Direction
				$output .= '<div class="sorting-group sorting-group-direction mnwall_iso_buttons">';
					$output .= '<span>'.JText::_('COM_MINITEKWALL_SORT_DIRECTION').'</span>';
					$output .= '<ul>';
						$output .= '<li>';
							$output .= '<a href="#" data-sort-value="asc" class="mnwall-filter mnw_filter_active">';
								$output .= JText::_('COM_MINITEKWALL_ASC');
							$output .= '</a>';
						$output .= '</li>';
						$output .= '<li>';
							$output .= '<a href="#" data-sort-value="desc" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_DESC').'</a>';
						$output .= '</li>';
					$output .= '</ul>';
				$output .= '</div>';
				}
				
			}
			
			if ($masonry_params['mas_sorting_type'] == 2) { 
			
				// Dropdown sortings
				$output .= '<div class="mnwall_iso_dropdown">';
					$output .= '<div class="dropdown-label sorting-label">';
						$output .= '<span data-label="'.JText::_('COM_MINITEKWALL_SORT_BY').'">';
							$output .= '<i class="fa fa-angle-down"></i>'.JText::_('COM_MINITEKWALL_SORT_BY');
						$output .= '</span>';
					$output .= '</div>';
					$output .= '<ul class="sorting-group sorting-group-filters">';
						$output .= '<li>';
							$output .= '<a href="#" data-sort-value="original-order" class="mnwall-filter mnw_filter_active">';
								$output .= JText::_('COM_MINITEKWALL_DEFAULT_ORDER');
							$output .= '</a>';
						$output .= '</li>';
						if ($masonry_params['mas_title_sorting']) {
							$output .= '<li><a href="#" data-sort-value="title" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_TITLE').'</a></li>';
						}
						if ($masonry_params['mas_category_sorting']) {
							$output .= '<li><a href="#" data-sort-value="category" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_CATEGORY').'</a></li>';
						}
						if ($masonry_params['mas_author_sorting']) {
							$output .= '<li><a href="#" data-sort-value="author" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_AUTHOR').'</a></li>';
						}
						if ($masonry_params['mas_date_sorting']) {
							$output .= '<li><a href="#" data-sort-value="date" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_DATE').'</a></li>';
						}
					$output .= '</ul>';
				$output .= '</div>';
			
				if ($masonry_params['mas_sorting_direction']) {
				// Dropdown direction
				$output .= '<div class="mnwall_iso_dropdown">';
					$output .= '<div class="dropdown-label sorting-label">';
						$output .= '<span data-label="'.JText::_('COM_MINITEKWALL_SORT_DIRECTION').'">';
							$output .= '<i class="fa fa-angle-down"></i>'.JText::_('COM_MINITEKWALL_SORT_DIRECTION');
						$output .= '</span>';
					$output .= '</div>';
					$output .= '<ul class="sorting-group sorting-group-direction">';
						$output .= '<li><a href="#" data-sort-value="asc" class="mnwall-filter mnw_filter_active">'.JText::_('COM_MINITEKWALL_ASC').'</a></li>';
						$output .= '<li><a href="#" data-sort-value="desc" class="mnwall-filter">'.JText::_('COM_MINITEKWALL_DESC').'</a></li>';
					$output .= '</ul>';
				$output .= '</div>';
				}
				
			}
						
		return $output;
	}
	
	public function getFiltersCss($masonry_params, $widgetID)
	{
		$document = JFactory::getDocument();
		$mnwall = 'mnwall_container_'.$widgetID;
		$background_color = $masonry_params['mas_filters_bg'];
		$border_radius = (int)$masonry_params['mas_filters_border_radius'];
		
		$css = '
		#'.$mnwall.' .mnwall_iso_buttons a {
			border-radius: '.$border_radius.'px;
		}
		#'.$mnwall.' .mnwall_iso_buttons a.mnw_filter_active {
			background-color: '.$background_color.';
			border-color: '.$background_color.';
		}
		#'.$mnwall.' .mnwall_iso_reset .btn-reset {
			border-radius: '.$border_radius.'px;
		}
		#'.$mnwall.' .mnwall_iso_reset .btn-reset:hover,
		#'.$mnwall.' .mnwall_iso_reset .btn-reset:focus {
			background-color: '.$background_color.';
			border-color: '.$background_color.';	
		}
		
		#'.$mnwall.' .mnwall_iso_dropdown .dropdown-label {
			border-radius: '.$border_radius.'px;
		}
		#'.$mnwall.' .mnwall_iso_dropdown.expanded .dropdown-label {
			border-radius: '.$border_radius.'px '.$border_radius.'px 0 0;
			background-color: '.$background_color.';
			border-color: '.$background_color.';
		}
		#'.$mnwall.' .mnwall_iso_dropdown ul li a.mnw_filter_active {
			color: '.$background_color.';
		}
		#'.$mnwall.' .mnwall_iso_dropdown:hover .dropdown-label {
			color: '.$background_color.';	
		}
		';	
			 
		$document->addStyleDeclaration( $css );
	}
	
}