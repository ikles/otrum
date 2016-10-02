<?php
/**
 * @package     Falang for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2014 Faboba. All rights reserved.
 */


// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgSystemFalangmissing extends JPlugin
{

    function plgSystemFalangmissing(&$subject, $config)
    {
        parent::__construct($subject, $config);
        //load plugin language
        JPlugin::loadLanguage( 'plg_system_falangmissing', JPATH_ADMINISTRATOR );
    }

    /**
     * Works out what to show if the translation is missing
     *
     * @param object $row_to_translate
     * @param string $language
     * @param string $reference_table
     */
    function onMissingTranslation(&$row_to_translate, $language, $reference_table, $tableArray){

        //0:Original content
		//1:Placeholder
		//2:Original with info
		//3:Original with alt

        $noTranslationBehaviour = 2;

        $default_language =JComponentHelper::getParams('com_languages')->get('site','en-GB');
        if ($noTranslationBehaviour >= 1 && ($reference_table == 'content' || $reference_table == 'k2_items' ) && $default_language != $language) {

            $defaultText = '<div class="falang-missing">' .JText::_($this->params->get('missing-text')). '</div>';

            if ($this->params->get('bootstrap-alert')) {
                $defaultText = '<div class="falang-missing alert '.$this->params->get('bootstrap-alert-style').' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' .JText::_($this->params->get('missing-text')). '</div>';
            }

            if ($noTranslationBehaviour==3 && isset($row_to_translate->id)){
                $defaultText="{jfalternative}".$row_to_translate->id."|content|$defaultText{/jfalternative}";
            }


            $cache = JFactory::getCache();
//            $fieldInfo = $cache->call("Falang::contentElementFields",$reference_table, $language);
            $fieldInfo = $cache->call(array("Falang","contentElementFields"),$reference_table, $language);

            $textFields = $fieldInfo["textFields"];
            if( $textFields !== null ) {
                $defaultSet = false;
                foreach ($textFields as $field) {
                    if( !$defaultSet && $fieldInfo["fieldTypes"][$field]=="htmltext") {
                        if ($noTranslationBehaviour==1)	{
                            $row_to_translate->$field = $defaultText;
                        } else if ($noTranslationBehaviour>=2) {
                            $cr="<br/>";
                            $row_to_translate->$field = $defaultText .$cr.(isset($row_to_translate->$field)?$row_to_translate->$field:"");
                        }
                        $defaultSet = true;
                    } else {
                        if ($noTranslationBehaviour==1)	{
                            $row_to_translate->$field = "";
                        } else if ($noTranslationBehaviour>=2) {
                            if ($fieldInfo["fieldTypes"][$field]=="htmltext"){
                                $cr="<br/>";
                            } else {
                                $cr="\n";
                            }
                            $row_to_translate->$field = (isset($row_to_translate->$field)?$row_to_translate->$field:"");
                    }
                }
                }
            }
    }
    }
}