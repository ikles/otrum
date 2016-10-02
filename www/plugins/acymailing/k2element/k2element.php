<?php
/**
 * @copyright    Copyright (C) 2009-2016 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgAcymailingK2element extends JPlugin{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'k2element');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_getPluginType(){

		$onePlugin = new stdClass();
		$onePlugin->name = 'K2 Content';
		$onePlugin->function = 'acymailingtagk2element_show';
		$onePlugin->help = 'plugin-k2element';

		return $onePlugin;
	}

	function acymailingtagk2element_show(){
		$config = acymailing_config();
		if($config->get('version') < '5.2.0'){
			acymailing_display('Please download and install the latest AcyMailing version otherwise this plugin will NOT work', 'error');
			return;
		}

		// Load language file
		$lang = JFactory::getLanguage();
		$lang->load('com_k2', JPATH_ADMINISTRATOR);

		$app = JFactory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.k2element';
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.id', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$pageInfo->filter_cat = $app->getUserStateFromRequest($paramBase.".filter_cat", 'filter_cat', '', 'int');
		$pageInfo->titlelink = $app->getUserStateFromRequest($paramBase.".titlelink", 'titlelink', 'link', 'string');
		$pageInfo->lang = $app->getUserStateFromRequest($paramBase.".lang", 'lang', '', 'string');
		$pageInfo->author = $app->getUserStateFromRequest($paramBase.".author", 'author', $this->params->get('default_author', '0'), 'string');
		$pageInfo->images = $app->getUserStateFromRequest($paramBase.".images", 'images', '1', 'string');
		$pageInfo->contenttype = $app->getUserStateFromRequest($paramBase.".contenttype", 'contenttype', 'intro', 'string');
		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->autotitlelink = $app->getUserStateFromRequest($paramBase.".autotitlelink", 'autotitlelink', 'link', 'string');
		$pageInfo->autoimages = $app->getUserStateFromRequest($paramBase.".autoimages", 'autoimages', '1', 'string');
		$pageInfo->automaxvalue = $app->getUserStateFromRequest($paramBase.".max", 'max', '20', 'int');
		$pageInfo->contentfilter = $app->getUserStateFromRequest($paramBase.".contentfilter", 'contentfilter', 'created', 'string');
		$pageInfo->contentorder = $app->getUserStateFromRequest($paramBase.".contentorder", 'contentorder', 'id', 'string');
		$pageInfo->autocontenttype = $app->getUserStateFromRequest($paramBase.".autocontenttype", 'autocontenttype', 'intro', 'string');
		$pageInfo->pict = $app->getUserStateFromRequest($paramBase.".pict", 'pict', $this->params->get('default_pict', 1), 'string');
		$pageInfo->pictheight = $app->getUserStateFromRequest($paramBase.".pictheight", 'pictheight', $this->params->get('maxheight', 150), 'string');
		$pageInfo->pictwidth = $app->getUserStateFromRequest($paramBase.".pictwidth", 'pictwidth', $this->params->get('maxwidth', 150), 'string');


		$db = JFactory::getDBO();

		$searchFields = array('a.id', 'a.alias', 'a.title', 'b.name', 'a.created_by');

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchFields)." LIKE $searchVal";
		}

		$my = JFactory::getUser();
		if($this->params->get('displayart', 'all') == 'onlypub'){
			$filters[] = "a.published = 1 AND a.`trash`=0";
		}elseif($this->params->get('displayart', 'all') == 'owned'){
			$filters[] = "a.created_by = ".intval($my->id)." AND a.`trash`=0 AND a.`published` = 1";
		}else{
			$filters[] = "a.published != -2 AND a.`trash`=0";
		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS a.*,b.name,b.username,c.name as catname,c.description as catdesc ';
		$query .= 'FROM `#__k2_items` as a';
		$query .= ' LEFT JOIN `#__users` as b ON a.created_by = b.id';
		$query .= ' LEFT JOIN `#__k2_categories` as c ON a.catid = c.id';
		if(!empty($pageInfo->filter_cat)) $filters[] = "a.catid = ".$pageInfo->filter_cat;

		if(!empty($filters)){
			$query .= ' WHERE ('.implode(') AND (', $filters).')';
		}

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$db->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $db->loadObjectList();
		if(!empty($rows[0]) && !isset($rows[0]->acy_created)){
			$db->setQuery('ALTER TABLE `#__k2_items` ADD `acy_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
			$db->query();
		}

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$type = JRequest::getString('type');

		?>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedContents = new Array();
			function applyContent(contentid, rowClass){
				var tmp = selectedContents.indexOf(contentid)
				if(tmp != -1){
					window.document.getElementById('content' + contentid).className = rowClass;
					delete selectedContents[tmp];
				}else{
					window.document.getElementById('content' + contentid).className = 'selectedrow';
					selectedContents.push(contentid);
				}
				updateTag();
			}

			function updateTag(){
				var tag = '';
				var otherinfo = '';
				for(var i = 0; i < document.adminForm.contenttype.length; i++){
					if(document.adminForm.contenttype[i].checked){
						selectedtype = document.adminForm.contenttype[i].value;
						otherinfo += '|type:' + document.adminForm.contenttype[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelink.length; i++){
					if(document.adminForm.titlelink[i].checked && document.adminForm.titlelink[i].value.length > 1){
						otherinfo += '|' + document.adminForm.titlelink[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.author.length; i++){
						if(document.adminForm.author[i].checked && document.adminForm.author[i].value.length > 1){
							otherinfo += '|' + document.adminForm.author[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pict.length; i++){
						if(document.adminForm.pict[i].checked){
							otherinfo += '|pict:' + document.adminForm.pict[i].value;
							if(document.adminForm.pict[i].value == 'resized'){
								document.getElementById('pictsize').style.display = '';
								if(document.adminForm.pictwidth.value) otherinfo += '|maxwidth:' + document.adminForm.pictwidth.value;
								if(document.adminForm.pictheight.value) otherinfo += '|maxheight:' + document.adminForm.pictheight.value;
							}else{
								document.getElementById('pictsize').style.display = 'none';
							}
						}
					}
				}

				if(window.document.getElementById('jflang') && window.document.getElementById('jflang').value != ''){
					otherinfo += '|lang:';
					otherinfo += window.document.getElementById('jflang').value;
				}

				for(var i in selectedContents){
					if(selectedContents[i] && !isNaN(i)){
						tag = tag + '{k2:' + selectedContents[i] + otherinfo + '}<br />';
					}
				}
				setTag(tag);
			}

			var selectedCat = new Array();
			function applyAuto(catid, rowClass){
				if(selectedCat[catid]){
					window.document.getElementById('cat' + catid).className = rowClass;
					delete selectedCat[catid];
				}else{
					window.document.getElementById('cat' + catid).className = 'selectedrow';
					selectedCat[catid] = 'selectedone';
				}

				updateAutoTag();
			}

			function updateAutoTag(){
				tag = '{autok2:';

				for(var icat in selectedCat){
					if(selectedCat[icat] == 'selectedone'){
						tag += icat + '-';
					}
				}

				if(document.adminForm.min_article && document.adminForm.min_article.value && document.adminForm.min_article.value != 0){
					tag += '|min:' + document.adminForm.min_article.value;
				}
				if(document.adminForm.max_article.value && document.adminForm.max_article.value != 0){
					tag += '|max:' + document.adminForm.max_article.value;
				}
				if(document.adminForm.contentorder.value){
					tag += document.adminForm.contentorder.value;
				}
				if(document.adminForm.contentfilter && document.adminForm.contentfilter.value){
					tag += document.adminForm.contentfilter.value;
				}
				if(document.adminForm.meta_article && document.adminForm.meta_article.value){
					tag += '|meta:' + document.adminForm.meta_article.value;
				}

				for(var i = 0; i < document.adminForm.contenttypeauto.length; i++){
					if(document.adminForm.contenttypeauto[i].checked){
						selectedtype = document.adminForm.contenttypeauto[i].value;
						tag += '|type:' + document.adminForm.contenttypeauto[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelinkauto.length; i++){
					if(document.adminForm.titlelinkauto[i].checked && document.adminForm.titlelinkauto[i].value.length > 1){
						tag += '|' + document.adminForm.titlelinkauto[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.authorauto.length; i++){
						if(document.adminForm.authorauto[i].checked && document.adminForm.authorauto[i].value.length > 1){
							tag += '|' + document.adminForm.authorauto[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pictauto.length; i++){
						if(document.adminForm.pictauto[i].checked){
							tag += '|pict:' + document.adminForm.pictauto[i].value;
							if(document.adminForm.pictauto[i].value == 'resized'){
								document.getElementById('pictsizeauto').style.display = '';
								if(document.adminForm.pictwidthauto.value) tag += '|maxwidth:' + document.adminForm.pictwidthauto.value;
								if(document.adminForm.pictheightauto.value) tag += '|maxheight:' + document.adminForm.pictheightauto.value;
							}else{
								document.getElementById('pictsizeauto').style.display = 'none';
							}
						}
					}
				}
				if(document.adminForm.cols && document.adminForm.cols.value > 1){
					tag += '|cols:' + document.adminForm.cols.value;
				}
				if(window.document.getElementById('jflangauto') && window.document.getElementById('jflangauto').value != ''){
					tag += '|lang:';
					tag += window.document.getElementById('jflangauto').value;
				}

				tag += '}';

				setTag(tag);
			}
			//-->
		</script>
		<?php

		$picts = array();
		$picts[] = JHTML::_('select.option', "1", JText::_('JOOMEXT_YES'));
		$pictureHelper = acymailing_get('helper.acypict');
		if($pictureHelper->available()) $picts[] = JHTML::_('select.option', "resized", JText::_('RESIZED'));
		$picts[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		//Content type
		$contenttype = array();
		$contenttype[] = JHTML::_('select.option', "title", JText::_('TITLE_ONLY'));
		$contenttype[] = JHTML::_('select.option', "intro", JText::_('INTRO_ONLY'));
		$contenttype[] = JHTML::_('select.option', "text", JText::_('FIELD_TEXT'));
		$contenttype[] = JHTML::_('select.option', "full", JText::_('FULL_TEXT'));

		//Title link params
		$titlelink = array();
		$titlelink[] = JHTML::_('select.option', "link", JText::_('JOOMEXT_YES'));
		$titlelink[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		//Author name
		$authorname = array();
		$authorname[] = JHTML::_('select.option', "author", JText::_('JOOMEXT_YES'));
		$authorname[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		$ordering = array();
		$ordering[] = JHTML::_('select.option', "|order:id,DESC", JText::_('ACY_ID'));
		$ordering[] = JHTML::_('select.option', "|order:ordering,ASC", JText::_('ACY_ORDERING'));
		$ordering[] = JHTML::_('select.option', "|order:created,DESC", JText::_('CREATED_DATE'));
		$ordering[] = JHTML::_('select.option', "|order:modified,DESC", JText::_('MODIFIED_DATE'));
		$ordering[] = JHTML::_('select.option', "|order:title,ASC", JText::_('FIELD_TITLE'));
		$ordering[] = JHTML::_('select.option', "|order:rand", JText::_('ACY_RANDOM'));

		$tabs = acymailing_get('helper.acytabs');
		echo $tabs->startPane('k2_tab');
		echo $tabs->startPanel(JText::_('TAG_ELEMENTS'), 'k2_listings');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td><?php echo JText::_('DISPLAY');?></td>
				<td colspan="2"><?php echo JHTML::_('acyselect.radiolist', $contenttype, 'contenttype', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->contenttype); ?></td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateTag();"';
					echo $jflanguages->display('lang', $pageInfo->lang); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $titlelink, 'titlelink', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->titlelink);?>
				</td>
				<td>
					<?php echo JText::_('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $authorname, 'author', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->author); ?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo JText::_('DISPLAY_PICTURES');?>
				</td>
				<td valign="top"><?php echo JHTML::_('acyselect.radiolist', $picts, 'pict', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->pict); ?>
					<span id="pictsize" <?php if($pageInfo->pict != 'resized') echo 'style="display:none;"'; ?>><br/><?php echo JText::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidth" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;"/>
					× <?php echo JText::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheight" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictheight; ?>" style="width:30px;"/>
				</span>
				</td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width="100%">
					<input placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $pageInfo->search;?>" class="text_area" onchange="document.adminForm.submit();"/>
					<button class="btn" onclick="this.form.submit();"><?php echo JText::_('JOOMEXT_GO'); ?></button>
					<button class="btn" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo JText::_('JOOMEXT_RESET'); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->_categories($pageInfo->filter_cat); ?>
				</td>
			</tr>
		</table>

		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<thead>
			<tr>
				<th class="title">
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('FIELD_TITLE'), 'a.title', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_AUTHOR'), 'b.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('K2_CATEGORY'), 'c.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_ORDERING'), 'a.ordering', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_ID'), 'a.id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pagination->getListFooter(); ?>
					<?php echo $pagination->getResultsCounter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for($i = 0, $a = count($rows); $i < $a; $i++){
				$row =& $rows[$i];
				?>
				<tr id="content<?php echo $row->id; ?>" class="<?php echo "row$k"; ?>" onclick="applyContent(<?php echo $row->id.",'row$k'"?>);" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						$text = '<b>'.JText::_('JOOMEXT_ALIAS').' : </b>'.$row->alias;
						echo acymailing_tooltip($text, $row->title, '', $row->title);
						?>
					</td>
					<td>
						<?php
						if(!empty($row->name)){
							$text = '<b>'.JText::_('ACY_NAME', true).' : </b>'.$row->name;
							$text .= '<br /><b>'.JText::_('ACY_USERNAME', true).' : </b>'.$row->username;
							$text .= '<br /><b>'.JText::_('ACY_ID', true).' : </b>'.$row->created_by;
							echo acymailing_tooltip($text, $row->name, '', $row->name);
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						echo acymailing_tooltip($row->catdesc, $row->catname, '', $row->catname);
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->ordering; ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>"/>
		<?php

		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_('TAG_CATEGORIES'), 'k2_auto');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td>
					<?php echo JText::_('DISPLAY');?>
				</td>
				<td colspan="2">
					<?php echo JHTML::_('acyselect.radiolist', $contenttype, 'contenttypeauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_type', 'intro'));?>
				</td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateAutoTag();"';
					$jflanguages->id = 'jflangauto';
					echo $jflanguages->display('langauto'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $titlelink, 'titlelinkauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_titlelink', 'link'));?>
				</td>
				<td>
					<?php echo JText::_('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $authorname, 'authorauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$this->params->get('default_author', '0')); ?>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo JText::_('DISPLAY_PICTURES'); ?></td>
				<td valign="top"><?php echo JHTML::_('acyselect.radiolist', $picts, 'pictauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_pict', '1')); ?>

					<span id="pictsizeauto" <?php if($this->params->get('default_pict', '1') != 'resized') echo 'style="display:none;"'; ?> ><br/><?php echo JText::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidthauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxwidth', '150');?>" style="width:30px;"/>
					× <?php echo JText::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheightauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxheight', '150');?>" style="width:30px;"/>
				</span>
				</td>
				<td valign="top"><?php echo JText::_('FIELD_COLUMNS'); ?></td>
				<td valign="top">
					<select name="cols" style="width:150px" onchange="updateAutoTag();" size="1">
						<?php for($o = 1; $o < 11; $o++) echo '<option value="'.$o.'">'.$o.'</option>'; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('MAX_ARTICLE'); ?>
				</td>
				<td>
					<input type="text" name="max_article" style="width:50px" value="20" onchange="updateAutoTag();"/>
				</td>
				<td>
					<?php echo JText::_('ACY_ORDER'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.genericlist', $ordering, 'contentorder', 'size="1" style="width:150px;" onchange="updateAutoTag();"', 'value', 'text', $pageInfo->contentorder); ?>
				</td>
			</tr>
			<?php if($type == 'autonews'){ ?>
				<tr>
					<td>
						<?php echo JText::_('MIN_ARTICLE'); ?>
					</td>
					<td>
						<input type="text" name="min_article" style="width:50px" value="1" onchange="updateAutoTag();"/>
					</td>
					<td>
						<?php echo JText::_('JOOMEXT_FILTER'); ?>
					</td>
					<td>
						<?php $filter = acymailing_get('type.contentfilter');
						$filter->onclick = "updateAutoTag();";
						echo $filter->display('contentfilter', '|filter:created'); ?>
					</td>
				</tr>
			<?php } ?>
		</table>
		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<?php $k = 0;
			foreach($this->catvalues as $oneCat){
				if(empty($oneCat->value)) continue;
				?>
				<tr id="cat<?php echo $oneCat->value ?>" class="<?php echo "row$k"; ?>" onclick="applyAuto(<?php echo $oneCat->value ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $oneCat->text;
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
	}

	private function _categories($filter_cat){
		//select all cats
		$db = JFactory::getDBO();
		$db->setQuery('SELECT id,alias,name,parent FROM `#__k2_categories` WHERE trash = 0 ORDER BY `ordering` ASC');
		$mosetCats = $db->loadObjectList();
		$this->cats = array();
		foreach($mosetCats as $oneCat){
			$this->cats[$oneCat->parent][] = $oneCat;
		}

		$this->catvalues = array();
		$this->catvalues[] = JHTML::_('select.option', 0, JText::_('ACY_ALL'));
		$this->_handleChildrens();
		return JHTML::_('select.genericlist', $this->catvalues, 'filter_cat', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$filter_cat);
	}

	private function _handleChildrens($parentId = 0, $level = 0){
		if(!empty($this->cats[$parentId])){
			foreach($this->cats[$parentId] as $cat){
				//$cat->name = str_repeat($this->separator,$level).$cat->cat_name;
				$this->catvalues[] = JHTML::_('select.option', $cat->id, str_repeat(" - - ", $level).$cat->name);
				$this->_handleChildrens($cat->id, $level + 1);
			}
		}
	}


	public function acymailing_replacetags(&$email, $send = true){
		$this->_replaceAuto($email);
		$this->_replaceOne($email);
	}

	private function _replaceOne(&$email){
		$match = '#{k2:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything...
		if(!$found) return;

		$this->newslanguage = new stdClass();
		if(!empty($email->language)){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT lang_id, lang_code FROM #__languages WHERE sef = '.$db->quote($email->language).' LIMIT 1');
			$this->newslanguage = $db->loadObject();
		}

		//we set the current catid so it can work with several Newsletters...
		$this->currentcatid = -1;
		//Set the read more link:
		$this->readmore = empty($email->template->readmore) ? JText::_('JOOMEXT_READ_MORE') : '<img src="'.ACYMAILING_LIVE.$email->template->readmore.'" alt="'.JText::_('JOOMEXT_READ_MORE', true).'" />';

		//Load the K2 model file
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'JSON.php');

		//We will need the mailer class as well
		$this->mailerHelper = acymailing_get('helper.mailer');

		$htmlreplace = array();
		$textreplace = array();
		$subjectreplace = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				//Don't need to process twice a tag we already have!
				if(isset($htmlreplace[$oneTag])) continue;

				$content = $this->_replaceContent($allresults, $i);
				$htmlreplace[$oneTag] = $content;
				$textreplace[$oneTag] = $this->mailerHelper->textVersion($content, true);
				$subjectreplace[$oneTag] = strip_tags($content);
			}
		}

		$email->body = str_replace(array_keys($htmlreplace), $htmlreplace, $email->body);
		$email->altbody = str_replace(array_keys($textreplace), $textreplace, $email->altbody);
		$email->subject = str_replace(array_keys($subjectreplace), $subjectreplace, $email->subject);
	}

	private function _replaceContent(&$results, $i){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		//1 : Transform the tag properly...
		$arguments = explode('|', strip_tags($results[1][$i]));
		$tag = new stdClass();
		$tag->id = (int)$arguments[0];
		$tag->itemid = (int)$this->params->get('itemid');
		$tag->wrap = (int)$this->params->get('wordwrap', 0);
		for($i = 1, $a = count($arguments); $i < $a; $i++){
			$args = explode(':', $arguments[$i]);
			$arg0 = trim($args[0]);
			if(empty($arg0)) continue;
			if(isset($args[1])){
				$tag->$arg0 = $args[1];
			}else{
				$tag->$arg0 = true;
			}
		}
		//We used to call it "images" but to make it consistent with the joomlacontent plugin, we rename it into pict.
		if(isset($tag->images) && !isset($tag->pict)) $tag->pict = $tag->images;

		//2 : Load the Joomla article... with the author, the section and the categories to create nice links
		$query = 'SELECT a.*,c.name as cattitle, c.alias as catalias, u.name as authorname FROM `#__k2_items` as a ';
		$query .= ' LEFT JOIN `#__k2_categories` AS c ON c.id = a.catid ';
		$query .= ' LEFT JOIN `#__users` AS u ON u.id = a.created_by ';
		$query .= 'WHERE a.id = '.intval($tag->id).' LIMIT 1';

		$db->setQuery($query);
		$article = $db->loadObject();

		$result = '';

		//In case of we could not load the article for any reason...
		if(empty($article)){
			if($app->isAdmin()) $app->enqueueMessage('The K2 content "'.$tag->id.'" could not be loaded', 'notice');
			return $result;
		}

		//We just loaded the article but we may need to translate it depending on tag->lang...
		if(empty($tag->lang) && !empty($this->newslanguage) && !empty($this->newslanguage->lang_code)) $tag->lang = $this->newslanguage->lang_code.','.$this->newslanguage->lang_id;

		$acypluginsHelper->translateItem($article, $tag, 'k2_items');
		if(!empty($tag->lang)){
			//We will load the correct article in the jf tables
			$langid = (int)substr($tag->lang, strpos($tag->lang, ',') + 1);
			if(!empty($langid) && (file_exists(JPATH_SITE.DS.'components'.DS.'com_joomfish'.DS.'helpers'.DS.'defines.php') || file_exists(JPATH_SITE.DS.'components'.DS.'com_falang'))){
				$db->setQuery("SELECT value FROM ".((ACYMAILING_J16 && file_exists(JPATH_SITE.DS.'components'.DS.'com_falang')) ? '`#__falang_content`' : '`#__jf_content`')." WHERE `published` = 1 AND `reference_table` = 'k2_categories' AND `language_id` = $langid AND `reference_field` = 'name' AND `reference_id` = ".$article->catid);
				$translation = $db->loadResult();
				if(!empty($translation)) $article->cattitle = $translation;
			}
		}

		$varFields = array();
		foreach($article as $fieldName => $oneField){
			$varFields['{'.$fieldName.'}'] = $oneField;
		}

		//When we load an artice, we may have a wrong content... we clean it.
		$acypluginsHelper->cleanHtml($article->introtext);
		$acypluginsHelper->cleanHtml($article->fulltext);

		require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
		$link = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias), $article->catid);
		if(!empty($tag->itemid)) $link .= (strpos($link, '?') ? '&' : '?').'Itemid='.$tag->itemid;
		if(!empty($tag->lang)){
			$lang = substr($tag->lang, 0, strpos($tag->lang, ACYMAILING_J16 ? '-' : ','));
			if(empty($lang)) $lang = $tag->lang;
			$link .= (strpos($link, '?') ? '&' : '?').'lang='.$lang;
		}
		if(!empty($tag->autologin)) $link .= (strpos($link, '?') ? '&' : '?').'user={usertag:username|urlencode}&passw={usertag:password|urlencode}';

		if(empty($tag->lang) && !empty($article->language) && $article->language != '*'){
			//We need to add the language code in the url if this article is only for one language file...
			//Let's load the right language code from the
			if(!isset($this->langcodes[$article->language])){
				//We load the right lang code from the language table.
				$db->setQuery('SELECT sef FROM #__languages WHERE lang_code = '.$db->Quote($article->language).' ORDER BY `published` DESC LIMIT 1');
				$this->langcodes[$article->language] = $db->loadResult();
				if(empty($this->langcodes[$article->language])) $this->langcodes[$article->language] = $article->language;
			}
			$link .= (strpos($link, '?') ? '&' : '?').'lang='.$this->langcodes[$article->language];
		}

		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		$json = new Services_JSON;
		$article->extra_fields = $json->decode($article->extra_fields);
		if(!empty($article->extra_fields)){
			$newFields = array();
			foreach($article->extra_fields as $oneField){
				$newFields[$oneField->id] = $oneField;
			}
			$article->extra_fields = $newFields;
		}

		//Add the title with a link or not on it.
		//If we add a link, we add in the same time a name="content-CONTENTID" so that we will be able to parse the content to create a nice summary
		$styleTitle = '';
		$styleTitleEnd = '';
		if($tag->type != "title"){
			$styleTitle = '<h2 class="acymailing_title">';
			$styleTitleEnd = '</h2>';
		}

		$resultTitle = '';

		if(empty($tag->notitle)){
			if(!empty($tag->link)){
				$resultTitle = '<a href="'.$link.'" ';
				if($tag->type != "title") $resultTitle .= 'style="text-decoration:none" name="k2content-'.$article->id.'" ';
				$resultTitle .= 'target="_blank" >'.$article->title.'</a>';
			}else{
				$resultTitle = $article->title;
			}
			$resultTitle = $styleTitle.$resultTitle.$styleTitleEnd;
		}

		//Add the author...
		if(!empty($tag->author)){
			$authorName = empty($article->created_by_alias) ? $article->authorname : $article->created_by_alias;
			if($tag->type == 'title') $result .= '<br />';
			$result .= '<span class="acymailing_authorname">'.$authorName.'</span><br />';
			$varFields['{author}'] = $authorName;
		}

		if(!empty($tag->created)){
			if($tag->type == 'title') $result .= '<br />';
			$dateFormat = empty($tag->dateformat) ? JText::_('DATE_FORMAT_LC2') : $tag->dateformat;
			$result .= '<span class="createddate">'.JHTML::_('date', $article->created, $dateFormat).'</span><br />';
			$varFields['{created}'] = JHTML::_('date', $article->created, $dateFormat);
		}

		//We add the intro text
		if($tag->type != "title"){

			if($tag->type == "intro"){
				$forceReadMore = false;
				$article->introtext = $acypluginsHelper->wrapText($article->introtext, $tag);
				if(!empty($acypluginsHelper->wraped)) $forceReadMore = true;
			}
			if(empty($article->fulltext) OR $tag->type != "text"){
				$result .= $article->introtext;
			}

			//We add the full text
			if($tag->type == "intro"){
				//We add the read more link but only if we have a fulltext after...
				if(empty($tag->noreadmore) && (!empty($article->fulltext) OR $forceReadMore)){
					$readMoreText = empty($tag->readmore) ? $this->readmore : $tag->readmore;
					$result .= '<a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$readMoreText.'</span></a>';
				}
			}elseif(!empty($article->fulltext)){
				if($tag->type != "text" && !empty($article->introtext) && !preg_match('#^<[div|p]#i', trim($article->fulltext))) $result .= '<br />';
				$result .= $article->fulltext;
			}

			//Display custom fields...
			if(!empty($tag->customfields) && !empty($article->extra_fields)){
				//Load the custom fields if we don't already have them...
				if(empty($this->customfields)){
					//Load the extra fields once
					$db->setQuery("SELECT * FROM #__k2_extra_fields WHERE `published` = 1 AND `type` NOT IN ('csv','labels') ORDER BY `ordering` ASC");
					$this->customfields = $db->loadObjectList();
				}

				$excluded = empty($tag->excludedcf) ? array() : explode(',', $tag->excludedcf);
				foreach($this->customfields as $oneField){
					//We set it... as we may not go into that loop if there is no value.
					$varFields['{cf:'.$oneField->name.'}'] = '';

					if(in_array($oneField->id, $excluded)) continue;

					if(!isset($article->extra_fields[$oneField->id]) || !isset($article->extra_fields[$oneField->id]->value)) continue;
					$disValue = '';
					if($oneField->type == 'date'){
						$time = $article->extra_fields[$oneField->id]->value;
						$dateFormat = (!ACYMAILING_J16) ? '%A, %d %B %Y' : 'l, d F Y';
						$disValue = JHTML::_('date', $time, $dateFormat, false);
					}elseif($oneField->type == 'link'){
						$disValue = '<a target="_blank" href="'.$article->extra_fields[$oneField->id]->value[1].'" >'.$article->extra_fields[$oneField->id]->value[0].'</a>';
					}elseif($oneField->type == 'multipleSelect' || $oneField->type == 'select' || $oneField->type == 'radio'){
						$object = json_decode($oneField->value);
						$myValues = $article->extra_fields[$oneField->id]->value;
						foreach($object as $oneObject){
							if((is_string($myValues) && $myValues == $oneObject->value) || is_array($myValues) && in_array($oneObject->value, $myValues)){
								$disValue .= $oneObject->name.', ';
							}
						}
						$disValue = trim($disValue, ', ');
					}elseif($oneField->type == 'image'){
						$disValue = '<img src="'.ltrim($article->extra_fields[$oneField->id]->value, '/').'" alt="" />';
					}elseif(is_string($article->extra_fields[$oneField->id]->value)){
						$disValue = nl2br($article->extra_fields[$oneField->id]->value);
					}else{
						continue;
					}
					if(strlen($disValue) < 1) continue;

					$article->extra_fields[$oneField->id]->disValue = $disValue;
					$varFields['{cf:'.$oneField->name.'}'] = $disValue; // Easier for custom templates
					$result .= '<br /><span class="fieldname">'.$oneField->name.'</span>: <span class="fieldvalue">'.$disValue.'</span>';
				}
			}

			//Display attachments...
			if(!empty($tag->attachments)){
				$db->setQuery('SELECT * FROM #__k2_attachments WHERE `itemID` = '.intval($article->id));
				$attachments = $db->loadObjectList();
				if(!empty($attachments)){
					$result .= '<fieldset><legend>'.JText::_('ATTACHMENTS').'</legend>';
					foreach($attachments as $oneAttachment){
						$result = '<a href="'.JURI::root().'media/k2/attachments/'.$oneAttachment->filename.'" target="_blank" >'.$oneAttachment->title.'</a><br />';
					}
					$result .= '</fieldset>';
				}
			}

			$md5picture = md5("Image".$article->id);
			//When we have a specific size, we will use the larger picture, not the small one.
			if(file_exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.$md5picture.'_S.jpg') && (empty($tag->pict) || $tag->pict != "resized")){
				$imagePath = JURI::root().'media/k2/items/cache/'.$md5picture.'_S.jpg';
			}elseif(file_exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.$md5picture.'_L.jpg')){
				$imagePath = JURI::root().'media/k2/items/cache/'.$md5picture.'_L.jpg';
			}

			if(empty($imagePath) || !empty($tag->removemainpict)){
				$result = $resultTitle.$result;
			}else{
				$varFields['{imagePath}'] = $imagePath;
				$imageLink = '<img src="'.$imagePath.'" alt="'.$article->image_caption.'" />';
				if(!empty($tag->link)) $imageLink = '<a href="'.$link.'" target="_blank" style="text-decoration:none" >'.$imageLink.'</a>';
				$varFields['{imageLink}'] = $imageLink;
				$result = $resultTitle.'<table cellspacing="5" cellpadding="0" border="0" ><tr><td valign="top">'.$imageLink.'</td><td valign="top">'.$result.'</td></tr></table>';
			}

			$result = '<div class="acymailing_content" style="clear:both">'.$result.'</div>';
		}else{
			$result = $resultTitle.$result;
		}

		//Add the cat title...
		if(!empty($tag->cattitle) && $this->currentcatid != $article->catid){
			$this->currentcatid = $article->catid;
			$result = '<h3 class="cattitle">'.$article->cattitle.'</h3>'.$result;
		}

		if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'k2element.php')){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins'.DS.'k2element.php');
			$result = ob_get_clean();
			$result = str_replace(array_keys($varFields), $varFields, $result);
		}

		$result = $acypluginsHelper->removeJS($result);

		//We have our content... lets check the pictures options
		if(isset($tag->pict)){
			$pictureHelper = acymailing_get('helper.acypict');
			if($tag->pict == '0'){
				$result = $pictureHelper->removePictures($result);
			}elseif($tag->pict == 'resized'){
				$pictureHelper->maxHeight = empty($tag->maxheight) ? $this->params->get('maxheight', 150) : $tag->maxheight;
				$pictureHelper->maxWidth = empty($tag->maxwidth) ? $this->params->get('maxwidth', 150) : $tag->maxwidth;
				if($pictureHelper->available()){
					$result = $pictureHelper->resizePictures($result);
				}elseif($app->isAdmin()){
					$app->enqueueMessage($pictureHelper->error, 'notice');
				}
			}
		}

		return $result;
	}

	private function _replaceAuto(&$email){
		$this->acymailing_generateautonews($email);

		if(!empty($this->tags)){
			$email->body = str_replace(array_keys($this->tags), $this->tags, $email->body);
			if(!empty($email->altbody)) $email->altbody = str_replace(array_keys($this->tags), $this->tags, $email->altbody);
			foreach($this->tags as $tag => $result){
				$email->subject = str_replace($tag, strip_tags(preg_replace('#</tr>[^<]*<tr[^>]*>#Uis', ' | ', $result)), $email->subject);
			}
		}
	}

	public function acymailing_generateautonews(&$email){

		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		$time = time();


		//Check if we should generate the SmartNewsletter or not...
		$match = '#{autok2:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything... so we won't try to stop the generation
		if(!$found) return $return;

		$this->tags = array();
		$db = JFactory::getDBO();

		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($this->tags[$oneTag])) continue;

				$arguments = explode('|', strip_tags($allresults[1][$i]));
				//The first argument is a list of sections and cats...
				$allcats = explode('-', $arguments[0]);
				$parameter = new stdClass();
				for($i = 1; $i < count($arguments); $i++){
					$args = explode(':', $arguments[$i]);
					$arg0 = trim($args[0]);
					if(empty($arg0)) continue;
					if(isset($args[1])){
						$parameter->$arg0 = $args[1];
					}else{
						$parameter->$arg0 = true;
					}
				}
				//Load the articles based on all arguments...
				$selectedArea = array();
				foreach($allcats as $oneCat){
					if(empty($oneCat)) continue;
					$selectedArea[] = (int)$oneCat;
				}

				$query = 'SELECT item.id FROM `#__k2_items` as item';
				$where = array();

				if(!empty($parameter->tags)){
					//We have tags... we select articles based on their tags
					//tags:tennis,handball,basket means the article should match all tags.
					$alltags = explode(',', $parameter->tags);
					$tagcond = array();
					foreach($alltags as $onetag){
						if(empty($onetag)) continue;
						$tagcond[] = $db->Quote(trim($onetag));
					}
					if(!empty($tagcond)){
						$db->setQuery('SELECT id FROM #__k2_tags WHERE name IN ('.implode(',', $tagcond).')');
						$allTagIds = acymailing_loadResultArray($db);
						if(count($allTagIds) != count($tagcond)){
							$app = JFactory::getApplication();
							$app->enqueueMessage(count($tagcond).' tags specified but we could only load '.count($allTagIds).' of them... Please make sure the tags you specified are valid', 'error');
						}
						foreach($allTagIds as $oneTagId){
							$query .= ' JOIN `#__k2_tags_xref` as tag'.$oneTagId.' ON item.id = tag'.$oneTagId.'.itemID AND tag'.$oneTagId.'.tagID = '.$oneTagId;
						}
					}
				}

				if(!empty($selectedArea)){
					$where[] = '`catid` IN ('.implode(',', $selectedArea).')';
				}

				if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate'])){
					$condition = '`publish_up` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
					// We need acy_created, the hour is not stored by K2 in the created date field
					$condition .= ' OR `acy_created` > DATE_FORMAT(CURRENT_TIMESTAMP()-SEC_TO_TIME('.intval(time() - $email->params['lastgenerateddate']).'), \'%Y-%m-%d %H:%i:%s\')';
					if($parameter->filter == 'modify'){
						$condition .= ' OR `modified` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
					}

					$where[] = $condition;
				}

				if(!empty($parameter->featured)){
					$where[] = '`featured` = 1';
				}elseif(!empty($parameter->nofeatured)){
					$where[] = '`featured` = 0';
				}

				$where[] = '`publish_up` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\'';
				$where[] = '`publish_down` > \''.date('Y-m-d H:i:s', $time - date('Z')).'\' OR `publish_down` = 0';
				if(empty($parameter->nopublished)) $where[] = '`published` = 1';
				$where[] = '`trash`=0';

				//Handle a date range in the query
				if(!empty($parameter->maxcreated)){
					$date = strtotime($parameter->maxcreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->maxcreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = '`created` < '.$db->Quote(date('Y-m-d H:i:s', $date));
				}

				if(!empty($parameter->mincreated)){
					$date = strtotime($parameter->mincreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->mincreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = '`created` > '.$db->Quote(date('Y-m-d H:i:s', $date));
				}

				//Access for J1.5.0 only
				if(!ACYMAILING_J16){
					if(isset($parameter->access)){
						$where[] = 'access <= '.intval($parameter->access);
					}else{
						if($this->params->get('contentaccess', 'registered') == 'registered'){
							$where[] = 'access <= 1';
						}elseif($this->params->get('contentaccess', 'registered') == 'public') $where[] = 'access = 0';
					}
				}elseif(isset($parameter->access)){
					//We set it only if the access is defined in the tag
					$where[] = 'access = '.intval($parameter->access);
				}

				//Add filter on language...
				if(!empty($parameter->language)){
					//We may have several languages separated by a comma
					$allLanguages = explode(',', $parameter->language);
					$langWhere = 'language IN (';
					foreach($allLanguages as $oneLanguage){
						$langWhere .= $db->Quote(trim($oneLanguage)).',';
					}
					$where[] = trim($langWhere, ',').')';
				}

				$query .= ' WHERE ('.implode(') AND (', $where).')';

				if(!empty($parameter->order)){
					if($parameter->order == 'rand'){
						$query .= ' ORDER BY rand()';
					}else{
						$ordering = explode(',', $parameter->order);
						$query .= ' ORDER BY `'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
					}
				}

				$start = '';
				if(!empty($parameter->start)) $start = intval($parameter->start).',';
				if(empty($parameter->max)) $parameter->max = 100;
				//We add a limit for the preview otherwise we could break everything
				$query .= ' LIMIT '.$start.(int)$parameter->max;

				$db->setQuery($query);
				$allArticles = acymailing_loadResultArray($db);

				if(!empty($parameter->min) && count($allArticles) < $parameter->min){
					//We won't generate the Newsletter
					$return->status = false;
					$return->message = 'Not enough k2 content for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min.' between '.acymailing_getDate($email->params['lastgenerateddate']).' and '.acymailing_getDate($time);
				}

				$stringTag = empty($parameter->noentrytext) ? '' : $parameter->noentrytext;
				if(!empty($allArticles)){
					if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'autok2.php')){
						ob_start();
						require(ACYMAILING_MEDIA.'plugins'.DS.'autok2.php');
						$stringTag = ob_get_clean();
					}else{
						//we insert the article tag one after the other in a table as they are already sorted
						$arrayElements = array();
						foreach($allArticles as $oneArticleId){
							$args = array();
							$args[] = 'k2:'.$oneArticleId;
							if(!empty($parameter->type)) $args[] = 'type:'.$parameter->type;
							if(!empty($parameter->link)) $args[] = 'link';
							if(!empty($parameter->author)) $args[] = 'author';
							if(!empty($parameter->lang)) $args[] = 'lang:'.$parameter->lang;
							if(!empty($parameter->notitle)) $args[] = 'notitle';
							if(!empty($parameter->cattitle)) $args[] = 'cattitle';
							if(!empty($parameter->created)) $args[] = 'created';
							if(!empty($parameter->customfields)) $args[] = 'customfields';
							if(!empty($parameter->excludedcf)) $args[] = 'excludedcf:'.$parameter->excludedcf;
							if(!empty($parameter->attachments)) $args[] = 'attachments';
							if(!empty($parameter->noreadmore)) $args[] = 'noreadmore';
							if(!empty($parameter->removemainpict)) $args[] = 'removemainpict';
							if(isset($parameter->images)) $args[] = 'images:'.$parameter->images;
							if(isset($parameter->pict)) $args[] = 'pict:'.$parameter->pict;
							if(!empty($parameter->itemid)) $args[] = 'itemid:'.$parameter->itemid;
							if(!empty($parameter->wrap)) $args[] = 'wrap:'.$parameter->wrap;
							if(!empty($parameter->maxwidth)) $args[] = 'maxwidth:'.$parameter->maxwidth;
							if(!empty($parameter->maxheight)) $args[] = 'maxheight:'.$parameter->maxheight;
							if(!empty($parameter->readmore)) $args[] = 'readmore:'.$parameter->readmore;
							$arrayElements[] = '{'.implode('|', $args).'}';
						}

						$acypluginsHelper = acymailing_get('helper.acyplugins');
						$stringTag = $acypluginsHelper->getFormattedResult($arrayElements, $parameter);
					}
				}

				$this->tags[$oneTag] = $stringTag;
			}
		}

		return $return;
	}
}//endclass