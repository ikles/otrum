<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="mtopright" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <i class="fa fa-bars"></i>
      <i class="fa fa-times"></i>
  </button>
  <div class="dropdown-menu" aria-labelledby="mtopright">
      <?php echo $module->content;?>
  </div>
</div>

