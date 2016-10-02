<?php
/*
 * ------------------------------------------------------------------------
 * Copyright (C) 2009 - 2013 The YouTech JSC. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: The YouTech JSC
 * Websites: http://www.smartaddons.com - http://www.cmsportal.net
 * ------------------------------------------------------------------------
*/
// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );
global $is_placehold;
global $placehold_size;

// Array param for cookie
$placehold_size = array (
    'listing' => '1170x424/ccc/ccc',
    'article' => '1170x424/ccc/ccc',
    'related_items'=>'555x390/ccc/ccc',
    'slideshow' => '1150x450/ccc/ccc',
    'latest_news' => '270x270/ccc/ccc',
    'popular' => '70x70/ccc/ccc',
    'featured_posts' => '230x180/ccc/ccc',
    'hot_stories' => '270x250/ccc/ccc',
    'video_box' => '270x150/ccc/ccc',
    'style1' => '70x70/ccc/ccc',
    'style2' => '130x80/ccc/ccc',
    'style3' => '170x120/ccc/ccc',
    'style4' => '270x180/ccc/ccc',
    'style5' => '270x180/ccc/ccc',
    'style6' => '70x70/ccc/ccc',
    'hightlight' => '428x300/ccc/ccc',
    'style_mega' => '270x130/ccc/ccc',
    'k2user' => '83x83/ccc/ccc',
    'k2cat' => '570x300/ccc/ccc',
    'k2cat2' => '170x120/ccc/ccc',
    'k2_item' => '1170x681/ccc/ccc',
    'k2_event' => '270x200/ccc/ccc',
    'k2_event_item' => '470x348/ccc/ccc',
	'k2_courses_itemrelated' => '270x200/ccc/ccc',
	'k2_courses' => '270x204/ccc/ccc',

);

$is_placehold = 1;

if (!function_exists ('yt_placehold') ) {
    function yt_placehold ($size = '100x100',$icon='0xe942', $alt = '', $title = '' ) {
        return '<img src="http://placehold.it/'.$size.'" alt = "'. $alt .'" title = "'. $title .'"/>';
    }
    function limit_text($text, $limit) {
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]);
      }
      return $text;
    }
}
?>
