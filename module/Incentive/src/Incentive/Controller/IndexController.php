<?php

namespace Incentive\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as Message;
use Zend\Mail\Transport\Sendmail as Sendmail;

use Incentive\Model\IncentiveEntity;

use GeoIp2\Database\Reader;

class IndexController extends AbstractActionController
{
  public function getIncentiveMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('IncentiveMapper');
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
      'created_user_id' => $user->getId(),
    );
    $order=array();
    $incentives = $this->getIncentiveMapper()->getIncentives(false, $filter, $order);

    return new ViewModel([
      'incentives' => $incentives,
      'user' => $user,
    ]);
  }
}
