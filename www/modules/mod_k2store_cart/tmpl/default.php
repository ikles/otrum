<?php
/*------------------------------------------------------------------------
# mod_k2store_cart - K2Store Cart
# ------------------------------------------------------------------------
# author    Ramesh Elamathi - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://k2store.org
# Technical Support:  Forum - http://k2store.org/forum/index.html
-------------------------------------------------------------------------*/


// no direct access
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
$ajax = $app->setUserState('mod_k2store_mini_cart.isAjax', 0);
$hide = false;
if($params->get('check_empty',0) && $list['product_count'] < 1) {
$hide = true;
}
?>
		<div class="k2store_cart_module_<?php echo $module->id; ?>">

		<?php if(!$hide): ?>
			<?php if($list['product_count'] > 0): ?>
				<?php echo JText::sprintf('K2STORE_CART_TOTAL', $list['product_count'], K2StorePrices::number($list['total'])); ?>
			<?php else : ?>
					<?php echo JText::_('K2STORE_NO_ITEMS_IN_CART'); ?>
			<?php endif; ?>

			<div class="k2store-minicart-button">
			<?php if($link_type =='link'):?>
			<a class="link" href="<?php echo JRoute::_('index.php?option=com_k2store&view=mycart');?>">
			<?php echo JText::_('K2STORE_VIEW_CART');?>
			</a>
			<?php else: ?>
			<input type="button" class="btn btn-primary button" onClick="window.location='<?php echo JRoute::_('index.php?option=com_k2store&view=mycart');?>'"
			value="<?php echo JText::_('K2STORE_VIEW_CART');?>"
			/>
			<?php endif;?>
			</div>
		<?php endif; ?>
		</div>
