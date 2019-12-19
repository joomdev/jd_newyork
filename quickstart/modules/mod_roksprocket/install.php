<?php
/**
 * @package   Gantry
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2017 RocketTheme, LLC
 * @license   GNU/GPLv2 and later
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die;

/**
 * Gantry package installer script.
 */
class Mod_RokSprocketInstallerScript
{
    public function postflight($type, $parent)
    {
        if ($type == 'install') {
            $this->removeModuleInstances('mod_roksprocket');
        }

        return true;
    }

    protected function removeModuleInstances($module_name)
    {
        $db = JFactory::getDbo();

        // Lets delete all the module copies for the type we are uninstalling
        $query = 'SELECT `id`' .
            ' FROM `#__modules`' .
            ' WHERE module = ' . $db->quote($module_name);
        $db->setQuery($query);

        try
        {
            $modules = $db->loadColumn();
        }
        catch (Exception $e)
        {
            $modules = array();
        }

        // Do we have any module copies?
        if (count($modules))
        {
            // Ensure the list is sane
            JArrayHelper::toInteger($modules);
            $modID = implode(',', $modules);

            // Wipe out any items assigned to menus
            $query = 'DELETE' .
                ' FROM #__modules_menu' .
                ' WHERE moduleid IN (' . $modID . ')';
            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JError::raiseWarning(100, JText::sprintf('JLIB_INSTALLER_ERROR_MOD_UNINSTALL_EXCEPTION', $db->stderr(true)));
            }

            // Wipe out any instances in the modules table
            $query = 'DELETE' .
                ' FROM #__modules' .
                ' WHERE id IN (' . $modID . ')';
            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JError::raiseWarning(100, JText::sprintf('JLIB_INSTALLER_ERROR_MOD_UNINSTALL_EXCEPTION', $db->stderr(true)));
            }
        }
    }
}
