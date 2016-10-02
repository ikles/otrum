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

class MinitekWallLibBaseMasonryOptions
{
	var $utilities = null;
	
	function __construct()
	{
		$this->utilities = new MinitekWallLibUtilities;		
		
		return;	
	}
	
	public function getMasonryItemSize($gridType, $item_index)
	{
		switch ($gridType) 
		{
			case '1':
				$item_size = 'mnwall-big';	
				break;
				
			case '3a':
				if ($item_index == '1') {
					$item_size = 'mnwall-big';	
				} else {
					$item_size = 'mnwall-horizontal';	
				}
				break;
				
			case '3b':
				if ($item_index == '2') {
					$item_size = 'mnwall-big';	
				} else {
					$item_size = 'mnwall-horizontal';	
				}
				break;
				
			case '3c':
				if ($item_index == '1') {
					$item_size = 'mnwall-big';	
				} else {
					$item_size = 'mnwall-vertical';	
				}
				break;
				
			case '4':
				if ($item_index == '1') {
					$item_size = 'mnwall-big';	
				} else if ($item_index == '2') {
					$item_size = 'mnwall-horizontal';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '5':
				if ($item_index == '1' || $item_index == '5') {
					$item_size = 'mnwall-horizontal';	
				} else if ($item_index == '2' || $item_index == '4') {
					$item_size = 'mnwall-small';	
				} else {
					$item_size = 'mnwall-big';	
				}
				break;
				
			case '5b':
				if ($item_index == '3') {
					$item_size = 'mnwall-big';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '6':
				if ($item_index == '1') {
					$item_size = 'mnwall-big';	
				} else if ($item_index == '3') {
					$item_size = 'mnwall-horizontal';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '7':
				if ($item_index == '1') {
					$item_size = 'mnwall-big';	
				} else if ($item_index == '2') {
					$item_size = 'mnwall-horizontal';	
				} else if ($item_index == '4') {
					$item_size = 'mnwall-vertical';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '8':
				if ($item_index == '1' || $item_index == '7' || $item_index == '8') {
					$item_size = 'mnwall-horizontal';	
				} else if ($item_index == '2') {
					$item_size = 'mnwall-big';	
				} else if ($item_index == '6') {
					$item_size = 'mnwall-vertical';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '9':
				if ($item_index == '1') {
					$item_size = 'mnwall-big';	
				} else if ($item_index == '2') {
					$item_size = 'mnwall-horizontal';	
				} else if ($item_index == '4' || $item_index == '5' || $item_index == '6') {
					$item_size = 'mnwall-vertical';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '9r':
				if ($item_index == '1' || $item_index == '2' || $item_index == '3') {
					$item_size = 'mnwall-horizontal';	
				} else if ($item_index == '4' || $item_index == '5') {
					$item_size = 'mnwall-big';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '11r':
				if ($item_index == '8' || $item_index == '9' || $item_index == '10' || $item_index == '11') {
					$item_size = 'mnwall-horizontal';	
				} else if ($item_index == '1' || $item_index == '2' || $item_index == '6' || $item_index == '7') {
					$item_size = 'mnwall-big';	
				} else {
					$item_size = 'mnwall-vertical';	
				}
				break;
				
			case '12r':
				if ($item_index == '1' || $item_index == '2' || $item_index == '3' || $item_index == '4') {
					$item_size = 'mnwall-horizontal';	
				} else if ($item_index == '10' || $item_index == '11' || $item_index == '12') {
					$item_size = 'mnwall-big';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
				
			case '16r':
				if ($item_index == '1' || $item_index == '5' || $item_index == '7' || $item_index == '15') {
					$item_size = 'mnwall-horizontal';	
				} else {
					$item_size = 'mnwall-small';	
				}
				break;
		}
		
		return $item_size;
	}
	
}