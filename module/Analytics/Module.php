<?php
namespace Analytics;

use Analytics\Model\AnalyticsMapper;
use Analytics\Form\ShippingAddressForm;
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
          'AnalyticsMapper' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $mapper = new AnalyticsMapper($dbAdapter);
            return $mapper;
          },
        ),
      );
    }
}
