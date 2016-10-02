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

class PlgSystemCacheCleanerHelperJotCache extends PlgSystemCacheCleanerHelperCache
{
	public function purge()
	{
		jimport('joomla.filesystem.file');
		$file = JPATH_ADMINISTRATOR . '/components/com_jotcache/models/main.php';

		if (!JFile::exists($file))
		{
			return;
		}

		require_once $file;
		require_once __DIR__ . '/jotcachemodel.php';

		$model = new JotCacheMainModelMain;
		$model->deleteall();
	}
}
