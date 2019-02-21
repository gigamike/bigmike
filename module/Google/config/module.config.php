<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Google\Controller\Index' => 'Google\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'google' => __DIR__ . '/../view',
				),
		),
);
