<?php
/**
 * Plugin pro přihlašování pomocí skautISu
 */

defined('_JEXEC') or die;

/**
 * SkautIS Authentication Plugin
 *
 */
class PlgAuthenticationSkautIS extends JPlugin
{
	/**
	 * Metoda pro přihlášení skautIS uživatele
	 *
	 * @param   array   $credentials  Array holding the user credentials
	 * @param   array   $options      Array of extra options
	 * @param   object  &$response    Authentication response object
	 *
	 * @return  boolean
	 */
	public function onUserAuthenticate($credentials, $options, &$response){
    $success=(($credentials['timestamp']-time())<10);

    if ($credentials['password']!='skautis'){
      $success=false;
    }
    if (!(@$credentials['skautisUserId']>0)){
      $success=false;
    }
    if ($success){
      $user = JUser::getInstance($credentials['user']);
    }

		$response->type = 'skautIS';

		if ($user&&($user->actiovation==0)&&($user->block==0))
		{


      $response->email = $user->email;
      $response->fullname = $user->name;

      if (JFactory::getApplication()->isAdmin())
      {
        $response->language = $user->getParam('admin_language');
      }
      else
      {
        $response->language = $user->getParam('language');
      }

      $response->status = JAuthentication::STATUS_SUCCESS;
      $response->error_message = '';
		}
		else
		{
			$response->status        = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::sprintf('JGLOBAL_AUTH_FAILED', 'Login failed.');//TODO message
		}
	}
}
