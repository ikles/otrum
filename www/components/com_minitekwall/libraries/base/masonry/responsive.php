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

class MinitekWallLibBaseMasonryResponsive
{
	
	public function loadResponsiveMasonry($masonry_params, $widgetID)
	{
		$document = JFactory::getDocument();
		$mnwall = 'mnwall_iso_container_'.$widgetID;
		
		// Responsive settings
		$responsive_lg = (int)$masonry_params['mas_responsive_lg'];
		$responsive_lg_min = $responsive_lg - 1;
		
		$md_type = $masonry_params['mas_md_type'];
		$responsive_md_num = (int)$masonry_params['mas_responsive_md_num'];
		$responsive_md = (int)$masonry_params['mas_responsive_md'];
		$responsive_md_min = $responsive_md - 1;
		
		$sm_type = $masonry_params['mas_sm_type'];
		$responsive_sm_num = (int)$masonry_params['mas_responsive_sm_num'];
		$responsive_sm = (int)$masonry_params['mas_responsive_sm'];
		$responsive_sm_min = $responsive_sm - 1;
		
		$xs_type = $masonry_params['mas_xs_type'];
		$responsive_xs_num = (int)$masonry_params['mas_responsive_xs_num'];
		$responsive_xs = (int)$masonry_params['mas_responsive_xs'];
		$responsive_xs_min = $responsive_xs - 1;
		
		$xxs_type = $masonry_params['mas_xxs_type'];
		$responsive_xxs_num = (int)$masonry_params['mas_responsive_xxs_num'];
		
		$column_item_height = $masonry_params['mas_column_item_height'];
		$detail_box_column = $masonry_params['mas_db_columns'];
		$show_title_column = $masonry_params['mas_db_title_columns'];
		$show_introtext_column = $masonry_params['mas_db_introtext_columns'];
		$show_date_column = $masonry_params['mas_db_date_columns'];
		$show_category_column = $masonry_params['mas_db_category_columns'];
		$show_location_column = $masonry_params['mas_db_location_columns'];
		$show_author_column = $masonry_params['mas_db_author_columns'];
		$show_price_column = $masonry_params['mas_db_price_columns'];
		$show_hits_column = $masonry_params['mas_db_hits_columns'];
		$show_count_column = $masonry_params['mas_db_count_columns'];
		$show_readmore_column = $masonry_params['mas_db_readmore_columns'];
		
		// Media CSS - LG screen
		$lg_media = '@media only screen and (min-width:'.$responsive_lg.'px) 
					{}';		 
		$document->addStyleDeclaration( $lg_media );
		
		// Media CSS - MD screen
		if (!$md_type) 
		{
			$md_media_jf = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title span {
								font-size: 24px !important;
								line-height: 28px !important;
							}
							#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title span,
							#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title span,
							#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title span {
								font-size: 18px !important;
								line-height: 20px !important;
							}	
						}';		 
			$document->addStyleDeclaration( $md_media_jf );
		}
		
		// Media CSS - MD screen - Equal columns
		if ($md_type) 
		{
			$items_width = number_format((float)(100 / $responsive_md_num), 2, '.', '');
			$md_media = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{ ';
						if ($masonry_params['mas_db_position_columns'] == 'below') 
						{
							$md_media .= '
								#'.$mnwall.' .mnwall-item {
									height: auto !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									position: static;
									padding: 4px 3px 10px !important;
									width: 100% !important;	
								}
							';
						} else {
							$md_media .= '
								#'.$mnwall.' .mnwall-item {
									height: '.$column_item_height.'px !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									width: 100% !important;	
									top: auto !important;
									bottom: 0 !important;
									left: 0 !important;
									padding: 4px 3px 10px !important;
								}
							';
							if ($masonry_params['mas_db_position_columns'] == 'bottom')
							{
								$md_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: auto !important;
									}
								';
							} else {
								$md_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: 100% !important;
									}
								';
							}
						}
			$md_media .= '	
							#'.$mnwall.' .mnwall-item {
								width: '.$items_width.'% !important;
							} 
							#'.$mnwall.' .mnwall-photo-link {
								height: '.$column_item_height.'px !important;
								width: 100% !important;	
								position: relative;
								display: block;
							} 
							.mnwall-columns #'.$mnwall.' .mnwall-photo-link img {
								height: 100% !important;
								width: auto;
								max-width: inherit;
								position: absolute;
								top: -9999px;
								bottom: -9999px;
								left: -9999px;
								right: -9999px;
								margin: auto;
							}			
							#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
								font-size: 20px !important;
								line-height: 28px;
							}
						}';		 
			$document->addStyleDeclaration( $md_media );
			
			if ($detail_box_column) { 
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: block !important;	
							}
						}
				';
			} else {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $detail_box_column_css );
			
			if ($show_title_column) { 
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: block !important;	
							}
						}
				';
			} else {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_title_column_css );	
			
			if ($show_introtext_column) { 
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: block !important;	
							}
						}
				';
			} else {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_introtext_column_css );	
				
			if ($show_date_column) { 
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: block !important;	
							}
						}
				';
			} else {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_date_column_css );		
		
			if ($show_category_column) { 
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: block !important;	
							}
						}
				';
			} else {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_category_column_css );
			
			if ($show_location_column) { 
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: block !important;	
							}
						}
				';
			} else {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_location_column_css );	
			
			if ($show_author_column) { 
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_author_column_css );	
			
			if ($show_price_column) { 
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: block !important;	
							}
						}
				';
			} else {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_price_column_css );	
			
			if ($show_hits_column) { 
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: block !important;	
							}
						}
				';
			} else {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_hits_column_css );	
			
			if ($show_count_column) { 
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: block !important;	
							}
						}
				';
			} else {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_count_column_css );	
			
			if ($show_readmore_column) { 
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: block !important;	
							}
						}
				';
			} else {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_readmore_column_css );	
		}
		
		// Media CSS - SM screen
		if (!$sm_type) 
		{
			$sm_media_jf = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title span {
								font-size: 22px !important;
								line-height: 26px !important;
							}
							#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title span,
							#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title span,
							#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title span {
								font-size: 17px !important;
								line-height: 20px !important;
							}					
						}';		 
			$document->addStyleDeclaration( $sm_media_jf );
		}
		
		// Media CSS - SM screen - Equal columns
		if ($sm_type) 
		{
			$items_width = number_format((float)(100 / $responsive_sm_num), 2, '.', '');
			$sm_media = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{ ';
						if ($masonry_params['mas_db_position_columns'] == 'below') 
						{
							$sm_media .= '
								#'.$mnwall.' .mnwall-item {
									height: auto !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									position: static;
									padding: 4px 3px 10px !important;
									width: 100% !important;	
								}
							';
						} else {
							$sm_media .= '
								#'.$mnwall.' .mnwall-item {
									height: '.$column_item_height.'px !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									width: 100% !important;	
									top: auto !important;
									bottom: 0 !important;
									left: 0 !important;
									padding: 4px 3px 10px !important;
								}
							';
							if ($masonry_params['mas_db_position_columns'] == 'bottom')
							{
								$sm_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: auto !important;
									}
								';
							} else {
								$sm_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: 100% !important;
									}
								';
							}
						}
			
			$sm_media .= '	
							#'.$mnwall.' .mnwall-item {
								width: '.$items_width.'% !important;
							}
							#'.$mnwall.' .mnwall-photo-link {
								height: '.$column_item_height.'px !important;
								width: 100% !important;	
								position: relative;
								display: block;
							} 
							.mnwall-columns #'.$mnwall.' .mnwall-photo-link img {
								height: 100% !important;
								width: auto;
								max-width: inherit;
								position: absolute;
								top: -9999px;
								bottom: -9999px;
								left: -9999px;
								right: -9999px;
								margin: auto;
							}	
							#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
								font-size: 19px !important;
								line-height: 28px;
							}
						}';		 
			$document->addStyleDeclaration( $sm_media );
			
			if ($detail_box_column) { 
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: block !important;	
							}
						}
				';
			} else {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $detail_box_column_css );	
			
			if ($show_title_column) { 
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: block !important;	
							}
						}
				';
			} else {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_title_column_css );	
			
			if ($show_introtext_column) { 
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: block !important;	
							}
						}
				';
			} else {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_introtext_column_css );	
			
			if ($show_date_column) { 
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: block !important;	
							}
						}
				';
			} else {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_date_column_css );	
			
			if ($show_category_column) { 
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: block !important;	
							}
						}
				';
			} else {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_category_column_css );	
			
			if ($show_location_column) { 
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: block !important;	
							}
						}
				';
			} else {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_location_column_css );		
			
			if ($show_author_column) { 
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: block !important;	
							}
						}
				';
			} else {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_author_column_css );	
			
			if ($show_price_column) { 
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: block !important;	
							}
						}
				';
			} else {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_price_column_css );
			
			if ($show_hits_column) { 
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: block !important;	
							}
						}
				';
			} else {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_hits_column_css );
			
			if ($show_count_column) { 
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: block !important;	
							}
						}
				';
			} else {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_count_column_css );	
			
			if ($show_readmore_column) { 
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: block !important;	
							}
						}
				';
			} else {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_readmore_column_css );	
		}
		
		// Media CSS - XS screen
		if (!$xs_type) 
		{
			$xs_media_jf = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-photo-link {
								width: 100% !important;	
								height: 100% !important;	
							}
						}';		 
			$document->addStyleDeclaration( $xs_media_jf );
		}
		
		// Media CSS - XS screen - Equal columns
		if ($xs_type) 
		{
			$items_width = number_format((float)(100 / $responsive_xs_num), 2, '.', '');
			$xs_media = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{ ';
						if ($masonry_params['mas_db_position_columns'] == 'below') 
						{
							$xs_media .= '
								#'.$mnwall.' .mnwall-item {
									height: auto !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									position: static;
									padding: 4px 3px 10px !important;
									width: 100% !important;	
								}
							';
						} else {
							$xs_media .= '
								#'.$mnwall.' .mnwall-item {
									height: '.$column_item_height.'px !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									width: 100% !important;	
									top: auto !important;
									bottom: 0 !important;
									left: 0 !important;
									padding: 4px 3px 10px !important;
								}
							';
							if ($masonry_params['mas_db_position_columns'] == 'bottom')
							{
								$xs_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: auto !important;
									}
								';
							} else {
								$xs_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: 100% !important;
									}
								';
							}
						}
			
			$xs_media .= '	
							#'.$mnwall.' .mnwall-item {
								width: '.$items_width.'% !important;
							}
							#'.$mnwall.' .mnwall-photo-link {
								height: '.$column_item_height.'px !important;
								width: 100% !important;	
								position: relative;
								display: block;
							}
							.mnwall-columns #'.$mnwall.' .mnwall-photo-link img {
								height: 100% !important;
								width: auto;
								max-width: inherit;
								position: absolute;
								top: -9999px;
								bottom: -9999px;
								left: -9999px;
								right: -9999px;
								margin: auto;
							}	
							#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
								font-size: 19px !important;
								line-height: 28px;
							}
						}';		 
			$document->addStyleDeclaration( $xs_media );
			
			if ($detail_box_column) { 
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: block !important;	
							}
						}
				';	
			} else {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $detail_box_column_css );	
			
			if ($show_title_column) { 
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: block !important;	
							}
						}
				';
			} else {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_title_column_css );	
			
			if ($show_introtext_column) { 
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: block !important;	
							}
						}
				';
			} else {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_introtext_column_css );	
			
			if ($show_date_column) { 
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: block !important;	
							}
						}
				';
			} else {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_date_column_css );
			
			if ($show_category_column) { 
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: block !important;	
							}
						}
				';
			} else {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_category_column_css );	
			
			if ($show_location_column) { 
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: block !important;	
							}
						}
				';
			} else {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_location_column_css );	
			
			if ($show_author_column) { 
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: block !important;	
							}
						}
				';
			} else {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: none !important;	
							}
						}
				';
			}
			$document->addStyleDeclaration( $show_author_column_css );		
			
			if ($show_price_column) { 
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_price_column_css );	
			
			if ($show_hits_column) { 
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_hits_column_css );	
			
			if ($show_count_column) { 
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_count_column_css );	
			
			if ($show_readmore_column) { 
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_readmore_column_css );	
		}
		
		// Media CSS - XXS screen
		if (!$xxs_type) 
		{
			$xxs_media_jf = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-photo-link {
								width: 100% !important;	
								height: 100% !important;	
							}
						}';		 
			$document->addStyleDeclaration( $xxs_media_jf );
		}
		
		// Media CSS - XXS screen - Equal columns
		if ($xxs_type) 
		{
			$items_width = number_format((float)(100 / $responsive_xxs_num), 2, '.', '');
			$xxs_media = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{ ';
						if ($masonry_params['mas_db_position_columns'] == 'below') 
						{
							$xxs_media .= '
								#'.$mnwall.' .mnwall-item {
									height: auto !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									position: static;
									padding: 4px 3px 10px !important;
									width: 100% !important;	
								}
							';
						} else {
							$xxs_media .= '
								#'.$mnwall.' .mnwall-item {
									height: '.$column_item_height.'px !important;
								} 
								#'.$mnwall.' .mnwall-item-inner {
									width: 100% !important;	
									top: auto !important;
									bottom: 0 !important;
									left: 0 !important;
									padding: 4px 3px 10px !important;
								}
							';
							if ($masonry_params['mas_db_position_columns'] == 'bottom')
							{
								$xxs_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: auto !important;
									}
								';
							} else {
								$xxs_media .= '
									#'.$mnwall.' .mnwall-item-inner {
										height: 100% !important;
									}
								';
							}
						}
			
			$xxs_media .= '	
							#'.$mnwall.' .mnwall-item {
								width: '.$items_width.'% !important;
							}
							#'.$mnwall.' .mnwall-photo-link {
								height: '.$column_item_height.'px !important;
								width: 100% !important;	
								position: relative;
								display: block;
							} 
							.mnwall-columns #'.$mnwall.' .mnwall-photo-link img {
								height: 100% !important;
								width: auto;
								max-width: inherit;
								position: absolute;
								top: -9999px;
								bottom: -9999px;
								left: -9999px;
								right: -9999px;
								margin: auto;
							}	
							#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
							#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
								font-size: 19px !important;
								line-height: 28px;
							}
						}';		  
			$document->addStyleDeclaration( $xxs_media );
			
			if ($detail_box_column) { 
				$detail_box_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: block !important;	
							}
						}
				';	
			} else {
				$detail_box_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $detail_box_column_css );	
			
			if ($show_title_column) { 
				$show_title_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: block !important;	
							}
						}
				';		
			} else {
				$show_title_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-title {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_title_column_css );	
			
			if ($show_introtext_column) { 
				$show_introtext_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: block !important;	
							}
						}
				';
			} else {
				$show_introtext_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_introtext_column_css );	
			
			if ($show_date_column) { 
				$show_date_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_date_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-date {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_date_column_css );	
			
			if ($show_category_column) { 
				$show_category_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: block !important;	
							}
						}
				';
			} else {
				$show_category_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_category_column_css );	
			
			if ($show_location_column) { 
				$show_location_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: block !important;	
							}
						}
				';
			} else {
				$show_location_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_location_column_css );	
			
			if ($show_author_column) { 
				$show_author_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: block !important;	
							}
						}
				';
			} else {
				$show_author_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
								display: none !important;	
							}
						}
				';	
			}
			$document->addStyleDeclaration( $show_author_column_css );		
			
			if ($show_price_column) { 
				$show_price_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_price_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-price {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_price_column_css );	
			
			if ($show_hits_column) { 
				$show_hits_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_hits_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_hits_column_css );	
			
			if ($show_count_column) { 
				$show_count_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_count_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-count {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_count_column_css );	
			
			if ($show_readmore_column) { 
				$show_readmore_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: block !important;	
							}
						}
				';	
			} else {
				$show_readmore_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px) 
						{			
							#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
								display: none !important;	
							}
						}
				';		
			}
			$document->addStyleDeclaration( $show_readmore_column_css );	
		}
		
		// List items - Responsive configuration
		if ($masonry_params['mas_grid'] == '99v')
		{
			$list_items_media = '
				.mnwall-list #'.$mnwall.' .mnwall-item {
					width: 100% !important;
					height: auto !important;
				} 
				.mnwall-list #'.$mnwall.' .mnwall-item-inner {
					width: auto !important;	
				}
				.mnwall-list #'.$mnwall.' .mnwall-photo-link {
					height: auto !important;
				} 
				.mnwall-list #'.$mnwall.' .mnwall-item-inner .mnwall-title a,
				.mnwall-list #'.$mnwall.' .mnwall-item-inner .mnwall-title span {
					font-size: 18px !important;
				}
				@media only screen and (max-width: 550px) 
				{	
					.mnwall-list #'.$mnwall.' .mnwall-cover {
						width: 100%;
						max-width: inherit;	
					}
					.mnwall-list #'.$mnwall.' .mnwall-photo-link img {
						width: 100%;
						max-width: 100%;
					}
				}
			';		 
			$document->addStyleDeclaration( $list_items_media );
		}
		
	}
	
}