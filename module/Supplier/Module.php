<?php
namespace Supplier;

use Supplier\Form\SupplierRegistrationForm;
use Country\Model\CountryMapper;

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
          'SupplierRegistrationForm' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $countryMapper = new CountryMapper($dbAdapter);
            $form = new SupplierRegistrationForm($dbAdapter, $countryMapper);
            return $form;
          },
  			),
    	);
    }
}
