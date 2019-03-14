<?php

namespace Chatbot\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

use Cart\Model\CartEntity;

class IndexController extends AbstractActionController
{
  public function getCartMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('CartMapper');
  }

  public function indexAction()
  {
    $viewModel = new ViewModel();
    $viewModel->setVariables(array('key' => 'value'))
              ->setTerminal(true);
    return $viewModel;
  }

  public function serverAction()
  {
    $config = [
        // Your driver-specific configuration
        // "telegram" => [
        //    "token" => "TOKEN"
        // ]
    ];

    // Load the driver(s) you want to use
    DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

    // Create an instance
    $botman = BotManFactory::create($config);

    // Give the bot something to listen for.
    $botman->hears('hello', function (BotMan $bot) {
      $bot->reply('Welcome to the Big Mike, your personal fitness coach by team Gigamike. Do you want to check if your weight is healthy? just say get my body mass index. Or ask about food nutrition facts. Example, by saying nutrition facts for rice. Or order supplements online by saying, add Enervon C.');
    });

    $botman->hears('get my body mass index', function (BotMan $bot) {
      $bot->reply('What is your height in centimeters? Just say my height is N centimeters');
    });

    $botman->hears('my height is {HeightCentimeters} centimeters', function (BotMan $bot, $heightCentimeters) {
      $container = new Container('BMI');
      $container->heightCentimeters = $heightCentimeters;
      $bot->reply('You are ' . $container->heightCentimeters . ' centimeters in height. What is your weight in kilograms? Just say my weight is N kilograms');
    });

    $botman->hears('my weight is {WeightKilograms} kilograms', function (BotMan $bot, $weightKilograms) {
      $container = new Container('BMI');
      $container->weightKilograms = $weightKilograms;

      $reply = '';

      if($container->heightCentimeters > 0 && $container->weightKilograms > 0){
        $bmi = $container->weightKilograms / ($container->heightCentimeters / 100 * $container->heightCentimeters / 100);

        if($bmi < 18.5){
          $bmiResut = "Your B M I category is underweight.";
          $reply .= 'You are ' . $container->weightKilograms . ' kilograms in weight. ' . $bmiResut . ".";
          $reply .= 'I can recommend a diet for underweight by saying diet for underweight. Or I can recommend a gym program for underweight by saying gym program for underweight.';
        }else if($bmi >= 18.5 && $bmi <= 24.9){
          $bmiResut = "Your B M I category is normal weight.";
          $reply .= 'You are ' . $container->weightKilograms . ' kilograms in weight. ' . $bmiResut . ".";
          $reply .= 'Congratulations! Your physically fit. I can recommend a diet to maintain your normal weight by saying diet for normal weight. Or I can recommend a gym program to maintain your normal weight by saying gym program for normal weight.';
        }else if($bmi >= 25 && $bmi <= 29.9){
          $bmiResut = "Your B M I category is overweight.";
          $reply .= 'You are ' . $container->weightKilograms . ' kilograms in weight. ' . $bmiResut . ".";
          $reply .= 'I can recommend a diet for underweight by saying diet for overweight. Or I can recommend a gym program for underweight by saying gym program for overweight.';
        }else if($bmi >= 30){
          $bmiResut = "Your B M I category is obese.";
          $reply .= 'You are ' . $container->weightKilograms . ' kilograms in weight. ' . $bmiResut . ".";
          $reply .= 'I can recommend a diet for underweight by saying diet for obese. Or I can recommend a gym program for obese by saying gym program for obese.';
        }
      }else{
        $bmiResut = "Invalid entries!";
        $reply .= $bmiResut;
      }

      $bot->reply($reply);
    });

    $botman->hears('diet for underweight', function (BotMan $bot) {
      $reply = 'Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. ';
      $reply .= 'Foods that should be included every day.';
      $reply .= 'Full cream milk 750 to 1000 ml or 3 to 4 cups.';
      $reply .= 'Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.';
      $reply .= 'Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.';
      $reply .= 'Fruit and vegetables 3to 5 servings.';
      $reply .= 'Fats and oils 90 grams 6 tablespoons.';
      $reply .= 'or Healthy desserts 1 to 2 servings';
      $bot->reply($reply);
    });
    $botman->hears('diet for normal weight', function (BotMan $bot) {
      $reply = 'Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. ';
      $reply .= 'Foods that should be included every day.';
      $reply .= 'Full cream milk 750 to 1000 ml or 3 to 4 cups.';
      $reply .= 'Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.';
      $reply .= 'Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.';
      $reply .= 'Fruit and vegetables 3to 5 servings.';
      $reply .= 'Fats and oils 90 grams 6 tablespoons.';
      $reply .= 'or Healthy desserts 1 to 2 servings';
      $bot->reply($reply);
    });
    $botman->hears('diet for overweight', function (BotMan $bot) {
      $reply = 'Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. ';
      $reply .= 'Foods that should be included every day.';
      $reply .= 'Full cream milk 750 to 1000 ml or 3 to 4 cups.';
      $reply .= 'Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.';
      $reply .= 'Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.';
      $reply .= 'Fruit and vegetables 3to 5 servings.';
      $reply .= 'Fats and oils 90 grams 6 tablespoons.';
      $reply .= 'or Healthy desserts 1 to 2 servings';
      $bot->reply($reply);
    });
    $botman->hears('diet for obese', function (BotMan $bot) {
      $reply = 'Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. ';
      $reply .= 'Foods that should be included every day.';
      $reply .= 'Full cream milk 750 to 1000 ml or 3 to 4 cups.';
      $reply .= 'Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.';
      $reply .= 'Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.';
      $reply .= 'Fruit and vegetables 3to 5 servings.';
      $reply .= 'Fats and oils 90 grams 6 tablespoons.';
      $reply .= 'or Healthy desserts 1 to 2 servings';
      $bot->reply($reply);
    });

    $botman->hears('gym program for underweight', function (BotMan $bot) {
      $bot->reply('Gym program for underweight<video width="200" height="200" controls>
  <source src="https://s3.amazonaws.com/gigamike/exercise.mp4" type="video/mp4">
Your browser does not support the video tag.
</video>');
    });
    $botman->hears('gym program for normal weight', function (BotMan $bot) {
      $bot->reply('Gym program for weight<video width="200" height="200" controls>
  <source src="https://s3.amazonaws.com/gigamike/exercise.mp4" type="video/mp4">
Your browser does not support the video tag.
</video>');
    });
    $botman->hears('gym program for overweight', function (BotMan $bot) {
      $bot->reply('Gym program for overweight<video width="200" height="200" controls>
  <source src="https://s3.amazonaws.com/gigamike/exercise.mp4" type="video/mp4">
Your browser does not support the video tag.
</video>');
    });
    $botman->hears('gym program for obese', function (BotMan $bot) {
      $bot->reply('Gym program for obese<video width="200" height="200" controls>
  <source src="https://s3.amazonaws.com/gigamike/exercise.mp4" type="video/mp4">
Your browser does not support the video tag.
</video>');
    });

    $botman->hears('add Enervon C to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 18;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('Enervon C added to cart.');
      }
    });

    $botman->hears('add Optimum Nutrition Whey Protein to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 15;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('Optimum Nutrition (ON) Gold Standard 100% Whey Protein Powder - 5 lbs, 2.27 kg (Double Rich Chocolate) added to cart');
      }
    });

    $botman->hears('add Centrum Multivitamin to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 16;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('Centrum Multivitamin for Adults added to cart');
      }
    });

    $botman->hears('add Muscle Tech Creatine to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 20;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('MuscleTech Platinum Creatine Monohydrate Powder, 14.1oz (80 Servings) added to cart');
      }
    });

    $botman->hears('add Hydroxycut Hardcore to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 21;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('Hydroxycut Hardcore Elite added to cart');
      }
    });

    $botman->hears('add Optimum Nutrition L Glutamine to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 22;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('Optimum Nutrition L-Glutamine Capsules added to cart');
      }
    });

    $botman->hears('add Animal Pak Multivitamin to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 19;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('Animal Pak Multivitamin 30 Paks added to cart');
      }
    });

    $botman->hears('add Schiek wrist wraps to my cart', function (BotMan $bot) {
      $authService = $this->serviceLocator->get('auth_service');
      if (!$authService->getIdentity()) {
        $bot->reply('Please signup or login to your account.');
      }else{
        $productId = 17;
        $quantity = 1;
        $userId = $this->identity()->id;

        $filter = array(
  				'product_id' => $productId,
  				'created_user_id' => $userId,
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
  				$cart->setProductId($productId);
  				$cart->setQuantity($quantity);
  				$cart->setCreatedUserId($userId);
  				$this->getCartMapper()->save($cart);
  			}
        $bot->reply('Schiek Black Line Heavy Duty Wrist Wraps added to cart');
      }
    });

    $botman->hears('nutrition fact for {food}', function (BotMan $bot, $food) {
      $isDebug = false;

  		$results = array();

  		if(empty($food)){
  			$reply = 'Invalid food entry.';
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
  							$text .= ".=Calories " . $nf_calories;
  						}
  						if(!empty($nf_total_fat)){
  							$text .= ".=Total Fats " . $nf_total_fat;
  						}
  						if(!empty($nf_saturated_fat)){
  							$text .= ".=Saturated Fat " . $nf_saturated_fat;
  						}
  						if(!empty($nf_cholesterol)){
  							$text .= ".=Cholesterol " . $nf_cholesterol;
  						}
  						if(!empty($nf_sodium)){
  							$text .= ".=Sodium " . $nf_calories;
  						}
  						if(!empty($nf_total_carbohydrate)){
  							$text .= ".=Carbohydrate " . $nf_total_carbohydrate;
  						}
  						if(!empty($nf_dietary_fiber)){
  							$text .= ".=Dietary Fiber " . $nf_dietary_fiber;
  						}
  						if(!empty($nf_sugars)){
  							$text .= ".=Sugars " . $nf_sugars;
  						}
  						if(!empty($nf_protein)){
  							$text .= ".=Protein " . $nf_protein;
  						}
  						if(!empty($nf_potassium)){
  							$text .= ".=potassium " . $nf_potassium;
  						}

  						$reply = $text;
  					}else{
  						$reply = 'Invalid food entry.';
  					}
  				}
  			}else{
  				$reply = 'Sorry we cant find the food nutrition fact.';
  			}
  		}

      $bot->reply($reply);
    });

    // Start listening
    $botman->listen();

    return $this->getResponse();
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
