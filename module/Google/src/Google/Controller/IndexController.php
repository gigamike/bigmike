<?php

namespace Google\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as AuthAdapter;

use User\Model\UserEntity;

class IndexController extends AbstractActionController
{
  public function getUserMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('UserMapper');
  }

  public function indexAction()
  {
  }

  public function oauthAction()
  {
    $config = $this->getServiceLocator()->get('Config');

    $client = new \Google_Client();
    $client->setClientId($config['google']['client_id']);
  	$client->setClientSecret($config['google']['client_secret']);
  	$client->setRedirectUri($config['baseUrl'] . "google/oauth");
  	$client->addScope("email");
    $client->addScope("profile");

  	if (isset($_GET['oauth'])) {
      // Start auth flow by redirecting to Google's auth server
      $auth_url = $client->createAuthUrl();
      header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
      exit();
  	} else if (isset($_GET['code'])) {
      // Receive auth code from Google, exchange it for an access token, and
      // redirect to your base URL
      $client->authenticate($_GET['code']);
      $_SESSION['access_token'] = $client->getAccessToken();
      $redirect_uri = $config['baseUrl'] . "google/oauth";
      header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
  		exit();
  	} else if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  		$client->setAccessToken($_SESSION['access_token']);
      $people_service = new \Google_Service_PeopleService($client);

  		// https://developers.google.com/people/api/rest/v1/people/get
  		$googleUser = $people_service->people->get('people/me', array('personFields' => 'addresses,ageRanges,biographies,birthdays,braggingRights,coverPhotos,emailAddresses,events,genders,imClients,interests,locales,memberships,metadata,names,nicknames,occupations,organizations,phoneNumbers,photos,relations,relationshipInterests,relationshipStatuses,residences,skills,taglines,urls'));
  		// print_r($googleUser);
  		// echo 'User: ' . $googleUser->names[0]->givenName . ' ' . $googleUser->names[0]->familyName . ' - ' . $googleUser->emailAddresses[0]['value'] . "\n<br>";

      if(!isset($googleUser->emailAddresses[0]['value'])){
        $this->flashMessenger()->setNamespace('error')->addMessage('Abort! Something is wrong');
        return $this->redirect()->toRoute('registration');
      }

      $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
      $authService = $this->serviceLocator->get('auth_service');

      $user = $this->getUserMapper()->getUserByEmail($googleUser->emailAddresses[0]['value']);
      if(count($user) > 0){
        $authAdapter = new AuthAdapter(
          $dbAdapter,
          'user',
          'email',
          'password',
          "? AND active='Y'"
        );
        $authAdapter->setIdentity($googleUser->emailAddresses[0]['value'])->setCredential($user->getPassword());
        $result = $authService->authenticate($authAdapter);
        if($result->isValid()) {
          // echo $result->getIdentity() . "\n\n";
          // print_r($authAdapter->getResultRowObject());
          $columnsToOmit = ['password', 'salt'];
          $write = $authAdapter->getResultRowObject(null, $columnsToOmit);
          // $authService->setStorage($this->_authStorage);
          $authService->getStorage()->write($write);

          if ($authService->getIdentity()!=null) {
            if($authService->getIdentity()->role == 'admin'){
              return $this->redirect()->toRoute('admin');
            }else if($authService->getIdentity()->role == 'supplier'){
              return $this->redirect()->toRoute('supplier');
            }else if($authService->getIdentity()->role == 'member'){
              return $this->redirect()->toRoute('home');
            }else{
              return $this->redirect()->toRoute('home');
            }
          }
        }
      }else{
        $user = new UserEntity;
        $user->setFirstName($googleUser->names[0]->givenName);
        $user->setLastName($googleUser->names[0]->familyName);
        $user->setEmail($googleUser->emailAddresses[0]['value']);
        $user->setCreatedUserId(0);
        $user->setRole('member');
        $user->setActive('Y');
        $user->setCountryId(0);
        $user->setCredits(50);
        $dynamicSalt = $this->getUserMapper()->dynamicSalt();
        $user->setSalt($dynamicSalt);
        $generatedPassword = $this->getUserMapper()->randomPassword();
        $password = md5($config['staticSalt'] . $generatedPassword . $dynamicSalt);
        $user->setPassword($password);
        $this->getUserMapper()->save($user);

        $message = "Thank you for registraion " . $googleUser->names[0]->givenName . "!";
        $subject = 'Thank you for registraion.';

        try {
          $mail = new Message();
          $mail->setFrom($config['email']);
          $mail->addTo($googleUser->emailAddresses[0]['value']);
          $mail->setSubject($subject);
          $mail->setBody($message);

          // Send E-mail message
          $transport = new Sendmail('-f'. $config['email']);
          // $transport->send($mail);
        } catch(\Exception $e) {
        }

        $authAdapter = new AuthAdapter(
          $dbAdapter,
          'user',
          'email',
          'password',
          "MD5(CONCAT('" . $config['staticSalt'] . "', ?, salt)) AND active='Y'"
        );
        $authAdapter->setIdentity($googleUser->emailAddresses[0]['value'])->setCredential($generatedPassword);
        $result = $authService->authenticate($authAdapter);
        if($result->isValid()) {
          // echo $result->getIdentity() . "\n\n";
          // print_r($authAdapter->getResultRowObject());
          $columnsToOmit = ['password', 'salt'];
          $write = $authAdapter->getResultRowObject(null, $columnsToOmit);
          // $authService->setStorage($this->_authStorage);
          $authService->getStorage()->write($write);

          if ($authService->getIdentity()!=null) {
            if($authService->getIdentity()->role == 'admin'){
              return $this->redirect()->toRoute('admin');
            }else if($authService->getIdentity()->role == 'supplier'){
              return $this->redirect()->toRoute('supplier');
            }else if($authService->getIdentity()->role == 'member'){
              return $this->redirect()->toRoute('home');
            }else{
              return $this->redirect()->toRoute('home');
            }
          }
        }
      }

      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid request.');
      return $this->redirect()->toRoute('registration');
    }

    $this->flashMessenger()->setNamespace('error')->addMessage('Abort! Something is wrong');
    return $this->redirect()->toRoute('registration');
  }
}
