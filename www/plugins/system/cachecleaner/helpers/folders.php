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

class PlgSystemCacheCleanerHelperFolders extends PlgSystemCacheCleanerHelperCache
{
	// Empty tmp folder
	public function purge_tmp()
	{
		$this->emptyFolder(JPATH_SITE . '/tmp');
	}

	// Empty custom folder
	public function purge_folders()
	{
		if (empty($this->params->clean_folders_selection))
		{
			return;
		}

		$folders = explode("\n", str_replace('\n', "\n", $this->params->clean_folders_selection));

		foreach ($folders as $folder)
		{
			if (!trim($folder))
			{
				continue;
			}

			$folder = rtrim(str_replace('\\', '/', trim($folder)), '/');
			$path   = str_replace('//', '/', JPATH_SITE . '/' . $folder);
			$this->emptyFolder($path);
		}
	}
}
