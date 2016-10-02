<?php
/**
 * @package         Cache Cleaner
 * @version         5.0.0PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/cache.php';

class PlgSystemCacheCleanerHelperTables extends PlgSystemCacheCleanerHelperCache
{
	public function purge($tables = array())
	{
		if (empty($tables))
		{
			return;
		}

		if (!is_array($tables))
		{
			$tables = explode(',', str_replace("\n", ',', $tables));
		}

		foreach ($tables as $table)
		{
			$this->emptyTable($table);
		}
	}
}
