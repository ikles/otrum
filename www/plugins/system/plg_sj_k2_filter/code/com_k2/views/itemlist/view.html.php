<?php
/**
 * @version    2.7.x
 * @package    K2
 * @author     JoomlaWorks http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.application.component.view');

class K2ViewItemlist extends K2ViewItemlistDefault
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$params = K2HelperUtilities::getParams('com_k2');
		$model = $this->getModel('itemlist');
		$limitstart = JRequest::getInt('limitstart');
		$view = JRequest::getWord('view');
		$task = JRequest::getWord('task');
		$db = JFactory::getDBO();

		// Add link
		if (K2HelperPermissions::canAddItem())
			$addLink = JRoute::_('index.php?option=com_k2&view=item&task=add&tmpl=component');
		$this->assignRef('addLink', $addLink);
		// Get data depending on task
		switch ($task)
		{
			case 'category' :
				// Get category
				$id = JRequest::getInt('id');
				JTable::addIncludePath(JPATH_SOURCE_COMPONENT_ADMINISTRATOR.DS.'tables');
				$category = JTable::getInstance('K2Category', 'Table');
				$category->load($id);
				$category->event = new stdClass;

				// State check
				if (!$category->published || $category->trash)
				{
					JError::raiseError(404, JText::_('K2_CATEGORY_NOT_FOUND'));
				}

				// Access check
				$user = JFactory::getUser();
				if (K2_JVERSION != '15')
				{
					if (!in_array($category->access, $user->getAuthorisedViewLevels()))
					{

						if ($user->guest)
						{
							$uri = JFactory::getURI();
							$url = 'index.php?option=com_users&view=login&return='.($uri->toString());
							$mainframe->enqueueMessage(JText::_('K2_YOU_NEED_TO_LOGIN_FIRST'), 'notice');
							$mainframe->redirect(JRoute::_($url, false));
						}
						else
						{
							JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
							return;
						}

					}
					$languageFilter = $mainframe->getLanguageFilter();
					$languageTag = JFactory::getLanguage()->getTag();
					if ($languageFilter && $category->language != $languageTag && $category->language != '*')
					{
						return;
					}
				}
				else
				{
					if ($category->access > $user->get('aid', 0))
					{
						if ($user->guest)
						{
							$uri = JFactory::getURI();
							$url = 'index.php?option=com_user&view=login&return='.($uri->toString());
							$mainframe->enqueueMessage(JText::_('K2_YOU_NEED_TO_LOGIN_FIRST'), 'notice');
							$mainframe->redirect(JRoute::_($url, false));
						}
						else
						{
							JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
							return;
						}
					}
				}

				// Hide the add new item link if user cannot post in the specific category
				if (!K2HelperPermissions::canAddItem($id))
				{
					unset($this->addLink);
				}

				// Merge params
				$cparams = class_exists('JParameter') ? new JParameter($category->params) : new JRegistry($category->params);

				// Get the meta information before merging params since we do not want them to be inherited
				$category->metaDescription = $cparams->get('catMetaDesc');
				$category->metaKeywords = $cparams->get('catMetaKey');
				$category->metaRobots = $cparams->get('catMetaRobots');
				$category->metaAuthor = $cparams->get('catMetaAuthor');

				if ($cparams->get('inheritFrom'))
				{
					$masterCategory = JTable::getInstance('K2Category', 'Table');
					$masterCategory->load($cparams->get('inheritFrom'));
					$cparams = class_exists('JParameter') ? new JParameter($masterCategory->params) : new JRegistry($masterCategory->params);
				}
				$params->merge($cparams);

				// Category link
				$category->link = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($category->id.':'.urlencode($category->alias))));

				// Category image
				$category->image = K2HelperUtilities::getCategoryImage($category->image, $params);

				// Category plugins
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper::importPlugin('content');
				$category->text = $category->description;
				if (K2_JVERSION != '15')
				{
					$dispatcher->trigger('onContentPrepare', array(
						'com_k2.category',
						&$category,
						&$params,
						$limitstart
					));
				}
				else
				{
					$dispatcher->trigger('onPrepareContent', array(
						&$category,
						&$params,
						$limitstart
					));
				}

				$category->description = $category->text;

				// Category K2 plugins
				$category->event->K2CategoryDisplay = '';
				JPluginHelper::importPlugin('k2');
				$results = $dispatcher->trigger('onK2CategoryDisplay', array(
					&$category,
					&$params,
					$limitstart
				));
				$category->event->K2CategoryDisplay = trim(implode("\n", $results));
				$category->text = $category->description;
				$dispatcher->trigger('onK2PrepareContent', array(
					&$category,
					&$params,
					$limitstart
				));
				$category->description = $category->text;

				$this->assignRef('category', $category);
				$this->assignRef('user', $user);

				// Category children
				$ordering = $params->get('subCatOrdering');
				$children = $model->getCategoryFirstChildren($id, $ordering);
				if (count($children))
				{
					foreach ($children as $child)
					{
						if ($params->get('subCatTitleItemCounter'))
						{
							$child->numOfItems = $model->countCategoryItems($child->id);
						}
						$child->image = K2HelperUtilities::getCategoryImage($child->image, $params);
						$child->name = htmlspecialchars($child->name, ENT_QUOTES);
						$child->link = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($child->id.':'.urlencode($child->alias))));
						$subCategories[] = $child;
					}
					$this->assignRef('subCategories', $subCategories);
				}

				// Set limit
				$limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items') + $params->get('num_links');

				// Set featured flag
				JRequest::setVar('featured', $params->get('catFeaturedItems'));

				// Set layout
				$this->setLayout('category');

				// Set title
				$title = $category->name;
				$category->name = htmlspecialchars($category->name, ENT_QUOTES);

				// Set ordering
				if ($params->get('singleCatOrdering'))
				{
					$ordering = $params->get('singleCatOrdering');
				}
				else
				{
					$ordering = $params->get('catOrdering');
				}

				$addHeadFeedLink = $params->get('catFeedLink');

				break;

			case 'user' :
				// Get user
				$id = JRequest::getInt('id');
				$userObject = JFactory::getUser($id);
				$userObject->event = new stdClass;

				// Check user status
				if ($userObject->block)
				{
					JError::raiseError(404, JText::_('K2_USER_NOT_FOUND'));
				}

				// Get K2 user profile
				$userObject->profile = $model->getUserProfile();

				// User image
				$userObject->avatar = K2HelperUtilities::getAvatar($userObject->id, $userObject->email, $params->get('userImageWidth'));

				// User K2 plugins
				$userObject->event->K2UserDisplay = '';
				if (is_object($userObject->profile) && $userObject->profile->id > 0)
				{
					$dispatcher = JDispatcher::getInstance();
					JPluginHelper::importPlugin('k2');
					$results = $dispatcher->trigger('onK2UserDisplay', array(
						&$userObject->profile,
						&$params,
						$limitstart
					));
					$userObject->event->K2UserDisplay = trim(implode("\n", $results));
					$userObject->profile->url = htmlspecialchars($userObject->profile->url, ENT_QUOTES, 'UTF-8');
				}
				$this->assignRef('user', $userObject);

				$date = JFactory::getDate();
				$now = K2_JVERSION == '15' ? $date->toMySQL() : $date->toSql();
				$this->assignRef('now', $now);

				// Set layout
				$this->setLayout('user');

				// Set limit
				$limit = $params->get('userItemCount');

				// Set title
				$title = $userObject->name;

				// Set ordering
				$ordering = $params->get('userOrdering');

				$addHeadFeedLink = $params->get('userFeedLink', 1);

				break;

			case 'tag' :
				// Set layout
				$this->setLayout('tag');

				// Set limit
				$limit = $params->get('tagItemCount');

				// Prevent spammers from using the tag view
				$tag = JRequest::getString('tag');
				$db = JFactory::getDBO();
				$db->setQuery('SELECT id, name FROM #__k2_tags WHERE name = '.$db->quote($tag));
				$tag = $db->loadObject();
				if (!$tag->id)
				{
					JError::raiseError(404, JText::_('K2_NOT_FOUND'));
					return false;
				}

				// Set title
				$title = JText::_('K2_DISPLAYING_ITEMS_BY_TAG').' '.$tag->name;

				// Set ordering
				$ordering = $params->get('tagOrdering');

				$addHeadFeedLink = $params->get('tagFeedLink', 1);

				break;

			case 'search' :
				// Set layout
				$this->setLayout('generic');

				// Set limit
				$limit = $params->get('genericItemCount');

				// Set title
				if (JRequest::getVar('searchword')){
					$title = JText::_('K2_SEARCH_RESULTS_FOR').' '.JRequest::getVar('searchword');
				}else{
					$items = $model->getData();
					$title = JText::_('K2_SEARCH_RESULTS_FOR').' '.count($items);
				}


				$addHeadFeedLink = $params->get('genericFeedLink', 1);

				break;

			case 'filter' :
				// Set layout
				$this->setLayout('generic');

				// Set limit
				$limit = $params->get('genericItemCount');

				// Set title
				$items = $model->getData();
				 $title = 'Search results:'.' '. '('.count($items).')';

				$addHeadFeedLink = $params->get('genericFeedLink', 1);

				break;

			case 'date' :
				// Set layout
				$this->setLayout('generic');

				// Set limit
				$limit = $params->get('genericItemCount');

				// Fix wrong timezone
				if (function_exists('date_default_timezone_get'))
				{
					$originalTimezone = date_default_timezone_get();
				}
				if (function_exists('date_default_timezone_set'))
				{
					date_default_timezone_set('UTC');
				}

				// Set title
				if (JRequest::getInt('day'))
				{
					$date = strtotime(JRequest::getInt('year').'-'.JRequest::getInt('month').'-'.JRequest::getInt('day'));
					$dateFormat = (K2_JVERSION == '15') ? '%A, %d %B %Y' : 'l, d F Y';
					$title = JText::_('K2_ITEMS_FILTERED_BY_DATE').' '.JHTML::_('date', $date, $dateFormat);
				}
				else
				{
					$date = strtotime(JRequest::getInt('year').'-'.JRequest::getInt('month'));
					$dateFormat = (K2_JVERSION == '15') ? '%B %Y' : 'F Y';
					$title = JText::_('K2_ITEMS_FILTERED_BY_DATE').' '.JHTML::_('date', $date, $dateFormat);
				}

				// Restore the original timezone
				if (function_exists('date_default_timezone_set') && isset($originalTimezone))
				{
					date_default_timezone_set($originalTimezone);
				}

				// Set ordering
				$ordering = 'rdate';

				$addHeadFeedLink = $params->get('genericFeedLink', 1);

				break;

			default :
				// Set layout
				$this->setLayout('category');
				$user = JFactory::getUser();
				$this->assignRef('user', $user);

				// Set limit
				$limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items') + $params->get('num_links');
				// Set featured flag
				JRequest::setVar('featured', $params->get('catFeaturedItems'));

				// Set title
				$title = $params->get('page_title');

				// Set ordering
				$ordering = $params->get('catOrdering');

				$addHeadFeedLink = $params->get('catFeedLink', 1);

				break;
		}

		// Set limit for model
		if (!$limit)
			$limit = 10;
		JRequest::setVar('limit', $limit);

		// Get items
		if (!isset($ordering))
		{
			$items = $model->getData();
		}
		else
		{
			$items = $model->getData($ordering);
		}

		// If a user has no published items, do not display their K2 user page (in the frontend) and redirect to the homepage of the site.
		if(count($items) == 0 && $task == 'user') {
			$mainframe->redirect(JUri::root());
		}

		// Pagination
		jimport('joomla.html.pagination');
		$total = count($items) ? $model->getTotal() : 0;
		$pagination = new JPagination($total, $limitstart, $limit);

		//Prepare items
		$user = JFactory::getUser();
		$cache = JFactory::getCache('com_k2_extended');
		$model = $this->getModel('item');

		for ($i = 0; $i < sizeof($items); $i++)
		{

			// Ensure that all items have a group. If an item with no group is found then assign to it the leading group
			$items[$i]->itemGroup = 'leading';

			//Item group
			if ($task == "category" || $task == "")
			{
				if ($i < ($params->get('num_links') + $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items')))
					$items[$i]->itemGroup = 'links';
				if ($i < ($params->get('num_secondary_items') + $params->get('num_leading_items') + $params->get('num_primary_items')))
					$items[$i]->itemGroup = 'secondary';
				if ($i < ($params->get('num_primary_items') + $params->get('num_leading_items')))
					$items[$i]->itemGroup = 'primary';
				if ($i < $params->get('num_leading_items'))
					$items[$i]->itemGroup = 'leading';
			}

			// Check if the model should use the cache for preparing the item even if the user is logged in
			if ($user->guest || $task == 'tag' || $task == 'search' || $task == 'filter' || $task == 'date')
			{
				$cacheFlag = true;
			}
			else
			{
				$cacheFlag = true;
				if (K2HelperPermissions::canEditItem($items[$i]->created_by, $items[$i]->catid))
				{
					$cacheFlag = false;
				}
			}

			// Prepare item
			if ($cacheFlag)
			{
				$hits = $items[$i]->hits;
				$items[$i]->hits = 0;
				JTable::getInstance('K2Category', 'Table');
				$items[$i] = $cache->call(array(
					$model,
					'prepareItem'
				), $items[$i], $view, $task);
				$items[$i]->hits = $hits;
			}
			else
			{
				$items[$i] = $model->prepareItem($items[$i], $view, $task);
			}

			// Plugins
			$items[$i] = $model->execPlugins($items[$i], $view, $task);

			// Trigger comments counter event if needed
			if ($params->get('catItemK2Plugins') &&
			    ($params->get('catItemCommentsAnchor') ||
			     $params->get('itemCommentsAnchor') ||
			     $params->get('itemComments')))
			{
				// Trigger comments counter event
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper::importPlugin('k2');
				$results = $dispatcher->trigger('onK2CommentsCounter', array(
					&$items[$i],
					&$params,
					$limitstart
				));
				$items[$i]->event->K2CommentsCounter = trim(implode("\n", $results));
			}
		}

		// Set title
		$document = JFactory::getDocument();
		$application = JFactory::getApplication();
		$menus = $application->getMenu();
		$menu = $menus->getActive();
		if (is_object($menu))
		{
			if (is_string($menu->params))
			{
				$menu_params = K2_JVERSION == '15' ? new JParameter($menu->params) : new JRegistry($menu->params);
			}
			else
			{
				$menu_params = $menu->params;
			}
			if (!$menu_params->get('page_title'))
			{
				$params->set('page_title', $title);
			}
		}
		else
		{
			$params->set('page_title', $title);
		}

		// We're adding a new variable here which won't get the appended/prepended site title,
		// when enabled via Joomla!'s SEO/SEF settings
		$params->set('page_title_clean', $title);

		if (K2_JVERSION != '15')
		{
			if ($mainframe->getCfg('sitename_pagetitles', 0) == 1)
			{
				$tmpTitle = JText::sprintf('JPAGETITLE', $mainframe->getCfg('sitename'), $params->get('page_title'));
				$params->set('page_title', $tmpTitle);
			}
			elseif ($mainframe->getCfg('sitename_pagetitles', 0) == 2)
			{
				$tmpTitle = JText::sprintf('JPAGETITLE', $params->get('page_title'), $mainframe->getCfg('sitename'));
				$params->set('page_title', $tmpTitle);
			}
		}
		$document->setTitle($params->get('page_title'));

		// Search - Update the Google Search results container (K2 v2.7.0+)
		if ($task == 'search')
		{
			$googleSearchContainerID = trim($params->get('googleSearchContainer', 'k2GoogleSearchContainer'));
			if ($googleSearchContainerID == 'k2Container')
			{
				$googleSearchContainerID = 'k2GoogleSearchContainer';
			}
			$params->set('googleSearchContainer', $googleSearchContainerID);
		}

		// Search - Update the Google Search results container (K2 v2.7.0+)
		if ($task == 'filter')
		{
			$googleSearchContainerID = trim($params->get('googleSearchContainer', 'k2GoogleSearchContainer'));
			if ($googleSearchContainerID == 'k2_Container')
			{
				$googleSearchContainerID = 'k2GoogleSearchContainer';
			}
			$params->set('googleSearchContainer', $googleSearchContainerID);
		}

		// Set metadata for category
		if ($task == 'category')
		{
			if ($category->metaDescription)
			{
				$document->setDescription($category->metaDescription);
			}
			else
			{
				$metaDescItem = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $this->category->description);
				$metaDescItem = strip_tags($metaDescItem);
				$metaDescItem = K2HelperUtilities::characterLimit($metaDescItem, $params->get('metaDescLimit', 150));
				if (K2_JVERSION != '15')
				{
					$metaDescItem = html_entity_decode($metaDescItem);
				}
				$document->setDescription($metaDescItem);
			}
			if ($category->metaKeywords)
			{
				$document->setMetadata('keywords', $category->metaKeywords);
			}
			if ($category->metaRobots)
			{
				$document->setMetadata('robots', $category->metaRobots);
			}
			if ($category->metaAuthor)
			{
				$document->setMetadata('author', $category->metaAuthor);
			}
		}

		if (K2_JVERSION != '15')
		{

			// Menu metadata options
			if ($params->get('menu-meta_description'))
			{
				$document->setDescription($params->get('menu-meta_description'));
			}

			if ($params->get('menu-meta_keywords'))
			{
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}

			if ($params->get('robots'))
			{
				$document->setMetadata('robots', $params->get('robots'));
			}

			// Menu page display options
			if ($params->get('page_heading'))
			{
				$params->set('page_title', $params->get('page_heading'));
			}
			$params->set('show_page_title', $params->get('show_page_heading'));

		}

		// Pathway
		$pathway = $mainframe->getPathWay();
		if (!isset($menu->query['task']))
			$menu->query['task'] = '';
		if ($menu)
		{
			switch ($task)
			{
				case 'category' :
					if ($menu->query['task'] != 'category' || $menu->query['id'] != JRequest::getInt('id'))
						$pathway->addItem($title, '');
					break;
				case 'user' :
					if ($menu->query['task'] != 'user' || $menu->query['id'] != JRequest::getInt('id'))
						$pathway->addItem($title, '');
					break;

				case 'tag' :
					if ($menu->query['task'] != 'tag' || $menu->query['tag'] != JRequest::getVar('tag'))
						$pathway->addItem($title, '');
					break;

				case 'search' :
				case 'filter' :
				case 'date' :
					$pathway->addItem($title, '');
					break;
			}
		}

		// Feed link
		$config = JFactory::getConfig();
		$menu = $application->getMenu();
		$default = $menu->getDefault();
		$active = $menu->getActive();
		if ($task == 'tag')
		{
			$link = K2HelperRoute::getTagRoute(JRequest::getVar('tag'));
		}
		else
		{
			$link = '';
		}
		$sef = K2_JVERSION == '30' ? $config->get('sef') : $config->getValue('config.sef');
		if (!is_null($active) && $active->id == $default->id && $sef)
		{
			$link .= '&Itemid='.$active->id.'&format=feed&limitstart=';
		}
		else
		{
			$link .= '&format=feed&limitstart=';
		}

		$feed = JRoute::_($link);
		$this->assignRef('feed', $feed);

		// Add head feed link
		if ($addHeadFeedLink)
		{
			$attribs = array(
				'type' => 'application/rss+xml',
				'title' => 'RSS 2.0'
			);
			$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array(
				'type' => 'application/atom+xml',
				'title' => 'Atom 1.0'
			);
			$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}

		// Assign data
		if ($task == "category" || $task == "")
		{

			// Leading items
			$offset = 0;
			$length = $params->get('num_leading_items');
			$leading = array_slice($items, $offset, $length);

			// Primary
			$offset = (int)$params->get('num_leading_items');
			$length = (int)$params->get('num_primary_items');
			$primary = array_slice($items, $offset, $length);

			// Secondary
			$offset = (int)($params->get('num_leading_items') + $params->get('num_primary_items'));
			$length = (int)$params->get('num_secondary_items');
			$secondary = array_slice($items, $offset, $length);

			// Links
			$offset = (int)($params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items'));
			$length = (int)$params->get('num_links');
			$links = array_slice($items, $offset, $length);

			// Assign data
			$this->assignRef('leading', $leading);
			$this->assignRef('primary', $primary);
			$this->assignRef('secondary', $secondary);
			$this->assignRef('links', $links);
		}
		else
		{
			$this->assignRef('items', $items);
		}

		// Set default values to avoid division by zero
		if ($params->get('num_leading_columns') == 0)
			$params->set('num_leading_columns', 1);
		if ($params->get('num_primary_columns') == 0)
			$params->set('num_primary_columns', 1);
		if ($params->get('num_secondary_columns') == 0)
			$params->set('num_secondary_columns', 1);
		if ($params->get('num_links_columns') == 0)
			$params->set('num_links_columns', 1);

		$this->assignRef('params', $params);
		$this->assignRef('pagination', $pagination);

		// Set Facebook meta data
		if($params->get('facebookMetatags', '1'))
		{
			$document = JFactory::getDocument();
			$uri = JURI::getInstance();
			$document->setMetaData('og:url', $uri->toString());
			$document->setMetaData('og:title', (K2_JVERSION == '15') ? htmlspecialchars($document->getTitle(), ENT_QUOTES, 'UTF-8') : $document->getTitle());
			$document->setMetaData('og:type', 'website');
			if ($task == 'category' && $this->category->image && strpos($this->category->image, 'placeholder/category.png') === false)
			{
				$image = substr(JURI::root(), 0, -1).str_replace(JURI::root(true), '', $this->category->image);
				$document->setMetaData('og:image', $image);
				$document->setMetaData('image', $image);
			}
			$document->setMetaData('og:description', strip_tags($document->getDescription()));
		}
		
		// path
		$path   = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'generic.php';
		// Look for template files in component folders
		$this->_addPath('template', dirname(dirname(dirname(__FILE__))).DS.'templates');
		if(JRequest::getVar('task') && JRequest::getVar('task') == 'filter'){
			if(!file_exists($path)){
				include_once dirname(dirname(dirname(__FILE__))).DS.'templates/generic.php';
			}else{
				include_once $path;
			}
		}

		$this->_addPath('template', JPATH_SOURCE_COMPONENT.DS.'templates'.DS.'default');

		// Look for overrides in template folder (K2 template structure)
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates');
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates'.DS.'default');

		// Look for overrides in template folder (Joomla! template structure)
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'default');
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2');


		// Look for specific K2 theme files
		if ($params->get('theme'))
		{
			$this->_addPath('template', JPATH_SOURCE_COMPONENT.DS.'templates'.DS.$params->get('theme'));
			$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates'.DS.$params->get('theme'));
			$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.$params->get('theme'));
		}

		$nullDate = $db->getNullDate();
		$this->assignRef('nullDate', $nullDate);
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('k2');
		$dispatcher->trigger('onK2BeforeViewDisplay');

//		var_dump($this->getLayout());
		parent::display($tpl);
	}
	function getItemExtraFields($itemExtraFields, &$item = null)
	{

		static $K2ItemExtraFieldsInstances = array();
		if ($item && isset($K2ItemExtraFieldsInstances[$item->id]))
		{
			$this->buildAliasBasedExtraFields($K2ItemExtraFieldsInstances[$item->id], $item);
			return $K2ItemExtraFieldsInstances[$item->id];
		}

		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'JSON.php');
		$json = new Services_JSON;
		$jsonObjects = $json->decode($itemExtraFields);
		$imgExtensions = array(
			'jpg',
			'jpeg',
			'gif',
			'png'
		);
		$params = K2HelperUtilities::getParams('com_k2');

		if (count($jsonObjects) < 1)
		{
			return NULL;
		}

		foreach ($jsonObjects as $object)
		{
			$extraFieldsIDs[] = $object->id;
		}
		JArrayHelper::toInteger($extraFieldsIDs);
		$condition = @implode(',', $extraFieldsIDs);

		$query = "SELECT extraFieldsGroup FROM #__k2_categories WHERE id=".(int)$item->catid;
		$db->setQuery($query);
		$group = $db->loadResult();

		$query = "SELECT * FROM #__k2_extra_fields WHERE `group` = ".(int)$group." AND published=1 AND (id IN ({$condition}) OR `type` = 'header') ORDER BY ordering ASC";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$size = count($rows);

		for ($i = 0; $i < $size; $i++)
		{

			$value = '';
			$values = array();
			foreach ($jsonObjects as $object)
			{
				if ($rows[$i]->id == $object->id)
				{
					if ($rows[$i]->type == 'textfield' || $rows[$i]->type == 'textarea' || $rows[$i]->type == 'date')
					{
						$value = $object->value;
						if ($rows[$i]->type == 'date' && $value)
						{
							$offset = (K2_JVERSION != '15') ? null : 0;
							$value = JHTML::_('date', $value, JText::_('K2_DATE_FORMAT_LC'), $offset);
						}

					}
					else if ($rows[$i]->type == 'image')
					{
						if ($object->value)
						{
							$src = '';
							if (JString::strpos('http://', $object->value) === false)
							{
								$src .= JURI::root(true);
							}
							$src .= $object->value;
							$value = '<img src="'.$src.'" alt="'.$rows[$i]->name.'" />';
						}
						else
						{
							$value = false;
						}

					}
					else if ($rows[$i]->type == 'labels')
					{
						$labels = explode(',', $object->value);
						if (!is_array($labels))
						{
							$labels = (array)$labels;
						}
						$value = '';
						foreach ($labels as $label)
						{
							$label = JString::trim($label);
							$label = str_replace('-', ' ', $label);
							$value .= '<a href="'.JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&searchword='.urlencode($label)).'">'.$label.'</a> ';
						}

					}
					else if ($rows[$i]->type == 'select' || $rows[$i]->type == 'radio')
					{
						foreach ($json->decode($rows[$i]->value) as $option)
						{
							if ($option->value == $object->value)
							{
								$value .= $option->name;
							}

						}
					}
					else if ($rows[$i]->type == 'multipleSelect')
					{
						foreach ($json->decode($rows[$i]->value) as $option)
						{
							if (@in_array($option->value, $object->value))
							{
								$values[] = $option->name;
							}

						}
						$value = @implode(', ', $values);
					}
					else if ($rows[$i]->type == 'csv')
					{
						$array = $object->value;
						if (count($array))
						{
							$value .= '<table cellspacing="0" cellpadding="0" class="csvTable">';
							foreach ($array as $key => $row)
							{
								$value .= '<tr>';
								foreach ($row as $cell)
								{
									$value .= ($key > 0) ? '<td>'.$cell.'</td>' : '<th>'.$cell.'</th>';
								}
								$value .= '</tr>';
							}
							$value .= '</table>';
						}

					}
					else
					{

						switch ($object->value[2])
						{
							case 'same' :
							default :
								$attributes = '';
								break;

							case 'new' :
								$attributes = 'target="_blank"';
								break;

							case 'popup' :
								$attributes = 'class="classicPopup" rel="{\'x\':'.$params->get('linkPopupWidth').',\'y\':'.$params->get('linkPopupHeight').'}"';
								break;

							case 'lightbox' :

								// Joomla! modal required
								if (!defined('K2_JOOMLA_MODAL_REQUIRED'))
									define('K2_JOOMLA_MODAL_REQUIRED', true);

								$filename = @basename($object->value[1]);
								$extension = JFile::getExt($filename);
								if (!empty($extension) && in_array($extension, $imgExtensions))
								{
									$attributes = 'data-k2-modal="image"';
								}
								else
								{
									$attributes = 'data-k2-modal="iframe"';
								}
								break;
						}
						$object->value[0] = JString::trim($object->value[0]);
						$object->value[1] = JString::trim($object->value[1]);

						if ($object->value[1] && $object->value[1] != 'http://' && $object->value[1] != 'https://')
						{
							if ($object->value[0] == '')
							{
								$object->value[0] = $object->value[1];
							}
							$rows[$i]->url = $object->value[1];
							$rows[$i]->text = $object->value[0];
							$rows[$i]->attributes = $attributes;
							$value = '<a href="'.$object->value[1].'" '.$attributes.'>'.$object->value[0].'</a>';
						}
						else
						{
							$value = false;
						}
					}

				}

			}

			if ($rows[$i]->type == 'header')
			{
				$tmp = json_decode($rows[$i]->value);
				if (!$tmp[0]->displayInFrontEnd)
				{
					$value = null;
				}
				else
				{
					$value = $tmp[0]->value;
				}
			}

			// Detect alias
			$tmpValues = $json->decode($rows[$i]->value);
			if (isset($tmpValues[0]) && isset($tmpValues[0]->alias) && !empty($tmpValues[0]->alias))
			{
				$rows[$i]->alias = $tmpValues[0]->alias;
			}
			else
			{
				$filter = JFilterInput::getInstance();
				$rows[$i]->alias = $filter->clean($rows[$i]->name, 'WORD');
				if (!$rows[$i]->alias)
				{
					$rows[$i]->alias = 'extraField'.$rows[$i]->id;
				}
			}

			if (JString::trim($value) != '')
			{
				$rows[$i]->value = $value;
				if (!is_null($item))
				{
					if (!isset($item->extraFields))
					{
						$item->extraFields = new stdClass;
					}
					$tmpAlias = $rows[$i]->alias;
					$item->extraFields->$tmpAlias = $rows[$i];
				}
			}
			else
			{
				unset($rows[$i]);
			}
		}

		if ($item)
		{
			$K2ItemExtraFieldsInstances[$item->id] = $rows;
		}
		$this->buildAliasBasedExtraFields($K2ItemExtraFieldsInstances[$item->id], $item);
		return $K2ItemExtraFieldsInstances[$item->id];
	}
	function buildAliasBasedExtraFields($extraFields, &$item)
	{
		if (is_null($item))
		{
			return false;
		}
		if (!isset($item->extraFields))
		{
			$item->extraFields = new stdClass;
		}
		foreach ($extraFields as $extraField)
		{
			$tmpAlias = $extraField->alias;
			$item->extraFields->$tmpAlias = $extraField;
		}
	}

}
