<?php
/*
 * @package Sj K2 Ajax Tabs
 * @version 3.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2015 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;

JHtml::stylesheet('modules/' . $module->module . '/assets/css/style.css');
if (!defined ('JQUERY_UI'))
{
    JHtml::stylesheet('modules/' . $module->module . '/assets/css/jquery-ui.css');
    JHtml::script('modules/' . $module->module . '/assets/js/jquery-ui.js');
    define( 'JQUERY_UI', 1 );
}
$uniqueid = 'sjk2filter_' . rand().time();
$cls_id = rand().'-'.time();
$options = $params->toObject();

$itemid             = $params->get ('itemid');
$show_keyword       = $params->get ('show_keyword');
$show_categories    = $params->get ('show_categories');
$show_date      = $params->get ('show_date');
// check category loaded
if ($params->get('pretext', '') != ''): ?>
    <div class="row">
    <div class="pre-text col-sm-3"><?php echo $params->get('pretext'); ?></div>
    <?php
endif; ?>
<!--[if lt IE 9]>
<div class="sj-k2-filter msie lt-ie9" id="<?php echo $uniqueid; ?>"><![endif]-->
<!--[if IE 9]>
<div class="sj-k2-filter msie" id="<?php echo $uniqueid; ?>"><![endif]-->
<!--[if gt IE 9]><!-->
<div class="sj-k2-filter <?php if ($params->get('pretext', '') != ''){ echo "col-sm-9"; } ?>" id="<?php echo $uniqueid; ?>"><!--<![endif]-->
    <form action="<?php echo $action; ?>" method="get" class="form-inline">
        <div class="form-group">
            <?php 
                if(isset($listExtra) && $listExtra != "" && count($listExtra)>0) // Get All Attribute
                {
                    foreach($listExtra as $item => $values)
                    {
                        $item = explode("_",$item);
                        include ("default_extrafield.php"); 
                    }
                }
            ?>
            
            <?php if($show_categories == 1) { ?>
            <select id="categories" class="input-filter input-cat" name="categories">
                <option selected value="*"><?php echo JText::_('SJ_FILTER_ALL_CATEGORIES'); ?></option>
                <?php foreach($list as $item):?>
                    <option value="<?php echo $item->id; ?>" <?php if($item->id == JRequest::getVar('categories')) echo 'selected="selected"'; ?>><?php echo $item->name;?></option>
                <?php endforeach;?>
            </select>
            <?php } if($show_date == 1) { ?>
            <input class="form-control input-filter input-date" value="<?php echo JRequest::getVar('created-from');?>" id="created-from-<?php echo $cls_id;?>" name="created-from" Placeholder="Date From" type="text">
            <input class="form-control input-filter input-date" value="<?php echo JRequest::getVar('created-to');?>" id="created-to-<?php echo $cls_id;?>" name="created-to" Placeholder="Date To" type="text">
            <?php } ?>

            <?php if($show_keyword == 1) { ?>
            <input class="form-control input-filter" value="<?php echo JRequest::getVar('searchword');?>" id="searchword" name="searchword" Placeholder="Keywords" type="text">
            <?php } ?> 

            <input class="btn btn-primary  btn-submit " type="submit" value="<?php echo JText::_('K2_FILTER_SUBMIT');?>" />
            <!--<button class="btn btn-primary" type="submit"><?php /*echo JText::_('K2_FILTER_SUBMIT');*/?></button>-->
        </div>
        <input type="hidden" name="moduleId" value="<?php echo $module->id;?>" />
        <input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />

    </form>

</div>
<?php
if ($params->get('posttext', '') != ''): ?>
    <div class="post-text"><?php echo $params->get('posttext'); ?></div>
    <?php
endif; ?>
<?php if ($params->get('pretext', '') != ''): ?>
    </div>
<?php endif; ?>
<script type="text/javascript">
    jQuery(function($) {
        $( "#created-from-<?php echo $cls_id;?>, #created-to-<?php echo $cls_id;?>" ).datepicker({
            dateFormat:"yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            buttonImageOnly: false,
        });
		
    });
</script>