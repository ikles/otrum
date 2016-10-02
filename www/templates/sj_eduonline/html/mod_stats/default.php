<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_stats
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<ul class="stats-module <?php echo $moduleclass_sfx ?>">
<?php foreach ($list as $item) : ?>
	<li><strong><?php echo $item->title;?>:&nbsp;</strong>
        <span><?php echo $item->data;?></span></li>
<?php endforeach; ?>
</ul>
