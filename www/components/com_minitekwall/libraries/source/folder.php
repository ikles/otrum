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
			
class MinitekWallLibSourceFolder
{	
	// Get Images count from folder
	public function getFolderImagesCount($data_source, $globalLimit)
	{
		$directory = 'media/minitekwall/'.$data_source['fold_folder'].'/';
		$images = glob($directory."*.{jpg,png}", GLOB_BRACE);
		$count = count($images);
		
		if ($count > $globalLimit)
		{
			$count = $globalLimit;	
		}
		
		return $count;
	}	
	
	// Get Images from folder
	public function getFolderImages($data_source, $startLimit, $pageLimit, $globalLimit)
	{
		$directory = 'media/minitekwall/'.$data_source['fold_folder'].'/';
		$images = glob($directory."*.{jpg,png}", GLOB_BRACE);

		foreach ($images as $key => $value)
		{			
			$images[$key] = new stdClass();
			$images[$key]->path = $value;
			$images[$key]->title = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($value));
			$images[$key]->created = date('Y-m-d H:i:s', filemtime($value));
		}
		
		// Ordering direction
		if ($data_source['fold_ordering_direction'] == 'ASC')
		{
			$dir = SORT_ASC;
		}
		else
		{
			$dir = SORT_DESC;
		}
		
		// Order by title
		if ($data_source['fold_ordering'] == 'title')
		{
			$title = array();
			foreach ($images as $key => $row)
			{
				$title[$key] = $row->title;
			}
			array_multisort($title, $dir, $images);
		}
		
		// Order by date created
		if ($data_source['fold_ordering'] == 'created')
		{
			$created = array();
			foreach ($images as $key => $row)
			{
				$created[$key] = $row->created;
			}
			array_multisort($created, $dir, $images);
		}
		
		// Set the list start limit
		$page = JRequest::getInt('page');
		if (!$page || $page == 1) {
			$limit	= $startLimit;
			$start = 0;
		} else {
			$start_limit = $startLimit;
			$limit = $pageLimit;
			$start = $start_limit + (($page - 2) * $limit);
			
			$view = JRequest::getVar('view');
			$pagination = JRequest::getVar('pagination');
			if ($view == 'filters' && ($pagination == 1 || $pagination == '4')) { // Pagination: Append / Infinite
				$start = 0;
				$limit = $start_limit + (($page - 1) * $limit);
			}
			
			if ($start < $globalLimit) {
				if ($start + $pageLimit >= $globalLimit) {
					$limit = $globalLimit - $start;
				}
			} else {
				$limit = 0;
			}
		}
		
		// Limit items according to pagination
		$images = array_slice($images, $start, $limit);

		return $images;
	}	
}
