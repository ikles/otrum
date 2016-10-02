<?php
/**
 * Sj K2 Filter plugin
 *
 * @package        Sj K2 Filter plugin
 * @author Gruz <arygroup@gmail.com>
 * @copyright    Copyleft - All rights reversed
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
if (!defined('DS')) { // Do this because some extensions still use DS, i.e. com_adsmanager
    define('DS', DIRECTORY_SEPARATOR);
}
if (JComponentHelper::isEnabled('com_k2', true)) {
    class plgSystemPlg_Sj_K2_Filter extends JPlugin
    {

        public function __construct(& $subject, $config)
        {
            //return;
            parent::__construct($subject, $config);
            $jinput = &JFactory::getApplication()->input;
            if ($jinput->get('option', null) == 'com_dump') {
                return;
            }
        }

        /**
         * Get's current $option as it's not defined at onAfterInitialize
         *
         * @author Gruz <arygroup@gmail.com>
         * @return    string            Option, i.e. com_content
         */
        function getOption()
        {
            return 'com_k2';
        }

        /**
         * Override MVC
         */
        public function onAfterRoute()
        {
            $option = $this->getOption();
            //constants to replace JPATH_COMPONENT, JPATH_COMPONENT_SITE and JPATH_COMPONENT_ADMINISTRATOR
            define('JPATH_SOURCE_COMPONENT', JPATH_BASE . '/components/' . $option);
            define('JPATH_SOURCE_COMPONENT_SITE', JPATH_SITE . '/components/' . $option);
            define('JPATH_SOURCE_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/' . $option);
            jimport('joomla.filesystem.file');
            jimport('joomla.filesystem.folder');


            // Fallback to Julio's method for backwards compatibility
            // This automatically searches and matches files in the code directory
            // without having to configure directly in the plugin params
            $this->_autoOverride();

        }


        private function _autoOverride()
        {
            $option = $this->getOption();

            //get files that can be overrided
            $componentOverrideFiles = $this->loadComponentFiles($option);
            //application name
            $applicationName = JFactory::getApplication()->getName();
            //template name
            $template = JFactory::getApplication()->getTemplate();

            //code paths
            $includePath = array();
            //template code path
//             $includePath[] = JPATH_THEMES.'/'.$template.'/code';
            //base extensions path
            $includePath[] = dirname(__FILE__) . '/code';
            //administrator extensions path
            //  $includePath[] = JPATH_ADMINISTRATOR.'/code';

            //loading override files
            if (!empty($componentOverrideFiles)) {
                foreach ($componentOverrideFiles as $componentFile) {
                    if ($filePath = JPath::find($includePath, $componentFile)) {
                        //include the original code and replace class name add a Default on
                        $originalFilePath = JPATH_BASE . '/components/' . $componentFile;
                        $bufferFile = JFile::read($originalFilePath);

                        //detect if source file use some constants
                        preg_match_all('/JPATH_COMPONENT(_SITE|_ADMINISTRATOR)|JPATH_COMPONENT/i', $bufferFile, $definesSource);

                        $bufferOverrideFile = JFile::read($filePath);

                        //detect if override file use some constants
                        preg_match_all('/JPATH_COMPONENT(_SITE|_ADMINISTRATOR)|JPATH_COMPONENT/i', $bufferOverrideFile, $definesSourceOverride);

                        // Append "Default" to the class name (ex. ClassNameDefault). We insert the new class name into the original regex match to get
                        $rx = '/class *[a-z0-9]* *(extends|{)/i';

                        preg_match($rx, $bufferFile, $classes);

                        if (empty($classes)) {
                            $rx = '/class *[a-z0-9]*/i';
                            preg_match($rx, $bufferFile, $classes);
                        }

                        $parts = explode(' ', $classes[0]);

                        $originalClass = $parts[1];

                        $replaceClass = $originalClass . 'Default';
                        if (count($definesSourceOverride[0])) {
                            JError::raiseError('Plugin SJ k2 filter', 'Your override file use constants, please replace code constants<br />JPATH_COMPONENT -> JPATH_SOURCE_COMPONENT,<br />JPATH_COMPONENT_SITE -> JPATH_SOURCE_COMPONENT_SITE and<br />JPATH_COMPONENT_ADMINISTRATOR -> JPATH_SOURCE_COMPONENT_ADMINISTRATOR');
                        } else {
                            //replace original class name by default
                            $bufferContent = str_replace($originalClass, $replaceClass, $bufferFile);

                            //replace JPATH_COMPONENT constants if found, because we are loading before define these constants
                            if (count($definesSource[0])) {
                                $bufferContent = preg_replace(array('/JPATH_COMPONENT/', '/JPATH_COMPONENT_SITE/', '/JPATH_COMPONENT_ADMINISTRATOR/'), array('JPATH_SOURCE_COMPONENT', 'JPATH_SOURCE_COMPONENT_SITE', 'JPATH_SOURCE_COMPONENT_ADMINISTRATOR'), $bufferContent);
                            }

                            // Finally we can load the base class
                            eval('?>' . $bufferContent . PHP_EOL . '?>');
                            require $filePath;

                        }

                    }
                }
            }
        }

        /**
         * loadComponentFiles function.
         *
         * @access private
         * @param mixed $option
         * @return void
         */
        private function loadComponentFiles($option)
        {
            $JPATH_COMPONENT = JPATH_BASE . '/components/' . $option;
            $files = array();

            //check if default controller exists
            if (JFile::exists($JPATH_COMPONENT . '/controller.php')) {
                $files[] = $JPATH_COMPONENT . '/controller.php';
            }

            //check if controllers folder exists
            if (JFolder::exists($JPATH_COMPONENT . '/controllers')) {
                $controllers = JFolder::files($JPATH_COMPONENT . '/controllers', '.php', false, true);
                $files = array_merge($files, $controllers);
            }

            //check if models folder exists
            if (JFolder::exists($JPATH_COMPONENT . '/models')) {
                $models = JFolder::files($JPATH_COMPONENT . '/models', '.php', false, true);
                $files = array_merge($files, $models);
            }

            //check if views folder exists
            if (JFolder::exists($JPATH_COMPONENT . '/views')) {
                //reading view folders
                $views = JFolder::folders($JPATH_COMPONENT . '/views');
                foreach ($views as $view) {
                    //get view formats files
                    $viewsFiles = JFolder::files($JPATH_COMPONENT . '/views/' . $view, '.php', false, true);
                    $files = array_merge($files, $viewsFiles);
                }
            }

            //check if helpers folder exists
            $foldername = 'helpers';
            //check if models folder exists
            if (JFolder::exists($JPATH_COMPONENT . '/' . $foldername)) {
                $models = JFolder::files($JPATH_COMPONENT . '/' . $foldername, '.php', false, true);
                $files = array_merge($files, $models);
            }


            $return = array();
            //cleaning files
            foreach ($files as $file) {
                $file = JPath::clean($file);
                $file = substr($file, strlen(JPATH_BASE . '/components/'));
                $return[] = $file;
            }

            return $return;
        }
    }
}else{
    echo JText::_('Component k2 does not exist');
}
