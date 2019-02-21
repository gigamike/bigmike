<?php
namespace Product;

use Product\Model\ProductMapper;
use Product\Model\ProductCategoryMapper;
use Product\Form\ProductSearchForm;
use Product\Form\ProductForm;

use Brand\Model\BrandMapper;
use Category\Model\CategoryMapper;

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
                'ProductMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new ProductMapper($dbAdapter);
                    return $mapper;
                },
                'ProductCategoryMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new ProductCategoryMapper($dbAdapter);
                    return $mapper;
                },
                'ProductSearchForm' => function ($sm) {
                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                  $categoryMapper = new CategoryMapper($dbAdapter);
                  $brandMapper = new BrandMapper($dbAdapter);

                  $form = new ProductSearchForm($dbAdapter, $brandMapper, $categoryMapper);
                  return $form;
                },
                'ProductForm' => function ($sm) {
                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                  $categoryMapper = new CategoryMapper($dbAdapter);
                  $brandMapper = new BrandMapper($dbAdapter);

                  $form = new ProductForm($dbAdapter, $brandMapper, $categoryMapper);
                  return $form;
                },
            ),
        );
    }

    public function getViewHelperConfig() {
      return array(
        'factories' => array(
          'getQRCode' => function($sm){
            return new \Product\View\Helper\GetQRCode($sm->getServiceLocator());
          },
        )
      );
    }
}
