<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 */
class plgSystemFalangextraparams extends JPlugin
{

    public function onContentPrepareForm($form, $data)
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;

        //test rockettheme : rokextender
        $rokextender_enabled = JPluginHelper::isEnabled('system', 'rokextender');

        if ($rokextender_enabled) {
            $this->loadRokextender($form, $data);
        }
        $hgassets_enabled = JPluginHelper::isEnabled('system', 'hg_assets');
        if ($hgassets_enabled) {
            $this->loadHgassets($form, $data);
        }
        $t3_enabled = JPluginHelper::isEnabled('system', 't3');
        if ($t3_enabled){
            $this->loadT3($form, $data);
        }
    }

    public function onAfterDispatch() {
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;

        $option = JFactory::getApplication()->input->get('option');
        $task = JFactory::getApplication()->input->get('task');
        $catid = JFactory::getApplication()->input->get('catid');

        $hgassets_enabled = JPluginHelper::isEnabled('system', 'hg_assets');
        if ($hgassets_enabled) {
            if ($option == 'com_falang' && $catid =='content' && ($task == 'translate.edit' || ($task == 'translate.apply') )){
                JFactory::getDocument()->addScript(JURI::root(true) . '/plugins/system/hg_assets/assets/js/hgscript.js');
            }
        }

    }

    /*
     * copy adapted to falang from /modules/mod_roknavmenu/lib/RokNavMenuEvents.php
     * */
    private function loadRokextender($form, $data) {
        $option = JFactory::getApplication()->input->get('option');
        $task = JFactory::getApplication()->input->get('task');
        require_once(JPATH_ROOT . "/modules/mod_roknavmenu/lib/RokNavMenuEvents.php");

        //$rokNavMenuEvents = RokNavMenuEvents();
        if ($option == 'com_falang' && ($task == 'translate.edit' || ($task == 'translate.apply') )){
            JForm::addFieldPath(JPATH_ROOT . '/modules/mod_roknavmenu/fields');
            // Load 2x Catalog Themes
            require_once(JPATH_ROOT . "/modules/mod_roknavmenu/lib/RokNavMenu.php");
            RokNavMenu::loadCatalogs();
            // Load 1x Themes
            //$rokNavMenuEvents->registerOldThemes();
            foreach (RokNavMenu::$themes as $theme_name => $theme_info)
            {
                $item_file = $theme_info['path'] . "/item.xml";
                if (JFile::exists($item_file))
                {
                    $form->loadFile($item_file, true);
                }

                $fields_folder = $theme_info['path'] . "/fields";
                if (JFolder::exists($fields_folder))
                {
                    JForm::addFieldPath($fields_folder);
                }
            }

        }
    }

    /*
     * copy adapted to falang from /plugins/system/hg_assets/hg_assets.php
     * */
    private function loadHgassets($form, $data) {
        $template = $this->params->get('hogastemplatename', 'ekho');

        $option = JFactory::getApplication()->input->get('option');
        $task = JFactory::getApplication()->input->get('task');
        $catid = JFactory::getApplication()->input->get('catid');


        if ($option == 'com_falang' && $catid =='content' && ($task == 'translate.edit' || ($task == 'translate.apply') )){
            if (JFile::exists(JPATH_SITE.'/templates/'.$template.'/html/com_content/fields/article.xml')) {
                $form->loadFile(JPATH_SITE . '/templates/' . $template . '/html/com_content/fields/article.xml', false);
            }
            //v1.4 new directory for xml params, no more template in path
            if (JFile::exists(JPATH_SITE.'/plugins/system/hg_assets/assets/form/article.xml')) {
                $form->loadFile(JPATH_SITE . '/plugins/system/hg_assets/assets/form/article.xml', false);
            }
        }

        if ($option == 'com_falang' && $catid =='modules' && ($task == 'translate.edit' || ($task == 'translate.apply') )){
            if (JFile::exists(JPATH_SITE.'/plugins/system/hg_assets/assets/fields/module.xml')) {
                $form->loadFile(JPATH_SITE . '/plugins/system/hg_assets/assets/fields/module.xml', false);
            }
            //v1.4 fields > form
            if (JFile::exists(JPATH_SITE.'/plugins/system/hg_assets/assets/form/module.xml')) {
                $form->loadFile(JPATH_SITE . '/plugins/system/hg_assets/assets/form/module.xml', false);
            }
        }

        //new v1.04 menu
        if ($option == 'com_falang' && $catid =='menu' && ($task == 'translate.edit' || ($task == 'translate.apply') )){
            if (JFile::exists(JPATH_SITE.'/plugins/system/hg_assets/assets/form/pageheader_menus.xml')) {
                $form->loadFile(JPATH_SITE . '/plugins/system/hg_assets/assets/form/pageheader_menus.xml', false);
            }
        }

    }


    private function loadT3($form, $data)
    {
        $option = JFactory::getApplication()->input->get('option');
        $task = JFactory::getApplication()->input->get('task');
        $catid = JFactory::getApplication()->input->get('catid');

        if ($option == 'com_falang' && $catid =='content' && ($task == 'translate.edit' || ($task == 'translate.apply') )){

            $tmpl = T3::detect() ? T3::detect() : (T3::getDefaultTemplate(true) ? T3::getDefaultTemplate(true) : false);

            if ($tmpl) {
                $tplpath = JPATH_ROOT . '/templates/' . (is_object($tmpl) && !empty($tmpl->tplname) ? $tmpl->tplname : $tmpl);
                $formpath = $tplpath . '/etc/form/';
                JForm::addFormPath($formpath);

                $extended = $formpath . $form->getName() . '.xml';
                if (is_file($extended)) {
                    JFactory::getLanguage()->load('tpl_' . $tmpl, JPATH_SITE);
                    $form->loadFile($form->getName(), false);
                }

                jimport('joomla.filesystem.folder');
                jimport('joomla.filesystem.file');

                // check for extrafields overwrite
                $path = $tplpath . '/etc/extrafields';
                if (!is_dir($path)) return;

                $files = JFolder::files($path, '.xml');
                if (!$files || !count($files)) {
                    return;
                }

                $extras = array();
                foreach ($files as $file) {
                    $extras[] = JFile::stripExt($file);
                }
                if (count($extras)) {


                    $app = JFactory::getApplication();
                    $input = $app->input;

                    $db = JFactory::getDbo();
                    $db->setQuery('select catid from #__content where id=' . $input->getInt('article_id'));
                    $catid = $db->loadResult();

                    if ($catid) {

                        if (version_compare(JVERSION, '3.0', 'lt')) {
                            jimport('joomla.application.categories');
                        }

                        $categories = JCategories::getInstance('Content', array('countItems' => 0));
                        $category = $categories->get($catid);
                        $params = $category->params;
                        if (!$params instanceof JRegistry) {
                            $params = new JRegistry;
                            $params->loadString($category->params);
                        }

                        if ($params instanceof JRegistry) {
                            $extrafile = $path . '/' . $params->get('t3_extrafields') . '.xml';
                            if (is_file($extrafile)) {
                                JForm::addFormPath($path);
                                $form->loadFile($params->get('t3_extrafields'), false);
                            }
                        }
                    }//if catid
                }
            }

        }

    }

}