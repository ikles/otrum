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

$tag_id = 'sj_meganew_' . rand() . time();
$theme = $params->get('theme', 'theme1');
JHtml::stylesheet('modules/' . $module->module . '/assets/css/styles.css');
JHtml::stylesheet('modules/' . $module->module . '/assets/css/styles-responsive.css');
ImageHelper::setDefault($params);
$colums = ' meganew-rps01-' . $params->get('nbm-column1') . ' meganew-rps02-' . $params->get('nbm-column2') . ' meganew-rps03-' . $params->get('nbm-column3') . ' meganew-rps04-' . $params->get('nbm-column4');
$colums_theme3 = ' meganewt3-rps01-' . $params->get('nbi-column1') . ' meganewt3-rps02-' . $params->get('nbi-column2') . ' meganewt3-rps03-' . $params->get('nbi-column3') . ' meganewt3-rps04-' . $params->get('nbi-column4');
$colums_theme3 = ($theme == 'theme3') ? $colums_theme3 : '';
$options = $params->toObject();

$first_image_config = array(
	'type' => $params->get('imgcfgfirst_type'),
	'width' => $params->get('imgcfgfirst_width'),
	'height' => $params->get('imgcfgfirst_height'),
	'quality' => 90,
	'function' => ($params->get('imgcfgfirst_function') == 'none') ? null : 'resize',
	'function_mode' => ($params->get('imgcfgfirst_function') == 'none') ? null : substr($params->get('imgcfgfirst_function'), 7),
	'transparency' => $params->get('imgcfgfirst_transparency', 1) ? true : false,
	'background' => $params->get('imgcfgfirst_background'));

?>

<?php if ($params->get('pretext') != ''): ?>
	<div class="meganew-pretext">
		<?php echo JText::_($params->get('pretext')); ?>
	</div>
<?php
endif;
if (!empty($list)) {
	?>
	<!--[if lt IE 9]>
	<div id="<?php echo $tag_id; ?>" class="sj-meganew msie lt-ie9 "><![endif]-->
	<!--[if IE 9]>
	<div id="<?php echo $tag_id; ?>" class="sj-meganew msie  "><![endif]-->
	<!--[if gt IE 9]><!-->
	<div id="<?php echo $tag_id; ?>" class="sj-meganew "><!--<![endif]-->
		<div class="meganew-wrap <?php echo $colums; ?>">
			<?php $res = 0;
			foreach ($list as $items) {
				$res++;
				$_items = $items->child; ?>

				<div class="meganew-box">
					<div class="meganew-box-inner">
						<div class="meganew-category">
							<a href="<?php echo $items->link; ?>"
							   title="<?php echo $items->name; ?>" <?php echo K2MegaNewsHelper::parseTarget($params->get('link_target')); ?> >
								<?php echo $items->name; ?>
							</a>
						</div>
						<div class="meganew-items <?php echo $theme; ?> <?php echo $colums_theme3; ?>">
							<?php include JModuleHelper::getLayoutPath($module->module, $layout . '_' . $theme); ?>
						</div>
					</div>
				</div>
				<?php
				$clear = 'clr1';
				if ($res % 2 == 0) $clear .= ' clr2';
				if ($res % 3 == 0) $clear .= ' clr3';
				if ($res % 4 == 0) $clear .= ' clr4';
				if ($res % 5 == 0) $clear .= ' clr5';
				if ($res % 6 == 0) $clear .= ' clr6';
				?>
				<div class="<?php echo $clear; ?>"></div>
			<?php } ?>
		</div>
	</div>
<?php } else { ?>
	<div class="no-item"><?php echo JText::_('Has no content to show!'); ?></div>
<?php } ?>
<?php if ($params->get('posttext') != ''): ?>
	<div class="meganew-posttext">
		<?php echo JText::_($params->get('posttext')); ?>
	</div>
<?php endif; ?>
