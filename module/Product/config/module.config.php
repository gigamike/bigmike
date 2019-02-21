<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Product\Controller\Index' => 'Product\Controller\IndexController',
				'Product\Controller\Supplier' => 'Product\Controller\SupplierController',
			),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'product' => __DIR__ . '/../view',
				),
		),
);
