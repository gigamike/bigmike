<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Page\Controller\Index' => 'Page\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'page' => __DIR__ . '/../view',
				),
		),
);
