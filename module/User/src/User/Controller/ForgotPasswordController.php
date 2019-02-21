<?php

namespace User\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

class ForgotPasswordController extends AbstractActionController
{
  public function getUserMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('UserMapper');
  }

  public function indexAction()
  {
    $config = $this->getServiceLocator()->get('Config');

    $form = $this->getServiceLocator()->get('ForgotPasswordForm');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($data);

      if($form->isValid()) {
        $data = $form->getData();

        $filter = [
          'email' => $data['email'],
        ];
        $getUser = $this->getUserMapper()->fetch(false, $filter, null, 1);
        if(count($getUser)){
          foreach ($getUser as $user) {
            // echo $user->getFirstName() . "|" . $user->getLastName() . "|" . $user->getEmail();

            $dynamicSalt = $this->getUserMapper()->dynamicSalt();
            $user->setSalt($dynamicSalt);
            $randomPassword = $this->getUserMapper()->randomPassword();
            $password = md5($config['staticSalt'] . $randomPassword . $dynamicSalt);
            $user->setPassword($password);
            $user->setModifiedUserId(0);
            $user->setModifiedDatetime(date('Y-m-d H:i:s'));
            $this->getUserMapper()->save($user);

            $message = "Your new password is " . $randomPassword . "\n Please login to " . $config['baseUrl'] . "login";
            $subject = 'Forgot Password.';

            try {
              $mail = new Message();
              $mail->setFrom($config['email']);
              $mail->addTo($data['email']);
              $mail->setSubject($subject);
              $mail->setBody($message);

              // Send E-mail message
              $transport = new Sendmail('-f'. $config['email']);
              // $transport->send($mail);
            } catch(\Exception $e) {
            }
          }

          $this->flashMessenger()->setNamespace('success')->addMessage('Email sent. Please check your email.');
          return $this->redirect()->toRoute('forgot-password');
        }
      }
    }

    return new ViewModel([
      'form' => $form
    ]);
  }
}
