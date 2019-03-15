<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

use Cart\Model\CartEntity;
use Incentive\Model\IncentiveEntity;

class IndexController extends AbstractActionController
{
	public function getProductMapper()
	{
		$sm = $this->getServiceLocator();
		return $sm->get('ProductMapper');
	}

	public function getCartMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('CartMapper');
  }

	public function getUserMapper()
	{
		$sm = $this->getServiceLocator();
		return $sm->get('UserMapper');
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

	public function getIncentiveMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('IncentiveMapper');
  }

	/*
	* https://apitester.com/
	*
	*/
	public function indexAction()
	{
		$config = $this->getServiceLocator()->get('Config');

		$filter = array();
		$order = array();
		$brands = $this->getBrandMapper()->fetch(false, $filter, $order);

		$filter = array();
		$order = array();
		$categories = $this->getCategoryMapper()->fetch(false, $filter, $order);

		return new ViewModel(array(
			'config' => $config,
			'brands' => $brands,
			'categories' => $categories,
    ));
	}

	private function _getResponseWithHeader()
  {
      $response = $this->getResponse();
      $response->getHeaders()
               // make can accessed by *
               ->addHeaderLine('Access-Control-Allow-Origin','*')
               // set allow methods
               ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET')
							 // json
							 ->addHeaderLine('Content-Type', 'application/json');
      return $response;
  }

	/*
	* http://aws2019.gigamike.net/api/search
	*
	*/
	public function searchAction()
	{
		$results = array();

		$keyword = $this->params()->fromQuery('keyword');
		$category_id = $this->params()->fromQuery('search_category_id');
		$brand_id = $this->params()->fromQuery('search_brand_id');

		$searchFilter = array();
		if(!empty($category_id)){
			$searchFilter['category_id'] = $category_id;
		}
		if(!empty($brand_id)){
			$searchFilter['brand_id'] = $brand_id;
		}
		if(!empty($keyword)){
			$searchFilter['keyword'] = $keyword;
		}

		$order = array('product.name');
		$products = $this->getProductMapper()->getProducts(false, $searchFilter, $order);
		if(count($products)>0){
			foreach($products as $row){

				switch($row->discount_type){
					case 'amount':
						$finalPrice = $row->price - $row->discount;
						break;
					case 'percentage':
						$discount = $row->price * ($row->discount/100);
						$finalPrice = $row->price - $row->discount;
						break;
					default:
						$finalPrice = $row->price;
				}

				$results[] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'description' => $row['description'],
					'currency' => 'PHP',
					'price' => $row['price'],
					'discount_type' => $row['discount_type'],
					'discount' => $row['discount'],
					'final_price' => number_format($finalPrice, 2, '.' , ','),
					'stock' => $row['stock'],
				);
			}
		}

		$response = $this->_getResponseWithHeader()->setContent(json_encode($results));
    return $response;
	}

	/*
	* http://aws2019.gigamike.net/api/cart-add
	*
	*/
	public function cartAddAction()
	{
		$results = array();
		$errors = array();

		$config = $this->getServiceLocator()->get('Config');

		$userId = $this->params()->fromQuery('user_id');
		$productId = $this->params()->fromQuery('product_id');
		$quantity = $this->params()->fromQuery('quantity');

		if(!$productId){
			$errors['product_id'] = 'Invalid Product ID.';
		}else{
			$product = $this->getProductMapper()->getProduct($productId);
			if(!$product){
				$errors['product_id'] = 'Invalid Product ID.';
			}
		}

		$quantity = (int)$quantity;
		if(!$quantity) {
			$errors['quantity'] = 'Invalid Quantity.';
		}else if(!is_numeric($quantity)){
			$errors['quantity'] = 'Invalid Quantity.';
		}

		if(!$userId) {
			$errors['user_id'] = 'Invalid User ID.';
		}else{
			$user = $this->getUserMapper()->getUser($userId);
			if(!$user){
				$errors['user_id'] = 'Invalid User ID.';
			}
		}

		if(count($errors) <= 0){
			$filter = array(
				'product_id' => $product->getId(),
				'created_user_id' => $user->getId(),
			);
			$order = array();
			$carts = $this->getCartMapper()->getCarts(false, $filter, $order);
			if(count($carts) > 0){
				foreach ($carts as $cart) {
					$quantity = $cart['quantity'] + $quantity;

					$currentCart = $this->getCartMapper()->getCart($cart['id']);
					if($currentCart){
						$currentCart->setQuantity($quantity);
						$this->getCartMapper()->save($currentCart);
					}
				}
			}else{
				$cart = new CartEntity;
				$cart->setProductId($product->getId());
				$cart->setQuantity($quantity);
				$cart->setCreatedUserId($user->getId());
				$this->getCartMapper()->save($cart);
			}

			$message = "Big Mike: Added to cart.";
			$subject = 'Big Mike: Added to cart.';

			try {
				$mail = new Message();
				$mail->setFrom($config['email']);
				$mail->addTo($user->getEmail());
				$mail->setSubject($subject);
				$mail->setBody($message);

				// Send E-mail message
				$transport = new Sendmail('-f'. $config['email']);
				$transport->send($mail);
			} catch(\Exception $e) {
			}

			$results['success'] = 'Successfully added to cart.';
		}else{
			foreach ($errors as $error) {
				$results['error'] = $error;
			}
		}

		$response = $this->_getResponseWithHeader()->setContent(json_encode($results));
    return $response;

		/*
		if($this->getRequest()->isPost()) {
      $data = $this->params()->fromPost();

			$userId = $data['user_id'];
			$productId = $data['product_id'];
			$quantity = $data['quantity'];

			if(!$productId){
  			$errors['product_id'] = 'Invalid Product ID.';
  		}else{
				$product = $this->getProductMapper()->getProduct($productId);
				if(!$product){
					$errors['product_id'] = 'Invalid Product ID.';
				}
			}

			$quantity = (int)$data['quantity'];
      if(!$quantity) {
  			$errors['quantity'] = 'Invalid Quantity.';
  		}else if(!is_numeric($quantity)){
        $errors['quantity'] = 'Invalid Quantity.';
      }

			if(!$userId) {
				$errors['user_id'] = 'Invalid User ID.';
			}else{
				$user = $this->getUserMapper()->getUser($this->identity()->id);
	  		if(!$user){
	        $errors['user_id'] = 'Invalid User ID.';
	  		}
			}

			if(count($errors) < 0){
				$filter = array(
					'product_id' => $product->getId(),
					'created_user_id' => $user->id,
				);
				$order = array();
				$carts = $this->getCartMapper()->getCarts(false, $filter, $order);
				if(count($carts) > 0){
					foreach ($carts as $cart) {
						$quantity = $cart->getQuantity() + $quantity;
						$cart->setQuantity($quantity);
						$this->getCartMapper()->save($cart);
					}
				}else{
					$cart = new CartEntity;
					$cart->setProductId($product->getId());
					$cart->setQuantity($quantity);
					$cart->setCreatedUserId($this->identity()->id);
					$cart->setProductId($product->getId());
					$this->getCartMapper()->save($cart);
				}

				$message = "Big Mike: Added to cart.";
				$subject = 'Big Mike: Added to cart.';

				try {
					$mail = new Message();
					$mail->setFrom($config['email']);
					$mail->addTo($user->getEmail());
					$mail->setSubject($subject);
					$mail->setBody($message);

					// Send E-mail message
					$transport = new Sendmail('-f'. $config['email']);
					// $transport->send($mail);
				} catch(\Exception $e) {
				}

				$results['success'] = 'Successfully added to cart.';
			}else{
				foreach ($errors as $error) {
					$results['error'] = $error;
				}
			}
		}

		$response = $this->_getResponseWithHeader()->setContent(json_encode($results));
    return $response;
		*/
	}

	/*
	* http://aws2019.gigamike.net/api/bmi-add?user_id=10&height_centimeters=142&weight_kilograms=52&bmi=25.8&bmi_category=obese
	*
	*/
	public function bmiAddAction()
	{
		$results = array();
		$errors = array();

		$config = $this->getServiceLocator()->get('Config');

		$userId = $this->params()->fromQuery('user_id');
		$height_centimeters = $this->params()->fromQuery('height_centimeters');
		$weight_kilograms = $this->params()->fromQuery('weight_kilograms');
		$bmi = $this->params()->fromQuery('bmi');
		$bmi_category = $this->params()->fromQuery('bmi_category');

		if(!$userId) {
			$errors['user_id'] = 'Invalid User ID.';
		}else{
			$user = $this->getUserMapper()->getUser($userId);
			if(!$user){
				$errors['user_id'] = 'Invalid User ID.';
			}
		}

		if(!$height_centimeters) {
			$errors['height_centimeters'] = 'Invalid height in centimeters.';
		}else if(!is_numeric($height_centimeters)){
			$errors['height_centimeters'] = 'Invalid height in centimeters.';
		}

		if(!$weight_kilograms) {
			$errors['weight_kilograms'] = 'Invalid weight in kilograms.';
		}else if(!is_numeric($weight_kilograms)){
			$errors['weight_kilograms'] = 'Invalid weight in kilograms.';
		}

		if(!$bmi) {
			$errors['bmi'] = 'Invalid BMI.';
		}else if(!is_numeric($bmi)){
			$errors['bmi'] = 'Invalid BMI.';
		}

		if(!$bmi_category) {
			$errors['bmi_category'] = 'Invalid BMI category.';
		}

		if($bmi_category == 'Normal weight'){
			$credit = 10;
		}else{
			$credit = 0;
		}

		if(count($errors) <= 0){
			$filter = array(
				'created_user_id' => $user->getId(),
				'month' => date('n'),
				'year' => date('Y'),
			);
			$order = array();
			$incentives = $this->getIncentiveMapper()->getIncentives(false, $filter, $order);
			if(count($incentives) <= 0){
				$incentive = new IncentiveEntity;
				$incentive->setHeightCentimeters($height_centimeters);
				$incentive->setWeightKilograms($weight_kilograms);
				$incentive->setBmi($bmi);
				$incentive->setBmiCategory($bmi_category);
				$incentive->setIncentive($credit);
				$incentive->setCreatedUserId($user->getId());
				$this->getIncentiveMapper()->save($incentive);
			}

			if($credit){
				$user->setCredits($credit);
				$this->getUserMapper()->save($user);
			}

			$results['success'] = 'Success.';
		}else{
			foreach ($errors as $error) {
				$results['error'] = $error;
			}
		}

		$response = $this->_getResponseWithHeader()->setContent(json_encode($results));
    return $response;
	}

	/*
	* http://aboitiz2018.gigamike.net/api/cart-delete
	*
	*/
	public function cartDeleteAction()
	{
		$results = array();

		$userId = $this->params()->fromQuery('user_id');
		$keyword = $this->params()->fromQuery('keyword');
		$cartId = $this->params()->fromQuery('cart_id');

		if(!$userId) {
			$errors['user_id'] = 'Invalid User ID.';
		}else{
			$user = $this->getUserMapper()->getUser($this->identity()->id);
			if(!$user){
				$errors['user_id'] = 'Invalid User ID.';
			}
		}

		if(empty($cartId) && empty($keyword)){
			$errors['product'] = 'Requires cartId or product keyword.';
		}else{
			if(!empty($cartId)){
				if(!$cartId){
					$errors['cart_id'] = 'Invalid Cart ID.';
				}else{
					$cart = $this->getCartMapper()->getCart($cartId);
					if($cart){
						$this->getCartMapper()->delete($id);
						$results['success'] = "Item successfully deleted.";
					}else{
						$errors['cart_id'] = 'Invalid Cart ID.';
					}
				}
			}else if(!empty($keyword)){

			}
		}

		$response = $this->_getResponseWithHeader()->setContent(json_encode($results));
    return $response;
	}

	/*
	* http://aws2019.gigamike.net/api/nutrition-fact
	* https://docs.google.com/document/d/1_q-K-ObMTZvO0qUEAxROrN3bwMujwAN25sLHwJzliK0/edit
	* https://trackapi.nutritionix.com/docs/
	* https://gist.github.com/mattsilv/9dfb709e7609537ffd3b1b8c097e9bfb
	*
	*
	*/
	public function nutritionFactAction()
	{
		$isDebug = false;

		$results = array();

		$food = $this->params()->fromQuery('food');
		if(empty($food)){
			$results['text'] = 'Invalid food entry.';
		}else{
			$config = $this->getServiceLocator()->get('Config');

			$data = array();

			if($isDebug){
				// test data to avoid threshold limit
				$data = array(
					'foods' => array(
						array(
							'food_name' => 'rice',
							'brand_name' => '',
							'serving_qty' => 1,
							'serving_unit' => 'cup',
							'serving_weight_grams' => 158,
							'nf_calories' => 205.4,
							'nf_total_fat' => 0.44,
							'nf_saturated_fat' => 0.12,
							'nf_cholesterol' => 0,
							'nf_sodium' => 1.58,
							'nf_total_carbohydrate' => 44.51,
							'nf_dietary_fiber' => 0.63,
							'nf_sugars' => 0.08,
							'nf_protein' => 4.25,
							'nf_potassium' => 55.3,
							'nf_p' => 67.94,
						),
					),
				);
			}else{
				$url = 'https://trackapi.nutritionix.com/v2/natural/nutrients';
				$post = json_encode(array(
					'query' => $food,
				));
				$headers = array(
					'Content-Type: application/json',
					'x-app-id: '. $config['nutritionix']['application_id'],
					'x-app-key: '. $config['nutritionix']['application_key'],
					'x-remote-user-id: 0',
				);
				$results = $this->_curl($url, $post, $headers);
				if($results['error_number'] == 0){
					$data = json_decode($results['result'], true);
				}
			}

			if(count($data) > 0){
				if(isset($data['foods'][0])){
					$food_name = isset($data['foods'][0]['food_name']) ? $data['foods'][0]['food_name'] : null;
					$brand_name = isset($data['foods'][0]['brand_name']) ? $data['foods'][0]['brand_name'] : null;
					$serving_qty = isset($data['foods'][0]['serving_qty']) ? $data['foods'][0]['serving_qty'] : null;
					$serving_unit = isset($data['foods'][0]['serving_unit']) ? $data['foods'][0]['serving_unit'] : null;
					$serving_weight_grams = isset($data['foods'][0]['serving_weight_grams']) ? $data['foods'][0]['serving_weight_grams'] : null;
					$nf_calories = isset($data['foods'][0]['nf_calories']) ? $data['foods'][0]['nf_calories'] : null;
					$nf_total_fat = isset($data['foods'][0]['nf_total_fat']) ? $data['foods'][0]['nf_total_fat'] : null;
					$nf_saturated_fat = isset($data['foods'][0]['nf_saturated_fat']) ? $data['foods'][0]['nf_saturated_fat'] : null;
					$nf_cholesterol = isset($data['foods'][0]['nf_cholesterol']) ? $data['foods'][0]['nf_cholesterol'] : null;
					$nf_sodium = isset($data['foods'][0]['nf_sodium']) ? $data['foods'][0]['nf_sodium'] : null;
					$nf_total_carbohydrate = isset($data['foods'][0]['nf_total_carbohydrate']) ? $data['foods'][0]['nf_total_carbohydrate'] : null;
					$nf_dietary_fiber = isset($data['foods'][0]['nf_dietary_fiber']) ? $data['foods'][0]['nf_dietary_fiber'] : null;
					$nf_sugars = isset($data['foods'][0]['nf_sugars']) ? $data['foods'][0]['nf_sugars'] : null;
					$nf_protein = isset($data['foods'][0]['nf_protein']) ? $data['foods'][0]['nf_protein'] : null;
					$nf_potassium = isset($data['foods'][0]['nf_potassium']) ? $data['foods'][0]['nf_potassium'] : null;

					if($food_name){
						$text = "Nutrition fact for " . $food_name;
						if(!empty($brand_name)){
							$text .= " with a brand name of " . $brand_name;
						}
						if(!empty($serving_qty) && !empty($serving_unit)){
							$text .= " with a serving quantity of " . $serving_qty . " " . $serving_unit;
						}
						if(!empty($serving_weight_grams)){
							$text .= " or " . $serving_weight_grams . " grams.";
						}

						if(!empty($nf_calories)){
							$text .= "<break time=\"2s\"/> Calories " . $nf_calories;
						}
						if(!empty($nf_total_fat)){
							$text .= "<break time=\"2s\"/> Total Fats " . $nf_total_fat;
						}
						if(!empty($nf_saturated_fat)){
							$text .= "<break time=\"2s\"/> Saturated Fat " . $nf_saturated_fat;
						}
						if(!empty($nf_cholesterol)){
							$text .= "<break time=\"2s\"/> Cholesterol " . $nf_cholesterol;
						}
						if(!empty($nf_sodium)){
							$text .= "<break time=\"2s\"/> Sodium " . $nf_calories;
						}
						if(!empty($nf_total_carbohydrate)){
							$text .= "<break time=\"2s\"/> Carbohydrate " . $nf_total_carbohydrate;
						}
						if(!empty($nf_dietary_fiber)){
							$text .= "<break time=\"2s\"/> Dietary Fiber " . $nf_dietary_fiber;
						}
						if(!empty($nf_sugars)){
							$text .= "<break time=\"2s\"/> Sugars " . $nf_sugars;
						}
						if(!empty($nf_protein)){
							$text .= "<break time=\"2s\"/> Protein " . $nf_protein;
						}
						if(!empty($nf_potassium)){
							$text .= "<break time=\"2s\"/> potassium " . $nf_potassium;
						}

						$results['text'] = $text;
					}else{
						$results['text'] = 'Invalid food entry.';
					}
				}
			}else{
				$results['text'] = 'Sorry we cant find the food nutrition fact.';
			}
		}

		$response = $this->_getResponseWithHeader()->setContent(json_encode($results));
    return $response;
	}

	private function _curl($url, $post = null, $headers = array()){
    $ch = curl_init();

		$countPost = count($post);
		if(!is_null($post)){
			curl_setopt($ch, CURLOPT_POST, $countPost);
  		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(count($headers) > 0){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

    $curlHeader = curl_getinfo($ch);
    $curlResult = curl_exec($ch);
    $curlErrorMessage = curl_error($ch);
    $curlErrorNo = curl_errno($ch);
    curl_close($ch);

    $results = array();
    $results['headers'] = $curlHeader;
    $results['error_number'] = $curlErrorNo;
    $results['error_message'] =$curlErrorMessage;
    $results['result'] = $curlResult;

    return $results;
  }
}
