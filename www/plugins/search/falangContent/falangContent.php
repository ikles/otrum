<?php
/**
 * Joom!Fish - Multi Lingual extention and translation manager for Joomla!
 * Copyright (C) 2003 - 2011 Think Network GmbH, Munich
 * 
 * All rights reserved.  The Joom!Fish project is a set of extentions for 
 * the content management system Joomla!. It enables Joomla! 
 * to manage multi lingual sites especially in all dynamic information 
 * which are stored in the database.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * -----------------------------------------------------------------------------
 * $Id: jfcontent.php 1580 2011-04-16 17:11:41Z akede $
 * @package joomfish
 * @subpackage jfcontent
 *
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

require_once JPATH_SITE.'/components/com_content/router.php';


/**
* Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param integer The state to search for -1=archived, 0=unpublished, 1=published [default]
* @param string A prefix for the section label, eg, 'Archived '
*/

class plgSearchFalangContent extends JPlugin
{

    /**
     * @return array An array of search areas
     */
    function onContentSearchAreas()
    {
        static $areas = array(
        'content' => 'JGLOBAL_ARTICLES'
        );
        return $areas;
    }


    function onContentSearch($text, $phrase='', $ordering='', $areas=null)
    {
        $rows = array();

        $db		= JFactory::getDBO();
        $user	= JFactory::getUser();
        $user	= JFactory::getUser();
        $groups	= implode(',', $user->getAuthorisedViewLevels());

        require_once JPATH_SITE.'/components/com_content/helpers/route.php';
        require_once JPATH_SITE.'/administrator/components/com_search/helpers/search.php';

    	$lang = JFactory::getLanguage()->getTag();

        if (is_array( $areas )) {
            // Use main content search area
            if (!array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) )) {
                return array();
            }
        }


        $sContent 		= $this->params->get( 'search_content', 		1 );
        $sArchived 		= $this->params->get( 'search_archived', 		1 );
        $limit 			= $this->params->def( 'search_limit', 		50 );
        //$activeLang 	= $this->params->def( 'active_language_only', 1);//value not set actually
        $activeLang = 1;
        $defaultSiteLang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

        $nullDate 		= $db->getNullDate();
        $date = JFactory::getDate();
        $now = $date->toSql();

        //search on #__content only
        if (!$activeLang || ($lang == $defaultSiteLang)) {
            $rows = $this->searchDefaultLanguage($text,$phrase,$ordering,$areas);
        }

        $text = trim( $text );
        if ($text == '') {
            return array();
        }

	$wheres = array();
	switch ($phrase) {
		case 'exact':
			$text		= $db->Quote( '%'.$db->escape( $text, true ).'%', false );
			$where = "LOWER(fc.value) LIKE ".$text;
			break;

		case 'all':
		case 'any':
		default:
			$words = explode( ' ', $text );
			$wheres = array();
			foreach ($words as $word) {
				$word		= $db->Quote( '%'.$db->escape( $word, true ).'%', false );
				$wheres[] 	= "LOWER(fc.value) LIKE ".$word;
			}
			$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}

	$morder = '';
	switch ($ordering) {
		case 'oldest':
			$order = 'a.created ASC';
			break;

		case 'popular':
			$order = 'a.hits DESC';
			break;

		case 'alpha':
			$order = 'a.title ASC';
			break;

		case 'category':
			$order = 'b.title ASC, a.title ASC';
			$morder = 'a.title ASC';
			break;

		case 'newest':
			default:
			$order = 'a.created DESC';
			break;
	}


	// search articles
	if ( $sContent && $limit > 0 )
	{
		// NB can't use concat since Joomfish won't translate sub-values
		$query = 'SELECT a.id as contid, b.id as catid, a.title AS title, a.created AS created,'
		//. ' CONCAT(a.introtext, a.`fulltext`) AS text,'
		. ' a.introtext, a.fulltext, '
		//. ' CONCAT(CONCAT_WS( "/", u.title, b.title ), " - ", jfl.name) AS section,'
		. ' b.title as cattitle,'
		. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'
		. ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(":", b.id, b.alias) ELSE b.id END as catslug,'
		. ' "2" AS browsernav, '
		. ' l.lang_code as jflang, l.title as jflname'
		. ' FROM #__content AS a'
		. ' INNER JOIN #__categories AS b ON b.id=a.catid'
		. "\n LEFT JOIN #__falang_content as fc ON reference_id = a.id"
		. "\n LEFT JOIN #__languages as l ON fc.language_id = l.lang_id"
		. ' WHERE ( '.$where.' )'
		. ' AND a.state = 1'
		. ' AND b.published = 1'
        . ' AND a.access IN ('.$groups.') '
		. ' AND b.access IN ('.$groups.') '
		. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
		. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
		. "\n AND fc.reference_table = 'content'"
		. ( $activeLang ? "\n AND l.lang_code = '$lang'" : '')
		. ' GROUP BY a.id'
		. ' ORDER BY '. $order
		;

		$db->setQuery( $query, 0, $limit );
		$list = $db->loadObjectList();
		$limit -= count($list);

		if(isset($list))
		{
			foreach($list as $key => $item)
			{
				$list[$key]->text = $item->introtext . $item->fulltext ;
				$list[$key]->section = $item->cattitle." - ".$item->jflname;
				$list[$key]->href = ContentHelperRoute::getArticleRoute($item->slug, $item->catslug);
			}
		}
		$rows[] = $list;
	}


	// search archived content
	if ( $sArchived && $limit > 0 )
	{
		$searchArchived = JText::_( 'Archived' );

		$query = 'SELECT a.title AS title, a.id as conti, b.id as catid,'
		. ' a.created AS created,'
		. ' a.introtext AS text,'
		. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'
		. ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(":", b.id, b.alias) ELSE b.id END as catslug,'
		. ' b.title as cattitle,'
		. ' "2" AS browsernav,'
		. ' l.lang_code as jflang, l.title as jflname'
		. ' FROM #__content AS a'
		. ' INNER JOIN #__categories AS b ON b.id=a.catid AND b.access IN ('. $groups .')'
		. "\n LEFT JOIN #__falang_content as fc ON reference_id = a.id"
        . "\n LEFT JOIN #__languages as l ON fc.language_id = l.lang_id"
		. ' WHERE ( '.$where.' )'
		. ' AND a.state = 2'
		. ' AND b.published = 1'
        . ' AND a.access IN ('.$groups.') '
        . ' AND b.access IN ('.$groups.') '
		. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
		. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
		. "\n AND fc.reference_table = 'content'"
		. ( $activeLang ? "\n AND l.lang_code = '$lang'" : '')
		. ' ORDER BY '. $order
		;


		$db->setQuery( $query, 0, $limit );
		$list3 = $db->loadObjectList();

		if(isset($list3))
		{
			foreach($list3 as $key => $item)
			{
				$list3[$key]->section = $item->sectitle."/".$item->cattitle." - ".$item->jflname;
				$list3[$key]->href = ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid);
			}
		}

		$rows[] = $list3;
	}

	$results = array();
	if(count($rows))
	{
		foreach($rows as $row)
		{
			$results = array_merge($results, (array) $row);
		}
	}

	return $results;
    }

	function searchDefaultLanguage($text, $phrase='', $ordering='', $areas=null)
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$tag = JFactory::getLanguage()->getTag();


		require_once JPATH_SITE.'/components/com_content/helpers/route.php';
		require_once JPATH_SITE.'/administrator/components/com_search/helpers/search.php';

		$searchText = $text;
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$sContent		= $this->params->get('search_content',		1);
		$sArchived		= $this->params->get('search_archived',		1);
		$limit			= $this->params->def('search_limit',		50);

		$nullDate		= $db->getNullDate();
		$date = JFactory::getDate();
		$now = $date->toSql();

		$text = trim($text);
		if ($text == '') {
			return array();
		}

		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $db->Quote('%'.$db->escape($text, true).'%', false);
				$wheres2	= array();
				$wheres2[]	= 'a.title LIKE '.$text;
				$wheres2[]	= 'a.introtext LIKE '.$text;
				$wheres2[]	= 'a.fulltext LIKE '.$text;
				$wheres2[]	= 'a.metakey LIKE '.$text;
				$wheres2[]	= 'a.metadesc LIKE '.$text;
				$where		= '(' . implode(') OR (', $wheres2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word) {
					$word		= $db->Quote('%'.$db->escape($word, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.title LIKE '.$word;
					$wheres2[]	= 'a.introtext LIKE '.$word;
					$wheres2[]	= 'a.fulltext LIKE '.$word;
					$wheres2[]	= 'a.metakey LIKE '.$word;
					$wheres2[]	= 'a.metadesc LIKE '.$word;
					$wheres[]	= implode(' OR ', $wheres2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		$morder = '';
		switch ($ordering) {
			case 'oldest':
				$order = 'a.created ASC';
				break;

			case 'popular':
				$order = 'a.hits DESC';
				break;

			case 'alpha':
				$order = 'a.title ASC';
				break;

			case 'category':
				$order = 'c.title ASC, a.title ASC';
				$morder = 'a.title ASC';
				break;

			case 'newest':
			default:
				$order = 'a.created DESC';
				break;
		}

		$rows = array();
		$query	= $db->getQuery(true);

		// search articles
		if ($sContent && $limit > 0)
		{
			$query->clear();
			$query->select('a.title AS title, a.metadesc, a.metakey, a.created AS created, '
						.'CONCAT(a.introtext, a.fulltext) AS text, c.title AS section, '
						.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, '
						.'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, '
						.'"2" AS browsernav');
			$query->from('#__content AS a');
			$query->innerJoin('#__categories AS c ON c.id=a.catid');
			$query->where('('. $where .')' . 'AND a.state=1 AND c.published = 1 AND a.access IN ('.$groups.') '
						.'AND c.access IN ('.$groups.') '
						.'AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).') '
						.'AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')' );
			$query->group('a.id');
			$query->order($order);

			// Filter by language
			if ($app->isSite() && JLanguageMultilang::isEnabled() ) {
				$query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
			}

			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);

			if (isset($list))
			{
				foreach($list as $key => $item)
				{
					$list[$key]->href = ContentHelperRoute::getArticleRoute($item->slug, $item->catslug);
				}
			}
			$rows[] = $list;
		}

		// search archived content
		if ($sArchived && $limit > 0)
		{
			$searchArchived = JText::_('JARCHIVED');

			$query->clear();
			$query->select('a.title AS title, a.metadesc, a.metakey, a.created AS created, '
						.'CONCAT(a.introtext, a.fulltext) AS text, '
						.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, '
						.'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, '
						.'CONCAT_WS("/", c.title) AS section, "2" AS browsernav' );
			$query->from('#__content AS a');
			$query->innerJoin('#__categories AS c ON c.id=a.catid AND c.access IN ('. $groups .')');
			$query->where('('. $where .') AND a.state = 2 AND c.published = 1 AND a.access IN ('. $groups
				.') AND c.access IN ('. $groups .') '
				.'AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).') '
				.'AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')' );
			$query->order($order);


			// Filter by language
            $multilang = false;
            if (FALANG_J25) {
                if ($app->getLanguageFilter()){$multilang=true;}
            } else {
                if (JLanguageMultilang::isEnabled()){$multilang=true;}
            }

			if ($app->isSite() && $multilang) {
				$query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
			}

			$db->setQuery($query, 0, $limit);
			$list3 = $db->loadObjectList();

			// find an itemid for archived to use if there isn't another one
            $item	= $app->getMenu()->getItems('link', 'index.php?option=com_content&view=archive', true);
            //sbou change isset  to !empty
			$itemid = !empty($item) ? '&Itemid='.$item->id : '';

			if (isset($list3))
			{
				foreach($list3 as $key => $item)
				{
					$date = JFactory::getDate($item->created);

					$created_month	= $date->format("n");
					$created_year	= $date->format("Y");

					$list3[$key]->href	= JRoute::_('index.php?option=com_content&view=archive&year='.$created_year.'&month='.$created_month.$itemid);
				}
			}

			$rows[] = $list3;
		}

		return $rows;
	}
}
