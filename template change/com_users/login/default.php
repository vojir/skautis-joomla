<?php
/**
 * Upravené view pro skrytí výchozího přihlašovacího formuláře
 */

defined('_JEXEC') or die;


$loginText='Požadovaná stránka je dostupná pouze přihlášeným uživatelům.';
$loginLinkText='Přihlásit přes SkautIS!';
$noAuthText='Nemáte právo zobrazit tuto stránku.';

$cookieLogin = $this->user->get('cookieLogin');


                      
if ($this->user->get('guest') || !empty($cookieLogin))
{
  echo '<p>'.$loginText.'</p>';
  echo '<p><a href="'.JRoute::_('index.php?option=com_skautis&task=loginIS').'">'.$loginLinkText.'</p>';
}
else
{
	echo '<p>'.$noAuthText.'</p>';
}       
