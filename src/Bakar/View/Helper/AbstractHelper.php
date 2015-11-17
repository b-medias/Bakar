<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\View\Helper;
use Zend\View\Helper\AbstractHelper	as ZendAbstractHelper;


abstract class AbstractHelper extends ZendAbstractHelper{
	const SYSTEMS_SERVICE	=	'Bakar\Service\SystemsService';
	protected $systems;
	protected $serviceManager;
	protected $pluginManager;
	
	public function __invoke(){
		return $this;
	}
	
	public function setPluginManager($pluginManager = NULL){
		if($pluginManager !== NULL){
			$this->pluginManager	=	$pluginManager;
		}
		return $this;
	}
	public function getPluginManager(){
		if($this->pluginManager === NULL){
			$this->setPluginManager($this->getServiceManager()->get('pluginmanager'));
		}
		return $this->pluginManager;
	}
	
	public function setServiceManager($serviceManager = NULL){
		if($serviceManager !== NULL){
			$this->serviceManager	=	$serviceManager;
		}
		return $this;
	}
	public function getServiceManager(){
		if($this->serviceManager === NULL){
			$this->setServiceManager($this->generateServiceManager());
		}
		return $this->serviceManager;
	}
	public function generateServiceManager(){
		return	$this->getView()->getHelperPluginManager()->getServiceLocator();
	}
	
	
	
	
	public function setSystems($systems = NULL){
		if($systems !== NULL){
			$this->systems	=	$systems;
		}
		return $this;
	}
	
	public function getSystems(){
		if($this->systems	===	NULL){
			$this->setSystems($this->generateSystems());
		}
		return $this->systems;
	}
	public function generateSystems(){
		return	$this->getServiceManager()->get(self::SYSTEMS_SERVICE);
	}
	
	
	public function getService($name){
		return	$this->getSystems()->getService($name);
	}
}