<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Incentive\Controller\Index' => 'Incentive\Controller\IndexController',
			),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'incentive' => __DIR__ . '/../view',
				),
		),
);
