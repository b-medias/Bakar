<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use ArrayObject;

abstract class AbstractListener implements ListenerAggregateInterface{
	private $namespace;
	private $arrayObject;
	private $moduleConfig;
	private $rootConfig;
	private $config;
	private $serviceManager;
	private $pluginManager;
	private $moduleName;
	private $listeners	=	array();
	
	public function test(){
		$this->debug('Hello world from '.get_called_class(), true);
	}
	
	public function debug($data, $exit = TRUE, $js = FALSE){
		echo	$js	?	'<script type="text/javascript">console.log('.print_r($data, true).');</script>'	:
						'<pre>'.print_r($data, true).'</pre>';
		
		if($exit){exit;}
	}

	public function setModuleName($moduleName = NULL){
		if($moduleName !== NULL){
			$this->moduleName = $moduleName;
		}
		return $this;
	}
	public function getModuleName(){
		if($this->moduleName === NULL){
			$className	=	get_called_class();				
			$className	=	substr($className, 0, strpos($className, '\\'));
			$this->setModuleName($className);
		}
		return $this->moduleName;
	}
	
	public function setNameSpace($namespace = NULL){
		if($namespace !== NULL){
			$this->namespace = $namespace;
		}
		return $this;
	}
	public function getNameSpace(){
		if($this->namespace === NULL){
			$className	=	get_called_class();			
			
			if(substr_count($className, '\\') > 2){
				$className	=	substr($className, strpos($className, '\\')+1);
			}
						
			$className	=	substr($className, 0, strpos($className, '\\'));
			$this->setNameSpace($className);
		}
		return $this->namespace;
	}	
	
	public function setArrayObject(ArrayObject $arrayObject = NULL){
		if($arrayObject !== NULL){
			$this->arrayObject	=	$arrayObject;
		}
		return $this;
	}
	public function getArrayObject($input = array(), $flags = ArrayObject::ARRAY_AS_PROPS, $iteratorClass = 'ArrayIterator'){
		$this->setArrayObject(new ArrayObject($input, $flags, $iteratorClass));
		return $this->arrayObject;
	}

	public function setModuleConfig($moduleConfig = NULL){
		if($moduleConfig !== NULL){
			$this->moduleConfig	=	$moduleConfig;
		}
		return $this;
	}
	public function getModuleConfig($key = NULL, $ArrayObject = TRUE){
		$return;
		
		if($this->moduleConfig === NULL){
			$this->setModuleConfig($this->getServiceLocator()->get('Config'));
		}
		
		if($this->moduleConfig === NULL){
			$this->setModuleConfig(array());
		}
		
		$this->moduleConfig	=	$ArrayObject === TRUE	?	$this->getArrayObject($this->moduleConfig)	:	$this->moduleConfig;
		$return				=	$this->moduleConfig;
		
		if($this->moduleConfig->offsetExists($key)){
			$return	=	$this->moduleConfig->offsetGet($key);		
		}
		
		return $return;
	}

	public function	setRootConfig($rootConfig = NULL){
		if($rootConfig !== NULL){
			$this->rootConfig	=	$rootConfig;
		}
		return $this;
	}
	public function getRootConfig($key = NULL){
		if($this->rootConfig === NULL){
			$this->setRootConfig($this->getModuleConfig('b'));
		}
				
		if($key !== NULL){
			$key	=	strtolower($key);
								
			if(array_key_exists($key, $this->rootConfig)){
				return	$this->rootConfig[$key];
			}
			
			return 	$this->rootConfig['default'];
		}
		
		return $this->rootConfig;
	}
	
	public function setConfig($config = NULL){
		if($config !== NULL){
			$this->config	=	$config;
		}
		return $this;
	}
	public function getConfig($arrayObject = TRUE){
		if($this->config === NULL){	
			$config	=	$arrayObject	?	$this->getArrayObject($this->getRootConfig($this->getModuleName()))	:
											$this->getRootConfig($this->getModuleName());
												
			$this->setConfig($config);
		}
		return $this->config;
	}
	
	public function setServiceManager($serviceManager = NULL){
		if($serviceManager !== NULL){
			$this->serviceManager 	=	$serviceManager;
		}
		return $this;
	}
	public function getServiceManager(){
		return $this->serviceManager;
	}
	public function getServiceLocator(){
		return	$this->getServiceManager();
	}
	
	public function setPluginManager($pluginManager = NULL){
		if($pluginManager !== NULL){
			$this->pluginManager	=	$pluginManager;
		}
		return $this;
	}
	public function getPluginManager(){
		if($this->pluginManager === NULL){
			$this->setPluginManager($this->getServiceManager()->get('controllerpluginmanager'));
		}
		return $this->pluginManager;
	}
		
	public function attach(EventManagerInterface $events){
		foreach($this->getListeners() as $listener){
			$this->listeners[]	=	$events	->getSharedManager()
											->attach(
												$listener['identifier'],
												$listener['event'],
												$listener['fx'],
												$listener['priority']
											);
		}
		return $this;
	}
	public function detach(EventManagerInterface $events){
		foreach($this->listeners as $key => $listener){
			if($events->detach($listener)){
				unset($this->listeners[$key]);
			}
		}
		return $this;
	}
}