<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2016 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class MinitekWallModelScroller extends JModelLegacy
{
	var $utilities = null;
	var $joomla_source = null;
	var $k2_source = null;
	var $jomsocial_source = null;
	var $virtuemart_source = null;
	var $easyblog_source = null;
	var $folder_source = null;
	var $options = null;
	var $responsive_scroller = null;
	var $scroller_javascript = null;
	
	function __construct()
	{
		$jinput = JFactory::getApplication()->input;
		$widgetID = $jinput->get('widget_id');
		$this->utilities = $this->getUtilitiesLib();
		$source_id = $this->utilities->getSourceID($widgetID); 			
		
		// Joomla source
		if ($source_id = 'joomla')
		{
			$this->joomla_source = $this->getJoomlaSource();
		}
		
		// K2 source
		$k2 = JPATH_ROOT.DS.'components'.DS.'com_k2';	
		if ($source_id = 'k2' && file_exists($k2.DS.'k2.php')) 
		{
			$this->k2_source = $this->getK2Source();
		}
		
		// Jomsocial source
		$js = JPATH_ROOT.DS.'components'.DS.'com_community';	
		if ($source_id = 'jomsocial' && file_exists($js.DS.'community.php')) 
		{
			$this->jomsocial_source = $this->getJomsocialSource();
		}
		
		// Virtuemart source
		$vm = JPATH_ROOT.DS.'components'.DS.'com_virtuemart';	
		if ($source_id = 'virtuemart' && file_exists($vm.DS.'virtuemart.php')) 
		{
			$this->virtuemart_source = $this->getVirtuemartSource();
		}
		
		// Easyblog source
		$eb = JPATH_ROOT.DS.'components'.DS.'com_easyblog';	
		if ($source_id = 'easyblog' && file_exists($eb.DS.'easyblog.php')) 
		{
			$this->easyblog_source = $this->getEasyblogSource();
		}
		
		// Folder source
		if ($source_id = 'folder') 
		{
			$this->folder_source = $this->getFolderSource();
		}
		
		$this->options = $this->getOptionsLib();
		$this->responsive_scroller = $this->getResponsiveScrollerLib();
		$this->scroller_javascript = $this->getScrollerJavascriptLib();
		
		parent::__construct();
	}
    
	public function getUtilitiesLib()
	{
		$utilities = new MinitekWallLibUtilities;
		
		return $utilities;
	}
	
	public function getJoomlaSource()
	{
		$joomla_source = new MinitekWallLibSourceJoomla;
		
		return $joomla_source;
	}
	
	public function getK2Source()
	{
		$k2_source = new MinitekWallLibSourceK2;
		
		return $k2_source;
	}
	
	public function getJomsocialSource()
	{
		$jomsocial_source = new MinitekWallLibSourceJomsocial;
		
		return $jomsocial_source;
	}
	
	public function getVirtuemartSource()
	{
		$virtuemart_source = new MinitekWallLibSourceVirtuemart;
		
		return $virtuemart_source;
	}
	
	public function getEasyblogSource()
	{
		$easyblog_source = new MinitekWallLibSourceEasyblog;
		
		return $easyblog_source;
	}
	
	public function getFolderSource()
	{
		$folder_source = new MinitekWallLibSourceFolder;
		
		return $folder_source;
	}
	
	public function getOptionsLib()
	{
		$options = new MinitekWallLibBaseOptions;
		
		return $options;
	}
		
	public function getResponsiveScrollerLib()
	{
		$options = new MinitekWallLibBaseScrollerResponsive;
		
		return $options;
	}
	
	public function getScrollerJavascriptLib()
	{
		$options = new MinitekWallLibBaseScrollerJavascript;
		
		return $options;
	}
	
	public function getAllResultsCount($widgetID)
	{
		// Get source
		$source_id = $this->utilities->getSourceID($widgetID); 		
		$data_source = $this->utilities->getSource($widgetID, $source_id);
		
		// Limits
		$scroller_params = $this->utilities->getScrollerParams($widgetID);
		$globalLimit = (int)$scroller_params['scr_global_limit'];
		
		// Joomla
		if ($source_id == 'joomla') 
		{
			$joomla_mode = $data_source['joomla_mode'];
				
			// Joomla Articles
			if ($joomla_mode == 'ja') {
				$joomla_articles_count = $this->joomla_source->getJoomlaArticlesCount($data_source, $globalLimit);
				if ($joomla_articles_count) {
					$output = $joomla_articles_count;
				}
			} 
		
			// Joomla Categories
			if ($joomla_mode == 'jc') {
				$joomla_categories_count = $this->joomla_source->getJoomlaCategoriesCount($data_source, $globalLimit);
				if ($joomla_categories_count) {
					$output = $joomla_categories_count;
				}
			}
			
		} 	
				
		// K2
		else if ($source_id == 'k2')
		{
			$k2_mode = $data_source['k2_mode'];
				
			// K2 Items
			if ($k2_mode == 'k2i') {
				$k2_items_count = $this->k2_source->getK2ItemsCount($data_source, $globalLimit);
				if ($k2_items_count) {
					$output = $k2_items_count;
				}
			}
			
			// K2 Categories
			if ($k2_mode == 'k2c') {
				$k2_categories_count = $this->k2_source->getK2CategoriesCount($data_source, $globalLimit);
				if ($k2_categories_count) {
					$output = $k2_categories_count;
				}
			}
			
			// K2 Authors
			if ($k2_mode == 'k2a') {
				$k2_authors_count = $this->k2_source->getK2AuthorsCount($data_source, $globalLimit);
				if ($k2_authors_count) {
					$output = $k2_authors_count;
				} 
			}
		
		}
		
		// Jomsocial
		else if ($source_id == 'jomsocial')
		{
			$jomsocial_mode = $data_source['jomsocial_mode'];
				
			// Users
			if ($jomsocial_mode == 'jsu') {
				$js_users_count = $this->jomsocial_source->getJomsocialUsersCount($data_source, $globalLimit);
				if ($js_users_count) {
					$output = $js_users_count;
				}
			}
			
			// Groups
			if ($jomsocial_mode == 'jsg') {
				$js_groups_count = $this->jomsocial_source->getJomsocialGroupsCount($data_source, $globalLimit);
				if ($js_groups_count) {
					$output = $js_groups_count;
				}
			}
			
			// Events
			if ($jomsocial_mode == 'jse') {
				$js_events_count = $this->jomsocial_source->getJomsocialEventsCount($data_source, $globalLimit);
				if ($js_events_count) {
					$output = $js_events_count;
				}
			}
			
			// Photos
			if ($jomsocial_mode == 'jsp') {
				$js_photos_count = $this->jomsocial_source->getJomsocialPhotosCount($data_source, $globalLimit);
				if ($js_photos_count) {
					$output = $js_photos_count;
				} 
			}
			
			// Albums
			if ($jomsocial_mode == 'jsa') {
				$js_albums_count = $this->jomsocial_source->getJomsocialAlbumsCount($data_source, $globalLimit);
				if ($js_albums_count) {
					$output = $js_albums_count;
				}
			}
			
			// Videos
			if ($jomsocial_mode == 'jsv') {
				$js_videos_count = $this->jomsocial_source->getJomsocialVideosCount($data_source, $globalLimit);
				if ($js_videos_count) {
					$output = $js_videos_count;
				} 
			}
			
		}
		
		// Virtuemart
		else if ($source_id == 'virtuemart')
		{
			//$virtuemart_mode = $data_source['virtuemart_mode'];
			$virtuemart_mode = 'vmp';
				
			// Products
			if ($virtuemart_mode == 'vmp') {
				$vm_products_count = $this->virtuemart_source->getVirtuemartProductsCount($data_source, $globalLimit);
				if ($vm_products_count) {
					$output = $vm_products_count;
				}
			}
						
		}
		
		// Easyblog
		else if ($source_id == 'easyblog')
		{
			//$easyblog_mode = $data_source['easyblog_mode'];
			$easyblog_mode = 'eba';
				
			// Articles
			if ($easyblog_mode == 'eba') {
				$eb_articles_count = $this->easyblog_source->getEasyblogArticlesCount($data_source, $globalLimit);
				if ($eb_articles_count) {
					$output = $eb_articles_count;
				}
			}
						
		}
		
		// Folder
		else if ($source_id == 'folder')
		{
			// Images
			$fold_images_count = $this->folder_source->getFolderImagesCount($data_source, $globalLimit);
			if ($fold_images_count) 
			{
				$output = $fold_images_count;
			}
		}
		
		if (isset($output))
			return $output;
	}
	
	public function getAllResults($widgetID)
	{
		// Get source
		$source_id = $this->utilities->getSourceID($widgetID); 		
		$data_source = $this->utilities->getSource($widgetID, $source_id);
			
		// Limits
		$scroller_params = $this->utilities->getScrollerParams($widgetID);
		$startLimit = (int)$scroller_params['scr_global_limit'];
		$pageLimit = 0;
		$globalLimit = (int)$scroller_params['scr_global_limit'];
		if ($startLimit > $globalLimit) {
			$startLimit = $globalLimit;
		}
	
		// Joomla
		if ($source_id == 'joomla') 
		{			
			$joomla_output = $this->getJoomlaResults($data_source, $startLimit, $pageLimit = 0, $globalLimit = 0);
			$output = $joomla_output;
		} 	
				
		// K2
		else if ($source_id == 'k2')
		{
			$k2_output = $this->getK2Results($data_source, $startLimit, $pageLimit = 0, $globalLimit = 0);
			$output = $k2_output;		
		}
		
		// Jomsocial
		else if ($source_id == 'jomsocial')
		{
			$jomsocial_output = $this->getJomsocialResults($data_source, $startLimit, $pageLimit = 0, $globalLimit = 0);
			$output = $jomsocial_output;		
		}
		
		// Virtuemart
		else if ($source_id == 'virtuemart')
		{
			$virtuemart_output = $this->getVirtuemartResults($data_source, $startLimit, $pageLimit = 0, $globalLimit = 0);
			$output = $virtuemart_output;		
		}
		
		// Easyblog
		else if ($source_id == 'easyblog')
		{
			$easyblog_output = $this->getEasyblogResults($data_source, $startLimit, $pageLimit = 0, $globalLimit = 0);
			$output = $easyblog_output;		
		}
		
		// Folder
		else if ($source_id == 'folder')
		{
			$folder_output = $this->getFolderResults($data_source, $startLimit, $pageLimit = 0, $globalLimit = 0);
			$output = $folder_output;		
		}
			
		return $output;
	}
		
	// Get Joomla results
	public function getJoomlaResults($data_source, $startLimit, $pageLimit, $globalLimit)
	{		
		$joomla_mode = $data_source['joomla_mode'];
		$output = array();
		
		// Joomla Articles
		if ($joomla_mode == 'ja') {
			$joomla_articles = $this->joomla_source->getJoomlaArticles($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($joomla_articles) {
				$output = array_merge($output, $joomla_articles);
			}
			
		} 
		
		// Joomla Categories
		if ($joomla_mode == 'jc') {
			$joomla_categories = $this->joomla_source->getJoomlaCategories($data_source, $startLimit, $pageLimit, $globalLimit);

			if ($joomla_categories) {
				$output = array_merge($output, $joomla_categories);
			}
						
		}
		
		return $output;
	}
	
	// Get K2 results
	public function getK2Results($data_source, $startLimit, $pageLimit, $globalLimit)
	{
		$k2_mode = $data_source['k2_mode'];
		$output = array();
		
		// K2 Items
		if ($k2_mode == 'k2i') {
			$k2_items = $this->k2_source->getK2Items($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($k2_items) {
				$output = array_merge($output, $k2_items);
			}
			
		}
		
		// K2 Categories
		if ($k2_mode == 'k2c') {
			$k2_categories = $this->k2_source->getK2Categories($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($k2_categories) {
				$output = array_merge($output, $k2_categories);
			}
			
		}
		
		// K2 Authors
		if ($k2_mode == 'k2a') {
			$k2_authors = $this->k2_source->getK2Authors($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($k2_authors) {
				$output = array_merge($output, $k2_authors);
			} 
			
		}
		
		return $output;
	}
	
	// Get Jomsocial results
	public function getJomsocialResults($data_source, $startLimit, $pageLimit, $globalLimit)
	{
		$jomsocial_mode = $data_source['jomsocial_mode'];
		$output = array();
		
		// Users
		if ($jomsocial_mode == 'jsu') {
			$js_users = $this->jomsocial_source->getJomsocialUsers($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($js_users) {
				$output = array_merge($output, $js_users);
			}
			
		}
		
		// Groups
		if ($jomsocial_mode == 'jsg') {
			$js_groups = $this->jomsocial_source->getJomsocialGroups($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($js_groups) {
				$output = array_merge($output, $js_groups);
			}
			
		}
		
		// Events
		if ($jomsocial_mode == 'jse') {
			$js_events = $this->jomsocial_source->getJomsocialEvents($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($js_events) {
				$output = array_merge($output, $js_events);
			}
			
		}
		
		// Photos
		if ($jomsocial_mode == 'jsp') {
			$js_photos = $this->jomsocial_source->getJomsocialPhotos($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($js_photos) {
				$output = array_merge($output, $js_photos);
			} 
			
		}
		
		// Albums
		if ($jomsocial_mode == 'jsa') {
			$js_albums = $this->jomsocial_source->getJomsocialAlbums($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($js_albums) {
				$output = array_merge($output, $js_albums);
			}
			
		}
		
		// Videos
		if ($jomsocial_mode == 'jsv') {
			$js_videos = $this->jomsocial_source->getJomsocialVideos($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($js_videos) {
				$output = array_merge($output, $js_videos);
			} 
			
		}
		
		return $output;
	}
	
	// Get Virtuemart results
	public function getVirtuemartResults($data_source, $startLimit, $pageLimit, $globalLimit)
	{		
		//$virtuemart_mode = $data_source['virtuemart_mode'];
		$virtuemart_mode = 'vmp';
		$output = array();
		
		// Virtuemart Products
		if ($virtuemart_mode == 'vmp') {
			$virtuemart_products = $this->virtuemart_source->getVirtuemartProducts($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($virtuemart_products) {
				$output = array_merge($output, $virtuemart_products);
			}
			
		} 
				
		return $output;
	}
	
	// Get Easyblog results
	public function getEasyblogResults($data_source, $startLimit, $pageLimit, $globalLimit)
	{		
		//$easyblog_mode = $data_source['easyblog_mode'];
		$easyblog_mode = 'eba';
		$output = array();
		
		// Easyblog Articles
		if ($easyblog_mode == 'eba') {
			$easyblog_products = $this->easyblog_source->getEasyblogArticles($data_source, $startLimit, $pageLimit, $globalLimit);
			
			if ($easyblog_products) {
				$output = array_merge($output, $easyblog_products);
			}
			
		} 
				
		return $output;
	}
	
	// Get Folder results
	public function getFolderResults($data_source, $startLimit, $pageLimit, $globalLimit)
	{		
		$output = array();
		
		// Folder Images
		$folder_images = $this->folder_source->getFolderImages($data_source, $startLimit, $pageLimit, $globalLimit);
			
		if ($folder_images) 
		{
			$output = array_merge($output, $folder_images);
		}
				
		return $output;
	}
	
}