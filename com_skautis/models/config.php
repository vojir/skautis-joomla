<?php
  jimport( 'joomla.application.component.model' );
              
                     
  class SkautisModelConfig extends JModelLegacy{
    
    /**
     *  Funkce vracející hodnotu nastavení
     */         
    public function getConfig($configName){
      $db=$this->getDBO();
      $db->setQuery('SELECT value FROM #__skautis_config WHERE `name`='.$db->quote($configName).' LIMIT 1;');
      $object=$db->loadObject();
      return @$object->value;
    }
    
    /**
     * Funkce ukládající hodnotu nastavení
     */
    public function setConfig($configName,$value){
      $db=$this->getDBO();
      $db->setQuery('UPDATE #__skautis_config SET `value`='.$db->quote($configName).' WHERE `name`='.$db->quote($value).' LIMIT 1;');
      return $db->query();
    }
    
  }
?>
