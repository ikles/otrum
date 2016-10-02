<?php
/**
 * @package   	JCE
 * @copyright 	Copyright (c) 2009-2015 Ryan Demmer. All rights reserved.
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('_JEXEC') or die('RESTRICTED');
?>
<div class="itemtype">
    <div>
        <label for="itemtype"><?php echo WFText::_('WF_LABEL_TYPE'); ?></label>
        <select id="itemtype">
            <option value="">-- <?php echo WFText::_('WF_OPTION_SELECT_TYPE'); ?> --</option>
        </select>
        <span class="loader"></span>
    </div>
    <div class="itemtype-options">
        <input type="radio" id="itemtype-replace" checked="checked" name="itemtype-option" />
        <label for="itemtype-replace"><?php echo WFText::_('WF_LABEL_REPLACE'); ?></label>
        <input type="radio" id="itemtype-new" name="itemtype-option" />
        <label for="itemtype-new"><?php echo WFText::_('WF_LABEL_NEW'); ?></label>

    </div>   
</div>
<div class="itemprop">
    <div>
        <label for="itemprop"><?php echo WFText::_('WF_LABEL_PROPERTY'); ?></label><select id="itemprop"></select>
    </div>
    <div>
        <label for="itemid"><?php echo WFText::_('WF_LABEL_ID'); ?></label><input type="text" value="" id="itemid" />
    </div>
</div>
