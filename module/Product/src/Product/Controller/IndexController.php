<?php

namespace Product\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

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

  public function getProductCategoryMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('ProductCategoryMapper');
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
    $paginator->setItemCountPerPage(9);

    $form = $this->getServiceLocator()->get('ProductSearchForm');
    $form->setData($searchFilter);

    $searchFilterBrand = array();
    $orderBrand = array(
      'brand',
    );
    $limit = 6;
    $brands = $this->getBrandMapper()->fetch(false, $searchFilterBrand, $orderBrand, $limit);

    return new ViewModel(array(
      'searchFilter' => $searchFilter,
      'paginator' => $paginator,
      'search_by' => $search_by,
      'page' => $page,
      'searchFilter' => $searchFilter,
      'route' => $route,
      'form' => $form,
      'brands' => $brands,
    ));
  }

  public function searchAction()
  {
    $request = $this->getRequest();
    if ($request->isPost()) {
      $formdata = (array) $request->getPost();

      $search_data = array();
      foreach ($formdata as $key => $value) {
        if ($key != 'submit') {
          if (!empty($value)) {
            $search_data[$key] = urlencode($value);
          }
        }
      }

      if (!empty($search_data)) {
        $search_by = json_encode($search_data);

        return $this->redirect()->toRoute('product', array('action' => 'index', 'search_by' => $search_by));
      }else{
        return $this->redirect()->toRoute('product', array('action' => 'index',));
      }
    }else{
      return $this->redirect()->toRoute('product', array('action' => 'index',));
    }
  }

  public function viewAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      return $this->redirect()->toRoute('home');
    }
    $product = $this->getProductMapper()->getProduct($id);
    if(!$product){
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Product.');
      return $this->redirect()->toRoute('home');
    }

    $tempProductCategories = array();
    $filter = array(
      'product_id' => $product->getId(),
    );
    $productCategories = $this->getProductCategoryMapper()->fetch(false, $filter);
    if(count($productCategories) > 0){
      foreach ($productCategories as $row) {
        $tempProductCategories[$row->getCategoryId()] = $row->getCategoryId();
      }
      $productCategories = $tempProductCategories;
    }

    $brand = $this->getBrandMapper()->getBrand($product->getBrandId());

    $filter = array(
      'ids' => $productCategories,
    );
    $order = array(
      'category',
    );
    $categories = $this->getCategoryMapper()->fetch(false, $filter, $order);

    $searchFilter = array(
      'category_ids' => $productCategories,
      'id_not_equal' => $product->getId(),
    );
    $order = array();
    $relatedProducts = $this->getProductMapper()->getProducts(false, $searchFilter, $order, 8);

    $config = $this->getServiceLocator()->get('Config');

    return new ViewModel(array(
      'product' => $product,
      'brand' => $brand,
      'categories' => $categories,
      'config' => $config,
      'relatedProducts' => $relatedProducts,
    ));
  }
}
