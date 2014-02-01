<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//JHtml::_('bootstrap.tooltip');


echo '<h2>'.JText::_('SKAUTIS_APP_CONFIG').'</h2>';

echo '<form method="POST">
            <input type="hidden" name="save" value="ok" />
            <table>
              <tr>
                <td><label for="skautis_url">'.JText::_('SKAUTIS_URL').'</label></td>
                <td>
                  <input type="text" name="skautis_url" id="skautis_url" value="'.$this->configModel->getConfig('SKAUTIS_URL').'" />
                </td>
              </tr>
              <tr>
                <td><label for="skautis_app_id">'.JText::_('SKAUTIS_APP_ID').'</label></td>
                <td>
                  <input type="text" name="skautis_app_id" id="skautis_app_id" value="'.$this->configModel->getConfig('SKAUTIS_APP_ID').'" />
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <input type="submit" value="'.JText::_('SAVE_CONFIG').'" />
                  <input type="reset" value="'.JText::_('RESET').'" />
                </td>
              </tr>
            </table>
          </form>';
echo '<h3>'.JText::_('SKAUTIS_RETURN_URLS').'</h3>
      <table>
        <tr>
          <td style="padding-right:30px;">
            '.JText::_('SKAUTIS_LOGIN_URL').'
          </td>
          <td>
            '.JURI::root(false).'<strong>index.php?option=com_skautis&task=loginOK</strong>
          </td>
        </tr>
        <tr>
          <td>
            '.JText::_('SKAUTIS_LOGOUT_URL').'
          </td>
          <td>
            '.JURI::root(false).'<strong>index.php?option=com_skautis&task=logoutOK</strong>
          </td>
        </tr>
      </table>
    <h3 style="margin-top:30px;">'.JText::_('USED_SKAUTIS_FUNCTIONS').'</h3>
    <p style="margin:20px;font-style:italic;">
    OrganizationUnit: UnitDetail, UnitContactAll, AccountAll, FunctionAll, FunctionAllRegistry, UnitTreeAll, PersonDetail
    UserManagement: UserDetail</p>';
?>

