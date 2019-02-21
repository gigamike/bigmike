<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Brand\Controller\Index' => 'Brand\Controller\IndexController',
				'Brand\Controller\Supplier' => 'Brand\Controller\SupplierController',
			),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'brand' => __DIR__ . '/../view',
				),
		),
);
