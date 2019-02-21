<?php

namespace Category\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Category\Form\CategoryForm;
use Category\Model\CategoryEntity;

class SupplierController extends AbstractActionController
{
  public function getCategoryMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('CategoryMapper');
  }

  public function indexAction()
  {
    if($this->getRequest()->isPost()) {
      $ids = $this->getRequest()->getPost('ids');
			if(count($ids) > 0){
				foreach($ids as $id){
          $this->getCategoryMapper()->delete($id);
				}
			}else{
        $this->flashMessenger()->setNamespace('error')->addMessage('Please select at least 1 category.');
        return $this->redirect()->toRoute('supplier-category');
      }

      $this->flashMessenger()->setNamespace('success')->addMessage('Selected category successfully deleted.');
      return $this->redirect()->toRoute('supplier-category');
    }

    $page = $this->params()->fromRoute('page');
    $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

		$filter = array();
		if (!empty($search_by)) {
			$filter = (array) json_decode($search_by);
		}
    $form = $this->getServiceLocator()->get('CategorySearchForm');
    $form->setData($filter);

    $order = ['category'];
    $paginator = $this->getCategoryMapper()->fetch(true, $filter,$order);
    $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
    $paginator->setItemCountPerPage(10);

    return new ViewModel([
      'form' => $form,
      'paginator' => $paginator,
    ]);
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
						$search_data[$key] = $value;
					}
				}
			}

			if (!empty($search_data)) {
				$search_by = json_encode($search_data);
				return $this->redirect()->toRoute('supplier-category', array('search_by' => $search_by));
			}else{
				return $this->redirect()->toRoute('supplier-category');
			}
		}else{
			return $this->redirect()->toRoute('supplier-category');
		}
	}

  public function addAction()
  {
    $form = $this->getServiceLocator()->get('CategoryForm');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($data);

      if($form->isValid()) {
        $data = $form->getData();

        $category = new CategoryEntity;
        $category->setCategory($data['category']);
				$category->setCreatedUserId($this->identity()->id);
        $this->getCategoryMapper()->save($category);

        $this->flashMessenger()->setNamespace('success')->addMessage('Category added successfully.');
        return $this->redirect()->toRoute('supplier-category');
      }
    }

    return new ViewModel([
      'form' => $form,
    ]);
  }

  public function editAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid category.');
			return $this->redirect()->toRoute('supplier-category');
		}
		$category = $this->getCategoryMapper()->getCategory($id);
		if(!$category){
			$this->flashMessenger()->setNamespace('error')->addMessage('Invalid category.');
			return $this->redirect()->toRoute('supplier-category');
		}

    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');

    $form = new CategoryForm($dbAdapter, $id);
    $form->bind($category);
	  $form->get('submit')->setAttribute('value', 'Edit');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($this->getRequest()->getPost()->toArray());
      if($form->isValid()) {
        $category->setCategory($data['category']);
        $category->setModifiedDatetime(date('Y-m-d H:i:s'));
        $category->setModifiedUserId($this->identity()->id);
        $this->getCategoryMapper()->save($category);

        $this->flashMessenger()->setNamespace('success')->addMessage('Category edited successfully.');
        return $this->redirect()->toRoute('supplier-category');
      }
    }

    return new ViewModel([
      'form' => $form,
      'category' => $category,
    ]);
  }

  public function deleteAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid category.');
      return $this->redirect()->toRoute('supplier-category');
    }
    $category = $this->getCategoryMapper()->getCategory($id);
    if(!$category){
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid category.');
      return $this->redirect()->toRoute('supplier-category');
    }

    $this->getCategoryMapper()->delete($id);

    $this->flashMessenger()->setNamespace('success')->addMessage('Category deleted successfully.');
    return $this->redirect()->toRoute('supplier-category');
  }
}
