<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>

<?php
  echo '<div class="skautisLoginDiv '.$params->get('TEXT_ALIGN').'">';
  if ($type=='logout'){
    ModSkautisLoginHelper::renewSkautisToken($params->get('SKAUTIS_URL'));
    $user=& JFactory::getUser();
    $session = JFactory::getSession();

    $photoUrl=$session->get('userPhoto','','skautIs');
    $photoSize=$params->get('PHOTO_SIZE');
    if (($photoSize!='hidden')&&(!empty($photoUrl))){
      echo '<img src="'.$photoUrl.'" alt="" class="photoImg '.$photoSize.' '.$params->get('PHOTO_POSITION').'" />';
    }
    if ($params->get('TEXT_ROWS')=='one'){
      echo '<strong>'.$user->name.'</strong> -&nbsp;';
    }else{
      echo '<div><strong>'.$user->name.'</strong></div>';
    }

    echo '<a href="'.JRoute::_('index.php?option=com_skautis&task=logout').'">'.JText::_('MOD_SKAUTIS_LOGOUT_LINK').'</a>';
  }else{
    echo '<a href="'.JRoute::_('index.php?option=com_skautis&task=loginIS').'">'.JText::_('MOD_SKAUTIS_LOGIN_LINK').'</a>';
  }
  echo '</div>';
?>
