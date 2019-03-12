<?php

namespace Chatbot\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

class IndexController extends AbstractActionController
{
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

    $this->ask('get my body mass index', function(Answer $answer) {
      // Save result
      $this->firstname = $answer->getText();

      $this->say('Nice to meet you '.$this->firstname);
      $this->askEmail();
    });

    // Start listening
    $botman->listen();

    return $this->getResponse();
  }

  private function _getBodyMassIndex(){

  }
}
