<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function getProductMapper()
    {
      $sm = $this->getServiceLocator();
      return $sm->get('ProductMapper');
    }

    public function getBrandMapper()
    {
      $sm = $this->getServiceLocator();
      return $sm->get('BrandMapper');
    }

    public function getCategoryMapper()
    {
      $sm = $this->getServiceLocator();
      return $sm->get('CategoryMapper');
    }

    public function indexAction()
    {
      $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
      $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
      $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

      $searchFilter = array();
      if (!empty($search_by)) {
        $searchFilter = (array) json_decode($search_by);
      }

      $order = array('product.name');
      $paginator = $this->getProductMapper()->getProducts(true, $searchFilter, $order);
      $paginator->setCurrentPageNumber($page);
      $paginator->setItemCountPerPage(12);

      $form = $this->getServiceLocator()->get('ProductSearchForm');
      $form->setData($searchFilter);

      $searchFilterBrand = array();
      $orderBrand = array(
        'brand',
      );
      $limit = 6;
      $brands = $this->getBrandMapper()->fetch(false, $searchFilterBrand, $orderBrand, $limit);

      $searchFilterCategory = array();
      $orderCategory= array(
        'category',
      );
      $limit = 6;
      $categories = $this->getCategoryMapper()->fetch(false, $searchFilterCategory, $orderCategory, $limit);

      return new ViewModel(array(
      'paginator' => $paginator,
      'search_by' => $search_by,
      'page' => $page,
      'searchFilter' => $searchFilter,
      'route' => $route,
      'form' => $form,
      'brands' => $brands,
      'categories' => $categories,
    ));
    }
}
