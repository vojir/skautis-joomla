<?php
  // No direct access to this file
  defined('_JEXEC') or die('Restricted access');

  // import Joomla controller library
  jimport('joomla.application.component.controller');

  /**
   * Hello World Component Controller
   * @property $configModel SkautisModelConfig
   */
class SkautisController extends JControllerLegacy
{
  var $document;
  var $configModel;


  public function index(){
    if (isset($_POST['save'])&&($_POST['save']=='ok')){
      $this->configModel->setConfig('SKAUTIS_URL',trim($_POST['skautis_url']));
      $this->configModel->setConfig('SKAUTIS_APP_ID',trim($_POST['skautis_app_id']));
      JFactory::getApplication()->enqueueMessage(JText::_('CONFIG_SAVED'));
    }

    $view=$this->getView('Config','html');
    $view->assignRef('configModel',$this->configModel);
    $view->display();

  }



  /**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{                                        
		parent::__construct( $default );
		$this->document = JFactory::getDocument();
    /** @var $configModel SkautisModelConfig */
    $this->configModel= $this->getModel('Config','SkautisModel');
	}
}
