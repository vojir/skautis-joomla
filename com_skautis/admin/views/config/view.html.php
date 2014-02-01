<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of plugins.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 * @since       1.5
 */
class SkautisViewConfig extends JViewLegacy{

  /**
   * Display the view
   */
  public function display($tpl = null){

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    $this->addToolbar();
    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @since   1.6
   */
  protected function addToolbar(){

    JToolbarHelper::title(JText::_('COM_SKAUTIS'), 'power-cord plugin');
    $this->sidebar = JHtmlSidebar::render();

  }

}
