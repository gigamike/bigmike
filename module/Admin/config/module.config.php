<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Admin\Controller\Index' => 'Admin\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'admin' => __DIR__ . '/../view',
				),
		),
);
