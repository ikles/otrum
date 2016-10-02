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

class MinitekWallHelperUtilities
{
	public static $extension = 'com_minitekwall';

	public function getSideMenuHTML()
	{
		$menus = Array(
			Array(
				'title' => JText::_('COM_MINITEKWALL_QUICKSTART'),
				'url' => 'index.php?option=com_minitekwall',
				'class' => 'fa fa-magic',
				'children' => Array()
			),
			Array(
				'title' => JText::_('COM_MINITEKWALL_WIDGETS'),
				'url' => 'index.php?option=com_minitekwall&view=widgets',
				'class' => 'fa fa-picture-o',
				'children' => Array()
			),
			Array(
				'title' => JText::_('COM_MINITEKWALL_DOCUMENTATION'),
				'url' => 'http://www.minitek.gr/support/documentation/joomla-extensions/components/minitek-wall',
				'class' => 'fa fa-book',
				'children' => Array()
			),
			Array(
				'title' => JText::_('COM_MINITEKWALL_ABOUT'),
				'url' => 'index.php?option=com_minitekwall&view=about',
				'class' => 'fa fa-info-circle',
				'children' => Array()
			),
			Array(
				'title' => JText::_('COM_MINITEKWALL_RATE_IT'),
				'url' => 'http://extensions.joomla.org/extensions/extension/news-display/articles-display/minitek-wall-pro',
				'class' => 'fa fa-star',
				'children' => Array()
			)
		);

		$view = JRequest::getcmd('view');
		$cfgSection = JRequest::getcmd('cfgSection','');
		$cfgSection = (!empty($cfgSection)) ? '&cfgSection='.$cfgSection : '';

		$html = '<ul class="nav nav-list">' . PHP_EOL;

		foreach ($menus as $menu)
		{
			$hasChildren = ! empty($menu['children']);
			$dropdownToggleClass = ($hasChildren) ? 'dropdown-toggle' : '';
			$isOpen = false;
			$current = '';

			$openClass = ($isOpen) ? 'open' : '';
			$openSubStyle = ($isOpen) ? 'display: block;' : '';
			$target = '';
			if($menu['url'] == 'http://www.minitek.gr/support/documentation/joomla-extensions/components/minitek-wall'
			|| $menu['url'] == 'http://extensions.joomla.org/extensions/extension/news-display/articles-display/minitek-wall-pro')
			{
				$target='target="_blank"';
			}

			$html .= '<li class="' . $openClass . '"><a href="' . JRoute::_($menu['url']) . '" class="' . $dropdownToggleClass . '" '.$target.'>';
			$html .= '<i class="' . $menu['class'] . '"></i> <span class="menu-text"> ' . $menu['title'] .' </span>';
			$html .= '</a>';
			$html .= '</li>'. PHP_EOL;
		}
		
		$html .= '</ul>' . PHP_EOL;
		
		$html .= '<div class="sidebar-collapse" id="sidebar-collapse">';
			$html .= '<i class="fa fa-angle-double-left"></i>';
		$html .= '</div>';

		return $html;
	}
	
	public function getNavbarHTML()
	{
		$version = self::localVersion();
		$version_match = str_replace(".","", $version);
		$version_match = (int)$version_match;
		$newVersion = self::currentVersion();
		$newVersion_match = str_replace(".","", $newVersion);
		$newVersion_match = (int)$newVersion_match;
	
		$user  = JFactory::getUser();
				
		$html = '<div class="navbar">' . PHP_EOL;	
		
			$html .= '<div class="navbar-inner">' . PHP_EOL;	
		
				$html .= '<div class="container-fluid">' . PHP_EOL;
		
					$html .= '<div class="brand-cont">';
						$html .= '<a href="'.JRoute::_("index.php?option=com_minitekwall").'" class="brand">';
						$html .= '<img src="components/com_minitekwall/assets/images/logo-white.png" alt="" />';
						$html .= JText::_('COM_MINITEKWALL');
						$html .= '</a>';
						
						if ($newVersion) {
						if ($version_match == $newVersion_match) {
							$html .= '<span id="mw-version" class="badge badge-success">'.$version.'</span>' . PHP_EOL;
							$html .= '<span id="mw-version-info">';
								$html .= '<button class="btn btn-info" type="button" data-toggle="modal" data-target="#myModal1">';
									$html .= '<i class="fa fa-info"></i>';
								$html .= '</button>';
								$html .= '<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModal1Label" aria-hidden="true">';
								  $html .= '<div class="modal-dialog">';
									$html .= '<div class="modal-content">';
									  $html .= '<div class="modal-body">';
									  $html .= '<div class="thumbnail">';
										$html .= '<h3><i class="fa fa-check text-success"></i>&nbsp;&nbsp;'.JText::_('COM_MINITEKWALL_YOU_HAVE_THE_LATEST_VERSION');
										$html .= '</h3>';
									  $html .= '</div>';
									  $html .= '</div>';
									$html .= '</div>';
								  $html .= '</div>';
								$html .= '</div>';
							$html .= '</span>' . PHP_EOL;
						} else {
							$html .= '<span id="mw-version" class="badge badge-important">'.$version.'</span>' . PHP_EOL;
							$html .= '<span id="mw-version-info">';
								$html .= '<button class="btn btn-danger" type="button" data-toggle="modal" data-target="#myModal2">';
									$html .= '<i class="fa fa-info"></i>';
								$html .= '</button>';
								$html .= '<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModal2Label" aria-hidden="true">';
								  $html .= '<div class="modal-dialog">';
									$html .= '<div class="modal-content">';
									  $html .= '<div class="modal-body">';
									  $html .= '<div class="thumbnail">';
										$html .= '<h3>';
										$html .= JText::_('COM_MINITEKWALL_NEW_VERSION_IS_RELEASED');
										$html .= '</h3>';
										$html .= '<a href="http://www.minitek.gr/joomla-extensions/minitek-wall" target="_blank" class="btn btn-info">'.JText::_('COM_MINITEKWALL_LEARN_MORE').'</a>';
										$html .= '&nbsp;&nbsp;'.JText::_('COM_MINITEKWALL_NEW_VERSION_OR').'&nbsp;&nbsp;';
										$html .= '<a href="index.php?option=com_installer&view=update" class="btn btn-info">';
										$html .= JText::_('COM_MINITEKWALL_NEW_VERSION_UPDATE_TO').'&nbsp;';
										$html .= $newVersion;
										$html .= '</a>';
									  $html .= '</div>';
									  $html .= '</div>';
									$html .= '</div>';
								  $html .= '</div>';
								$html .= '</div>';
							$html .= '</span>' . PHP_EOL;
						}
						}
					$html .= '</div>';
					
					$html .= '<div class="configuration-cont pull-right">';
						
						// Configuration button
						if ($user->authorise('core.admin', 'com_minitekwall')) 
						{  
							$html .= '<a class="btn-configuration" href="index.php?option=com_config&view=component&component=com_minitekwall&path=&return='.base64_encode(JURI::getInstance()->toString()).'">';
								$html .= '<i class="fa fa-gear"></i>'.JText::_('COM_MINITEKWALL_CONFIGURATION');
							$html .= '</a>';
						}
						
							
					$html .= '</div>' . PHP_EOL;	
		
				$html .= '</div>' . PHP_EOL;	
			
			$html .= '</div>' . PHP_EOL;	
			
		$html .= '</div>' . PHP_EOL;

		
		return $html;
	}

	public static function getActions($categoryId = 0, $articleId = 0)
	{
		// Reverted a change for version 2.5.6
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($articleId) && empty($categoryId))
		{
			$assetName = 'com_minitekwall';
		}
		else
		{
			$assetName = 'com_minitekwall.widget.'.(int) $articleId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	public static function filterText($text)
	{
		JLog::add('MinitekWallHelperUtilities::filterText() is deprecated. Use JComponentHelper::filterText() instead.', JLog::WARNING, 'deprecated');

		return JComponentHelper::filterText($text);
	}
	
	public static function checkModuleIsInstalled()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Construct the query
		$query->select('*')
			->from('#__extensions AS e');
		$query->where('e.element = ' . $db->quote('mod_minitekwall'));
		
		// Setup the query
		$db->setQuery($query);
		
		$moduleExists = $db->loadObject();
		
		if ($moduleExists)
		{	
			return true;
		} else {
			return false;
		}
	}
	
	public function currentVersion()
	{	
		$params  = JComponentHelper::getParams('com_minitekwall');
		
		if ($params->get('version_check', 1)) 
		{
			if (self::isDomainAvailable('http://update.minitek.gr'))
			{
				if (self::isXMLAvailable('http://update.minitek.gr/joomla-extensions/minitek_wall_pro.xml'))
				{
					$xml_file = file_get_contents('http://update.minitek.gr/joomla-extensions/minitek_wall_pro.xml');
					if ($xml_file)
					{
						$updates = new SimpleXMLElement($xml_file);
						$version = (string)$updates->update[0]->version;
					} else {
						$version = 0;
					}
				}
				else
				{
					$version = 0;
				}
			}
			else
			{
				$version = 0;
			}
		}
		else
		{
			$version = 0;
		}
		
		return $version;
	}

	public function localVersion()
	{
		$xml = JFactory::getXML(JPATH_ADMINISTRATOR .'/components/com_minitekwall/minitekwall.xml');
		$version = (string)$xml->version;
	
		return $version;
	}
	
	function isDomainAvailable($domain)
   	{
		//check, if a valid url is provided
		if(!filter_var($domain, FILTER_VALIDATE_URL))
		{
			   return false;
		}
		
		//initialize curl
		$curlInit = curl_init($domain);
		curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt($curlInit,CURLOPT_HEADER,true);
		curl_setopt($curlInit,CURLOPT_NOBODY,true);
		curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
		
		//get answer
		$response = curl_exec($curlInit);
		
		curl_close($curlInit);
		
		if ($response) return true;
		
		return false;
   	}
	
	function isXMLAvailable($file)
   	{
		$ch = curl_init($file);

		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);
		
		if ($response >= 400)
		{
			return false;
		}
		else if ($response = 200)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}
}
