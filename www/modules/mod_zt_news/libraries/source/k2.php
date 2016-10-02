<?php

/**
 * ZT News
 *
 * @package     Joomla
 * @subpackage  Module
 * @version     2.0.0
 * @author      ZooTemplate
 * @email       support@zootemplate.com
 * @link        http://www.zootemplate.com
 * @copyright   Copyright (c) 2015 ZooTemplate
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');

if (!class_exists('ZtNewsSourceK2'))
{

// k2 route
    if (is_file(JPATH_SITE . '/components/com_k2/helpers/route.php'))
    {
        require_once(JPATH_SITE . '/components/com_k2/helpers/route.php');
    }

    /**
     * Joomla content source
     */
    class ZtNewsSourceK2 extends ZtNewsSourceAbstract
    {

        protected $_source = 'k2';
        protected $_table_items = '#__k2_items';
        protected $_table_categories = '#__k2_categories';

        /**
         * Build query to getItems
         * @return string
         */
        protected function _buildQuery()
        {
            $query = ' SELECT a.*, cc.name as cat_title,' .
                    ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,' .
                    ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug' .
                    ' FROM ' . $this->_table_items . ' AS a' .
                    ' INNER JOIN ' . $this->_table_categories . ' AS cc ON cc.id = a.catid' .
                    ' WHERE ' . $this->_buildWhere() . $this->_buildWhereExtend() .
                    ' AND cc.published = 1' .
                    ' ORDER BY ' . $this->_buildOrder() .
                    ' LIMIT ' . $this->_params->get('max_items', 10);
            return $query;
        }

        protected function _buildWhere()
        {
            return ' `a`.`published` = 1';
        }

        /**
         * Prepare item properties
         */
        protected function _prepareItem($item)
        {
            $item->link = JRoute::_(K2HelperRoute::getItemRoute($item->id, $item->catid));
            $item->introtext = JHtml::_('string.truncate', $item->introtext, $this->_params->get('intro_length', 200));
            $item->cat_link = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid . ':' . urlencode($item->catslug))));
            return $item;
        }

        /**
         * Prepare images for item
         */
        protected function _prepareItemImages($item)
        {
            $srcDir = JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'src';
            $originalImage = $srcDir . '/' . md5("Image" . $item->id);
            if (JFile::exists($originalImage))
            {
                $item->thumb = modZTNewsHelper::getThumbnailLink($originalImage, $this->_params->get('thumb_main_width'), $this->_params->get('thumb_main_height'));
                $item->subThumb = modZTNewsHelper::getThumbnailLink($originalImage, $this->_params->get('thumb_list_width'), $this->_params->get('thumb_list_height'));
            }
            return $item;
        }

        /**
         * Recursive to get all children categories of joomla article
         */
        protected function _getChildrenCategories($cids)
        {
            $db = JFactory::getDBO();
            $query = ' SELECT id '
                    . ' FROM #__k2_categories '
                    . ' WHERE parent IN ( ' . implode(',', $cids) . ' ) '
                    . ' AND published = 1 '
                    . ' AND parent != 0 '
                    . ' ORDER BY id '
            ;
            $db->setQuery($query);

            $rows = $db->loadColumn();
            $this->_categories = array_merge($this->_categories, $rows);
            if (count($rows) > 0)
            {
                $this->_getChildrenCategories($rows);
            }
            $this->_categories = array_unique($this->_categories);
            return $this->_categories;
        }

    }

}