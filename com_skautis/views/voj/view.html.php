<?php

jimport( 'joomla.application.component.view');

class SkautisViewVoj extends JViewLegacy
{
	function display($tpl = null)
	{
    $document = JFactory::getDocument();
    $document->addStyleSheet('/media/com_skautis/css/style.css');
    
		$params = $this->get('Params');
		$this->assignRef( 'params', $params );
		parent::display($tpl);
	}

  function formatPhoneNumber($value){
    $value=str_replace(' ','',$value);
    return substr($value,0,4).' '.substr($value,4,3).' '.substr($value,7,3).' '.substr($value,10,3);
  }
  
  function iconSrc($src){
    if ($src!=''){
      return '/media/com_skautis/icons/'.$src;
    }else{
      return '/media/com_skautis/icons/none.png';
    }
  }

}

?>
