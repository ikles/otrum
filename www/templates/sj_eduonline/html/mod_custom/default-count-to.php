<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>


<div class="<?php echo $moduleclass_sfx ?>">
    <?php echo $module->content;?>
</div>
<script src="templates/sj_flooring/html/mod_custom/waypoints.min.js" type="text/javascript"></script>
<script src="templates/sj_flooring/html/mod_custom/jquery.counterup.js" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($) {
    $('.counter').counterUp({
        delay: 100,
        time: 2000
    });
});
//]]>
</script>
