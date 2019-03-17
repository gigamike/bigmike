<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

 return array(
   'service_manager' => array(
     'factories' => array(
       'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
       'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
     ),
     'aliases' => array(
       'Zend\Authentication\AuthenticationService' => 'auth_service',
     ),
   ),
   /*
   'session' => array(
    'config' => array(
        'class' => 'Zend\Session\Config\SessionConfig',
        'options' => array(
            'name' => 'myapp',
        ),
    ),
    'storage' => 'Zend\Session\Storage\SessionArrayStorage',
    'validators' => array(
      'Zend\Session\Validator\RemoteAddr',
      'Zend\Session\Validator\HttpUserAgent',
    ),
   ),
   */
   'baseUrl' => 'http://www.local71.web/bigmike-master',
   // for console, http://stackoverflow.com/questions/2412009/starting-with-zend-tutorial-zend-db-adapter-throws-exception-sqlstatehy000
   // sudo mkdir /var/mysql
   // cd /var/mysql && sudo ln -s /Applications/XAMPP/xamppfiles/var/mysql/mysql.sock
   'db' => array(
     'driver' => 'Pdo',
     'dsn' => "mysql:dbname=aws2019;host=localhost",
     'username' => 'root',
     'password' => 'root',
     'driver_options' => array(
       PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
     ),
   ),


   'ip' => '112.210.106.129',
   'email' => 'gigamike@gigamike.net',
   'staticSalt' => 'oYiwd1SQL4UopvW',
   'reCaptcha' => [
    'site_key' => '6LeYkXAUAAAAAIok4bTZ3gGGFcQH1kPd6T_vr1ya',
    'secret_key'     => '6LeYkXAUAAAAAD85r8-cxQh4Z3-mFRfBgfRhfvNa',
    'url_verify' => 'https://www.google.com/recaptcha/api/siteverify',
  ],
  // https://developers.facebook.com/
  'facebook' => [
    'app_id'      => '552252351911439',
    'app_secret'  => 'e90ebe713a061c2ecbb5a20a297864f9',
    'app_version'  => 'v3.1',
  ],
  // https://console.developers.google.com/
  'google' => [
    'client_id'      => '953485555798-ar507hr2pknsoucc7h66hsva0nt93kal.apps.googleusercontent.com',
    'client_secret'  => 'LDHbMR7zjBmTDF6buttRWEPX',
  ],
  'googleMap' => array(
      'Api Key' => 'AIzaSyCtDULReJ9qfITMs3a664_aV3b_Olufvak',
   ),
  'aws' => [
    'access_key'      => 'AKIAITHPPLOCJ6LEEXHA',
    'secret_access_key'  => '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',
    'region' => 'us-east-1',
  ],
  'pathBrandLogo' => [
    'absolutePath' => getcwd() . "/public_html/img/brand/",
    'relativePath' =>  "/img/brand/",
  ],
  'pathProductPhoto' => [
    'absolutePath' => getcwd() . "/public_html/img/product/",
    'relativePath' => "/img/product/",
  ],
  'nutritionix' => [
    'application_id' => 'e33bbdf5',
    'application_key'  => 'ae700a3269f3532cd79a6ca718e98199',
  ],
 );
