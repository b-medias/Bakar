<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Module;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\Stdlib\ArrayObject;

abstract class AbstractModule{
	protected $event;
	protected $application;
	protected $eventManager;
	protected $serviceManager;
	protected $routeMatch;
	protected $request;
	protected $response;
	protected $pluginManager;
	protected $db;
	protected $namespace;
	
	public function setEvent($event = NULL){
		if($event !== NULL){
			$this->event	=	$event;
		}
		return $this;
	}
	public function getEvent(){
		return	$this->event;
	}
	
	public function setApplication($application = NULL){
		if($application !== NULL){
			$this->application	=	$application;
		}
		return $this;
	}
	public function getApplication(){
		if($this->application === NULL){
			$this->setApplication($this->getEvent()->getApplication());
		}
		return $this->application;
	}
	
	public function setEventManager($eventManager = NULL){
		if($eventManager !== NULL){
			$this->eventManager	=	$eventManager;
		}
		return $this;
	}
	public function getEventManager(){
		if($this->eventManager === NULL){
			$this->setEventManager($this->getApplication()->getEventManager());
		}
		return $this->eventManager;
	}
	
	public function setServiceManager($serviceManager = NULL){
		if($serviceManager !== NULL){
			$this->serviceManager	=	$serviceManager;
		}
		return $this;
	}
	public function getServiceManager(){
		if($this->serviceManager === NULL){
			$this->setServiceManager($this->getApplication()->getServiceManager());
		}
		return $this->serviceManager;
	}
	
	public function setRouteMatch($routeMatch = NULL){
		if($routeMatch !== NULL){
			$this->routeMatch	=	$routeMatch;
		}
		return $this;
	}
	public function getRouteMatch(){
		if($this->routeMatch === NULL){
			$this->setRouteMatch($this->getEvent()->getRouteMatch());
		}
		return $this->routeMatch;
	}
	
	public function setRequest($request = NULL){
		if($request !== NULL){
			$this->request	=	$request;
		}
		return $this;
	}
	public function getRequest(){
		if($this->request === NULL){
			$this->setRequest($this->getEvent()->getRequest());
		}
		return $this->request;
	}
	
	public function setResponse($response = NULL){
		if($response !== NULL){
			$this->response	= $response;
		}
		return $this;
	}
	public function getResponse(){
		if($this->response === NULL){
			$this->setResponse($this->getEvent()->getResponse());
		}
		return $this->response;
	}
	
	public function setPluginManager($pluginManager = NULL){
		if($pluginManager !== NULL){
			$this->pluginManager	=	$pluginManager;
		}
		return	$this;
	}
	public function getPluginManager(){
		if($this->pluginManager === NULL){
			$this->setPluginManager($this->getServiceManager()->get('controllerpluginmanager'));
		}
		return $this->pluginManager;
	}
	
	public function setDb($db = NULL){
		if($db !== NULL){
			$this->db	=	$db;
		}
		return $this;
	}
	public function getDb(){
		if($this->db === NULL){
			$this->setDb($this->getPluginManager()->get('dbplugin'));
		}
		return $this->db;
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
			$className	=	substr($className, 0, strpos($className, '\\'));
			$this->setNameSpace($className);
		}
		return $this->namespace;
	}
	
	public function test(){
		$this->debug('Hello world from '.get_called_class(), true);
	}
	public function debug($data, $exit = TRUE, $js = FALSE){
		echo	$js	?	'<script type="text/javascript">console.log('.print_r($data, true).');</script>'	:
						'<pre>'.print_r($data, true).'</pre>';
		
		if($exit){exit;}
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
}