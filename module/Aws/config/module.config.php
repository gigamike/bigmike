<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Aws\Controller\Index' => 'Aws\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'aws' => __DIR__ . '/../view',
				),
		),
);
