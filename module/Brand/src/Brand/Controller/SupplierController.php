<?php

namespace Brand\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Brand\Form\BrandForm;
use Brand\Model\BrandEntity;

class SupplierController extends AbstractActionController
{
  public function getBrandMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('BrandMapper');
  }

  public function indexAction()
  {
    if($this->getRequest()->isPost()) {
      $ids = $this->getRequest()->getPost('ids');
      $config = $this->getServiceLocator()->get('Config');

			if(count($ids) > 0){
				foreach($ids as $id){
          $brand = $this->getBrandMapper()->getBrand($id);
          if($brand){
            $ext = pathinfo($brand->getLogoName(), PATHINFO_EXTENSION);
            $directory = $config['pathBrandLogo']['absolutePath'] . $brand->getId();
            $file = $directory . "/logo." . $ext;
            if(file_exists($file)){
              unlink($file);
            }
            if(file_exists($directory)){
              unlink($directory);
            }

            $this->getBrandMapper()->delete($id);
          }
				}
			}else{
        $this->flashMessenger()->setNamespace('error')->addMessage('Please select at least 1 brand.');
        return $this->redirect()->toRoute('supplier-brand');
      }

      $this->flashMessenger()->setNamespace('success')->addMessage('Selected brand successfully deleted.');
      return $this->redirect()->toRoute('supplier-brand');
    }

    $page = $this->params()->fromRoute('page');
    $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

		$filter = array();
		if (!empty($search_by)) {
			$filter = (array) json_decode($search_by);
		}
    $form = $this->getServiceLocator()->get('BrandSearchForm');
    $form->setData($filter);

    $order = ['brand'];
    $paginator = $this->getBrandMapper()->fetch(true, $filter,$order);
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
				return $this->redirect()->toRoute('supplier-brand', array('search_by' => $search_by));
			}else{
				return $this->redirect()->toRoute('supplier-brand');
			}
		}else{
			return $this->redirect()->toRoute('supplier-brand');
		}
	}

  public function addAction()
  {
    $form = $this->getServiceLocator()->get('BrandForm');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($data);

      if($form->isValid()) {
        $data = $form->getData();

        $isLogoError = false;
		    if(!isset($_FILES['logo'])){
	        $isLogoError = true;
	        $form->get('logo')->setMessages(array('Required field logo.'));
		    }else{
	        $allowed =  array('png', 'jpg', 'jpeg', 'gif');
	        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
	        if(!in_array($ext, $allowed) ) {
            $isLogoError = true;
            $form->get('logo')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
	        }
	        switch ($_FILES['logo']['error']){
            case 1:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('The file is bigger than this PHP installation allows.'));
              break;
            case 2:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('The file is bigger than this form allows.'));
              break;
            case 3:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('Only part of the file was uploaded.'));
              break;
            case 4:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('No file was uploaded.'));
              break;
            default:
	        }
		    }

        if(!$isLogoError){
          $brand = new BrandEntity;
          $brand->setBrand($data['brand']);
          $brand->setLogoName($_FILES['logo']['name']);
  				$brand->setCreatedUserId($this->identity()->id);
          $this->getBrandMapper()->save($brand);

          $config = $this->getServiceLocator()->get('Config');
          $directory = $config['pathBrandLogo']['absolutePath'] . $brand->getId();
	        if(!file_exists($directory)){
	            mkdir($directory, 0755);
	        }

          $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
	        $destination = $directory . "/logo." . $ext;
	        if(!file_exists($destination)){
	           move_uploaded_file($_FILES['logo']['tmp_name'], $destination);
	        }

          $this->flashMessenger()->setNamespace('success')->addMessage('Brand added successfully.');
          return $this->redirect()->toRoute('supplier-brand');
        }
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
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid brand.');
			return $this->redirect()->toRoute('supplier-brand');
		}
		$brand = $this->getBrandMapper()->getBrand($id);
		if(!$brand){
			$this->flashMessenger()->setNamespace('error')->addMessage('Invalid brand.');
			return $this->redirect()->toRoute('supplier-brand');
		}

    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');

    $config = $this->getServiceLocator()->get('Config');

    $form = new BrandForm($dbAdapter, $id);
    $form->bind($brand);
	  $form->get('submit')->setAttribute('value', 'Edit');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($this->getRequest()->getPost()->toArray());
      if($form->isValid()) {

        $isLogoError = false;
        $isLogoUpload = false;
		    if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
          $isLogoUpload = true;
	        $allowed =  array('png', 'jpg', 'jpeg', 'gif');
	        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
	        if(!in_array($ext, $allowed) ) {
            $isLogoError = true;
            $form->get('logo')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
	        }
	        switch ($_FILES['logo']['error']){
            case 1:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('The file is bigger than this PHP installation allows.'));
              break;
            case 2:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('The file is bigger than this form allows.'));
              break;
            case 3:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('Only part of the file was uploaded.'));
              break;
            case 4:
              $isLogoError = true;
              $form->get('logo')->setMessages(array('No file was uploaded.'));
              break;
            default:
	        }
		    }

        $brand->setBrand($data['brand']);
        $brand->setModifiedDatetime(date('Y-m-d H:i:s'));
        $brand->setModifiedUserId($this->identity()->id);

        if($isLogoUpload){
          if(!$isLogoError){
            $config = $this->getServiceLocator()->get('Config');
            $directory = $config['pathBrandLogo']['absolutePath'] . $brand->getId();
  	        if(!file_exists($directory)){
  	            mkdir($directory, 0755);
  	        }

            $ext = pathinfo($brand->getLogoName(), PATHINFO_EXTENSION);
            $existingLogo = $directory . "/logo." . $ext;
            if(file_exists($existingLogo)){
              unlink($existingLogo);
            }

            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
  	        $destination = $directory . "/logo." . $ext;
  	        if(!file_exists($destination)){
  	           move_uploaded_file($_FILES['logo']['tmp_name'], $destination);
  	        }

            $brand->setLogoName($_FILES['logo']['name']);
            $this->getBrandMapper()->save($brand);

            $this->flashMessenger()->setNamespace('success')->addMessage('Brand edited successfully.');
            return $this->redirect()->toRoute('supplier-brand');
          }
        }else{
          $this->getBrandMapper()->save($brand);
          $this->flashMessenger()->setNamespace('success')->addMessage('Brand edited successfully.');
          return $this->redirect()->toRoute('supplier-brand');
        }
      }
    }

    $ext = pathinfo($brand->getLogoName(), PATHINFO_EXTENSION);
    $directory = $config['pathBrandLogo']['absolutePath'] . $brand->getId();
    $logo = $directory . "/logo." . $ext;
    if(file_exists($logo)){
      $logo = $config['pathBrandLogo']['relativePath'] . $brand->getId() . "/logo." . $ext;
    }else{
      $logo = null;
    }

    return new ViewModel([
      'form' => $form,
      'brand' => $brand,
      'logo' => $logo,
    ]);
  }

  public function deleteAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid brand.');
      return $this->redirect()->toRoute('supplier-brand');
    }
    $brand = $this->getBrandMapper()->getBrand($id);
    if(!$brand){
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid brand.');
      return $this->redirect()->toRoute('supplier-brand');
    }

    $config = $this->getServiceLocator()->get('Config');
    $ext = pathinfo($brand->getLogoName(), PATHINFO_EXTENSION);
    $directory = $config['pathBrandLogo']['absolutePath'] . $brand->getId();
    $file = $directory . "/logo." . $ext;
    if(file_exists($file)){
      unlink($file);
    }
    if(file_exists($directory)){
      unlink($directory);
    }

    $this->getBrandMapper()->delete($id);

    $this->flashMessenger()->setNamespace('success')->addMessage('Brand deleted successfully.');
    return $this->redirect()->toRoute('supplier-brand');
  }
}
