<?php
/**
 * @package SJ Mega News for K2
 * @version 3.1.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
?>
<?php $rest = 0;
foreach ($_items as $item) {
	$rest++; ?>
	<div class="meganew-item">
		<div class="meganew-item-inner">
			<?php if ($params->get('item_title_display') == 1) { ?>
				<div class="item-title">
					<a href="<?php echo $item->link; ?>"
					   title="<?php echo $item->name ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?>  >
						<?php echo K2MegaNewsHelper::truncate($item->name, $params->get('item_title_max_characs')); ?>
					</a>
				</div>
			<?php
			}
			$img = K2MegaNewsHelper::getK2Image($item, $params);
			if ($img) {
				?>
				<div class="item-image">
					<a href="<?php echo $item->link; ?>"
					   title="<?php echo $item->name ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?>  >
						<?php echo K2MegaNewsHelper::imageTag($img); ?>
					</a>
				</div>
			<?php } ?>

			<?php if ($options->item_desc_display == 1 && $item->displayIntrotext != '') { ?>
				<div class="item-description">
					<?php echo $item->displayIntrotext; ?>
				</div>
			<?php } ?>

			<?php if ($params->get('itemCommentsCounter') == 1) { ?>
				<div class="item-comment">
					<?php
					if ($item->numOfComments == 1) {
						echo $item->numOfComments . '&nbsp;' . 'comment';
					} else {
						echo $item->numOfComments . '&nbsp;' . 'comments';
					}
					?>
				</div>
			<?php } ?>

			<?php if ($item->tags != '' && !empty($item->tags)) { ?>
				<div class="item-tags">
					<div class="tags">
						<?php $hd = -1;
						foreach ($item->tags as $tag): $hd++; ?>
							<span class="tag-<?php echo $tag->id . ' tag-list' . $hd; ?>">
								<a class="label label-info" href="<?php echo $tag->link; ?>"
								   title="<?php echo $tag->name; ?>" target="_blank">
									<?php echo $tag->name; ?>
								</a>
							</span>
						<?php endforeach; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ($params->get('item_readmore_display') == 1) { ?>
				<div class="item-readmore">
					<a href="<?php echo $item->link; ?>"
					   title="<?php echo $item->name; ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?> >
						<?php echo $params->get('item_readmore_text'); ?>
					</a>
				</div>
			<?php } ?>

		</div>
	</div>
	<?php
	$cleart = 'clrt1';
	if ($rest % 2 == 0) $cleart .= ' clrt2';
	if ($rest % 3 == 0) $cleart .= ' clrt3';
	if ($rest % 4 == 0) $cleart .= ' clrt4';
	if ($rest % 5 == 0) $cleart .= ' clrt5';
	if ($rest % 6 == 0) $cleart .= ' clrt6';
	?>
	<div class="<?php echo $cleart; ?>"></div>
<?php } ?>
<?php
if ((int)$params->get('item_viewall_display', 1)) {
	?>
	<div class="meganew-viewall">
		<a href="<?php echo $items->link; ?>"
		   title="<?php echo $items->name; ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?> >
			<?php echo $params->get('item_viewall_text', 'View') . ' ' . $items->name; ?>
		</a>
	</div>
<?php } ?>