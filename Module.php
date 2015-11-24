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
use Zend\Mvc\ModuleRouteListener;

include __DIR__.'/src/'.__NAMESPACE__.'/Module/AbstractModule.php';

class Module extends Module\AbstractModule{		
	private $systems;
	public function onBootstrap(MvcEvent $e){
		$this	->setEvent($e)
				->php()
				->setConfigForJs();
		
		$this	->getEventManager()
				->attach('route', [$this, 'route']);
		
		$this	->getEventManager()
				->attach('dispatch', [$this, 'dispatch']);
				
		$this	->getEventManager()
				->attach('render', [$this, 'render']);
				
		$this	->getEventManager()
				->attach('finish', [$this, 'finish']);
				
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($this->getEventManager());
	}
	public function route(MvcEvent $e){
		$this	->setEvent($e)
				->modules();
	}
	public function dispatch($e){
		$this	->setEvent($e);
	}
	public function render(MvcEvent $e){
		$this	->setEvent($e)
				->setIdentity();
		
		return $this;
	}
	public function finish(MvcEvent $e){
		$this	->setEvent($e);
	}
	
	public function modules(){
		$modules	=	$this	->getSystems()
								->getEventService()
								->trigger('modules', $this, ['modules' => []])
								->getEvent()
								->getParam('modules');

		$this	->getSystems()
				->setModules($this->getArrayObject($modules));
		
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
			'factories'	=>	[],
		];
	}
	
 	public function setSystems($systems = NULL){ 
 		if($systems !== NULL){ 
 			$this->systems	=	$systems; 
 		} 
 		return $this; 
 	} 
 	public function getSystems(){ 
 		if($this->systems === NULL){ 
			$rootConfig	=	$this->getConfig();
			$b			=	$rootConfig['b'];
			$config		=	$b['bakar'];
			$config		=	$config['bakar'];
			$systems	=	$config['service'];
			$systems	=	$this->getServiceManager()->get($systems);
			$this->setSystems($systems);
 		} 
 		return $this->systems; 
	} 
}
