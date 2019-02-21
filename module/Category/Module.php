<?php
namespace Category;

use Category\Model\CategoryMapper;
use Category\Form\CategorySearchForm;
use Category\Form\CategoryForm;

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
                'CategoryMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new CategoryMapper($dbAdapter);
                    return $mapper;
                },
                'CategorySearchForm' => function ($sm) {
                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                  $form = new CategorySearchForm($dbAdapter);
                  return $form;
                },
                'CategoryForm' => function ($sm) {
                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                  $form = new CategoryForm($dbAdapter);
                  return $form;
                },
            ),
        );
    }
}
