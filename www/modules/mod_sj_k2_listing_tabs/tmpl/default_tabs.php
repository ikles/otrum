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
ImageHelper::setDefault($params);
$cat_image_config = array(
    'type' => $params->get('imgcfgcat_type'),
    'width' => $params->get('imgcfgcat_width'),
    'height' => $params->get('imgcfgcat_height'),
    'quality' => 90,
    'function' => ($params->get('imgcfgcat_function') == 'none') ? null : 'resize',
    'function_mode' => ($params->get('imgcfgcat_function') == 'none') ? null : substr($params->get('imgcfgcat_function'), 7),
    'transparency' => $params->get('imgcfgcat_transparency', 1) ? true : false,
    'background' => $params->get('imgcfgcat_background'));	 
?>
<div class="ltabs-tabs-wrap">
    <span class='ltabs-tab-selected'></span>
    <span class='ltabs-tab-arrow'>&#9660;</span>
    <ul class="ltabs-tabs cf">
        <?php 
        foreach ($tabs as $index => $tab) {
			$index = $tab->id;
            if ($params->get('filter_type') == "filter_categories") {?>
                <li class="ltabs-tab <?php echo isset($tab->sel) ? '  tab-sel tab-loaded' : ''; ?> <?php echo ($index == ('*')) ? ' tab-all' : ''; ?>"
                    data-category-id="<?php echo $index; ?>"
                    data-active-content=".items-category-<?php echo ($index == "*") ? 'all' : $index; ?>"
                    data-field_order="<?php echo $params->get('source_order'); ?>"
                    >
                    <?php
                    if ($params->get('tab_icon_display', 1) == 1) {
                        if ($index != "*") {
                            $item_img = K2ListingTabsHelper::getK2CImage($tab, $params, 'imgcfgcat');;
                            if ($item_img) {
                                ?>
                                <div class="ltabs-tab-img">
									<?php echo K2ListingTabsHelper::imageTag($item_img,$cat_image_config); ?>
                                    
                                </div>
                            <?php
                            }
                        } else {
                            $item_img = 'modules/' . $module->module . '/assets/images/icon-catall.png';
                            ?>
                            <div class="ltabs-tab-img">
                                <img class="cat-all" src="<?php echo $item_img; ?>"
                                     title="<?php echo 'All'; ?>" alt="<?php echo 'All'; ?>"
                                     style="width: 32px; height:74px;"/>
                            </div>
                        <?php
                        }
                        ?>

                    <?php } ?>
                    <span
                        class="ltabs-tab-label"><?php echo K2ListingTabsHelper::truncate($tab->name, $params->get('tab_max_characters')); ?>
					</span>
                </li>
            <?php
            } else {
                ?>
                <li class="ltabs-tab <?php echo isset($tab->sel) ? '  tab-sel tab-loaded' : ''; ?> <?php echo ($index == ('*')) ? ' tab-all' : ''; ?>"
                    data-category-id="<?php echo $index; ?>"
                    data-field_order="<?php echo $index; ?>"
                    data-active-content=".items-category-<?php echo $index; ?>">
					<span class="ltabs-tab-label"><?php echo K2ListingTabsHelper::truncate($tab->name, $params->get('tab_max_characters')); ?>
			</span>
                </li>
            <?php
            }
        } ?>
    </ul>
</div>
