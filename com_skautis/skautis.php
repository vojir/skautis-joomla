<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Skautis');
                                              
// Perform the Request task
$controller->execute(JRequest::getCmd('task',JRequest::getCmd('view','index')));
                                                                              
// Redirect if set by the controller
$controller->redirect();
