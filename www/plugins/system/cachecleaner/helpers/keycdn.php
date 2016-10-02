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

class PlgSystemCacheCleanerHelperKeyCDN extends PlgSystemCacheCleanerHelperCache
{
	public function purge()
	{
		if (empty($this->params->keycdn_authentication_key))
		{
			$this->setError(JText::sprintf('CC_ERROR_CDN_NO_AUTHENTICATIONION_KEY', JText::_('CC_KEYCDN')));

			return;
		}

		if (empty($this->params->keycdn_zones))
		{
			$this->setError(JText::sprintf('CC_ERROR_CDN_NO_ZONES', JText::_('CC_KEYCDN')));

			return;
		}

		$api = $this->getAPI();
		if (!$api || is_string($api))
		{
			$error = JText::sprintf('CC_ERROR_CDN_COULD_NOT_INITIATE_API', JText::_('CC_KEYCDN'));
			if (is_string($api))
			{
				$error .= '<br>' . $api;
			}

			$this->setError($error);

			return;
		}

		$zones = explode(',', $this->params->keycdn_zones);
		foreach ($zones as $zone)
		{
			$api_call = json_decode($api->get('zones/purge/' . $zone . '.json'));
			if (is_null($api_call))
			{
				$api_call = new stdClass;
			}

			if (!isset($api_call->status)
				|| $api_call->status != 'success'
			)
			{
				$this->setError(JText::sprintf('CC_ERROR_CDN_COULD_NOT_PURGE_ZONE', JText::_('CC_KEYCDN'), $zone));

				return false;
			}
		}
	}

	public function getAPI()
	{
		require_once __DIR__ . '/../api/KeyCDN.php';

		return new KeyCDN(trim($this->params->keycdn_authentication_key));
	}
}
