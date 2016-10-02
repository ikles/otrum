<?php
/**
 * @package SJ Listing Tabs For K2
 * @version 1.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2016 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
$small_image_config = array(
    'type' => $params->get('imgcfg_type'),
    'width' => $params->get('imgcfg_width'),
    'height' => $params->get('imgcfg_height'),
    'quality' => 90,
    'function' => ($params->get('imgcfg_function') == 'none') ? null : 'resize',
    'function_mode' => ($params->get('imgcfg_function') == 'none') ? null : substr($params->get('imgcfg_function'), 7),
    'transparency' => $params->get('imgcfg_transparency', 1) ? true : false,
    'background' => $params->get('imgcfg_background'));
//	$tag_id = 'sj_listing_tabs_'.$module->id;
if (!empty($list)) {
    $app = JFactory::getApplication();
    $k = $app->input->getInt('ajax_reslisting_start', 0);
	$options = $params->toObject();
	$i = 0;
	$count = count($list);
    foreach ($list as $item) {
        $i++;$k++; ?>
		<?php if ($params->get('type_show') == 'loadmore'){ ?>
        <div class="ltabs-item new-ltabs-item">
		<?php }else{?>
		<?php if($params->get('type_show') == 'slider' && ($i % $params->get('nb_rows') == 1 || $params->get('nb_rows') == 1)) { ?>
			<div class="ltabs-item ">
        <?php }?>
		<?php }?>
            <div class="item-inner">
               <?php $img = K2ListingTabsHelper::getK2Image($item, $params);
					if ($img) {
				?>
				<div class="item-image">
					<?php echo K2ListingTabsHelper::imageTag($img,$small_image_config); ?>
				</div>
					<?php } ?>
				<?php if ($options->item_title_display == 1 || $options->item_desc_display == 1 || ($item->tags != '') || $options->item_readmore_display == 1) { ?>
					<div class="item-info">
						<?php if ($options->item_title_display == 1) { ?>
							<div class="item-title">
								<a href="<?php echo $item->link; ?>"
								   title="<?php echo $item->title ?>" <?php echo K2ListingTabsHelper::parseTarget($params->get('item_link_target')); ?>>
									<?php echo K2ListingTabsHelper::truncate($item->title, $params->get('item_title_max_characs', 25)); ?>
								</a>
							</div>
						<?php } ?>
						<?php if (($options->item_desc_display == 1 && !empty($item->displayIntrotext)) || ($item->tags != '') || $options->item_readmore_display == 1 || isset($item->created)) { ?>
							<div class="item-content">
								<?php if ($options->item_desc_display == 1) { ?>
									<div class="item-description">
										<?php echo $item->displayIntrotext; ?>
									</div>
								<?php } ?>

								<?php if(isset($item->created)){ ?>
                                    <span class="item-date">
										<?php echo JText::_('TEMPLATE_STARTING_FROM'); ?> <?php echo JHTML::_('date', $item->created, JText::_('TEMPLATE_DATE_FORMAT_LC4')); ?>
									</span>
                                <?php } ?>

								<?php if ($item->tags != '') { ?>
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
								<?php if ($options->item_readmore_display == 1) { ?>
									<div class="item-readmore">
										<a href="<?php echo $item->link; ?>"
										   title="<?php echo $item->title ?>" <?php echo K2ListingTabsHelper::parseTarget($params->get('item_link_target')); ?>>
											<?php echo $options->item_readmore_text; ?>
										</a>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
            </div>
        <?php if($params->get('type_show') == 'slider' && ($i % $params->get('nb_rows') == 0 || $i == $count)) { ?>
        </div>
    <?php }?>
	<?php if ($params->get('type_show') == 'loadmore'){ ?>
		</div>
	<?php } ?>

        <?php 
		if ($params->get('type_show') == 'loadmore'){
		$clear = 'clr1';
        if ($k % 2 == 0) $clear .= ' clr2';
        if ($k % 3 == 0) $clear .= ' clr3';
        if ($k % 4 == 0) $clear .= ' clr4';
        if ($k % 5 == 0) $clear .= ' clr5';
        if ($k % 6 == 0) $clear .= ' clr6';
        ?>
        <div class="<?php echo $clear; ?>"></div>
		<?php }?>
    <?php
    } ?>
<?php
}else{
	echo 'Has no content to show!.';
}?>

