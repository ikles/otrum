<?php
/**
 * @copyright    Copyright (C) 2009-2016 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgAcymailingK2users extends JPlugin{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'k2users');
			$this->params = new JParameter($plugin->params);
		}
	}

	function acymailing_getPluginType(){
		$onePlugin = new stdClass();
		$onePlugin->name = 'K2 Users';
		$onePlugin->function = 'acymailingtagk2users_show';
		$onePlugin->help = 'plugin-k2users';

		return $onePlugin;
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){
		if($this->params->get('displayfilter_'.$context, true) == false) return;

		$type['k2groups'] = 'K2 User Groups';

		$db = JFactory::getDBO();
		$db->setQuery("SELECT `id`,`name` FROM `#__k2_user_groups` ORDER BY `name` ASC");
		$k2groups = $db->loadObjectList('id');
		$k2groupsin = acymailing_get('type.operatorsin');

		$return = '<div id="filter__num__k2groups">'.$k2groupsin->display("filter[__num__][k2groups][type]");
		$return .= ' '.JHTML::_('select.genericlist', $k2groups, "filter[__num__][k2groups][group]", 'class="inputbox" size="1"', 'id', 'name').'</div>';

		return $return;
	}

	function onAcyProcessFilter_k2groups(&$query, $filter, $num){
		//We get the e-mail of the users which belong to this group, it's much easier that way
		$query->db->setQuery('SELECT DISTINCT `userID` FROM `#__k2_users` WHERE `group` = '.intval($filter['group']));
		$allIds = acymailing_loadResultArray($query->db);
		if(empty($allIds)) $allIds[] = '-1';
		$operator = ($filter['type'] == 'IN') ? 'IN' : "NOT IN";
		$query->where[] = "sub.userid $operator (".implode(",", $allIds).")";
	}

	function acymailingtagk2users_show(){
		$text = '<table class="adminlist table table-striped table-hover" cellpadding="1">';
		$fields = acymailing_getColumns('#__k2_users');

		$k = 0;
		foreach($fields as $fieldname => $oneField){
			$type = '';
			if(strpos(strtolower($oneField), 'date') !== false) $type = '|type:date';
			if($fieldname == 'image') $type = '|type:image';
			$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="setTag(\'{k2user:'.$fieldname.$type.'}\');insertTag();" ><td>'.$fieldname.'</td></tr>';
			$k = 1 - $k;
		}
		$text .= '</table>';

		echo $text;
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$match = '#{k2user:(.*)}#Ui';
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

		$values = null;
		if(!empty($user->userid)){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT * FROM `#__k2_users` WHERE `userID` = '.$user->userid.' LIMIT 1');
			$values = $db->loadObject();
		}

		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				//Don't need to process twice a tag we already have!
				if(isset($tags[$oneTag])) continue;

				//We explode each argument of the tag
				$arguments = explode('|', $allresults[1][$i]);
				$field = $arguments[0];
				unset($arguments[0]);
				$mytag = new stdClass();
				//We get the default value with the params plugin
				$mytag->default = $this->params->get('default_'.$field, '');
				if(!empty($arguments)){
					foreach($arguments as $onearg){
						$args = explode(':', $onearg);
						if(isset($args[1])){
							$mytag->{$args[0]} = $args[1];
						}else{
							$mytag->{$args[0]} = 1;
						}
					}
				}
				$replaceme = isset($values->$field) ? $values->$field : $mytag->default;
				if(!empty($mytag->type)){
					if($mytag->type == 'date'){
						$replaceme = acymailing_getDate(strtotime($replaceme));
					}
					if($mytag->type == 'image' AND !empty($replaceme)){
						$replaceme = '<img src="'.ACYMAILING_LIVE.'media/k2/users/'.$replaceme.'"/>';
					}
				}

				if(!empty($mytag->lower)) $replaceme = strtolower($replaceme);
				if(!empty($mytag->ucwords)) $replaceme = ucwords($replaceme);
				if(!empty($mytag->ucfirst)) $replaceme = ucfirst($replaceme);

				$tags[$oneTag] = $replaceme;
			}
		}

		foreach($results as $var => $allresults){
			$email->$var = str_replace(array_keys($tags), $tags, $email->$var);
		}
	}//endfct
}//endclass