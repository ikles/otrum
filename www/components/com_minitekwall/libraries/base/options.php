<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2016 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	https://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
} 

class MinitekWallLibBaseOptions
{
	var $utilities = null;
	
	function __construct()
	{
		$this->utilities = new MinitekWallLibUtilities;		
		
		return;	
	}
	
	public function getFolderDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams)
	{
		// Get source params
		$source = $this->utilities->getSource($widgetID, 'folder');
		
		foreach ($items as &$item)
		{
			// Content type
			$item->itemType = JText::_('COM_MINITEKWALL_'.$source['fold_title']);
			
			// Image
			if ($detailBoxParams['images'])
			{
				$item->itemImageRaw = $item->path;
				
				$item->itemImage = $item->itemImageRaw;
		
				if (
					$detailBoxParams['crop_images'] && 
					$item->itemImage && 
					$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->title) 
				) 
				{
					$item->itemImage = $image;
				}  
			}
			
			// Title
			$item->itemTitle = $this->utilities->wordLimit($item->title, $detailBoxParams['detailBoxTitleLimit']);
			$item->itemTitleRaw = $item->title;
			if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
			{
				$item->hover_itemTitle = $this->utilities->wordLimit($item->title, $hoverBoxParams['hoverBoxTitleLimit']);
			}
			
			// Date
			$item->itemDate = JHTML::_('date', $item->created, $detailBoxParams['detailBoxDateFormat']);
			if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
			{
				$item->hover_itemDate = JHTML::_('date', $item->created, $hoverBoxParams['hoverBoxDateFormat']);
			}
			$item->itemDateRaw = $item->created;
		}
		
		return $items;
	}
	
	public function getEasyblogDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams)
	{
		// Get source params
		$source = $this->utilities->getSource($widgetID, 'easyblog');
		$easyblog_source = new MinitekWallLibSourceEasyblog;
		$easyblog_mode = 'eba';
		$items = EB::formatter('list', $items);
		
		foreach ($items as &$item)
		{
			if ($easyblog_mode == 'eba')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['eb_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$item->itemImageRaw = false;
					
					if ($source['eb_image_type'] == '1') // Article image
					{
						if ($item->hasImage()) 
						{
							$item->itemImageRaw = $item->getImage('large');
						}
					} 
					else if ($source['eb_image_type'] == 2 || !$item->itemImageRaw) // Inline image
					{
						if ($item->intro)
						{
							$introtext_temp = strip_tags($item->intro, '<img>');
							preg_match('/<img[^>]+>/i', $introtext_temp, $new_image);
						}
						else
						{
							$introtext_temp = strip_tags($item->content, '<img>');
							preg_match('/<img[^>]+>/i', $introtext_temp, $new_image);
						}
						
						$src = false;
						if ($new_image) 
						{
							$doc = new DOMDocument();
							$doc->loadHTML($new_image[0]);
							$xpath = new DOMXPath($doc);
							$src = $xpath->evaluate("string(//img/@src)"); 
						}
						if ($src) 
						{
							$item->itemImageRaw = $src;
						}
					}
										
					// Fallback image
					if (!$item->itemImageRaw && $detailBoxParams['fallback_image'])
					{
						$item->itemImageRaw = JURI::root().''.$detailBoxParams['fallback_image'];
					}
					
					// Final image
					$item->itemImage =  $item->itemImageRaw;
					
					// Crop images
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->title ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->title, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->title;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->title, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = $item->getPermalink();
			
				// Introtext
				$item->itemIntrotext = $item->getIntro(true);
				$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->itemIntrotext);
				$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				$item->hover_itemIntrotext = $item->getIntro(true);
				$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->hover_itemIntrotext);
				$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
			
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created;
				
				// Categories
				$item->itemCategoriesRaw = $item->categories;	
				$item->itemCategory = '';
				$item->itemCategories = '';
				foreach ($item->categories as $key=>$category) {
					$item->itemCategory .= '<a href="'.$category->getPermalink().'">';
						$item->itemCategory .= $category->getTitle();
					$item->itemCategory .= '</a>';
					$item->itemCategories .= $this->utilities->cleanName($category->getTitle()).' ';
					if ($key < count($item->itemCategoriesRaw) - 1) 
					{
						$item->itemCategory .= '&nbsp;&#124;&nbsp;'; 
					}	
				}
				
				// Author
				$author = $item->author;
				$item->itemAuthorRaw = $author->getName();
				$item->itemAuthorLink = $author->getPermalink();
				$item->itemAuthor = '<a href="'.$item->itemAuthorLink.'">'.$item->itemAuthorRaw.'</a>';
				
				// Tags
				$item->itemTags = $item->getTags();
								
				// Hits
				$item->itemHits = $item->hits;		
			}
		
		}
		
		return $items;
	}
	
	public function getVirtuemartDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams)
	{
		// Get source params
		$source = $this->utilities->getSource($widgetID, 'virtuemart');
		$virtuemart_source = new MinitekWallLibSourceVirtuemart;
		$virtuemart_mode = 'vmp';
	
		foreach ($items as &$item)
		{
			if ($virtuemart_mode == 'vmp')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['vmp_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$item->itemImageRaw = $item->images[0]->file_url;
					
					// Fallback image
					if (!$item->itemImageRaw && $detailBoxParams['fallback_image'])
					{
						$item->itemImageRaw = JURI::root().''.$detailBoxParams['fallback_image'];
					}
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->product_name ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->product_name, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->product_name;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->product_name, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$item->virtuemart_product_id.'&virtuemart_category_id='.$item->virtuemart_category_id.'&Itemid='.$source['vmp_itemid']);
			
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->product_s_desc);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->product_s_desc);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->product_s_desc);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->product_s_desc);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}	
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created_on, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created_on, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created_on;
				
				// Categories
				$item->itemCategoriesRaw = $item->categoryItem;	
				$item->itemCategory = '';
				$item->itemCategories = '';
				foreach ($item->itemCategoriesRaw as $key=>$category) {
					$item->itemCategory .= '<a href="'.JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category["virtuemart_category_id"].'&Itemid='.$source['vmp_itemid']).'">';
						$item->itemCategory .= $category['category_name'];
					$item->itemCategory .= '</a>';
					$item->itemCategories .= $this->utilities->cleanName($category['category_name']).' ';
					if ($key < count($item->itemCategoriesRaw) - 1) 
					{
						$item->itemCategory .= '&nbsp;&#124;&nbsp;'; 
					}	
				}
				
				// Manufacturers
				$manufacturerModel = VmModel::getModel('Manufacturer');
				$item->itemAuthorsRaw = $item->virtuemart_manufacturer_id;	
				$item->itemAuthor = '';
				$item->itemAuthors = '';
				foreach($item->itemAuthorsRaw as $key=>$itemManufacturer) 
				{
					$manufacturer = $manufacturerModel->getManufacturer((int)$itemManufacturer);						
					$item->itemAuthor .= $manufacturer->mf_name;
					$item->itemAuthors .= $manufacturer->mf_name.' ';
					if ($key < count($item->itemAuthorsRaw) - 1) 
					{
						$item->itemAuthor .= '&nbsp;&#124;&nbsp;'; 
					}	
				}
				
				// Sales
				$item->itemCount = $item->product_sales;
				if ($item->itemCount == 1) {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_SALE'); 
				} else {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_SALES'); 
				}
								
				// Price
				$currency = CurrencyDisplay::getInstance( );
				switch ($source['vmp_price_type']) 
				{	
					case "prices['salesPrice']" :
						$item->itemPrice = $currency->createPriceDiv ('salesPrice', '', $item->prices);
						break;	
					case "prices['discountedPriceWithoutTax']" :
						if ($item->prices['discountedPriceWithoutTax'] != $item->prices['priceWithoutTax']) {
							$item->itemPrice = $currency->createPriceDiv ('discountedPriceWithoutTax', '', $item->prices);
						} else {
							$item->itemPrice = $currency->createPriceDiv ('priceWithoutTax', '', $item->prices);
						}
						break;
					case "prices['basePrice']" :
						$item->itemPrice = $currency->createPriceDiv ('basePrice', '', $item->prices);
						if (round($item->prices['basePrice'],$currency->_priceConfig['basePriceVariant'][1]) != $item->prices['basePriceVariant']) {
							$item->itemPrice = $currency->createPriceDiv ('basePriceVariant', '', $item->prices);
						}
						break;
					case "prices['basePriceWithTax']" :
						$item->itemPrice = $currency->createPriceDiv ('basePriceWithTax', '', $item->prices);
						break;
					default :
						$item->itemPrice = $currency->createPriceDiv ('salesPrice', '', $item->prices, FALSE, FALSE, 1.0, TRUE);
						break;
				} 
				
			}
		
		}
		
		return $items;
	}
	
	public function getJomsocialDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams)
	{
		// Get source params
		$source = $this->utilities->getSource($widgetID, 'jomsocial');
		$jomsocial_source = new MinitekWallLibSourceJomsocial;
		$jomsocial_mode = $source['jomsocial_mode'];
	
		foreach ($items as &$item)
		{
			if ($jomsocial_mode == 'jsu')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['jsu_title']);
				
				// Image
				$user_name = JFactory::getUser($item->userid)->get('name');
				if ($detailBoxParams['images'])
				{
					if ($item->avatar) 
					{
						if (file_exists(JPATH_SITE.'/'.preg_replace( '/avatar\//', 'avatar/profile-', $item->avatar)))
						{
							$item->itemImageRaw = preg_replace( '/avatar\//', 'avatar/profile-', $item->avatar);
						} else {
							$item->itemImageRaw = $item->avatar;
						}
					} else {
						$gender = $jomsocial_source->getGender($item->userid);
						if ($gender) {
						  if ($gender == 'Male') {	
							$item->itemImageRaw = 'components/com_community/assets/user-Male.png';
						  } else if ($gender == 'Female') {	
							$item->itemImageRaw = 'components/com_community/assets/user-Female.png';
						  }
						} else {
						  $item->itemImageRaw = 'components/com_community/assets/default.jpg';
						}
					}
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $user_name, $type = 'user' ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($user_name, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $user_name;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($user_name, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = CRoute::_('index.php?option=com_community&view=profile&userid='.$item->userid);
				
				// Introtext
				$user_description = $jomsocial_source->getUserDescription($item->userid);
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $user_description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $user_description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $user_description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $user_description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}	
				
				// Date
				$user_date = $jomsocial_source->getUserRegistrationDate($item->userid);
				$item->itemDate = JHTML::_('date', $user_date, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $user_date, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $user_date;
				
				// Category
				$user_city = $jomsocial_source->getCity($item->userid);
				if ($user_city)
				{
					$item->itemCategoryRaw = $user_city;
					$item->itemCategory = $item->itemCategoryRaw;
				}
				
				// Location
				$user_country = $jomsocial_source->getCountry($item->userid);
				if ($user_country)
				{
					$item->itemLocationRaw = $user_country;
					$item->itemLocation = $item->itemLocationRaw;
				}
			
			}
			else if ($jomsocial_mode == 'jsg')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['jsg_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					if ($item->avatar) 
					{
						$item->itemImageRaw = preg_replace( '/group\//', 'group/group-', $item->avatar);
					} 
					else 
					{
						$item->itemImageRaw = 'components/com_community/assets/group.png';
					} 
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->name, $type = 'group' ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->name, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->name;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->name, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid='.$item->id);
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}	
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created;
				
				// Category
				$item->itemCategoryRaw = $jomsocial_source->getGroupCategory($item->categoryid);	
				$item->itemCategoryLink = urldecode(CRoute::_('index.php?option=com_community&view=groups&task=display&categoryid='.$item->categoryid));
				$item->itemCategory = '<a href="'.$item->itemCategoryLink.'">'.$item->itemCategoryRaw.'</a>';		
				
				// Author
				$item->itemAuthorRaw = JFactory::getUser($item->ownerid)->name;
				$item->itemAuthorLink = CRoute::_('index.php?option=com_community&view=profile&userid='.$item->ownerid);
				$item->itemAuthor = '<a href="'.$item->itemAuthorLink.'">'.$item->itemAuthorRaw.'</a>';		
				
				// Hits
				$item->itemHits = $item->hits;
				
				// Count
				$item->itemCount = $item->membercount;
				if ($item->itemCount == 1) {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_MEMBER'); 
				} else {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_MEMBERS'); 
				}
				
			}
			else if ($jomsocial_mode == 'jse')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['jse_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					if ($item->cover) 
					{
						$item->itemImageRaw = $item->cover;
					} 
					else 
					{
						$item->itemImageRaw = 'components/com_community/assets/cover-event.png';
					} 
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->title, $type = 'event' ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->title, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->title;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->title, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				if ($item->type == 'profile') 
				{
					$item->itemLink = CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid='.$item->id);
				} 
				else
				{
					$item->itemLink = CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid='.$item->id.'&groupid='.$item->contentid);
				}
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}	
				
				// Date
				$item->itemDate = JHTML::_('date', $item->startdate, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->startdate, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->startdate;
				
				// Category
				$item->itemCategoryRaw = $jomsocial_source->getEventCategory($item->catid);	
				$item->itemCategoryLink = urldecode(CRoute::_('index.php?option=com_community&view=events&categoryid='.$item->catid));
				$item->itemCategory = '<a href="'.$item->itemCategoryLink.'">'.$item->itemCategoryRaw.'</a>';
				
				// Location
				$item->itemLocationRaw = $item->location;	
				$item->itemLocation = $item->location;

				
				// Author
				$item->itemAuthorRaw = JFactory::getUser($item->creator)->name;
				$item->itemAuthorLink = CRoute::_('index.php?option=com_community&view=profile&userid='.$item->creator);
				$item->itemAuthor = '<a href="'.$item->itemAuthorLink.'">'.$item->itemAuthorRaw.'</a>';		
				
				// Hits
				$item->itemHits = $item->hits;
				
				// Count
				$item->itemCount = $item->confirmedcount;
				if ($item->itemCount == 1) {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ATTENDEE'); 
				} else {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ATTENDEES'); 
				}
						
			}
			else if ($jomsocial_mode == 'jsp')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['jsp_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					if ($item->original) 
					{
						if (file_exists(JPATH_SITE.'/'.$item->original))
						{
							$item->itemImageRaw = $item->original;
						} else {
							$item->itemImageRaw = preg_replace( '/originalphotos\//', 'photos/', $item->original);
						}
					} 
					else 
					{
						$item->itemImageRaw = $item->image;
					}
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->caption, $type = 'photo' ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->caption, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->caption;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->caption, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = CRoute::_('index.php?option=com_community&view=photos&task=photo&albumid='.$item->albumid.'&userid='.$item->creator.'&photoid='.$item->id);
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created;
				
				// Category
				$albumType = $jomsocial_source->getAlbumType($item->albumid);
				$albumGroupid = $jomsocial_source->getAlbumGroupid($item->albumid);
				$albumCreator = $jomsocial_source->getAlbumCreator($item->albumid);
				if ($albumType == 'group') 
				{
					$item->itemCategoryLink = CRoute::_('index.php?option=com_community&view=photos&task=album&albumid='.$item->albumid.'&groupid='.$albumGroupid);
				} else
				{
					$item->itemCategoryLink = CRoute::_('index.php?option=com_community&view=photos&task=album&albumid='.$item->albumid.'&userid='.$albumCreator);
				}
				$item->itemCategoryRaw = $jomsocial_source->getAlbumName($item->albumid);
				$item->itemCategory = '<a href="'.$item->itemCategoryLink.'">'.$item->itemCategoryRaw.'</a>';	
				
				// Author
				$item->itemAuthorRaw = JFactory::getUser($item->creator)->name;
				$item->itemAuthorLink = CRoute::_('index.php?option=com_community&view=profile&userid='.$item->creator);
				$item->itemAuthor = '<a href="'.$item->itemAuthorLink.'">'.$item->itemAuthorRaw.'</a>';		
				
				// Hits
				$item->itemHits = $item->hits;
								
			}
			else if ($jomsocial_mode == 'jsa')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['jsa_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$item->itemImageRaw = preg_replace( '/thumb_/', '', $jomsocial_source->getAlbumCover($item->id));
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->name, $type = 'album' ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->name, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->name;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->name, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				if ($item->type == 'group') 
				{
					$item->itemLink = CRoute::_('index.php?option=com_community&view=photos&task=album&albumid='.$item->id.'&groupid='.$item->groupid);
				} 
				else
				{
					$item->itemLink = CRoute::_('index.php?option=com_community&view=photos&task=album&albumid='.$item->id.'&userid='.$item->creator);
				}
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}	
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created;
				
				// Author
				$item->itemAuthorRaw = JFactory::getUser($item->creator)->name;
				$item->itemAuthorLink = CRoute::_('index.php?option=com_community&view=profile&userid='.$item->creator);
				$item->itemAuthor = '<a href="'.$item->itemAuthorLink.'">'.$item->itemAuthorRaw.'</a>';		
				
				// Hits
				$item->itemHits = $item->hits;
				
			}
			else if ($jomsocial_mode == 'jsv')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['jsv_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$item->itemImageRaw = $item->thumb;
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->title, $type = 'video' ) 
					) 
					{
						$item->itemImage = $image;
					}  
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->title, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->title;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->title, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				if ($item->creator_type == 'user') 
				{
					$item->itemLink = CRoute::_('index.php?option=com_community&view=videos&task=video&userid='.$item->creator.'&videoid='.$item->id);
				} 
				else
				{
					$item->itemLink = CRoute::_('index.php?option=com_community&view=videos&task=video&groupid='.$item->creator.'&videoid='.$item->id);
				}
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}	
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created;
				
				// Category
				$item->itemCategoryLink = CRoute::_('index.php?option=com_community&view=videos&task=display&catid='.$item->category_id);
				$item->itemCategoryRaw = $jomsocial_source->getVideoCategory($item->category_id);
				$item->itemCategory = '<a href="'.$item->itemCategoryLink.'">'.$item->itemCategoryRaw.'</a>';	
				
				// Author
				$item->itemAuthorRaw = JFactory::getUser($item->creator)->name;
				$item->itemAuthorLink = CRoute::_('index.php?option=com_community&view=profile&userid='.$item->creator);
				$item->itemAuthor = '<a href="'.$item->itemAuthorLink.'">'.$item->itemAuthorRaw.'</a>';		
				
				// Hits
				$item->itemHits = $item->hits;
				
			}
		}
		
		return $items;
	}
	
	public function getK2DisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams)
	{
		// Get source params
		$model = K2Model::getInstance('Item', 'K2Model');
		$source = $this->utilities->getSource($widgetID, 'k2');
		$k2_mode = $source['k2_mode'];

		foreach ($items as &$item)
		{
			if ($k2_mode == 'k2i')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['k2i_title']);
			
				// Image
				if ($detailBoxParams['images'])
				{
					$componentParams = JComponentHelper::getParams('com_k2');
					$date = JFactory::getDate($item->modified);
					$timestamp = '?t='.$date->toUnix();
					$imageSize = $source['k2i_image_size'];
					
					$item->itemImageRaw = NULL;
					
					if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_'.$imageSize.'.jpg'))
					{
						$item->itemImageRaw  = 'media/k2/items/cache/'.md5("Image".$item->id).'_'.$imageSize.'.jpg';
						if ($componentParams->get('imageTimestamp'))
						{
							$item->itemImageRaw .= $timestamp;
						}
					}
					
					// Image fallback
					if (!$item->itemImageRaw)
					{
						$item_text = $item->introtext.' '.$item->fulltext;
						$introtext_temp = strip_tags($item_text, '<img>');
						preg_match('/<img[^>]+>/i', $introtext_temp, $new_image);
						$src = false;	
						if ($new_image) {
							$doc = new DOMDocument();
							$doc->loadHTML($new_image[0]);
							$xpath = new DOMXPath($doc);
							$src = $xpath->evaluate("string(//img/@src)"); 
						}
						if ($src) 
						{
							$item->itemImageRaw = $src;
						} else {
							if (array_key_exists('fallback_image', $detailBoxParams) && $detailBoxParams['fallback_image'])
							{
								$item->itemImageRaw = JURI::root().''.$detailBoxParams['fallback_image'];
							}
						}
					}
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->title ) 
					) 
					{
						$item->itemImage = $image;
					}
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->title, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->title;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->title, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias))));
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created;
				
				// Category
				$item->itemCategoryRaw = $item->categoryname;
				$item->itemCategoryLink = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid.':'.urlencode($item->categoryalias))));
				$item->itemCategory = '<a href="'.$item->itemCategoryLink.'">'.$item->itemCategoryRaw.'</a>';
				
				// Author
				if (!empty($item->created_by_alias))
				{
					$item->itemAuthorRaw = $item->created_by_alias;
					$item->itemAuthorLink = Juri::root(true);
				}
				else
				{
					$author = JFactory::getUser($item->created_by);
					$item->itemAuthorRaw = $author->name;
					$item->itemAuthorLink = JRoute::_(K2HelperRoute::getUserRoute($item->created_by));
				}
				$item->itemAuthor = '<a href="'.$item->itemAuthorLink.'">'.$item->itemAuthorRaw.'</a>';
				
				// Hits
				$item->itemHits = $item->hits;
				
				// Tags
				$tags = $model->getItemTags($item->id);
				$item->itemTags = $tags;
				foreach ($item->itemTags as $itemTag)
				{
					$itemTag->title = $itemTag->name;	
				}
				
			}
			else if ($k2_mode == 'k2c')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['k2c_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$item->itemImageRaw = NULL;
					
					if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'categories'.DS.$item->image))
					{
						$item->itemImageRaw = 'media/k2/categories/'.$item->image;						
					} 
					
					/*// Fallback image
					else
					{
						$item->itemImage = JURI::base().'components/com_minitekwall/assets/images/category.jpg';
					}*/
					
					$item->itemImage = $item->itemImageRaw;
					
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->name ) 
					) 
					{
						$item->itemImage = $image;
					}
											
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->name, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->name;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->name, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->id.':'.urlencode($item->alias))));		
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}
				
				// Count
				$this->k2_source = new MinitekWallLibSourceK2;
				$item->itemCount = $this->k2_source->countCategoryItems($item->id);
				if ($item->itemCount == 1) {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ARTICLE'); 
				} else {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ARTICLES'); 
				}
				
			}
			else if ($k2_mode == 'k2a')
			{
				$author = JFactory::getUser($item->created_by);
				$db = JFactory::getDBO();
				$query = "SELECT ".$db->quoteName('id').", ".$db->quoteName('gender').", ".$db->quoteName('description').", ".$db->quoteName('image').", ".$db->quoteName('url').", ".$db->quoteName('group').", ".$db->quoteName('plugins')." FROM ".$db->quoteName('#__k2_users')." WHERE ".$db->quoteName('userID')." = ".$db->quote((int)$author->id);
				$db->setQuery($query);
				$author->profile = $db->loadObject();
				
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['k2a_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$componentParams = JComponentHelper::getParams('com_k2');
					$item->itemImageRaw = NULL;
					
					if (isset($author->profile->image) && $author->profile->image) 
					{
						$item->itemImageRaw = K2HelperUtilities::getAvatar($author->id, $author->email, $componentParams->get('userImageWidth'));
						$item->itemImageRaw = str_replace( JURI::root(true), '', $item->itemImageRaw );
						$item->itemImageRaw = trim($item->itemImageRaw, '/');
					}
					
					/* // Image fallback
					else 
					{
						$author->itemImageRaw = JURI::base().'components/com_minitekwall/assets/images/author.jpg';
					}*/
					
					$item->itemImage = $item->itemImageRaw;
					
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $author->name ) 
					) 
					{
						$item->itemImage = $image;
					}
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($author->name, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $author->name;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($author->name, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Link
				$item->itemLink = JRoute::_(K2HelperRoute::getUserRoute($author->id));
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					if ($author->profile)
					{
						$item->itemIntrotext = preg_replace('/\{.*\}/', '', $author->profile->description);
						$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
						$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
					}
				}
				else
				{
					if ($author->profile)
					{
						$item->itemIntrotext = preg_replace('/\{.*\}/', '', $author->profile->description);
						$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					}
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					if ($author->profile)
					{
						$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $author->profile->description);
						$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
						$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
					}
				}
				else
				{
					if ($author->profile)
					{
						$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $author->profile->description);
						$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					}
				}
				
				// Count
				$this->k2_source = new MinitekWallLibSourceK2;
				$item->itemCount = $this->k2_source->countAuthorItems($author->id, $source['k2a_category_id']);
				if ($item->itemCount == 1) 
				{
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ARTICLE'); 
				} else {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ARTICLES'); 
				}
			}
		}
	
		return $items;
	}
	
	public function getJoomlaDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams)
	{
		$com_path = JPATH_SITE.'/components/com_content/';
		if (!class_exists('ContentHelperRoute'))
		{
			require_once $com_path.'helpers/route.php';
		}

		// Get source params
		$source = $this->utilities->getSource($widgetID, 'joomla');
		$joomla_mode = $source['joomla_mode'];
		
		foreach ($items as &$item)
		{
			if ($joomla_mode == 'ja')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['ja_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$images = json_decode($item->images, true);
					if ($source['ja_image_type'] == 'introtext') {
						$item->itemImageRaw = $images['image_intro'];
					} else if ($source['ja_image_type'] == 'fulltext') {
						$item->itemImageRaw = $images['image_fulltext'];
					} else if ($source['ja_image_type'] == 'inline') {
						$introtext_temp = strip_tags($item->introtext, '<img>');
						preg_match('/<img[^>]+>/i', $introtext_temp, $new_image);
						$src = false;
						if ($new_image) {
							$doc = new DOMDocument();
							$doc->loadHTML($new_image[0]);
							$xpath = new DOMXPath($doc);
							$src = $xpath->evaluate("string(//img/@src)"); 
						}
						if ($src) {
							$item->itemImageRaw = $src;
						} else {
							$item->itemImageRaw = $images['image_intro'];
						}
					}
					
					// Image fallback
					if (!$item->itemImageRaw) 
					{
						if (array_key_exists('fallback_image', $detailBoxParams) && $detailBoxParams['fallback_image'])
						{
							$item->itemImageRaw = JURI::root().''.$detailBoxParams['fallback_image'];
						}
					}
					
					$item->itemImage =  $item->itemImageRaw;
			
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->title ) 
					) 
					{
						$item->itemImage = $image;
					}
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->title, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->title;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->title, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Links
				$item->slug = $item->id.':'.$item->alias;
				$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;
				$item->itemLink = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
				$item->itemCategoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{ 
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->introtext);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}
				 
				// Date
				$ja_date_field = $source['ja_date_field'];
				$item->itemDate = JHTML::_('date', $item->$ja_date_field, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->$ja_date_field, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created;
				
				// Category
				$item->itemCategoryRaw = $item->category_title;
				$item->itemCategory = '<a href="'.$item->itemCategoryLink.'">'.$item->itemCategoryRaw.'</a>';
				
				// Author
				$item->itemAuthorRaw = $item->author;
				$item->itemAuthor = $item->itemAuthorRaw;
				
				// Hits
				$item->itemHits = $item->hits;
								
				// Tags
				$item_tags = new JHelperTags;
				$item->itemTags = $item_tags->getItemTags('com_content.article', $item->id);
			
			}
			else if ($joomla_mode == 'jc')
			{
				// Content type
				$item->itemType = JText::_('COM_MINITEKWALL_'.$source['jc_title']);
				
				// Image
				if ($detailBoxParams['images'])
				{
					$cat_params = json_decode($item->params, true);
					$item->itemImageRaw = $cat_params['image'];	
					
					// Define new images
					$item->itemImage =  $item->itemImageRaw;
						
					// Image fallback
					/*if (!$item->itemImageRaw)
					{
						$item->itemImage = JURI::base().'components/com_minitekwall/assets/images/category.jpg';
					}*/
			
					// Crop images
					if (
						$detailBoxParams['crop_images'] && 
						$item->itemImage && 
						$image = $this->utilities->renderImages($item->itemImage, $detailBoxParams['image_width'], $detailBoxParams['image_height'], $item->title ) 
					) 
					{
						$item->itemImage = $image;
					}
				}
				
				// Title
				$item->itemTitle = $this->utilities->wordLimit($item->title, $detailBoxParams['detailBoxTitleLimit']);
				$item->itemTitleRaw = $item->title;
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxTitle'])
				{
					$item->hover_itemTitle = $this->utilities->wordLimit($item->title, $hoverBoxParams['hoverBoxTitleLimit']);
				}
				
				// Links
				$item->itemLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));
				
				// Introtext
				if ($detailBoxParams['detailBoxStripTags'])
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
					$item->itemIntrotext = $this->utilities->wordLimit($item->itemIntrotext, $detailBoxParams['detailBoxIntrotextLimit']);
				}
				else
				{
					$item->itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->itemIntrotext = preg_replace('/\[.*\]/', '', $item->itemIntrotext);
				}
				
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxStripTags'])
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
					$item->hover_itemIntrotext = $this->utilities->wordLimit($item->hover_itemIntrotext, $hoverBoxParams['hoverBoxIntrotextLimit']);
				}
				else
				{
					$item->hover_itemIntrotext = preg_replace('/\{.*\}/', '', $item->description);
					$item->hover_itemIntrotext = preg_replace('/\[.*\]/', '', $item->hover_itemIntrotext);
				}
				
				// Date
				$item->itemDate = JHTML::_('date', $item->created_time, $detailBoxParams['detailBoxDateFormat']);
				if ($hoverBoxParams['hoverBox'] && $hoverBoxParams['hoverBoxDate'])
				{
					$item->hover_itemDate = JHTML::_('date', $item->created_time, $hoverBoxParams['hoverBoxDateFormat']);
				}
				$item->itemDateRaw = $item->created_time;
				
				// Author
				$item->itemAuthorRaw = $item->created_user_id;
				$item->itemAuthor = JFactory::getUser($item->itemAuthorRaw)->name;
				
				// Count
				$item->itemCount = $item->numitems;
				if ($item->itemCount == 1) {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ARTICLE'); 
				} else {
					$item->itemCount = $item->itemCount.'&nbsp;'.JText::_('COM_MINITEKWALL_ARTICLES'); 
				}

			}
		}
		
		return $items;
	}
	
	public function getWidgetDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams)
	{
		$sourceID = $this->utilities->getSourceID($widgetID);
		
		if ($sourceID == 'joomla')
		{
			return $this->getJoomlaDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams);
		}
		if ($sourceID == 'k2')
		{
			return $this->getK2DisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams);
		}
		if ($sourceID == 'jomsocial')
		{
			return $this->getJomsocialDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams);
		}
		if ($sourceID == 'virtuemart')
		{
			return $this->getVirtuemartDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams);
		}
		if ($sourceID == 'easyblog')
		{
			return $this->getEasyblogDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams);
		}
		if ($sourceID == 'folder')
		{
			return $this->getFolderDisplayOptions($widgetID, $items, $detailBoxParams, $hoverBoxParams);
		}
	}	
}