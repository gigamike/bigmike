<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Api\Controller\Index' => 'Api\Controller\IndexController',
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'api' => __DIR__ . '/../view',
		),
	),
);
