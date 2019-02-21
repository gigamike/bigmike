<?php

namespace User\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

use User\Form\AdminUserEditForm;
use User\Model\UserEntity;

class AdminController extends AbstractActionController
{
  public function getUserMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('UserMapper');
  }

  public function indexAction()
  {
    if($this->getRequest()->isPost()) {
      $ids = $this->getRequest()->getPost('ids');
			if(count($ids) > 0){
				foreach($ids as $id){
          if($this->identity()->id != $id){
            $this->getUserMapper()->delete($id);
          }
				}
			}else{
        $this->flashMessenger()->setNamespace('error')->addMessage('Please select at least 1 user.');
        return $this->redirect()->toRoute('admin-user');
      }

      $this->flashMessenger()->setNamespace('success')->addMessage('Selected users successfully deleted.');
      return $this->redirect()->toRoute('admin-user');
    }

    $page = $this->params()->fromRoute('page');
    $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

		$filter = array();
		if (!empty($search_by)) {
			$filter = (array) json_decode($search_by);
		}
    $form = $this->getServiceLocator()->get('AdminUserSearchForm');
    $form->setData($filter);

    $order = ['first_name', 'last_name', 'email'];
    $paginator = $this->getUserMapper()->fetch(true, $filter,$order);
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
				return $this->redirect()->toRoute('admin-user', array('search_by' => $search_by));
			}else{
				return $this->redirect()->toRoute('admin-user');
			}
		}else{
			return $this->redirect()->toRoute('admin-user');
		}
	}

  public function addAction()
  {
    $config = $this->getServiceLocator()->get('Config');

    $form = $this->getServiceLocator()->get('AdminUserAddForm');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($data);

      if($form->isValid()) {
        $data = $form->getData();

        $user = new UserEntity;
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
				$user->setCreatedUserId($this->identity()->id);
        $user->setRole($data['role']);
        $user->setActive('Y');
				$dynamicSalt = $this->getUserMapper()->dynamicSalt();
				$user->setSalt($dynamicSalt);
				$password = md5($config['staticSalt'] . $data['password'] . $dynamicSalt);
				$user->setPassword($password);
        $this->getUserMapper()->save($user);

        $message = "User added " . $data['first_name'] . "!";
        $message .= "\n\nEmail: " . $data['email'];
        $message .= "\n\nPassword: " . $data['password'];
        $subject = 'User added.';

        try {
          $mail = new Message();
          $mail->setFrom($config['email']);
          $mail->addTo($data['email']);
          $mail->setSubject($subject);
          $mail->setBody($message);

          // Send E-mail message
          $transport = new Sendmail('-f'. $config['email']);
          // $transport->send($mail);
        } catch(\Exception $e) {
        }

        $this->flashMessenger()->setNamespace('success')->addMessage('User added successfully.');
        return $this->redirect()->toRoute('admin-user');
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
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid user.');
			return $this->redirect()->toRoute('admin-user');
		}
		$user = $this->getUserMapper()->getUser($id);
		if(!$user){
			$this->flashMessenger()->setNamespace('error')->addMessage('Invalid User.');
			return $this->redirect()->toRoute('admin-user');
		}

    $config = $this->getServiceLocator()->get('Config');

    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');

    $form = new AdminUserEditForm($dbAdapter, $user);
    $form->bind($user);
	  $form->get('submit')->setAttribute('value', 'Edit');
    if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();
      $form->setData($this->getRequest()->getPost()->toArray());
      if($form->isValid()) {
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setRole($data['role']);
        $user->setModifiedDatetime(date('Y-m-d H:i:s'));
        $user->setModifiedUserId($this->identity()->id);
        $this->getUserMapper()->save($user);

        $this->flashMessenger()->setNamespace('success')->addMessage('User edited successfully.');
        return $this->redirect()->toRoute('admin-user');
      }
    }

    return new ViewModel([
      'form' => $form,
      'config' => $config,
      'user' => $user,
    ]);
  }

  public function deleteAction()
  {
    $id = (int)$this->params('id');
    if (!$id) {
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid user.');
      return $this->redirect()->toRoute('admin-user');
    }
    $user = $this->getUserMapper()->getUser($id);
    if(!$user){
      $this->flashMessenger()->setNamespace('error')->addMessage('Invalid User.');
      return $this->redirect()->toRoute('admin-user');
    }

    $this->getUserMapper()->delete($id);

    $this->flashMessenger()->setNamespace('success')->addMessage('User deleted successfully.');
    return $this->redirect()->toRoute('admin-user');
  }
}
