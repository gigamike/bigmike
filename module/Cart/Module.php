<?php
namespace Cart;

use Cart\Model\CartMapper;
use Cart\Form\ShippingAddressForm;
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
          'CartMapper' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $mapper = new CartMapper($dbAdapter);
            return $mapper;
          },
          'ShippingAddressForm' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $countryMapper = new CountryMapper($dbAdapter);
            $form = new ShippingAddressForm($dbAdapter, $countryMapper);
            return $form;
          },
        ),
      );
    }
}
