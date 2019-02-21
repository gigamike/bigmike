<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Category\Controller\Index' => 'Category\Controller\IndexController',
				'Category\Controller\Supplier' => 'Category\Controller\SupplierController',
			),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'category' => __DIR__ . '/../view',
				),
		),
);
