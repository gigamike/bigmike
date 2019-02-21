<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Facebook\Controller\Index' => 'Facebook\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'facebook' => __DIR__ . '/../view',
				),
		),
);
