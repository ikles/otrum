<?php
$ajax = $app->setUserState('mod_k2store_mini_cart.isAjax', 0);
?>
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