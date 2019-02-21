<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

use User\Model\UserEntity;

class RegistrationController extends AbstractActionController
{
    public function getUserMapper()
    {
      $sm = $this->getServiceLocator();
      return $sm->get('UserMapper');
    }

    public function indexAction()
    {
      // product
      $productId = (int)$this->params('id');

      $config = $this->getServiceLocator()->get('Config');

      $form = $this->getServiceLocator()->get('RegistrationForm');
      $user = new UserEntity();
		  $form->bind($user);

      if($this->getRequest()->isPost()) {
        $data = $this->params()->fromPost();
        $form->setData($data);

        if($form->isValid()) {
          $data = $form->getData();
					$user->setCreatedUserId(0);
          $user->setRole('member');
          $user->setActive('Y');
					$dynamicSalt = $this->getUserMapper()->dynamicSalt();
					$user->setSalt($dynamicSalt);
					$password = md5($config['staticSalt'] . $user->getPassword() . $dynamicSalt);
					$user->setPassword($password);
          $user->setCountryId(0);
          $user->setCredits(50);
          $this->getUserMapper()->save($user);

          $message = "Thank you for registraion " . $user->getFirstName() . "!";
          $subject = 'Thank you for registraion.';

          try {
            $mail = new Message();
            $mail->setFrom($config['email']);
            $mail->addTo($user->getEmail());
            $mail->setSubject($subject);
            $mail->setBody($message);

            // Send E-mail message
            $transport = new Sendmail('-f'. $config['email']);
            // $transport->send($mail);
          } catch(\Exception $e) {
          }

          if ($productId) {
            $product = $this->getProductMapper()->getProduct($productId);
            if($product){
              return $this->redirect()->toRoute('cart', array('action' => 'add', 'id' => $productId));
            }
          }

          $this->flashMessenger()->setNamespace('success')->addMessage('Thank you for registraion.');
          return $this->redirect()->toRoute('registration');
        }
      }

      $fb = new \Facebook\Facebook([
        'app_id' => $config['facebook']['app_id'], // Replace {app-id} with your app id
        'app_secret' => $config['facebook']['app_secret'],
        'default_graph_version' => $config['facebook']['app_version'],
      ]);
      $helper = $fb->getRedirectLoginHelper();
      $permissions = ['email']; // Optional permissions
      $facebookLoginUrl = $helper->getLoginUrl($config['baseUrl'] . "facebook/oauth", $permissions);

      $client = new \Google_Client();
      $client->setClientId($config['google']['client_id']);
    	$client->setClientSecret($config['google']['client_secret']);
    	$client->setRedirectUri($config['baseUrl'] . "google/oauth");
    	$client->addScope("email");
      $client->addScope("profile");
      $service = new \Google_Service_Oauth2($client);
      $googleLoginUrl = $client->createAuthUrl();

      return new ViewModel([
        'form' => $form,
        'config' => $config,
        'facebookLoginUrl' => $facebookLoginUrl,
        'googleLoginUrl' => $googleLoginUrl,
      ]);
    }
}
