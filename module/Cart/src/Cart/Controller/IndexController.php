<?php

namespace Cart\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as Message;
use Zend\Mail\Transport\Sendmail as Sendmail;

use Cart\Model\CartEntity;

use GeoIp2\Database\Reader;

class IndexController extends AbstractActionController
{
  public function getCartMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('CartMapper');
  }

  public function getProductMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('ProductMapper');
  }

  public function getCountryMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('CountryMapper');
  }

  public function getUserMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('UserMapper');
  }

  public function indexAction()
  {
    $authService = $this->serviceLocator->get('auth_service');
    if (!$authService->getIdentity()) {
      $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
      return $this->redirect()->toRoute('login');
    }

    $user = $this->getUserMapper()->getUser($this->identity()->id);
    if(!$user){
      $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
      return $this->redirect()->toRoute('login');
    }

    $filter = array(
      'created_user_id' => $this->identity()->id,
    );
    $order = array();
    $carts = $this->getCartMapper()->getCarts(false, $filter, $order);

    return new ViewModel([
      'carts' => $carts,
      'user' => $user,
    ]);
  }

  public function addAction()
  {
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();

      $product_id = (int)$data['product_id'];
      if (!$product_id) {
  			return $this->redirect()->toRoute('home');
  		}

      $product = $this->getProductMapper()->getProduct($product_id);
      if(!$product){
        return $this->redirect()->toRoute('home');
      }

      $quantity = (int)$data['quantity'];
      if (!$quantity) {
  			return $this->redirect()->toRoute('product', array('action' => 'view', 'id' => $product->getId()));
  		}else if(!is_numeric($quantity)){
        return $this->redirect()->toRoute('product', array('action' => 'view', 'id' => $product->getId()));
      }

      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
        return $this->redirect()->toRoute('login');
      }

      $user = $this->getUserMapper()->getUser($this->identity()->id);
  		if(!$user){
        $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
        return $this->redirect()->toRoute('login');
  		}

      $filter = array(
        'product_id' => $product->getId(),
        'created_user_id' => $this->identity()->id,
      );
      $order = array();
      $carts = $this->getCartMapper()->getCarts(false, $filter, $order);
      if(count($carts) > 0){
        foreach ($carts as $cart) {
          $quantity = $cart['quantity'] + $quantity;
          
          $currentCart = $this->getCartMapper()->getCart($cart['id']);
					if($currentCart){
						$currentCart->setQuantity($quantity);
						$this->getCartMapper()->save($currentCart);
					}
        }
      }else{
        $cart = new CartEntity;
        $cart->setProductId($product->getId());
        $cart->setQuantity($quantity);
        $cart->setCreatedUserId($this->identity()->id);
        $cart->setProductId($product->getId());
        $this->getCartMapper()->save($cart);
      }

      $this->flashMessenger()->setNamespace('success')->addMessage('Added to cart successfully.');
      return $this->redirect()->toRoute('cart');
    }

    return $this->redirect()->toRoute('home');
  }

  public function deleteAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
      return $this->redirect()->toRoute('cart');
    }

    $cart = $this->getCartMapper()->getCart($id);
    if(!$cart){
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
      return $this->redirect()->toRoute('cart');
    }

    $this->getCartMapper()->delete($id);

    $this->flashMessenger()->setNamespace('success')->addMessage('Item deleted successfully.');
    return $this->redirect()->toRoute('cart');
  }

  public function buyCreditsAction()
  {
    $user = array();

    $authService = $this->serviceLocator->get('auth_service');
    if ($authService->getIdentity()!=null) {
      $user = $this->getUserMapper()->getUser($authService->getIdentity()->id);
    }

    return new ViewModel([
      'user' => $user,
    ]);
  }

  public function paypalApiAction()
  {
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();

      $amount = 0;

      switch($data['amount']){
        case '1000':
          $amount = 1100;
          break;
        case '5000':
          $amount = 5500;
          break;
        case '10000':
          $amount = 11000;
          break;
        default:
      }

      $user = $this->getUserMapper()->getUser($data['userid']);
  		if($user){
        $totalAmount = $user->getCredits() + $amount;
        $user->setCredits($totalAmount);
        $this->getUserMapper()->save($user);
  		}

      $config = $this->getServiceLocator()->get('Config');

      $mail = new  Message();

      $subject = "Paypal Test.";
      $message = "User ID: " . $data['userid'];
      $message .= "Amount: " . $data['amount'];

      $mail->setFrom($config['email']);
      $mail->addTo($config['email']);
      $mail->setEncoding("UTF-8");
      $mail->setSubject($subject);
      $mail->setBody($message);

      $transport = new Sendmail();
      $transport->send($mail);
    }

    return $this->getResponse();
  }

  public function checkoutAction()
  {
    $authService = $this->serviceLocator->get('auth_service');
    if ($authService->getIdentity()!=null) {
      $user = $this->getUserMapper()->getUser($authService->getIdentity()->id);
    }else{
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid user.');
      return $this->redirect()->toRoute('cart');
    }

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

    $form = $this->getServiceLocator()->get('ShippingAddressForm');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($data);

      if($form->isValid()) {
        $data = $form->getData();

        $total = 0;
        $filter = array(
          'created_user_id' => $this->identity()->id,
        );
        $order = array();
        $carts = $this->getCartMapper()->getCarts(false, $filter, $order);
        if(count($carts) > 0){
          foreach ($carts as $row) {
            switch($row->discount_type){
              case 'amount':
                $price = $row->price - $row->discount;
                break;
              case 'percentage':
                $discount = $row->price * ($row->discount/100);
                $price = $row->price - $row->discount;
                break;
              default:
                $price = $row->price;
            }

            $total += $price;
          }
        }

        $this->getCartMapper()->deleteByCreatedUserId($user->getId());

        $remainingCredit = $user->getCredits() - $total;
        $user->setCredits($remainingCredit);
        $user->setAddress($data['address']);
        $user->setCity($data['city']);
        $country = $this->getCountryMapper()->getCountryByCountryCode($isoCode);
        if($country){
          $user->setCountryId($country->getId());
        }
        $user->setZip($data['zip']);
        $this->getUserMapper()->save($user);

        $message = "Thank you for ordering " . $user->getFirstName() . "!";
        $subject = 'Thank you for ordering.';

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

        $this->flashMessenger()->setNamespace('success')->addMessage('Thank you for ordering in Big Mike. Item shipping is on its way.');
        return $this->redirect()->toRoute('cart');
      }else{
        print_r($form->getMessages());
      }
    }else{
      $country = $this->getCountryMapper()->getCountryByCountryCode($isoCode);
      if($country){
        $form->get('country_id')->setValue($country->getId());
      }
      if($city){
        $form->get('city')->setValue($city);
      }
      $form->get('first_name')->setValue($user->getFirstName());
      $form->get('last_name')->setValue($user->getLastName());
      $form->get('address')->setValue($user->getAddress());
      $form->get('zip')->setValue($user->getZip());
    }

    return new ViewModel([
      'form' => $form,
      'config' => $config,
    ]);
  }
}
