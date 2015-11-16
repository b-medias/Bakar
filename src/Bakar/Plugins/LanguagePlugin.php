<?php
/**
*	ViewPlugin
*	Version 1.0
*/
namespace Bakar\Plugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;

class LanguagePlugin extends AbstractPlugin{
	private $default	=	array(
		'identifiantContainer'	=>	'locale',
		'identifiantRequest' 	=>	'language',
		'identifiantSession'	=>	'language',
		'language'				=>	'fr',
	);
	private $language;
	private $identifiantRequest;
	private $identifiantSession;
	private $identifiantContainer;
	private $container;
	private $pluginManager;
	private $serviceLocator;
	
	public function __construct($pm){
		$this->setPluginManager($pm);
		$this->setServiceLocator($pm->getServiceLocator());
	}
    
	public function setPluginManager($pluginManager = NULL){
		if($pluginManager !== NULL){
			$this->pluginManager = $pluginManager;
		}
		return $this;
	}
	public function getPluginManager(){
		return	$this->pluginManager;
	}
	public function setServiceLocator($serviceLocator = NULL){
		if($serviceLocator !== NULL){
			$this->serviceLocator	=	$serviceLocator;
		}
		return $this;
	}
	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	public function convertToAuthorizedLanguage($language = NULL){
		$languages	=	array(
			'fr',
			'nl',
			'en',
			'de',
		);
		
		if(!in_array($language, $languages)){
			$language	=	$this->default['language'];
		}
		return $language;
	}
	public function saveInSession(){
		$locale	=	$this->getContainer();
		$locale->offsetSet($this->getIdentifiantSession(), $this->language);
		return $this;
	}
	public function fromSession(){
		return	$this	->getContainer()
						->offsetGet($this->getIdentifiantSession());
	}
	public function setLanguage($language = NULL){
		if($language !== NULL){
			$this->language = $language;
		}
		return $this;
	}
	public function getLanguage($format = NULL, $test = NULL){
		$locale		=	$this->getContainer();
		if($this->getController() != NULL && $this->getController()->params()->fromRoute($this->getIdentifiantRequest())){
			$language	=	$this->getController()->params()->fromRoute($this->getIdentifiantRequest());
		}
		else if($locale->offsetExists($this->getIdentifiantSession())){
			$language 	=	$locale->offsetGet($this->getIdentifiantSession());
		}
		else{
			$language	=	\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$language	=	$this->convertToAuthorizedLanguage($language);
		}

		switch($format){
			case 'upper':
			$language	=	strtoupper($language);
			break;
			
			case 'lower':
			$language	=	strtolower($language);
			break;
			
			default:
			break;
		}
		
		$this->setLanguage($language);
		$this->saveInSession();
				
		return $this->language;
	}
	public function setContainer($container = NULL){
		if($container !== NULL){
			$this->container = $container;
		}
		return $this;
	}
	public function getContainer(){
		if($this->container === NULL){
			$this->setContainer(new Container($this->getIdentifiantContainer()));
		}
		return $this->container;
	}
	public function setIdentifiantContainer($identifiantContainer = NULL){
		if($identifiantContainer !== NULL){
			$this->identifiantContainer = $identifiantContainer;
		}
		return $this;
	}
	public function getIdentifiantContainer(){
		if($this->identifiantContainer === NULL){
			$this->setIdentifiantContainer($this->default['identifiantContainer']);
		}
		return $this->identifiantContainer;
	}
	public function setIdentifiantRequest($identifiantRequest = NULL){
		if($identifiantRequest !== NULL){
			$this->identifiantRequest = $identifiantRequest;
		}
		return $this;
	}
	public function getIdentifiantRequest(){
		if($this->identifiantRequest === NULL){
			$this->setIdentifiantRequest($this->default['identifiantRequest']);
		}
		return $this->identifiantRequest;
	}
	public function setIdentifiantSession($identifiantSession = NULL){
		if($identifiantSession !== NULL){
			$this->identifiantSession = $identifiantSession;
		}
		return $this;
	}
	public function getIdentifiantSession(){
		if($this->identifiantSession === NULL){
			$this->setIdentifiantSession($this->default['identifiantSession']);
		}
		return $this->identifiantSession;
	}
	public function firstLetter($upper = FALSE){
		$language	=	substr($language, 0, 1);
		if($upper === TRUE){
			$language	=	strtoupper($language);
		}
		else{
			$language	=	strtolower($language);
		}
		return $language;
	}
	
	public function format($language = NULL){
		if($language === NULL){
			$language	=	$this->getLanguage();
		}
		
		if($language === 'us'){
			$language = 'en';
		}
		
		$language	=	$language.'_'.strtoupper($language);
		return $language;
	}
}