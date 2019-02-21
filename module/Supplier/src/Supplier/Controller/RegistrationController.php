<?php

namespace Supplier\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

use User\Model\UserEntity;

use GeoIp2\Database\Reader;

class RegistrationController extends AbstractActionController
{
    public function getUserMapper()
    {
      $sm = $this->getServiceLocator();
      return $sm->get('UserMapper');
    }

    public function getCountryMapper()
    {
      $sm = $this->getServiceLocator();
      return $sm->get('CountryMapper');
    }

    public function indexAction()
    {
      $config = $this->getServiceLocator()->get('Config');

      if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
      }
      if($_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
        $_SERVER['REMOTE_ADDR'] = $config['ip'];
      }
      $latitude = null;
      $longitude = null;
      $city = null;
      $country = null;
      $isoCode = null;

      if($_SERVER['REMOTE_ADDR']){
        $basePath = dirname($_SERVER['DOCUMENT_ROOT']);
        $reader = new Reader($basePath . "/geoip/GeoLite2-City.mmdb");

        $record = $reader->city($_SERVER['REMOTE_ADDR']);

        $latitude = $record->location->latitude;
        $longitude = $record->location->longitude;

        $city = $record->city->name;
        $country = $record->country->name;
        $isoCode = $record->country->isoCode;

        /*
        echo "Your IP: " . $_SERVER['REMOTE_ADDR'] . "<br>";
        echo "Your Country Code: " . $record->country->isoCode . "<br>";
        echo "Your Country Name: " . $record->country->name . "<br>";
        echo "Your latitude: " . $record->location->latitude . "<br>";
        echo "Your longitude " . $record->location->longitude . "<br>";
        echo "Your City Name: " . $record->city->name . "<br>";
        */
      }

      $form = $this->getServiceLocator()->get('SupplierRegistrationForm');
      $user = new UserEntity();
		  $form->bind($user);

      if($this->getRequest()->isPost()) {
        $data = $this->params()->fromPost();
        $form->setData($data);

        if($form->isValid()) {
          $data = $form->getData();
					$user->setCreatedUserId(0);
          $user->setRole('supplier');
          $user->setActive('Y');
					$dynamicSalt = $this->getUserMapper()->dynamicSalt();
					$user->setSalt($dynamicSalt);
					$password = md5($config['staticSalt'] . $user->getPassword() . $dynamicSalt);
					$user->setPassword($password);
          $user->setLatitude($latitude);
          $user->setLongitude($longitude);
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

          $this->flashMessenger()->setNamespace('success')->addMessage('Thank you for registraion.');
          return $this->redirect()->toRoute('supplier-registration');
        }
      }else{
        $country = $this->getCountryMapper()->getCountryByCountryCode($isoCode);
        if($country){
          $form->get('country_id')->setValue($country->getId());
        }
        if($city){
          $form->get('city')->setValue($city);
        }
      }

      return new ViewModel([
        'form' => $form,
        'config' => $config,
      ]);
    }
}
