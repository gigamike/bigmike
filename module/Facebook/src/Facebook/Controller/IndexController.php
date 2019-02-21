<?php

namespace Facebook\Controller;

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

    $fb = new \Facebook\Facebook([
      'app_id' => $config['facebook']['app_id'], // Replace {app-id} with your app id
      'app_secret' => $config['facebook']['app_secret'],
      'default_graph_version' => $config['facebook']['app_version'],
    ]);

    $helper = $fb->getRedirectLoginHelper();

    try {
      $accessToken = $helper->getAccessToken();
    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      // echo 'Graph returned an error: ' . $e->getMessage();
      // exit;
      $this->flashMessenger()->setNamespace('error')->addMessage('Graph returned an error: ' . $e->getMessage());
      return $this->redirect()->toRoute('registration');
    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      // echo 'Facebook SDK returned an error: ' . $e->getMessage();
      // exit;
      $this->flashMessenger()->setNamespace('error')->addMessage('Facebook SDK returned an error: ' . $e->getMessage());
      return $this->redirect()->toRoute('registration');
    }

    if (! isset($accessToken)) {
      if ($helper->getError()) {
        // header('HTTP/1.0 401 Unauthorized');
        // echo "Error: " . $helper->getError() . "\n";
        // echo "Error Code: " . $helper->getErrorCode() . "\n";
        // echo "Error Reason: " . $helper->getErrorReason() . "\n";
        // echo "Error Description: " . $helper->getErrorDescription() . "\n";
        $this->flashMessenger()->setNamespace('error')->addMessage('HTTP/1.0 401 Unauthorized: ' . $helper->getErrorReason());
        return $this->redirect()->toRoute('registration');
      } else {
        // header('HTTP/1.0 400 Bad Request');
        // echo 'Bad request';
        $this->flashMessenger()->setNamespace('error')->addMessage('HTTP/1.0 400 Bad Request: Bad request');
        return $this->redirect()->toRoute('registration');
      }
      exit;
    }

    // Logged in
    // echo '<h3>Access Token</h3>';
    // var_dump($accessToken->getValue());

    // The OAuth 2.0 client handler helps us manage access tokens
    $oAuth2Client = $fb->getOAuth2Client();

    // Get the access token metadata from /debug_token
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    // echo '<h3>Metadata</h3>';
    // var_dump($tokenMetadata);

    // Validation (these will throw FacebookSDKException's when they fail)
    $tokenMetadata->validateAppId($config['facebook']['app_id']); // Replace {app-id} with your app id
    // If you know the user ID this access token belongs to, you can validate it here
    //$tokenMetadata->validateUserId('123');
    $tokenMetadata->validateExpiration();

    if (! $accessToken->isLongLived()) {
      // Exchanges a short-lived access token for a long-lived one
      try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
      } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
        // exit;
        $this->flashMessenger()->setNamespace('error')->addMessage('Error getting long-lived access token: ' . $e->getMessage());
        return $this->redirect()->toRoute('registration');
      }

      // echo '<h3>Long-lived</h3>';
      // var_dump($accessToken->getValue());
    }

    $_SESSION['fb_access_token'] = (string) $accessToken;

    // User is logged in with a long-lived access token.
    // You can redirect them to a members-only page.
    //header('Location: https://example.com/members.php');

    try {
      // Returns a `Facebook\FacebookResponse` object
      $response = $fb->get('/me?fields=id,first_name,last_name,email', (string) $accessToken);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // echo 'Graph returned an error: ' . $e->getMessage();
      // exit;
      $this->flashMessenger()->setNamespace('error')->addMessage('Graph returned an error: ' . $e->getMessage());
      return $this->redirect()->toRoute('registration');
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // echo 'Facebook SDK returned an error: ' . $e->getMessage();
      // exit;
      $this->flashMessenger()->setNamespace('error')->addMessage('Facebook SDK returned an error: ' . $e->getMessage());
      return $this->redirect()->toRoute('registration');
    }

    $facebookUser = $response->getGraphUser();

    // echo 'First Name: ' . $facebookUser['first_name'];
    // echo 'Last Name: ' . $facebookUser['last_name'];
    // echo 'Email: ' . $facebookUser['email'];
    // OR
    // echo 'Name: ' . $facebookUser->getName();

    if(!isset($facebookUser['email'])){
      $this->flashMessenger()->setNamespace('error')->addMessage('Abort! Something is wrong');
      return $this->redirect()->toRoute('registration');
    }

    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
    $authService = $this->serviceLocator->get('auth_service');

    $user = $this->getUserMapper()->getUserByEmail($facebookUser['email']);
    if(count($user) > 0){
      $authAdapter = new AuthAdapter(
        $dbAdapter,
        'user',
        'email',
        'password',
        "? AND active='Y'"
      );
      $authAdapter->setIdentity($facebookUser['email'])->setCredential($user->getPassword());
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
      $user->setFirstName($facebookUser['first_name']);
      $user->setLastName($facebookUser['last_name']);
      $user->setEmail($facebookUser['email']);
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

      $message = "Thank you for registraion " . $facebookUser['first_name'] . "!";
      $subject = 'Thank you for registraion.';

      try {
        $mail = new Message();
        $mail->setFrom($config['email']);
        $mail->addTo($facebookUser['email']);
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
      $authAdapter->setIdentity($facebookUser['email'])->setCredential($generatedPassword);
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
}
