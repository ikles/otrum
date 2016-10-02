<?php
/**
 * @package Sj Filter for VirtueMart
 * @version 3.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2015 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');

if (!class_exists('JFormFieldSjK2extrafield')) {

	class JFormFieldSjK2extrafield extends JFormFieldList
	{
		protected $extrafield = null;
		
		protected function getExtrafields()
		{
			$extrafield = array();
			$published = array(1);

			// Let's get the id for the current item, either category or content item.
			$jinput = JFactory::getApplication()->input;
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
					->select('a.*')
					->from('#__k2_extra_fields AS a');
			$query->where('type="radio" AND a.published IN (' . implode(',', $published) . ')');
			$query->order('a.name ASC');
			$db->setQuery($query);
			$row = $db->loadObject();
			try {
				$extrafield = $db->loadObjectList();
			} catch (RuntimeException $e) {
				JError::raiseWarning(500, $e->getMessage);
			}
			return $extrafield;
		}
		public function getOptions()
		{
			$options = parent::getOptions();
			$extrafield = $this->getExtrafields();
			if (count($extrafield)) {
				foreach ($extrafield as $key => $extra) {
					$value = $extra->id;
					$text = $extra->name;
					$options[] = JHtml::_('select.option', $value, $text);
				}
			}
			return $options;
		}

	}
}