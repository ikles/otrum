<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="onelineblockoptions" id="executed_actions_block">
	<span class="acyblocktitle"><?php echo JText::_('ACTIONS'); ?></span>
	<?php echo JText::_('ACY_DISTRIBUTION_DELETE_MESSAGE'); ?>
	<div id="actionsarea"></div>
	<button class="acymailing_button" onclick="addAction();return false;"><?php echo JText::_('ADD_ACTION'); ?></button>
</div>
