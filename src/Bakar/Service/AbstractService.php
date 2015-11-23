<?php
/**
* Bakar (http://www.bakar.be)
*
* @link         http://www.bakar.be
* @copyright    Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version 		02032015.1633
*/

namespace Bakar\Service;

use Zend\Stdlib\ArrayObject;
use DateTime;

abstract class AbstractService implements InterfaceService{
	private $serviceManager;
	private $pluginManager;
	private $controller;
	private $config;
	private $arrayObject;
	private $moduleConfig;
	private $namespace;
	private $repository;
	private $eventService;
	
	public function setServiceManager($serviceManager = NULL){
		if($serviceManager !== NULL){
			$this->serviceManager = $serviceManager;
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
	
	public function setController($controller = NULL){
		if($controller !== NULL){
			$this->controller	=	$controller;
		}
		return $this;
	}
	public function getController(){
		if($this->controller === NULL){
			$this->setController($this->getPluginManager()->getController());
		}
		return $this->controller;
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
	
	public function setNameSpace($namespace = NULL){
		if($namespace !== NULL){
			$this->namespace = $namespace;
		}
		return $this;
	}
	public function getNameSpace(){
		if($this->namespace === NULL){
			$this->setNameSpace($this->getController()->params()->fromRoute('__NAMESPACE__'));
		}
		return $this->namespace;
	}
	
	public function setConfig($config = NULL){
		if($config !== NULL){
			$this->config = $config;
		}
		return $this;
	}
	public function getConfig($key = NULL, $ArrayObject = TRUE){
		$this->setConfig($this->getModuleConfig('b'));
		$return	=	NULL;
			
		if($key === NULL){$key	=	$this->getNameSpace();}
		
		$key	=	strtolower($key);
		
		if(is_array($this->config)){
			$this->config	=	$this->getArrayObject($this->config);
		}
		
		if($this->config !== NULL && $this->config->offsetExists($key)){
			$return	=	$this->config->offsetGet($key);
			
			if($ArrayObject && is_array($return)){
				$return	=	$this->getArrayObject($return);
			}
			
			if(!$ArrayObject && is_object($return)){
				$return	=	$return->getArrayCopy();
			}
		}
		
		return $return;
	}
	
	public function setEventService($eventService = NULL){
		if($eventService !== NULL){
			$this->eventService	=	$eventService;
		}
		return $this;
	}
	public function getEventService(){
		if($this->eventService === NULL){
			$this->setEventService($this->getServiceManager()->get('Application/Event/ApplicationEvent'));
		}
		return $this->eventService;
	}
	
	public function setRepository($repository = NULL){
		if($repository !== NULL){
			$this->repository	=	$repository;
		}
		return $this;
	}
	public function getRepository(){
		$this->setRepository($this->getServiceManager()->get($this->getConfig()->offsetGet('repository')));

		return $this->repository;
	}
	
	public function getSha1(){
		return sha1(uniqid(rand(), true));
	}
	public function getMd5(){
		return md5(uniqid(rand(), true));
	}
	public function getToken($format = 'md5', $max = NULL){
		$token	=	$format == 'sha1'	?	$this->getSha1()	:	$this->getMd5();
		$token	=	$max !== NULL	?	substr($token, 0, $max)	:	$token;
		return $token;
	}
	public function getKey($max = 6){
		$key	=	$this->getMd5();
		$key	=	substr($key, 0, $max);
		return $key;
	}
	public function getDateTime($dateTime = NULL, $format = NULL, $createFormat = NULL){			
		if($dateTime == NULL){
			$dateTime	=	new DateTime();
		}
		else{
			if($createFormat !== NULL){
				$dateTime	=	DateTime::createFromFormat($createFormat, $dateTime);
			}
			else{
				$dateTime	=	new DateTime($dateTime);
			}
		}

		if($format !== NULL){
			$dateTime	=	$dateTime->format($format);
		}
		return $dateTime;
	}
	public function now($format = NULL){
		return	$this->getDateTime(NULL, $format, NULL);
	}
	
	public function isValid(){}
	public function isError(){}
	public function setRawData(){}
	public function getRawData(){}
	public function appendMessage(){}
	public function prependMessage(){}
	public function addMessage(){}
	public function setMessage(){}
	public function getMessage(){}
}