<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Cron\Controller\Index' => 'Cron\Controller\IndexController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'cron' => __DIR__ . '/../view',
				),
		),
);
