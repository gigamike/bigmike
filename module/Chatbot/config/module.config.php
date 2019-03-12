<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Chatbot\Controller\Index' => 'Chatbot\Controller\IndexController',
			),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'chatbot' => __DIR__ . '/../view',
				),
		),
);
