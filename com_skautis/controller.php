<?php      
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
                       
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * Hello World Component Controller
 */
class SkautisController extends JControllerLegacy
{
  var $document;
  /** @var string adresa skautIS serveru */
  private $skautisUrl='';
  /** @var string identifikace skautIS aplikace */
  private $skautisAppId='';

  /**
   *  Akce pro zobrazení detailů konkrétní osoby
   */
  public function person(){
    exit('in progress');
  }       

  /**
   *  Akce pro zobrazení detailů vyšší organizační jednotky
   */     
  public function voj(){
    $view=&$this->getView('Voj',$this->document->getType());
    $app = JFactory::getApplication();
    $params=$app->getParams();
    //exit(var_dump($params));
    $idUnit=$params->get('idUnit');
    $session = JFactory::getSession();
    $skautIsToken=$session->get('skautIsToken',null,'skautIs');
    
    $soapOrg = new SoapClient($this->skautisUrl.'/JunakWebservice/OrganizationUnit.asmx?WSDL');
    $paramsArr=array('unitDetailInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID'=>$idUnit));
    $unitDetail=@$soapOrg->UnitDetail($paramsArr)->UnitDetailResult;
            
    $view=&$this->getView('Voj',$this->document->getType());
    $view->assignRef('unitDetail',$unitDetail);
    
    //chceme kontakty?
    if ($params->get('showContacts')=='true'){
      $paramsArr=array('unitContactAllInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID_Unit'=>$idUnit));
      $unitContactAll=@$soapOrg->UnitContactAll($paramsArr)->UnitContactAllResult;
      $view->assignRef('unitContactAll',$unitContactAll);
    }
    
    //chceme info o bankovních účtech?
    if ($params->get('showAccounts')=='true'){
      $paramsArr=array('accountAllInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID_Unit'=>$idUnit,'IsValid'=>true));
      $accountAll=@$soapOrg->AccountAll($paramsArr)->AccountAllResult;
      $view->assignRef('accountAll',$accountAll);
    }
    
    //chceme informace o funkcích?
    if ($params->get('showFunctions')=='true'){
      //nacteni funkci v jednotce
      if ($skautIsToken){
        $paramsArr=array('functionAllInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID_Unit'=>$idUnit,'IsValid'=>true));
        $functionAll=@$soapOrg->FunctionAll($paramsArr)->FunctionAllResult;  
        $view->assignRef('functionAll',$functionAll);
      }else{
        $functionAllRegistry=array();
        $paramsArr=array('functionAllRegistryInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID_Unit'=>$idUnit,'ReturnStatutory'=>true));
        $functionAllRegistry['Statutory']=@$soapOrg->FunctionAllRegistry($paramsArr)->FunctionAllRegistryResult;
        $paramsArr=array('functionAllRegistryInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID_Unit'=>$idUnit,'ReturnAssistant'=>true));
        $functionAllRegistry['Assistant']=@$soapOrg->FunctionAllRegistry($paramsArr)->FunctionAllRegistryResult;
        $paramsArr=array('functionAllRegistryInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID_Unit'=>$idUnit,'ReturnContact'=>true));
        $functionAllRegistry['Contact']=@$soapOrg->FunctionAllRegistry($paramsArr)->FunctionAllRegistryResult;
        $view->assign('functionAllRegistry',(object)$functionAllRegistry);
      }
      //--nacteni funkci v jednotce
    }
    
    //chceme informace o podřízených jednotkách?
    if ($params->get('showUnitsTree')=='true'){
      $paramsArr=array('unitTreeAllInput'=>array('ID_Login'=>$skautIsToken,'ID_Application'=>$this->skautisAppId,'ID_UnitParent'=>$idUnit,'IsValid'=>true));
      $unitTreeAll=@$soapOrg->UnitTreeAll($paramsArr)->UnitTreeAllResult;
      $view->assignRef('unitTreeAll',$unitTreeAll);
    }              
    
    //načtení informací o článcích
    $idArticleTop=$params->get('idArticleTop');
    $idArticleBottom=$params->get('idArticleBottom');
    
    if ($idArticleTop||$idArticleBottom){
      $articlesModel=&$this->getModel('Articles','SkautisModel');
      if ($idArticleTop>0){
        $view->assign('topText',$articlesModel->getArticleContent($idArticleTop));
      }
      if ($idArticleBottom>0){
        $view->assign('bottomText',$articlesModel->getArticleContent($idArticleBottom));
      }
    }
    
    $view->display();      
  }

  /**
   *  Akce po úspěšném odhlášení
   */     
  public function login(){
    $this->setRedirect('/');
  }
  
  /**
   *  Akce po úspěšném odhlášení
   */     
  public function logoutOK(){
    $this->setRedirect('/');
  }
  
  /**
   *  Akce pro přesměrování na skautIS přihlašování
   */     
  public function loginIS(){
    $this->setRedirect($this->skautisUrl.'/Login/?appid='.$this->skautisAppId);
  }
  
  /**
   *  Akce pro odhlášení uživatele - smaže lokální přihlášení a odhlásí uživatele od skautISu
   */
  public function logout(){ 
    
    $session = JFactory::getSession(); 
    $skautIsToken=$session->get('skautIsToken',null,'skautIs');
    $app = JFactory::getApplication();
    $app->logout();
    if ($skautIsToken){  
      $session->clear('skautIsToken','skautIs');
      $this->setRedirect($this->skautisUrl.'/Login/LogOut.aspx?AppID='.$this->skautisAppId.'&Token='.$skautIsToken);
    }
  }      

  /**
   *  Akce po úspěšném přihlášení
   */     
  public function loginOK(){
    try{ 
      $session = JFactory::getSession();
      $skautIsToken=@$_REQUEST['skautIS_Token'];
                                   
      $soap = new SoapClient($this->skautisUrl.'/JunakWebservice/UserManagement.asmx?WSDL');
      $params=array('userDetailInput'=>array('ID_Login'=>$_REQUEST['skautIS_Token']));
      $userDetail=@$soap->UserDetail($params)->UserDetailResult;
                                 
      if (($userDetail->IsActive)&&($userDetail->IsEnabled)){
        $username=$userDetail->UserName;
        $idPerson=$userDetail->ID_Person;
        $idUser=$userDetail->ID;
        $hasMembership=$userDetail->HasMembership;
        
        if ($idPerson){
          $soapOrg = new SoapClient($this->skautisUrl.'/JunakWebservice/OrganizationUnit.asmx?WSDL');
          $params=array('personDetailInput'=>array('ID_Login'=>$skautIsToken,'ID'=>$idPerson));
          $personDetail=@$soapOrg->PersonDetail($params)->PersonDetailResult;
        }else{
          $personDetail=null;
        }
        
        //zjištění, jestli zvolený uživatel existuje => pokud ne, tak ho nově vytvoříme
        /** @var $usersModel SkautisModelUsers */
        $usersModel=& $this->getModel('Users','SkautisModel');
        if ($usersModel->userExists($idUser,$username)){
          //uživatel existuje - jen ho aktualizujeme
          if ($personDetail){
            $usersModel->updateSkautisUser($userDetail,$personDetail);//aktualizace uživatelského jména a e-mailu
          }
        }else{
          //potřebujeme zaregistrovat nového uživatele
          $usersModel->registerSkautisUser($userDetail,$personDetail);
        }
        $session->set('skautIsToken',$skautIsToken,'skautIs');


        $app = JFactory::getApplication();
        $app->login(array('username'=>$userDetail->UserName,'password'=>'skautis','skautisUserId'=>$idUser,'user'=>$usersModel->getUserId($idUser),'timestamp'=>time()),array('silent'=>false));

        
        //aktualizace fotky
        $params=array('personPhotoInput'=>array('ID_Login'=>$skautIsToken,'ID'=>$idPerson,'Size'=>'normal'));
        $personPhoto=@$soapOrg->PersonPhoto($params)->PersonPhotoResult;
        if ($personPhoto){                   
          $photoUrl=$usersModel->updateSkautisUserPhoto($idPerson,$personPhoto,'normal');
          if ($photoUrl){
            $session->set('userPhoto',$photoUrl,'skautIs');
          }else{
            $session->clear('userPhoto','skautIs');
          }
        }
        //--aktualizace fotky
                           
        $this->setRedirect('/');
      }else{
        //zobrazení informace o tom, že uživatel není aktivní
        JFactory::getApplication()->enqueueMessage(JText::_('SKAUTIS_USER_NOT_ACTIVE'),'error');
      }
    }catch (Exception $e){
      JFactory::getApplication()->enqueueMessage(JText::_('SKAUTIS_LOGIN_ERROR'),'error');
    }
  }
  
  /**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{                                        
		parent::__construct( $default );
		$this->document =& JFactory::getDocument();

    /** @var $configModel SkautisModelConfig */
    $configModel=& $this->getModel('Config','SkautisModel');
    $this->skautisUrl=$configModel->getConfig('SKAUTIS_URL');
    $this->skautisAppId=$configModel->getConfig('SKAUTIS_APP_ID');
	}
}
