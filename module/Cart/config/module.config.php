<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Cart\Controller\Index' => 'Cart\Controller\IndexController',
			),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'cart' => __DIR__ . '/../view',
				),
		),
);
