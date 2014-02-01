<?php
  jimport( 'joomla.application.component.model' );
              
                     
  class SkautisModelUsers extends JModelLegacy{
    const PHOTOS_PATH='./media/com_skautis/persons';
    const PHOTOS_PATH_WEB='/media/com_skautis/persons';
  	
    /**
     * Funkce pro kontrolu, jestli existuje uživatel se zadaným uživatelským jménem
     * @param int $skautISUserId - ID uživatele v rámci skautISu
     * @param string $username - uživatelské jméno v rámci skautISu
     * @return bool
     */
    public function userExists($skautISUserId,$username){
      $db=$this->getDBO();
      $db->setQuery('SELECT * FROM #__skautis_users WHERE id_skautis_user='.$db->quote($skautISUserId).' LIMIT 1;');
      $skautISUsersRow=$db->loadObject();
      $db->setQuery('SELECT id FROM #__users WHERE username='.$db->quote($username).' LIMIT 1;');
      $usersRow=$db->loadObject();
      if ($skautISUsersRow&&$usersRow){
        return true;
      }else{
        return false;
      }
    }
    
    /**
     *  Funkce pro kontrolu, jestli je možné použít e-mail k registraci
     */         
    private function checkEmail($email,$ignoreUserId=0){
      if(!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/',$email)){
        return false;
      } 
      $db=$this->getDBO();
      $db->setQuery('SELECT id FROM #__users WHERE email='.$db->quote($email).(($ignoreUserId>0)?' AND id!='.$db->quote($ignoreUserId):'').' LIMIT 1;');
      if ($db->loadObject()){
        return false;
      }else{
        return true;
      }
    }
    
    /**
     *  Funkce pro uložení fotky přiřazené ke konkrétní osobě
     */         
    public function updateSkautisUserPhoto($idPerson,$skautisPersonPhoto,$size){
      $extension=strtolower($skautisPersonPhoto->PhotoExtension);
      if ($extension=='jpg'){
        $extension='jpeg';
      }elseif(($extension!='jpeg')&&($extension!='png')){
        return false;
      }
      $db=$this->getDBO();
      $db->setQuery('UPDATE #__skautis_users SET photo_extension='.$db->quote($extension).' WHERE id_person='.$db->quote($idPerson).';');
      $db->query();
      if(file_put_contents(self::PHOTOS_PATH.'/person'.$idPerson.'_'.$size.'.'.$extension,$skautisPersonPhoto->PhotoContent)!==false){
        return self::PHOTOS_PATH_WEB.'/person'.$idPerson.'_'.$size.'.'.$extension; 
      }
      return '';
    }
    
    /**
     *  Funkce pro aktualizaci informací o uživateli dle informací ze skautISu
     */         
    public function updateSkautisUser($skautisUserDetails,$skautisPersonDetails){
      $updateStr='';              
      $db=$this->getDBO();

      $db->setQuery('SELECT * FROM #__users WHERE username='.$db->quote($skautisUserDetails->UserName));
      $userRow=$db->loadObject();
      if ($userRow){
        if ((!empty($skautisPersonDetails->DisplayName))&&($skautisPersonDetails->DisplayName!=$userRow->name)){
          $updateStr.=', name='.$db->quote($skautisPersonDetails->DisplayName);
        }
      }

      if ((!empty($skautisPersonDetails->Email))&&($userRow->email!=$skautisPersonDetails->Email)&&$this->checkEmail($skautisPersonDetails->Email,$userRow->id)){
        $updateStr.=', email='.$db->quote($skautisPersonDetails->Email);
      }

      if (!empty($updateStr)){
        $updateStr=substr($updateStr,2);
        if ($updateStr!=''){
          $db->setQuery('UPDATE #__users SET '.$updateStr.' WHERE username='.$db->quote($skautisUserDetails->UserName).' LIMIT 1;');
          $db->query();
        }
      }
    }
    
    /**
     *  Funkce pro přidání nového uživatele do databáze
     */
    public function registerSkautisUser($skautisUserDetails,$skautisPersonDetails=null){
      require_once JPATH_ROOT.'/components/com_users/models/registration.php';
      $db=$this->getDBO();
      //require_once JPATH_ROOT.'/libraries/joomla/application/component/helper.php';
      $usersModelRegistration = new UsersModelRegistration();
      jimport('joomla.mail.helper');
      jimport('joomla.user.helper');
      
      $password = $this->getSkautisUserPassword($skautisUserDetails);

      //kontrola, jestli není uživatel v některé z tabulek (např. jen změna uživatelského jména
      $db->setQuery('SELECT * FROM #__skautis_users WHERE id_skautis_user='.$db->quote($skautisUserDetails->ID).' LIMIT 1;');
      $skautISUsersRow=$db->loadObject();
      if (!empty($skautISUsersRow)){
        //uživatel skautISu už se sem přihlašoval, ale s jiným jménem?
        $db->setQuery('SELECT id FROM #__users WHERE id='.$db->quote($skautISUsersRow->id).' LIMIT 1;');
        $usersRow=$db->loadObject();
        if (!$usersRow){
          //uživatel už neexistuje v tabulce uživatelů
          $db->setQuery('DELETE FROM #__skautis_users WHERE id='.$db->quote($skautISUsersRow->id).' LIMIT 1;');
          $db->query();
        }else{
          $db->setQuery('UPDATE #__users SET username='.$db->quote($skautisUserDetails->UserName).' WHERE id='.$db->quote().' LIMIT 1;');
          $db->quote();
          return;
        }
      }else{
        $db->setQuery('SELECT id FROM #__users WHERE username='.$db->quote($skautisUserDetails->UserName).' LIMIT 1;');
        $usersRow=$db->loadObject();
        $db->setQuery('INSERT INTO #__skautis_users (id,id_skautis_user,id_person)VALUES('.$db->quote($usersRow->id).','.$db->quote(@$skautisUserDetails->ID).','.$db->quote(@$skautisUserDetails->ID_Person).');');
        $db->query();
        return;
      }
      //--kontrola, jestli není uživatel v některé z tabulek (např. jen změna uživatelského jména

      $email=$skautisUserDetails->UserName.'@skautis.XXX';
      $name=$skautisUserDetails->UserName;
      
      if ($skautisPersonDetails){
        if (@$skautisPersonDetails->DisplayName!=''){
          $name=$skautisPersonDetails->DisplayName;
        }
        if ($this->checkEmail(@$skautisPersonDetails->Email)){
          $email=$skautisPersonDetails->Email;
        }
      }
      
      $data = array( 'username' => $skautisUserDetails->UserName,
                     'name' => $name,
                     'email1' => $email,
                     'password1' => $password, // First password field
                     'password2' => $password, // Confirm password field
                     'block' => 0,
                     'sendEmail'=>0,
                     'activation'=>0 );
                     
      $usersModelRegistration->register($data);
      
      //zaregistrování doplňujících informací
      $db->setQuery('SELECT id FROM #__users WHERE username='.$db->quote($skautisUserDetails->UserName).' LIMIT 1;');
      $userRow=$db->loadObject();

      $db->setQuery('INSERT INTO #__skautis_users (id,id_skautis_user,id_person)VALUES('.$db->quote($userRow->id).','.$db->quote($skautisUserDetails->ID).','.$db->quote($skautisUserDetails->ID_Person).');');
      $db->query();
    }  
    
    /**
     *  Funkce vracející heslo pro registraci uživatele
     */         
    public function getSkautisUserPassword($skautisUserDetails){
      return substr(sha1('skautis'.$skautisUserDetails->ID),7,10);
    }


    /**
     * Funkce vracející ID joomla uživatele na základě ID uživatele ze skautISu
     * @param $skautISUserId
     * @return int
     */
    public function getUserId($skautISUserId){
      $db=$this->getDBO();
      $db->setQuery('SELECT id FROM #__skautis_users WHERE id_skautis_user='.$db->quote($skautISUserId).' LIMIT 1;');
      $row=$db->loadObject();
      return @$row->id;
    }
    
  }
?>
