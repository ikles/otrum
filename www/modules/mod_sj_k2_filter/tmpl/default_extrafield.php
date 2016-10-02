<select class="input-filter input-extrafield" name="extra_<?php echo $item[0];?>">
	<option selected value=""><?php echo $item[1];?></option>
	<?php foreach($values as $value):?>
		<option value="<?php echo $value['value_id']; ?>" <?php echo (in_array($value['value_id'],explode(',',JRequest::getVar('extra_'.$item[0].''))) ? 'selected="selected"' : '') ?>><?php echo $value['value_name'];?></option>
	<?php endforeach;?>
</select>

