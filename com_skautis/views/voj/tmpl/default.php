<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

//vypsání obsahu detailu jednotky

  echo '<h1>'.ucfirst($this->unitDetail->DisplayName).'</h1>';
  
  /*základní informace o jednotce*/
  $postalAddressArr=array();
  if (trim($this->unitDetail->PostalFirstLine)!=''){
    $postalAddressArr[]=$this->unitDetail->PostalFirstLine;
  }
  if (trim($this->unitDetail->PostalStreet)!=''){
    $postalAddressArr[]=$this->unitDetail->PostalStreet;
  }
  if (trim($this->unitDetail->PostalCity)!=''){
    $postalAddressArr[]=$this->unitDetail->PostalCity;
  }
  if (trim($this->unitDetail->PostalPostcode)!=''){
    $postalAddressArr[]=$this->unitDetail->PostalPostcode;
  }
  echo '<table class="basicTable">
          <tr>
            <td class="labelTd">'.JText::_('REGISTRATION_NUMBER').'</td>
            <td><strong>'.$this->unitDetail->RegistrationNumber.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('IC').'</td>
            <td><strong>'.$this->unitDetail->IC.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('DIC').'</td>
            <td><strong>'.$this->unitDetail->DIC.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('ADDRESS').'</td>
            <td>
              <strong>'.implode(', ',array($this->unitDetail->Street,$this->unitDetail->City,$this->unitDetail->Postcode)).'
              </strong>
            </td>
          </tr>';
  if (count($postalAddressArr)>0){
    echo '<tr>
            <td>'.JText::_('POSTAL_ADDRESS').'</td>
            <td>
              <strong>'.implode(', ',$postalAddressArr).'
              </strong>
            </td>
          </tr>';
  }
  echo '</table>';
  /*--základní informace o jednotce*/

  /*TOP TEXT*/
  if ($this->topText){
    echo '<div class="topText">'.$this->topText.'</div>';
  }
  /*--TOP TEXT*/

  /*Informace o kontaktech na jednotku*/
  if ($this->unitContactAll){
    //připravíme si kontakty
    if (count($this->unitContactAll->UnitContactAllOutput)>0){
      echo '<h3>'.JText::_('UNIT_CONTACTS').'</h3>';
      $emailHlavni='';
      $emaily=array();
      $telefonHlavni='';
      $telefony=array();
      $weby=array();
      $ostatni=array();
      $contactsArr=array();
      if (!is_array($this->unitContactAll->UnitContactAllOutput)){
        $this->unitContactAll->UnitContactAllOutput=array($this->unitContactAll->UnitContactAllOutput);
      } 
      
      foreach ($this->unitContactAll->UnitContactAllOutput as $unitContact){
        if ($unitContact->ID_ContactType=='email_hlavni'){
          $emailHlavni=array('value'=>$unitContact->Value,'note'=>$unitContact->Note,'icon'=>$unitContact->Icon);
        }elseif ($unitContact->ID_ContactType=='email'){
          $emaily[]=array('value'=>$unitContact->Value,'note'=>$unitContact->Note,'icon'=>$unitContact->Icon);
        }elseif ($unitContact->ID_ContactType=='web'){
          $weby[]=array('value'=>$unitContact->Value,'note'=>$unitContact->Note,'icon'=>$unitContact->Icon);
        }elseif ($unitContact->ID_ContactType=='telefon_hlavni'){
          $telefonHlavni=array('value'=>$this->formatPhoneNumber($unitContact->DisplayValue),'note'=>$unitContact->Note,'icon'=>$unitContact->Icon);
        }elseif (($unitContact->ID_ContactType=='mobil')||($unitContact->ID_ContactType=='telefon_jinam')){
          $telefony[]=array('value'=>$unitContact->DisplayValue,'note'=>$unitContact->Note,'icon'=>$unitContact->Icon);
        }else{
          if (isset($unitContact->DisplayIcon)){
            $ostatni[]=array('value'=>$unitContact->DisplayValue,'note'=>$unitContact->Note,'displayIcon'=>$unitContact->DisplayIcon);
          }else{
            $ostatni[]=array('value'=>$unitContact->DisplayValue,'note'=>$unitContact->Note,'icon'=>$unitContact->Icon);
          }
        }      	
      }
      
      if ($emailHlavni||(count($emaily)>0)){
        echo '<h4>'.JText::_('EMAILS').'</h4>';
        echo '<table class="unitContactsTable">';
        if ($emailHlavni){
          echo '<tr>
                  <td class="iconTd"><img src="'.$this->iconSrc($emailHlavni['icon']).'" alt="" /></td>
                  <td><a href="mailto:'.$emailHlavni['value'].'"><strong>'.$emailHlavni['value'].'</strong></a></td>
                  <td class="noteTd">'.$emailHlavni['note'].'</td>
                </tr>';
        }
        if (count($emaily)>0){
          foreach ($emaily as $email){
            echo '<tr>
                    <td class="iconTd"><img src="'.$this->iconSrc($email['icon']).'" alt="" /></td>
                    <td><a href="mailto:'.$email['value'].'">'.$email['value'].'</a></td>
                    <td class="noteTd">'.$email['note'].'</td>
                  </tr>';	
          }
        }
        echo '</table>';      
      }
      
      if ($telefonHlavni||(count($telefony)>0)){
        echo '<h4>'.JText::_('TELEPHONES').'</h4>';
        echo '<table class="unitContactsTable">';
        if ($telefonHlavni){
          echo '<tr>
                  <td class="iconTd"><img src="'.$this->iconSrc($telefonHlavni['icon']).'" alt="" /></td>
                  <td><strong>'.$telefonHlavni['value'].'</strong></td>
                  <td class="noteTd">'.$telefonHlavni['note'].'</td>
                </tr>';
        }
        if (count($telefony)>0){
          foreach ($telefony as $telefon){
            echo '<tr>
                    <td class="iconTd"><img src="'.$this->iconSrc($telefon['icon']).'" alt="" /></td>
                    <td>'.$telefon['value'].'</td>
                    <td class="noteTd">'.$telefon['note'].'</td>
                  </tr>';	
          }
        }
        echo '</table>';      
      }
      
      if (count($weby)>0){
        echo '<h4>'.JText::_('WWW').'</h4>';
        echo '<table class="unitContactsTable">';
        foreach ($weby as $web){
        	echo '<tr>
                  <td class="iconTd"><img src="'.$this->iconSrc($web['icon']).'" alt="" /></td>
                  <td><a href="'.$web['value'].'">'.$web['value'].'</a></td>
                  <td class="noteTd">'.$web['note'].'</td>
                </tr>';
        }
        echo '</table>';
      }
             
      if (count($ostatni)>0){
        echo '<h4>'.JText::_('OTHER_CONTACTS').'</h4>';
        echo '<table class="unitContactsTable">';
        foreach ($ostatni as $kontakt){
        	echo '<tr>
                  <td class="iconTd">'.($kontakt['displayIcon']?$kontakt['displayIcon']:'<img src="'.$this->iconSrc($kontakt['icon']).'" alt="" />').'</td>
                  <td>'.$kontakt['value'].'</td>
                  <td class="noteTd">'.$kontakt['note'].'</td>
                </tr>';
        }
        echo '</table>';
      } 
    }
  }
  /*--Informace o kontaktech na jednotku*/
  
  /*Informace o bankovních účtech*/
  if (($this->accountAll)&&(count($this->accountAll->AccountAllOutput)>0)){
    $accountsArr=array();
    echo '<h3>'.JText::_('BANK_ACCOUNTS').'</h3>';
    echo '<table class="bankAccountsTable">';
    if (!isset($this->accountAll->AccountAllOutput->DisplayName)){
      //možná máme víc účtů
      if (count($this->accountAll->AccountAllOutput)>0){
        foreach($this->accountAll->AccountAllOutput as $account){
          $accountsArr[]=$account;
        }
      }
    }else{
      if (isset($this->accountAll->AccountAllOutput)){
        $accountsArr[]=$this->accountAll->AccountAllOutput;
      }
    }
    foreach ($accountsArr as $account) {  
    	echo '<tr>
              <td class="iconTd"><img src="'.$this->iconSrc('banka.png').'" alt="" /></td>';
              echo '
              <td class="nameTd">'.($account->IsMain?'<strong>'.$account->DisplayName.'</strong>':$account->DisplayName).'</td>
              <td class="bankTd">'.$account->Bank.'</td>
              <td class="noteTd">'.$account->Note.'</td>';
              echo '
            </tr>';        
    }
    echo '</table>';
  }
  /*--Informace o bankovních účtech*/
  
  /*Informace o obsazení funkcí*/  
  if ($this->functionAllRegistry){
    echo '<h3>'.JText::_('UNIT_LEADERS').'</h3>';
    echo '<table class="leadersTable">';
    if (isset($this->functionAllRegistry->Statutory->FunctionAllRegistryOutput->Person)){
      echo '<tr>
              <td class="functionTd">'.JText::_('PERSON_STATUTORY').'</td>
              <td class="personTd">'.$this->functionAllRegistry->Statutory->FunctionAllRegistryOutput->Person.'</td>
            </tr>';
    }
    if (isset($this->functionAllRegistry->Assistant->FunctionAllRegistryOutput->Person)){
      echo '<tr>
              <td class="functionTd">'.JText::_('PERSON_ASSISTANT').'</td>
              <td class="personTd">'.$this->functionAllRegistry->Assistant->FunctionAllRegistryOutput->Person.'</td>
            </tr>';
    }
    if (isset($this->functionAllRegistry->Contact->Person)){
      echo '<tr>
              <td class="functionTd">'.JText::_('PERSON_CONTACT').'</td>
              <td class="personTd">'.$this->functionAllRegistry->Contact->FunctionAllRegistryOutput->Person.'</td>
            </tr>';
    }
    echo '</table>';
  }elseif(($this->functionAll)&&(count($this->functionAll->FunctionAllOutput)>0)){
    echo '<h3>'.JText::_('UNIT_LEADERS').'</h3>';
    echo '<table class="leadersTable">';
    foreach ($this->functionAll->FunctionAllOutput as $function) {
      echo '<tr>
              <td class="functionTd">'.$function->FunctionType.'</td>
              <td class="personTd">'.$function->Person.'</td>
            </tr>';	
    }
    
    echo '</table>';
  }
  /*--Informace o obsazení funkcí*/
  
  /*Informace o podřízených jednotkách*/
  if (($this->unitTreeAll)&&(count($this->unitTreeAll->UnitTreeAllOutput)>0)){
    echo '<h3>'.JText::_('CHILDREN_UNITS').'</h3>';
    echo '<ul>';
    foreach ($this->unitTreeAll->UnitTreeAllOutput as $unit){
    	echo '<li>'.$unit->SortName.'</li>';
    }
    echo '</ul>';
  }    
  /*--Informace o podřízených jednotkách*/
  
  /*BOTTOM TEXT*/
  if ($this->topText){
    echo '<div class="bottomText">'.$this->bottomText.'</div>';
  }
  /*--BOTTOM TEXT*/
?>