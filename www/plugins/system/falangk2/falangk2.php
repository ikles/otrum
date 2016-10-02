<?php
/**
 * @package     Falang for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013 Faboba. All rights reserved.
 */


// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgSystemFalangk2 extends JPlugin
{

    function plgSystemFalangk2(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    function onAfterInitialise()
    {
        // Determine Joomla! version
        if (version_compare(JVERSION, '3.0', 'ge'))
        {
            if (!defined('K2_JVERSION')){
                define('K2_JVERSION', '30');
            }
        }
        else if (version_compare(JVERSION, '2.5', 'ge'))
        {
            if (!defined('K2_JVERSION')){
                define('K2_JVERSION', '25');
            }
        }
        else
        {
            if (!defined('K2_JVERSION')){
                define('K2_JVERSION', '15');
            }
        }

        // Define the DS constant under Joomla! 3.0
        if (!defined('DS'))
        {
            define('DS', DIRECTORY_SEPARATOR);
        }

        // Import Joomla! classes
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        jimport('joomla.application.component.controller');
        jimport('joomla.application.component.model');
        jimport('joomla.application.component.view');

        // Get application
        $mainframe = JFactory::getApplication();

        // Load the K2 classes
        JLoader::register('K2Table', JPATH_ADMINISTRATOR.'/components/com_k2/tables/table.php');
        JLoader::register('K2Controller', JPATH_BASE.'/components/com_k2/controllers/controller.php');
        JLoader::register('K2Model', JPATH_ADMINISTRATOR.'/components/com_k2/models/model.php');


        if ($mainframe->isSite())
        {
            K2Model::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models');
        }
        else
        {
            // Fix warning under Joomla! 1.5 caused by conflict in model names
            if (K2_JVERSION != '15' || (K2_JVERSION == '15' && JRequest::getCmd('option') != 'com_users'))
            {
                K2Model::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'models');
            }
        }
        JLoader::register('K2View', JPATH_ADMINISTRATOR.'/components/com_k2/views/view.php');
        JLoader::register('K2HelperHTML', JPATH_ADMINISTRATOR.'/components/com_k2/helpers/html.php');

        // Define the default Itemid for users and tags. Defined here instead of the K2HelperRoute for performance reasons.
        // UPDATE : Removed in K2 2.6.7. All K2 links without Itemid now use the anyK2Link defined in the router helper.
        // define('K2_USERS_ITEMID', $componentParams->get('defaultUsersItemid'));
        // define('K2_TAGS_ITEMID', $componentParams->get('defaultTagsItemid'));

        /*
        if(JRequest::getCmd('option')=='com_k2' && JRequest::getCmd('task')=='save' && !$mainframe->isAdmin()){
            $dispatcher = JDispatcher::getInstance();
            foreach($dispatcher->_observers as $observer){
                if($observer->_name=='jfdatabase' || $observer->_name=='jfrouter' || $observer->_name=='missing_translation'){
                    $dispatcher->detach($observer);
                }
            }
        }
        */

        $option = JRequest::getCmd('option');
        $task = JRequest::getCmd('task');
        $type = JRequest::getCmd('catid');

        if ($option != 'com_falang')
            return;

        if (!JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'JSON.php'))
        {
            return;
        }

        JPlugin::loadLanguage('com_k2', JPATH_ADMINISTRATOR);

        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
        require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'JSON.php');

        if ($option == 'com_falang' && ($task == 'translate.apply' || $task == 'translate.save') && $type == 'k2_items')
        {
            $objects = array();
            //change in 1.2 use jinput but problem for RAW type in joomla 2.5 not in 3.3
            $jinput = JFactory::getApplication()->input;
            $language_id = $jinput->getInt('select_language_id',0);
            $reference_id = $jinput->getInt('reference_id',0);
            //v1.3 add published
            $published = $jinput->getInt('published',0);

            if (version_compare(JVERSION, '3.0', 'ge'))
            {
                $post = $jinput->getArray($_POST);
                foreach ($post as $key => $value)
                {
                    if (( bool )JString::stristr($key, 'K2ExtraField_'))
                    {
                        $object = new JObject;
                        $object->set('id', JString::substr($key, 13));
                        //html working on j3 only
                        $object->set('value', $jinput->get($key,'','RAW'));
                        unset($object->_errors);
                        $objects[] = $object;
                    }
                }
            } else {
                $variables = JRequest::get('post');
                foreach ($variables as $key => $value)
                {
                    if (( bool )JString::stristr($key, 'K2ExtraField_'))
                    {
                        $object = new JObject;
                        $object->set('id', JString::substr($key, 13));
                        //$object->set('value', JRequest::getString($key,'','post',JREQUEST_ALLOWHTML ));
                        //version 2.5 utilisation de la variable $value directemnt qui renvoie une valeur ou un objet
                        $object->set('value', $value);
                        unset($object->_errors);
                        $objects[] = $object;
                    }
                }
            }


            $json = new Services_JSON;
            $extra_fields = $json->encode($objects);

            $extra_fields_search = '';

            foreach ($objects as $object)
            {
                $extra_fields_search .= $this->getSearchValue($object->id, $object->value);
                $extra_fields_search .= ' ';
            }

            $user = JFactory::getUser();

            $db = JFactory::getDBO();

            $query = "SELECT COUNT(*) FROM #__falang_content WHERE reference_field = 'extra_fields' AND language_id = {$language_id} AND reference_id = {$reference_id} AND reference_table='k2_items'";
            $db->setQuery($query);
            $result = $db->loadResult();

            if ($result > 0)
            {
                //sbou
                $query = "UPDATE #__falang_content SET value=".$db->Quote($extra_fields).", published = {$published} WHERE reference_field = 'extra_fields' AND language_id = {$language_id} AND reference_id = {$reference_id} AND reference_table='k2_items'";
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $modified = date("Y-m-d H:i:s");
                $modified_by = $user->id;
                $query = "INSERT INTO #__falang_content (`id`, `language_id`, `reference_id`, `reference_table`, `reference_field` ,`value`, `original_value`, `original_text`, `modified`, `modified_by`, `published`) VALUES (NULL, {$language_id}, {$reference_id}, 'k2_items', 'extra_fields', ".$db->Quote($extra_fields).", '','', ".$db->Quote($modified).", {$modified_by}, {$published} )";
                $db->setQuery($query);
                $db->query();
            }

            $query = "SELECT COUNT(*) FROM #__falang_content WHERE reference_field = 'extra_fields_search' AND language_id = {$language_id} AND reference_id = {$reference_id} AND reference_table='k2_items'";
            $db->setQuery($query);
            $result = $db->loadResult();

            if ($result > 0)
            {
                $query = "UPDATE #__falang_content SET value=".$db->Quote($extra_fields_search).", published = {$published} WHERE reference_field = 'extra_fields_search' AND language_id = {$language_id} AND reference_id = {$reference_id} AND reference_table='k2_items'";
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $modified = date("Y-m-d H:i:s");
                $modified_by = $user->id;
                $published = JRequest::getVar('published', 0);
                $query = "INSERT INTO #__falang_content (`id`, `language_id`, `reference_id`, `reference_table`, `reference_field` ,`value`, `original_value`, `original_text`, `modified`, `modified_by`, `published`) VALUES (NULL, {$language_id}, {$reference_id}, 'k2_items', 'extra_fields_search', ".$db->Quote($extra_fields_search).", '','', ".$db->Quote($modified).", {$modified_by}, {$published} )";
                $db->setQuery($query);
                $db->query();
            }

        }

        //v1.3
        if ($option == 'com_falang' && ($task == 'translate.unpublish' || $task == 'translate.publish' ) && $type == 'k2_items') {
            $jinput = JFactory::getApplication()->input;

            $publish = $task == 'translate.publish'?1:0;

            $db = JFactory::getDBO();
            //clck on the items list publish
            $cid =  $jinput->get('cid',array(),'array');
            if( strpos($cid[0], '|') >= 0 ) {
                list($translation_id, $contentid, $language_id) = explode('|', $cid[0]);
            }
            foreach( $cid as $cid_row ) {
                list($translation_id, $contentid, $language_id) = explode('|', $cid_row);
                $query = "UPDATE #__falang_content SET published = {$publish} WHERE language_id = {$language_id} AND reference_id = {$contentid} AND reference_table='k2_items' AND id = {$translation_id}";
                $db->setQuery($query);
                $db->query();
            }
        }

        //v1.5
        if ($option == 'com_falang' && ($task == 'translate.unpublish' || $task == 'translate.publish' ) && $type == 'k2_extra_fields') {
            $jinput = JFactory::getApplication()->input;

            $publish = $task == 'translate.publish'?1:0;

            $db = JFactory::getDBO();
            //clck on the items list publish
            $cid =  $jinput->get('cid',array(),'array');
            if( strpos($cid[0], '|') >= 0 ) {
                list($translation_id, $contentid, $language_id) = explode('|', $cid[0]);
            }
            foreach( $cid as $cid_row ) {
                list($translation_id, $contentid, $language_id) = explode('|', $cid_row);
                //$query = "UPDATE #__falang_content SET published = {$publish} WHERE language_id = {$language_id} AND reference_id = {$contentid} AND reference_table='k2_extra_fields' AND id = {$translation_id}";
                //remove translationid to publish all field
                $query = "UPDATE #__falang_content SET published = {$publish} WHERE language_id = {$language_id} AND reference_id = {$contentid} AND reference_table='k2_extra_fields'";
                $db->setQuery($query);
                $db->query();
            }
        }


        //sbou
        //use to show extra fiels on falang translation form
        if ($option == 'com_falang' && ($task == 'translate.edit' || $task == 'translate.apply') && $type == 'k2_items')
        {

            if ($task == 'translate.edit')
            {
                $cid = JRequest::getVar('cid');
                $array = explode('|', $cid[0]);
                $reference_id = $array[1];
            }

            if ($task == 'translate.apply')
            {
                $reference_id = JRequest::getInt('reference_id');
            }

            $item = JTable::getInstance('K2Item', 'Table');
            $item->load($reference_id);
            $category_id = $item->catid;
            $language_id = JRequest::getInt('select_language_id');

            $db = JFactory::getDBO();
            $category = JTable::getInstance('K2Category', 'Table');
            $category->load($category_id);
            $mefgfork2_enabled = JPluginHelper::isEnabled('k2', 'mefgfork2');
            //standard case no mefgfork2 plugin not exist or not published
            if (!$mefgfork2_enabled)
            {

                $group = $category->extraFieldsGroup;
                $query = "SELECT * FROM #__k2_extra_fields WHERE `group`=".$db->Quote($group)." AND published=1 ORDER BY ordering";
                $db->setQuery($query);
                $extraFields = $db->loadObjectList();
            } else {
                //change for mefgfork
                // Get the K2 plugin params (the stuff you see when you edit the plugin in the plugin manager)
                //use for mefgfork2
                JLoader::register('K2Parameter', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2parameter.php');
                JLoader::register('MEFGK2ModelExtraField', JPATH_SITE.'/plugins/k2/mefgfork2/mefgfork2.php');

                // change $this->pluginName with mefgfork2 because we are not in the same plugin
                $plugin = &JPluginHelper::getPlugin('k2', 'mefgfork2');
                if(K2_JVERSION=='15') $pluginParams = new JParameter($plugin->params);
                else $pluginParams = new JRegistry($plugin->params);
                // Get the output of the K2 plugin fields (the data entered by your site maintainers)

                // change $this->pluginName with mefgfork2 because we are not in the same plugin
                $plugins = new K2Parameter($category->plugins, '', 'mefgfork2');
                $efg = array($category->extraFieldsGroup);
                if(is_array($plugins->get('efg'))) $efg = array_unique(array_merge($efg, $plugins->get('efg')));
                $checkboxFields = $plugins->get('checkboxfields');
                $orderExtraFields = $pluginParams->get('efg_sortfields');

                if(count($efg))  {
                    $db->setQuery("SELECT * FROM #__k2_extra_fields_groups WHERE id IN(".implode(",",$efg).")");
                    $efgArray = $db->loadObjectList('id');
                }
                $extraFieldModel = new MEFGK2ModelExtraField();
                $output = '';
                $extraFields = array();
                foreach ($efg as $group) {
                    $extraFields = array_merge($extraFields,$extraFieldModel->getExtraFieldsByGroup($group));
                }
            }



            //end change


            $json = new Services_JSON;
            $output = '';
            if (count($extraFields))
            {
                $output .= '<h1>'.JText::_('K2_EXTRA_FIELDS').'</h1>';
                foreach ($extraFields as $extrafield)
                {
                    $extraField = $json->decode($extrafield->value);
                    $output .= '<div style="width: 50%;float:left">';
                    $output .= '<h2>'.JText::_('K2_ORIGINAL').'</h2>';
                    $output .= trim($this->renderOriginal($extrafield, $reference_id));
                    $output .= '</div>';
                    $output .= '<div style="width: 50%;float:left">';
                    $output .= '<h2>'.JText::_('K2_TRANSLATION').'</h2>';
                    $output .= trim($this->renderTranslated($extrafield, $reference_id));
                    $output .= '</div>';
                    $output .= '<div style="clear:both;"></div>';
                }
            }

            $pattern = '/\r\n|\r|\n/';
            //$pattern = '/\r/';



            // *** Mootools Snippet ***
            // for items

            //change in preg_replace the replacement from nothing to \\r\\n\\

            $js = "
			window.addEvent('domready', function(){
				var div = new Element('div', {'id': 'K2ExtraFields'}).set('html','".preg_replace($pattern, '\\r\\n\\', $output)."').inject($('extras'),'inside');
			});
			";

            //jquery system (not in use)
            $js2 = '
            jQuery( document ).ready(function($) {
                $("<div/>",{
                  html : "'.preg_replace($pattern, '\\r\\n\\', addslashes($output)).'",
                  id : "K2ExtraFields"
                }).appendTo("#extras");
            });
            ';

            JHTML::_('behavior.framework');

            $document = JFactory::getDocument();
            $document->addScriptDeclaration($js);

            // *** Embedded CSS Snippet ***
            $document->addCustomTag('
			<style type="text/css" media="all">
				#K2ExtraFields { color:#000; font-size:11px; padding:6px 2px 4px 4px; text-align:left; }
				#K2ExtraFields h1 { margin-bottom:18px;padding:0;font-weight: normal; font-size:19.5px; line-height:36px;border-bottom: 1px solid #DDDDDD;}
				#K2ExtraFields h2 { font-size:13px;font-weight: normal }
				#K2ExtraFields strong { font-style:italic; }
			</style>
			');
        }

        if ($option == 'com_falang' && ($task == 'translate.apply' || $task == 'translate.save') && $type == 'k2_extra_fields')
        {

            $language_id = JRequest::getInt('select_language_id');
            $reference_id = JRequest::getInt('reference_id');
            $extraFieldType = JRequest::getVar('extraFieldType');

            $objects = array();
            $values = JRequest::getVar('option_value');
            $names = JRequest::getVar('option_name');
            $target = JRequest::getVar('option_target');

            //v1.5 add published
            $published = JRequest::getInt('published',0);


            for ($i = 0; $i < sizeof($values); $i++)
            {
                $object = new JObject;
                $object->set('name', $names[$i]);

                if ($extraFieldType == 'select' || $extraFieldType == 'multipleSelect' || $extraFieldType == 'radio')
                {
                    $object->set('value', $i + 1);
                }
                elseif ($extraFieldType == 'link')
                {
                    if (substr($values[$i], 0, 7) == 'http://')
                    {
                        $values[$i] = $values[$i];
                    }
                    else
                    {
                        $values[$i] = 'http://'.$values[$i];
                    }
                    $object->set('value', $values[$i]);
                }
                else
                {
                    $object->set('value', $values[$i]);
                }

                $object->set('target', $target[$i]);
                unset($object->_errors);
                $objects[] = $object;
            }

            $json = new Services_JSON;
            $value = $json->encode($objects);

            $user = JFactory::getUser();

            $db = JFactory::getDBO();

            $query = "SELECT COUNT(*) FROM #__falang_content WHERE reference_field = 'value' AND language_id = {$language_id} AND reference_id = {$reference_id} AND reference_table='k2_extra_fields'";
            $db->setQuery($query);
            $result = $db->loadResult();

            if ($result > 0)
            {
                $query = "UPDATE #__falang_content SET value=".$db->Quote($value)." WHERE reference_field = 'value' AND language_id = {$language_id} AND reference_id = {$reference_id} AND reference_table='k2_extra_fields'";
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $modified = date("Y-m-d H:i:s");
                $modified_by = $user->id;
                $published = JRequest::getVar('published', 0);
                $query = "INSERT INTO #__falang_content (`id`, `language_id`, `reference_id`, `reference_table`, `reference_field` ,`value`, `original_value`, `original_text`, `modified`, `modified_by`, `published`) VALUES (NULL, {$language_id}, {$reference_id}, 'k2_extra_fields', 'value', ".$db->Quote($value).", '','', ".$db->Quote($modified).", {$modified_by}, {$published} )";
                $db->setQuery($query);
                $db->query();
            }

        }

        if ($option == 'com_falang' && ($task == 'translate.edit' || $task == 'translate.apply') && $type == 'k2_extra_fields')
        {

            if ($task == 'translate.edit')
            {
                $cid = JRequest::getVar('cid');
                $array = explode('|', $cid[0]);
                $reference_id = $array[1];
            }

            if ($task == 'translate.apply')
            {
                $reference_id = JRequest::getInt('reference_id');
            }

            $extraField = JTable::getInstance('K2ExtraField', 'Table');
            $extraField->load($reference_id);
            $language_id = JRequest::getInt('select_language_id');

            if ($extraField->type == 'multipleSelect' || $extraField->type == 'select' || $extraField->type == 'radio')
            {
                $subheader = '<strong>'.JText::_('K2_OPTIONS').'</strong>';
            }
            else
            {
                $subheader = '<strong>'.JText::_('K2_DEFAULT_VALUE').'</strong>';
            }

            $json = new Services_JSON;
            $objects = $json->decode($extraField->value);
            $output = '<input type="hidden" value="'.$extraField->type.'" name="extraFieldType" />';
            $output .= '<h1>'.JText::_('K2_EXTRA_FIELDS').'</h1>';
            $output .= '<div style="width: 50%;float:left">';
                if (count($objects))
                {
                    $output .= '<h2>'.JText::_('K2_ORIGINAL').'</h2>';
                    $output .= $subheader.'<br />';

                    foreach ($objects as $object)
                    {
                        //v2.1 addslashes
                        //2.3 quote missing who make problem when there are space in value
                        $output .= '<p><input type="text" readonly="readonly" value="'.addslashes($object->name).'" /></p>';
                        if ($extraField->type == 'textfield' || $extraField->type == 'textarea')
                            $output .= '<p>'.$object->value.'</p>';

                    }
                }
            $output .= '</div>';

            $db = JFactory::getDBO();
            $query = "SELECT `value` FROM #__falang_content WHERE reference_field = 'value' AND language_id = {$language_id} AND reference_id = {$reference_id} AND reference_table='k2_extra_fields'";
            $db->setQuery($query);
            $result = $db->loadResult();

            $translatedObjects = $json->decode($result);

            $output .= '<div style="width: 50%;float:left">';

            if (count($objects))
            {
                $output .= '<h2>'.JText::_('K2_TRANSLATION').'</h2>';
                $output .= $subheader.'<br />';
                foreach ($objects as $key => $value)
                {
                    if (isset($translatedObjects[$key]))
                        $value = $translatedObjects[$key];

                    if ($extraField->type == 'textarea')
                        $output .= '<p><textarea name="option_name[]" cols="30" rows="15"> '.$value->name.'</textarea></p>';
                    else
                        $output .= '<p><input type="text" name="option_name[]" value="'.addslashes($value->name).'" /></p>';
                        $output .= '<p><input type="hidden" name="option_value[]" value="'.$value->value.'" /></p>';
                        $output .= '<p><input type="hidden" name="option_target[]" value="'.$value->target.'" /></p>';
                }
            }
            $output .= '</div>';

            $pattern = '/\r\n|\r|\n/';

            // *** Mootools Snippet ***

            $js = "
			window.addEvent('domready', function(){
				var div = new Element('div', {'id': 'K2ExtraFields'}).set('html','".preg_replace($pattern, '', $output)."').inject($('extras'),'inside');
			});
			";

            JHTML::_('behavior.framework');
            $document = JFactory::getDocument();
            $document->addScriptDeclaration($js);

            // *** Embedded CSS Snippet ***
            $document->addCustomTag('
			<style type="text/css" media="all">
				#K2ExtraFields { color:#000; font-size:11px; padding:6px 2px 4px 4px; text-align:left; }
				#K2ExtraFields h1 { margin-bottom:18px;padding:0;font-weight: normal; font-size:19.5px; line-height:36px;border-bottom: 1px solid #DDDDDD;}
				#K2ExtraFields h2 { font-size:13px;font-weight: normal }
				#K2ExtraFields strong { font-style:italic; }
			</style>
			');

        }
        return;
    }

    function onAfterRender()
    {
        $response = JResponse::getBody();
        $searches = array(
            '<meta name="og:url"',
            '<meta name="og:title"',
            '<meta name="og:type"',
            '<meta name="og:image"',
            '<meta name="og:description"'
        );
        $replacements = array(
            '<meta property="og:url"',
            '<meta property="og:title"',
            '<meta property="og:type"',
            '<meta property="og:image"',
            '<meta property="og:description"'
        );
        if (JString::strpos($response, 'prefix="og: http://ogp.me/ns#"') === false)
        {
            $searches[] = '<html ';
            $searches[] = '<html>';
            $replacements[] = '<html prefix="og: http://ogp.me/ns#" ';
            $replacements[] = '<html prefix="og: http://ogp.me/ns#">';
        }
        $response = JString::str_ireplace($searches, $replacements, $response);
        JResponse::setBody($response);
    }

    function getSearchValue($id, $currentValue)
    {

        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
        $row = JTable::getInstance('K2ExtraField', 'Table');
        $row->load($id);

        require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'JSON.php');
        $json = new Services_JSON;
        $jsonObject = $json->decode($row->value);

        //v1.6
        if (!isset($row->type)){return;}

        $value = '';
        if ($row->type == 'textfield' || $row->type == 'textarea')
        {
            $value = $currentValue;
        }
        else if ($row->type == 'multipleSelect' || $row->type == 'link')
        {
            foreach ($jsonObject as $option)
            {
                if (@in_array($option->value, $currentValue))
                    $value .= $option->name.' ';
            }
        }
        else
        {
            foreach ($jsonObject as $option)
            {
                if ($option->value == $currentValue)
                    $value .= $option->name;
            }
        }
        return $value;

    }

    function renderOriginal($extraField, $itemID)
    {

        $mainframe = JFactory::getApplication();
        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
        require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'JSON.php');
        $json = new Services_JSON;
        $item = JTable::getInstance('K2Item', 'Table');
        $item->load($itemID);

        $defaultValues = $json->decode($extraField->value);

        foreach ($defaultValues as $value)
        {
            if ($extraField->type == 'textfield' || $extraField->type == 'textarea')
                $active = $value->value;
            else if ($extraField->type == 'link')
            {
                $active[0] = $value->name;
                $active[1] = $value->value;
                $active[2] = $value->target;
            }
            else
                $active = '';
        }

        if (isset($item))
        {
            $currentValues = $json->decode($item->extra_fields);
            if (count($currentValues))
            {
                foreach ($currentValues as $value)
                {
                    if ($value->id == $extraField->id)
                    {
                        $active = $value->value;
                    }

                }
            }

        }

        $output = '';

        switch ($extraField->type)
        {
            case 'textfield' :
                $output = '<div><strong>'.$extraField->name.'</strong><br /><input type="text" disabled="disabled" name="OriginalK2ExtraField_'.$extraField->id.'" value="'.addslashes($active).'"/></div><br /><br />';
                break;

            case 'textarea' :
                // since 12.3 JFactory::getEditor() is deprecated.
                //$editor = JEditor::getInstance();
                $editor = JFactory::getEditor();
                $params = array( 'smilies'=> '0' ,
                    'style'  => '0' ,
                    'layer'  => '0' ,
                    'table'  => '0' ,
                    'clear_entities'=>'0',
                );
                JFilterOutput::objectHTMLSafe($active, ENT_QUOTES, array('video', 'params', 'plugins'));

                $json = new Services_JSON;
                $editorParams = $json->decode($extraField->value);
                //v2.9
                //si le field n'utilise pas d'editeur , on ne doit pas en utiliser non plus.
                if ($editorParams[0]->editor == 0) {
                    $active = addslashes($active);
                    $output = '<div><strong>'.$extraField->name.'</strong><br /><textarea disabled="disabled" name="OriginalK2ExtraField_'.$extraField->id.'" rows="10" cols="40">'.htmlspecialchars($active).'</textarea></div><br /><br />';
                } else {
                    $output = '<div><strong>'.$extraField->name.'</strong><br />'.addslashes($editor->display('OriginalK2ExtraField_'.$extraField->id, $active, '400', '400', '20', '20', false,null, null,null, $params)).'</div><br /><br />';
                }
                break;
            case 'link' :
                $output = '<div><strong>'.$extraField->name.'</strong><br />';
                $output .= '&nbsp;<input disabled="disabled" size="30"	type="text" name="OriginalK2ExtraField_'.$extraField->id.'[]" value="'.addslashes($active[0]).'"/><br />';
                $output .= '&nbsp;<input disabled="disabled" size="30"	type="text" name="OriginalK2ExtraField_'.$extraField->id.'[]" value="'.addslashes($active[1]).'"/><br />';
                $output .= '<br /><br /></div>';
                break;
            // v2.1 add image case
            case 'image' :
                $output = '<div><strong>'.$extraField->name.'</strong><br /><input type="text" disabled="disabled" name="OriginalK2ExtraField_'.$extraField->id.'" value="'.htmlspecialchars($active).'"/></div><br /><br />';
                break;
            // v2.7 add image case
            case 'date':
                $output = '<div><strong>'.$extraField->name.'</strong><br /><input type="text" disabled="disabled" name="OriginalK2ExtraField_'.$extraField->id.'" value="'.htmlspecialchars($active).'"/></div><br /><br />';
                break;
        }

        return $output;

    }

    function renderTranslated($extraField, $itemID)
    {

        $mainframe = JFactory::getApplication();
        require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'JSON.php');
        $json = new Services_JSON;

        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
        $item = JTable::getInstance('K2Item', 'Table');
        $item->load($itemID);

        $defaultValues = $json->decode($extraField->value);

        foreach ($defaultValues as $value)
        {
            if ($extraField->type == 'textfield' || $extraField->type == 'textarea')
                $active = $value->value;
            else if ($extraField->type == 'link')
            {
                $active[0] = $value->name;
                $active[1] = $value->value;
                $active[2] = $value->target;
            }
            else
                $active = '';
        }

        if (isset($item))
        {
            $currentValues = $json->decode($item->extra_fields);
            if (count($currentValues))
            {
                foreach ($currentValues as $value)
                {
                    if ($value->id == $extraField->id)
                    {
                        $active = $value->value;
                    }

                }
            }

        }

        $language_id = JRequest::getInt('select_language_id');
        $db = JFactory::getDBO();
        $query = "SELECT `value` FROM #__falang_content WHERE reference_field = 'extra_fields' AND language_id = {$language_id} AND reference_id = {$itemID} AND reference_table='k2_items'";
        $db->setQuery($query);
        $result = $db->loadResult();
        $currentValues = $json->decode($result);
        if (count($currentValues))
        {
            foreach ($currentValues as $value)
            {
                if ($value->id == $extraField->id)
                {
                    $active = $value->value;
                }

            }
        }

        $output = '';

        switch ($extraField->type)
        {

            case 'textfield' :
                $output = '<div><strong>'.$extraField->name.'</strong><br /><input type="text" name="K2ExtraField_'.$extraField->id.'" value="'.addslashes($active).'"/></div><br /><br />';
                break;

            case 'textarea' :
                // since 12.3 JFactory::getEditor() is deprecated.
                //$editor = JEditor::getInstance();
                $editor = JFactory::getEditor();
                $params = array( 'smilies'=> '0' ,
                    'style'  => '1' ,
                    'layer'  => '0' ,
                    'table'  => '0' ,
                    'clear_entities'=>'0',
                );
                $json = new Services_JSON;
                $editorParams = $json->decode($extraField->value);
                if ($editorParams[0]->editor == 0) {
                    $active = addslashes($active);
                   $output = '<div><strong>'.$extraField->name.'</strong><br /><textarea name="K2ExtraField_'.$extraField->id.'" rows="10" cols="40">'.htmlspecialchars($active).'</textarea></div><br /><br />';
                } else {
                    $output = '<div><strong>'.$extraField->name.'</strong><br />'.addslashes($editor->display('K2ExtraField_'.$extraField->id, $active, '350', '400', '20', '20', false,null, null,null, $params)).'</div><br /><br />';

                }
                break;
            case 'select' :
                //v2.2 //add showNull feature
                //v2.1 //fix quote in values
                $showNull = false;
                foreach ($defaultValues as  $key => $value){
                    if ($value->showNull == 1){
                        $showNull = true;
                        $defaultValues[$key]->name = addslashes($value->name);
                    } else {
                        $defaultValues[$key]->name = addslashes($value->name);
                    }
                }
                if ($showNull){
                    $nullvalue = new stdClass;
                    $nullvalue->name = "";
                    $nullvalue->value = "";
                    $nullvalue->showNull = 1;
                    array_unshift ($defaultValues,$nullvalue);
                }

                $output = '<div style="display:none">'.JHTML::_('select.genericlist', $defaultValues, 'K2ExtraField_'.$extraField->id,'', 'value', 'name', addslashes($active)).'</div>';
                break;

            case 'multipleSelect' :
                //v2.1 //fix quote in values
                foreach ($defaultValues as  $key => $value){
                    $defaultValues[$key]->name = addslashes($value->name);
                }
                //v2.6
                $mselectValues = is_array($active)?$active:addslashes($active);
                //v2.6+ on peut supprimer le display none.
                $output = '<div style="display:none">'.JHTML::_('select.genericlist', $defaultValues, 'K2ExtraField_'.$extraField->id.'[]', 'multiple="multiple"', 'value', 'name',$mselectValues ).'</div>';
                break;

            case 'radio' :
                //v2.1 //fix quote in values
                foreach ($defaultValues as  $key => $value){
                    $defaultValues[$key]->name = addslashes($value->name);
                }
                $output = '<div style="display:none">'.JHTML::_('select.radiolist', $defaultValues, 'K2ExtraField_'.$extraField->id, '', 'value', 'name', addslashes($active)).'</div>';
                break;

            case 'link' :
                $output = '<div><strong>'.$extraField->name.'</strong><br />';
                $output .= '<input type="text" size="30" name="K2ExtraField_'.$extraField->id.'[]" value="'.addslashes($active[0]).'"/><br />';
                $output .= '<input type="text" size="30" name="K2ExtraField_'.$extraField->id.'[]" value="'.addslashes($active[1]).'"/><br />';
                $output .= '<input type="hidden" name="K2ExtraField_'.$extraField->id.'[]" value="'.addslashes($active[2]).'"/><br />';
                $output .= '<br /><br /></div>';
                break;
            //v2.1
            case 'image' :
                $output = '<div><strong>'.$extraField->name.'</strong><br /><input type="text" name="K2ExtraField_'.$extraField->id.'" value="'.htmlspecialchars($active).'"/></div><br /><br />';
                break;
            //v2.7 add date case
            case 'date' :
                $output = '<div><strong>'.$extraField->name.'</strong><br /><input type="text" name="K2ExtraField_'.$extraField->id.'" value="'.htmlspecialchars($active).'"/></div><br /><br />';
                break;

        }

        return $output;

    }
}