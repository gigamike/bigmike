<?php

namespace Product\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Product\Form\ProductForm;
use Product\Model\ProductEntity;
use Product\Model\ProductCategoryEntity;

use GeoIp2\Database\Reader;
use Gumlet\ImageResize;

class SupplierController extends AbstractActionController
{
  public function getProductMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('ProductMapper');
  }

  public function getProductCategoryMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('ProductCategoryMapper');
  }

  public function getBrandMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('BrandMapper');
  }

  public function indexAction()
  {
    $page = $this->params()->fromRoute('page');
    $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

		$filter = array();
		if (!empty($search_by)) {
			$filter = (array) json_decode($search_by);
		}
    $form = $this->getServiceLocator()->get('ProductSearchForm');
    $form->setData($filter);

    $order = ['name'];
    $paginator = $this->getProductMapper()->getProducts(true, $filter,$order);
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
				return $this->redirect()->toRoute('supplier-product', array('search_by' => $search_by));
			}else{
				return $this->redirect()->toRoute('supplier-product');
			}
		}else{
			return $this->redirect()->toRoute('supplier-product');
		}
	}

  public function addAction()
  {
    $config = $this->getServiceLocator()->get('Config');

    $form = $this->getServiceLocator()->get('ProductForm');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($data);

      if($form->isValid()) {
        $isError = false;

        $data = $form->getData();

        $discount_type = $data['discount_type'];
        $discount = $data['discount'];

        if(empty($discount_type)){
          $discount_type = null;
          $discount = null;
        }else{
          if(empty($discount)){
            $isError = true;
            $form->get('discount')->setMessages(array('Required field discount.'));
          }
        }

        if(!$isError){
          if(!isset($_FILES['photo1'])){
            $isError = true;
  	        $form->get('photo1')->setMessages(array('Required field photo.'));
  		    }else{
  	        $allowed =  array('jpg');
  	        $ext = pathinfo($_FILES['photo1']['name'], PATHINFO_EXTENSION);
  	        if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('photo1')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
  	        }
  	        switch ($_FILES['photo1']['error']){
              case 1:
                $isError = true;
                $form->get('photo1')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('photo1')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('photo1')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('photo1')->setMessages(array('No file was uploaded.'));
                break;
              default:
  	        }
  		    }

          if(!isset($_FILES['photo2'])){
            $isError = true;
            $form->get('photo2')->setMessages(array('Required field photo.'));
          }else{
            $allowed =  array('jpg');
            $ext = pathinfo($_FILES['photo2']['name'], PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('photo2')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
            }
            switch ($_FILES['photo2']['error']){
              case 1:
                $isError = true;
                $form->get('photo2')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('photo2')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('photo2')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('photo2')->setMessages(array('No file was uploaded.'));
                break;
              default:
            }
          }

          if(!isset($_FILES['photo3'])){
            $isError = true;
            $form->get('photo3')->setMessages(array('Required field photo.'));
          }else{
            $allowed =  array('jpg');
            $ext = pathinfo($_FILES['photo3']['name'], PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('photo3')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
            }
            switch ($_FILES['photo3']['error']){
              case 1:
                $isError = true;
                $form->get('photo3')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('photo3')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('photo3')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('photo3')->setMessages(array('No file was uploaded.'));
                break;
              default:
            }
          }

          $isUploadPanorama = false;
          if(isset($_FILES['panorama']) && !empty($_FILES['panorama']['tmp_name'])){
            $isUploadPanorama = true;

            $allowed =  array('jpg');
            $ext = pathinfo($_FILES['panorama']['name'], PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('panorama')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
            }
            switch ($_FILES['panorama']['error']){
              case 1:
                $isError = true;
                $form->get('panorama')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('panorama')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('panorama')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('panorama')->setMessages(array('No file was uploaded.'));
                break;
              default:
            }
          }

          if(!$isError){
            $product = new ProductEntity;
            $product->setBrandId($data['brand_id']);
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setStock($data['stock']);
            $product->setPrice($data['price']);
            $product->setDiscountType($data['discount_type']);
            $product->setDiscount($data['discount']);
            $product->setPhotoName1($_FILES['photo1']['name']);
            $product->setPhotoName2($_FILES['photo2']['name']);
            $product->setPhotoName3($_FILES['photo3']['name']);
            if($isUploadPanorama){
              $product->setPanorama($_FILES['panorama']['name']);
            }
            $product->setCreatedUserId($this->identity()->id);
            $this->getProductMapper()->save($product);

            if(count($data['category_id']) > 0){
              foreach ($data['category_id'] as $category_id) {
                $productCategory = new ProductCategoryEntity;
                $productCategory->setProductId($product->getId());
                $productCategory->setCategoryId($category_id);
                $this->getProductCategoryMapper()->save($productCategory);
              }
            }

            $directory = $config['pathProductPhoto']['absolutePath'] . $product->getId();
            if(!file_exists($directory)){
              mkdir($directory, 0755);
            }

            $ext = pathinfo($_FILES['photo1']['name'], PATHINFO_EXTENSION);
            $destination = $directory . "/photo1-orig." . $ext;
            if(!file_exists($destination)){
               move_uploaded_file($_FILES['photo1']['tmp_name'], $destination);
            }
            $destination2 = $directory . "/photo1-700x400." . $ext;
            if(file_exists($destination2)){
               unlink($destination2);
            }
            $image = new ImageResize($destination);
            $image->resize(700, 400);
            $image->save($destination2);

            $ext = pathinfo($_FILES['photo2']['name'], PATHINFO_EXTENSION);
            $destination = $directory . "/photo2-orig." . $ext;
            if(!file_exists($destination)){
               move_uploaded_file($_FILES['photo2']['tmp_name'], $destination);
            }
            $destination2 = $directory . "/photo2-700x400." . $ext;
            if(file_exists($destination2)){
               unlink($destination2);
            }
            $image = new ImageResize($destination);
            $image->resize(700, 400);
            $image->save($destination2);

            $ext = pathinfo($_FILES['photo3']['name'], PATHINFO_EXTENSION);
            $destination = $directory . "/photo3-orig." . $ext;
            if(!file_exists($destination)){
               move_uploaded_file($_FILES['photo3']['tmp_name'], $destination);
            }
            $destination2 = $directory . "/photo3-700x400." . $ext;
            if(file_exists($destination2)){
               unlink($destination2);
            }
            $image = new ImageResize($destination);
            $image->resize(700, 400);
            $image->save($destination2);

            if($isUploadPanorama){
              $ext = pathinfo($_FILES['panorama']['name'], PATHINFO_EXTENSION);
              $destination = $directory . "/panorama-orig." . $ext;
              if(!file_exists($destination)){
                 move_uploaded_file($_FILES['panorama']['tmp_name'], $destination);
              }
              $destination2 = $directory . "/panorama." . $ext;
              if(file_exists($destination2)){
                 unlink($destination2);
              }
              $image = new ImageResize($destination);
              $image->resize(6000, 3000);
              $image->save($destination2);
            }

            $this->flashMessenger()->setNamespace('success')->addMessage('Product added successfully.');
            return $this->redirect()->toRoute('supplier-product', array('action' => 'qrcode', 'id' => $product->getId(),));
          } // isError
        } // isError
      }
    }

    return new ViewModel([
      'form' => $form,
      'config' => $config,
    ]);
  }

  public function editAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
			return $this->redirect()->toRoute('supplier-product');
		}
		$product = $this->getProductMapper()->getProduct($id);
		if(!$product){
			$this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
			return $this->redirect()->toRoute('supplier-product');
		}

    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');

    $config = $this->getServiceLocator()->get('Config');

    $form = $this->getServiceLocator()->get('ProductForm');
    $form->bind($product);
	  $form->get('submit')->setAttribute('value', 'Edit');

    $request = $this->getRequest();
    if($this->getRequest()->isPost()) {
      $form->setData($request->getPost()->toArray());
      if($form->isValid()) {
        $isError = false;

        $data = $form->getData();
        $discount_type = $data->getDiscountType();
        $discount = $data->getDiscount();

        if(empty($discount_type)){
          $discount_type = null;
          $discount = null;
        }else{
          if(empty($discount)){
            $isError = true;
            $form->get('discount')->setMessages(array('Required field discount.'));
          }
        }

        if(!$isError){
          $isUploadPhoto1 = false;
          if(isset($_FILES['photo1']) && !empty($_FILES['photo1']['tmp_name'])){
            $isUploadPhoto1 = true;

  	        $allowed =  array('jpg');
  	        $ext = pathinfo($_FILES['photo1']['name'], PATHINFO_EXTENSION);
  	        if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('photo1')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
  	        }
  	        switch ($_FILES['photo1']['error']){
              case 1:
                $isError = true;
                $form->get('photo1')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('photo1')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('photo1')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('photo1')->setMessages(array('No file was uploaded.'));
                break;
              default:
  	        }
  		    }

          $isUploadPhoto2 = false;
          if(isset($_FILES['photo2']) && !empty($_FILES['photo1']['tmp_name'])){
            $isUploadPhoto2 = true;

            $allowed =  array('jpg');
            $ext = pathinfo($_FILES['photo2']['name'], PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('photo2')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
            }
            switch ($_FILES['photo2']['error']){
              case 1:
                $isError = true;
                $form->get('photo2')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('photo2')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('photo2')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('photo2')->setMessages(array('No file was uploaded.'));
                break;
              default:
            }
          }

          $isUploadPhoto3 = false;
          if(isset($_FILES['photo3']) && !empty($_FILES['photo1']['tmp_name'])){
            $isUploadPhoto3 = true;

            $allowed =  array('jpg');
            $ext = pathinfo($_FILES['photo3']['name'], PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('photo3')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
            }
            switch ($_FILES['photo3']['error']){
              case 1:
                $isError = true;
                $form->get('photo3')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('photo3')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('photo3')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('photo3')->setMessages(array('No file was uploaded.'));
                break;
              default:
            }
          }

          $isUploadPanorama = false;
          if(isset($_FILES['panorama']) && !empty($_FILES['panorama']['tmp_name'])){
            $isUploadPanorama = true;

            $allowed =  array('jpg');
            $ext = pathinfo($_FILES['panorama']['name'], PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed) ) {
              $isError = true;
              $form->get('panorama')->setMessages(array("File type not allowed. Only " . implode(',', $allowed)));
            }
            switch ($_FILES['panorama']['error']){
              case 1:
                $isError = true;
                $form->get('panorama')->setMessages(array('The file is bigger than this PHP installation allows.'));
                break;
              case 2:
                $isError = true;
                $form->get('panorama')->setMessages(array('The file is bigger than this form allows.'));
                break;
              case 3:
                $isError = true;
                $form->get('panorama')->setMessages(array('Only part of the file was uploaded.'));
                break;
              case 4:
                $isError = true;
                $form->get('panorama')->setMessages(array('No file was uploaded.'));
                break;
              default:
            }
          }

          if(!$isError){
            $product->setModifiedDatetime(date('Y-m-d H:i:s'));
            $product->setModifiedUserId($this->identity()->id);

            $this->getProductCategoryMapper()->deleteByProductId($product->getId());
            $category_ids = $request->getPost('category_id');
            if(count($category_ids) > 0){
              foreach ($category_ids as $category_id) {
                $productCategory = new ProductCategoryEntity;
                $productCategory->setProductId($product->getId());
                $productCategory->setCategoryId($category_id);
                $this->getProductCategoryMapper()->save($productCategory);
              }
            }

            $directory = $config['pathProductPhoto']['absolutePath'] . $product->getId();
            if(!file_exists($directory)){
              mkdir($directory, 0755);
            }

            if($isUploadPhoto1){
              $ext = pathinfo($_FILES['photo1']['name'], PATHINFO_EXTENSION);
              $destination = $directory . "/photo1-orig." . $ext;
              if(!file_exists($destination)){
                 move_uploaded_file($_FILES['photo1']['tmp_name'], $destination);
              }
              $destination2 = $directory . "/photo1-700x400." . $ext;
              if(file_exists($destination2)){
                 unlink($destination2);
              }
              $image = new ImageResize($destination);
              $image->resize(700, 400);
              $image->save($destination2);
            }
            if($isUploadPhoto2){
              $ext = pathinfo($_FILES['photo2']['name'], PATHINFO_EXTENSION);
              $destination = $directory . "/photo2-orig." . $ext;
              if(!file_exists($destination)){
                 move_uploaded_file($_FILES['photo2']['tmp_name'], $destination);
              }
              $destination2 = $directory . "/photo2-700x400." . $ext;
              if(file_exists($destination2)){
                 unlink($destination2);
              }
              $image = new ImageResize($destination);
              $image->resize(700, 400);
              $image->save($destination2);
            }
            if($isUploadPhoto3){
              $ext = pathinfo($_FILES['photo3']['name'], PATHINFO_EXTENSION);
              $destination = $directory . "/photo3-orig." . $ext;
              if(!file_exists($destination)){
                 move_uploaded_file($_FILES['photo3']['tmp_name'], $destination);
              }
              $destination2 = $directory . "/photo3-700x400." . $ext;
              if(file_exists($destination2)){
                 unlink($destination2);
              }
              $image = new ImageResize($destination);
              $image->resize(700, 400);
              $image->save($destination2);
            }

            if($isUploadPanorama){
              $ext = pathinfo($_FILES['panorama']['name'], PATHINFO_EXTENSION);
              $destination = $directory . "/panorama-orig." . $ext;
              if(!file_exists($destination)){
                 move_uploaded_file($_FILES['panorama']['tmp_name'], $destination);
              }
              $destination2 = $directory . "/panorama." . $ext;
              if(file_exists($destination2)){
                 unlink($destination2);
              }
              $image = new ImageResize($destination);
              $image->resize(6000, 3000);
              $image->save($destination2);
            }

            $this->flashMessenger()->setNamespace('success')->addMessage('Product edited successfully.');
            return $this->redirect()->toRoute('supplier-product');
          } // is error
        } // is error
      }
    }else{
      $filter = array(
        'product_id' => $product->getId(),
      );
      $productCategories = $this->getProductCategoryMapper()->fetch(false, $filter);
      if(count($productCategories) > 0){
        foreach ($productCategories as $row){
          $currentCategorySelected[] = $row->getCategoryId();
        }
        $form->get('category_id')->setAttribute('value', $currentCategorySelected);
      }
    }

    $ext = pathinfo($product->getPhotoName1(), PATHINFO_EXTENSION);
    $directory = $config['pathProductPhoto']['absolutePath'] . $product->getId();
    $photo1 = $directory . "/photo1-700x400." . $ext;
    if(file_exists($photo1)){
      $photo1 = $config['pathProductPhoto']['relativePath'] . $product->getId() . "/photo1-700x400." . $ext;
    }else{
      $photo1 = null;
    }

    $ext = pathinfo($product->getPhotoName2(), PATHINFO_EXTENSION);
    $directory = $config['pathProductPhoto']['absolutePath'] . $product->getId();
    $photo2 = $directory . "/photo2-700x400." . $ext;
    if(file_exists($photo2)){
      $photo2 = $config['pathProductPhoto']['relativePath'] . $product->getId() . "/photo2-700x400." . $ext;
    }else{
      $photo2 = null;
    }

    $ext = pathinfo($product->getPhotoName3(), PATHINFO_EXTENSION);
    $directory = $config['pathProductPhoto']['absolutePath'] . $product->getId();
    $photo3 = $directory . "/photo3-700x400." . $ext;
    if(file_exists($photo3)){
      $photo3 = $config['pathProductPhoto']['relativePath'] . $product->getId() . "/photo3-700x400." . $ext;
    }else{
      $photo3 = null;
    }

    return new ViewModel([
      'form' => $form,
      'product' => $product,
      'photo1' => $photo1,
      'photo2' => $photo2,
      'photo3' => $photo3,
    ]);
  }

  public function deleteAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
      return $this->redirect()->toRoute('supplier-product');
    }
    $product = $this->getProductMapper()->getProduct($id);
    if(!$product){
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
      return $this->redirect()->toRoute('supplier-product');
    }

    $config = $this->getServiceLocator()->get('Config');
    $directory = $config['pathProductPhoto']['absolutePath'] . $product->getId();

    $ext = pathinfo($product->getPhotoName1(), PATHINFO_EXTENSION);
    $file = $directory . "/photo1-700x400." . $ext;
    if(file_exists($file)){
      unlink($file);
    }
    $file = $directory . "/photo1-orig." . $ext;
    if(file_exists($file)){
      unlink($file);
    }

    $ext = pathinfo($product->getPhotoName2(), PATHINFO_EXTENSION);
    $file = $directory . "/photo2-700x400." . $ext;
    if(file_exists($file)){
      unlink($file);
    }
    $file = $directory . "/photo2-orig." . $ext;
    if(file_exists($file)){
      unlink($file);
    }

    $ext = pathinfo($product->getPhotoName3(), PATHINFO_EXTENSION);
    $file = $directory . "/photo3-700x400." . $ext;
    if(file_exists($file)){
      unlink($file);
    }
    $file = $directory . "/photo3-orig." . $ext;
    if(file_exists($file)){
      unlink($file);
    }

    if(file_exists($directory)){
      rmdir($directory);
    }

    $this->getProductCategoryMapper()->deleteByProductId($id);
    $this->getProductMapper()->delete($id);

    $this->flashMessenger()->setNamespace('success')->addMessage('Product deleted successfully.');
    return $this->redirect()->toRoute('supplier-product');
  }

  public function qrcodeAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
			return $this->redirect()->toRoute('supplier-product');
		}
		$product = $this->getProductMapper()->getProduct($id);
		if(!$product){
			$this->flashMessenger()->setNamespace('error')->addMessage('Invalid product.');
			return $this->redirect()->toRoute('supplier-product');
		}

    $brand = $this->getBrandMapper()->getBrand($product->getBrandId());

    $config = $this->getServiceLocator()->get('Config');

    return new ViewModel([
      'product' => $product,
      'brand' => $brand,
      'config' => $config,
    ]);
  }
}
