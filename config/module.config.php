<?php
/**
* Bakar (http://www.bakar.be)
*
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar;
$__NAMESPACE__	=	strtolower(__NAMESPACE__);
$__ADMIN__     	=	'Admin';
$__VERSION__	=	[
						'js'	=>	'3.0',
						'server'=>	'3.0',
						'admin'	=>	'3.0',
						'client'=>	'3.0',
						'module'=>	'3.0',
						'update'=>	'06/11/2015 15:20:00',
					];

return	[
	'b'				=>	[
		'default'		=>	[
			'client'	=>	[
				'service'	=>	__NAMESPACE__.'\Service\\'.__NAMESPACE__.'Service',
			],
		],
		
		$__NAMESPACE__	=>	[
			'version'		=>	$__VERSION__,

			'js'			=>	[
				'version'	=>	$__VERSION__,
			],
			
			$__NAMESPACE__	=>	[
				'service'	=>	__NAMESPACE__.'\Service\\'.__NAMESPACE__.'Service',
				'repository'=>	__NAMESPACE__.'\Repository\\'.__NAMESPACE__.'Repository',
				'listener'	=>	__NAMESPACE__.'\Listener\\'.__NAMESPACE__.'Listener',
				'event'		=>	__NAMESPACE__.'\Event\\'.__NAMESPACE__.'Event',
			],
			
			'admin'			=>	[
				'config'	=>	$__ADMIN__.'\Config\AdminConfig',
				'service'	=>	$__ADMIN__.'\Service\AdminService',
				'repository'=>	$__ADMIN__.'\Repository\AdminRepository',
				'event'		=>	$__ADMIN__.'\Event\AdminEvent',
				'listener'	=>	$__ADMIN__.'\Listener\AdminListener',
			],

			'php'			=>	[
				'date.timezone'						=>	'Europe/Brussels',
				'mbstring.internal_encoding'		=>	'UTF-8',
				'soap.wsdl_cache_enabled'			=>	0,
				'xdebug.var_display_max_depth'		=>	1,
				'xdebug.var_display_max_children'	=>	5,
				'xdebug.var_display_max_data'		=>	2,
			],
		],
	],

	
	'sessionConfig'	=>	[
		'use_cookies'			=> 	true,
		//'cookie_domain'		=> 	'http://www.housecare.be',
		'cache_expire'			=> 	((60 * 60) * 24) * 3,
		'remember_me_seconds'	=> 	((60 * 60) * 24) * 3,
		'cookie_lifetime'		=> 	((60 * 60) * 24) * 3,
		'name'					=>	'default',
	],
	
	'cookiesConfig'	=>	[
		'time'	=>	time() + (((60 * 60) * 24) * 3),
		'key'	=>	'b-identity',
	],
	
	'view_helpers'	=>	[
		'invokables'	=>	[
			'FlasMessengerHelper'	=>	__NAMESPACE__.'\View\Helper\FlashMessengerHelper',
		],
	],
	
	'view_manager'	=>	[
		'template_map'			=>	[
			'ajax'									=>	__DIR__.'/../view/plugins/ajax.phtml',
			'paginator'								=>	__DIR__.'/../view/plugins/paginator.phtml',
			'carousel/bootstrap'					=>	__DIR__.'/../view/partials/carousel/bootstrap.phtml',
			__NAMESPACE__.'/carousel/fraction'		=>	__DIR__.'/../view/partials/carousel/fraction.phtml',
			__NAMESPACE__.'/breadcrumb'				=>	__DIR__.'/../view/partials/breadcrumbs/breadcrumb.phtml',
			__NAMESPACE__.'/breadcrumb/render'		=>	__DIR__.'/../view/partials/breadcrumbs/render.phtml',
			__NAMESPACE__.'/widgets/alert'			=>	__DIR__.'/../view/partials/widgets/alert.phtml',
			__NAMESPACE__.'/widgets/flash'			=>	__DIR__.'/../view/partials/widgets/flash.phtml',
			__NAMESPACE__.'/widgets/fake'			=>	__DIR__.'/../view/partials/widgets/fake.phtml',
			__NAMESPACE__.'/navigation/language'	=>	__DIR__.'/../view/partials/navigation/language.phtml',
			__NAMESPACE__.'/email/dispatch-error'	=>	__DIR__.'/../view/email/dispatch-error.phtml',
		],
		'template_path_stack' 	=>	[
			__DIR__ . '/../view',
		],
		'strategies' 			=>	[
			'ViewJsonStrategy',
		],
	],
];