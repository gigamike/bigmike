<?php
namespace Brand;

use Brand\Model\BrandMapper;
use Brand\Form\BrandSearchForm;
use Brand\Form\BrandForm;

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
                'BrandMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new BrandMapper($dbAdapter);
                    return $mapper;
                },
                'BrandSearchForm' => function ($sm) {
                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                  $form = new BrandSearchForm($dbAdapter);
                  return $form;
                },
                'BrandForm' => function ($sm) {
                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                  $form = new BrandForm($dbAdapter);
                  return $form;
                },
            ),
        );
    }
}
