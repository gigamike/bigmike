<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Member\Controller\Index' => 'Member\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'member' => __DIR__ . '/../view',
				),
		),
);
