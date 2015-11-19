<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Service;

use Zend\Stdlib\ArrayObject;
use DateTime;

abstract class AbstractService{
	private $options;
	private $namespace;
	private $arrayObject;
	private $globalConfig;
	private $modulesConfig;
	private $moduleConfig;
	private $rootConfig;
	private $config;
	private $service;
	private $serviceManager;
	private $pluginManager;
	private $controller;
	private $repository;
	private $moduleName;
	private $eventService;
	private $systems;
	private $modules;
	private $layout;
	
	public function test(){
		$this->debug('Hello world from '.get_called_class(), true);
	}
	
	public function debug($data, $exit = TRUE, $js = FALSE, $type = 'print'){
		echo	$js		?	'<script type="text/javascript">console.log('.print_r($data, true).');</script>'	:						
				$type	==	'print'	?	'<pre>'.print_r($data, true).'</pre>'	:	var_dump($data);
		
		if($exit){exit;}
	}
	
	public function setLayout($layout = NULL){
		if($layout !== NULL){
			$this->layout	=	$layout;
		}
		
		return $this;
	}
	public function getLayout(){
		if($this->layout === NULL){
			$this->setLayout(strtolower($this->getNameSpace()).'/layout/layout');
		}
		
		return 	$this->layout;
	}

	public function _modules($e){
		$modules	=	$e->getParam('modules');
		$modules[]	=	$this->getModuleName();
		$e->setParam('modules', $modules);
		return $e;
	}
	public function setModules($modules = NULL){
		if($modules !== NULL){
			$this->modules	=	$modules;
		}
		return $this;
	}
	public function getModules(){
		if($this->modules === NULL){
			$this->setModules($this->getArrayObject());
		}
		return $this->modules;
	}
	
	public function setOptions($options = NULL){
		if($options !== NULL){
			$this->options	=	$options;
		}
		return $this;
	}
	public function getOptions(){
		if($this->options === NULL){
			$config	=	$this->getConfig();
			$config	=	$config[strtolower($this->getNameSpace())];
			$options=	$config['options'];
			$this->setOptions($options);
		}
		return $this->options;
	}
	public function getOption($option = NULL){
		if($option !== NULL && array_key_exists($option, $this->getOptions())){
			$options	=	$this->getOptions();
			$option		=	$options[$option];
		}
		return $option;
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

	public function setGlobalConfig($globalConfig = NULL){
		if($globalConfig !== NULL){
			$this->globalConfig	=	$globalConfig;
		}
		return $this;
	}
	public function getGlobalConfig(){
		if($this->globalConfig === NULL){
			$this->setGlobalConfig($this->getServiceLocator()->get('Config'));
		}
		return $this->globalConfig;
	}
	public function setModulesConfig($modulesConfig = NULL){
		if($modulesConfig !== NULL){
			$this->modulesConfig	=	$modulesConfig;
		}
		return $this;
	}
	public function getModulesConfig(){
		if($this->modulesConfig === NULL){
			$this->setModulesConfig($this->getGlobalConfig());
		}
		return $this->modulesConfig;
	}
	public function setModuleConfig($moduleConfig = NULL){
		if($moduleConfig !== NULL){
			$this->moduleConfig	=	$moduleConfig;
		}
		return $this;
	}
	public function getModuleConfig($key = NULL, $ArrayObject = TRUE){
		$return;
		$modulesConfig	=	$this->getModulesConfig();
		
		if($modulesConfig === NULL){
			$modulesConfig	=	[];
		}
		
		$modulesConfig	=	$ArrayObject === TRUE	?	$this->getArrayObject($modulesConfig)	:	$modulesConfig;
		$return				=	$modulesConfig;
		
		if($modulesConfig->offsetExists($key)){
			$return	=	$modulesConfig->offsetGet($key);		
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
	
	public function getFormConfig($form = NULL, $arrayObject = TRUE){
		$config	=	$this->getConfig();
		$forms	=	$config[strtolower($this->getNameSpace())]['form'];
			
		$form	=	$form	!==	NULL	?	$forms[$form]	:	$forms;
		$form	=	$arrayObject		?	$this->getArrayObject($form)	:	$form;

		return $form;
	}
	
	public function setService($service = NULL){
		if($service !== NULL){
			$this->service	=	$service;
		}
		return $this;
	}
	public function getService(){
		if($this->service === NULL){
			$config	=	$this->getConfig();
			$name	=	strtolower($this->getNameSpace());
			$this->setService($this->getServiceLocator()->get($config[$name]['service']));
		}
		return $this->service;
	}

	public function setRepository($repository = NULL){
		if($repository !== NULL){
			$this->repository	=	$repository;
		}
		return $this;
	}
	public function getRepository(){
		if($this->repository === NULL){
			$config	=	$this->getConfig();
			$name	=	strtolower($this->getNameSpace());
			$this->setRepository($this->getServiceLocator()->get($config[$name]['repository']));
		}
		return $this->repository;
	}
	
	public function setEventService($eventService = NULL){
		if($eventService !== NULL){
			$this->eventService	=	$eventService;
		}
		return $this;
	}
	public function getEventService(){
		if($this->eventService === NULL){
			$config	=	$this->getConfig();
			$name	=	strtolower($this->getNameSpace());
			$this->setEventService($this->getServiceLocator()->get($config[$name]['event']));
		}
		return $this->eventService;
	}
	
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
	
	public function getIp(){
		return	$this	->getServiceManager()
						->get('Request')
						->getServer('REMOTE_ADDR');
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
	
	public function setSystems($systems = NULL){
		if($systems !== NULL){
			$this->systems	=	$systems;
		}
		return $this;
	}
	public function getSystems(){
		if($this->systems === NULL){
			$config	=	$this->getRootConfig('bakar');
			$name	=	'bakar';
			$this->setSystems($this->getServiceLocator()->get($config[$name]['service']));
		}
		return $this->systems;
	}
	
	public function getExternalService($namespace, $service = NULL){
		$config		=	$this->getModuleConfig('b');
		if($service === NULL){$service = $namespace;}
		
		$service	=	$config[$namespace][$service]['service'];

		return	$this->getServiceLocator()->get($service);
	}
	
	/**
	*	LABS
	*/
	
	private $identity;
	public function setIdentity($identity = NULL){
		if($identity !== NULL){
			$this->identity	=	$identity;
		}
		return $this;
	}
	public function getIdentity(){
		if($this->identity === NULL){
			$this->setIdentity($this->getController()->view()->getVar('identity'));
		}
		return $this->identity;
	}
	public function unslug($string){
		if($string !== NULL){
			$string	=	explode('_', $string);
			$string	=	implode(' ', $string);
			$string	=	explode('-', $string);
			$string	=	implode(' ', $string);
		}
		return $string;
	}
	
	private $filterData;
	private $rawData;
	private $valid;
	private $message;
	public function reset(){
		$this->setValid(NULL);
		$this->setRawData(NULL);
		$this->setFilterData(NULL);
		$this->setMessage(NULL);
		return $this;
	}
	public function setValid($valid){
		$this->valid	=	$valid;
		return $this;
	}
	public function getValid(){
		return $this->isValid();
	}
	public function isValid(){
		return $this->valid;
	}
	public function setRawData($rawData = NULL){
		$this->rawData	=	$rawData;
		return $this;
	}
	public function getRawData(){
		return $this->rawData;
	}
	public function setFilterData($filterData = NULL){
		$this->filterData	=	$filterData;
		return $this;
	}
	public function getFilterData(){
		return $this->filterData;	
	}
	public function setMessage($message = NULL){
		$this->message	=	$message;
		return $this;
	}
	public function getMessage(){
		return $this->message;
	}

	private $environment;
	public function setEnvironment($environment = NULL){
		if($environment !== NULL){
			$this->environment	=	$environment;
		}
		return $this;
	}
	public function getEnvironment(){
		if($this->environment === NULL){
			$dbConfig	=	$this->getModuleConfig('db');
			$environment=	$dbConfig['environment'];
			$this->setEnvironment($environment);
		}
		return $this->environment;
	}
}
