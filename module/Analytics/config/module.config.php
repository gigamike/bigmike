<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Analytics\Controller\Index' => 'Analytics\Controller\IndexController',
			),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'analytics' => __DIR__ . '/../view',
				),
		),
);
