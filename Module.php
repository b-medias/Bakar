<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;

include __DIR__.'/src/'.__NAMESPACE__.'/Module/AbstractModule.php';

class Module extends Module\AbstractModule{		
	public function onBootstrap(MvcEvent $e){
		$this	->setEvent($e)
				->php()
				->setConfigForJs();
		
		$this	->getEventManager()
				->attach('render', [$this, 'render']);
	}
	public function render(MvcEvent $e){
		$this	->setEvent($e)
				->setIdentity();
		
		return $this;
	}
	public function php(){
		$rootConfig	=	$this->getConfig();
		$b			=	$rootConfig['b'];
		$config		=	$b[strtolower(__NAMESPACE__)];
		$php		=	$config['php'];
		
		foreach($php as	$key => $value){
			ini_set($key, $value);
		}
		return $this;
	}
	public function setConfigForJs(){
		$rootConfig	=	$this->getConfig();
		$b			=	$rootConfig['b'];
		$config		=	$b[strtolower(__NAMESPACE__)];
		$js			=	$config['js'];
		$this->getEvent()->getViewModel()->setVariable('js', $js);
		return $this;
	}
	public function setIdentity(){
		$rootConfig	=	$this->getConfig();
		$b			=	$rootConfig['b'];
		$config		=	$b[strtolower(__NAMESPACE__)];
		$config		=	$config[strtolower(__NAMESPACE__)];
		$service	=	$config['service'];
		$service	=	$this->getServiceManager()->get($service);
		
		$e			=	$service	->getEventService()
									->trigger('identity', $this, ['identity' => NULL])
									->getEvent();
		
		/**
		*	RETURN DIRECT ACCESS
		* 
		if($identity == NULL && $route != 'signin' && $route != 'login'){
			return	$this->redirect()->toRoute('home');
		}
		*/
		
		$this->getEvent()->getViewModel()->setVariable('identity', $e->getParam('identity'));
		return $this;
	}
	public function getConfig(){
		return include __DIR__ . '/config/module.config.php';
	}
	public function getAutoloaderConfig(){
		return [
			'Zend\Loader\StandardAutoloader'	=>	[
				'namespaces'	=>	[
					__NAMESPACE__	=>	__DIR__.'/src/'. __NAMESPACE__,
				],
			],
		];
	}
	
	public function getServiceConfig(){
		return [
			'factories'	=>	[
				__NAMESPACE__.'/Service/'.__NAMESPACE__.'Service'	=>	function($serviceManager){
																			$service	=	new Service\BakarService();
																			$service->setServiceManager($serviceManager);
																			return $service;
																		},
				__NAMESPACE__.'/Event/'.__NAMESPACE__.'Event'		=>	function($serviceManager){
																			$event		=	new Event\BakarEvent;
																			return 	$event->setServiceManager($serviceManager);
																		},
			],
		];
	}
	public function getControllerPluginConfig(){
		return [
			'factories'	=>	[
				'viewPlugin'			=>	function (){
					$plugin	=	new Plugins\ViewPlugin;
					return	$plugin;
				},
				'dbPlugin'				=>	function(){
					$plugin	=	new Plugins\DbPlugin;
					return	$plugin;		
				},
				'devicePlugin'			=>	function(){
					$plugin	=	new Plugins\DevicePlugin;
					return $plugin;
				},
			],
		];
	}
	public function getViewHelperConfig(){
		return [
			'factories'	=>	array[],
		];
	}
}
