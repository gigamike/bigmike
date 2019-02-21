<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Blog\Controller\Index' => 'Blog\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'blog' => __DIR__ . '/../view',
				),
		),
);
