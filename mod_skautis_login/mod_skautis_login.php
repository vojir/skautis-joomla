<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
            
// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

//$params->def('greeting', 1);

$type	= ModSkautisLoginHelper::getType();
$return	= ModSkautisLoginHelper::getReturnURL($params, $type);
$user	= JFactory::getUser();

$document = JFactory::getDocument();
$document->addStyleSheet('/media/mod_skautis_login/style.css');

require JModuleHelper::getLayoutPath('mod_skautis_login', $params->get('layout', 'default'));
