<?php
namespace User;

use Zend\Authentication\AuthenticationService;
use User\Model\UserMapper;
use User\Form\RegistrationForm;
use User\Form\ForgotPasswordForm;
use User\Form\AdminUserSearchForm;
use User\Form\AdminUserAddForm;

class Module
{
    public function getConfig()
    {
      return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
      return array(
        'Zend\Loader\StandardAutoloader' => array(
          'namespaces' => array(
            __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
          ),
        ),
      );
    }

    public function getServiceConfig()
    {
    	return array(
  			'factories' => array(
          'auth_service' =>  function($sm) {
            $authService = new AuthenticationService();
            return $authService;
          },
					'UserMapper' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$mapper = new UserMapper($dbAdapter);
						return $mapper;
					},
          'RegistrationForm' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $form = new RegistrationForm($dbAdapter);
            return $form;
          },
          'ForgotPasswordForm' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $form = new ForgotPasswordForm($dbAdapter);
            return $form;
          },
          'AdminUserSearchForm' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $form = new AdminUserSearchForm($dbAdapter);
            return $form;
          },
          'AdminUserAddForm' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $form = new AdminUserAddForm($dbAdapter);
            return $form;
          },
  			),
    	);
    }
}
