<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Contact\Controller\Index' => 'Contact\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'contact' => __DIR__ . '/../view',
				),
		),
);