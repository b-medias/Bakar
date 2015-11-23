<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Controller;
use Zend\Stdlib\ArrayObject;

abstract class AbstractActionController extends \Zend\Mvc\Controller\AbstractActionController{
	private $namespace;
	private $arrayObject;
	private $moduleConfig;
	private $rootConfig;
	private $config;
	private $service;
	private $repository;
	private $eventService;
	private $systems;
	private $view;
	private $moduleName;
	private $translator;
	private $modules;

	public function test(){
		$this->debug('Hello world from '.get_called_class().' -> '.__FUNCTION__, true);
	}
	public function debug($data, $exit = TRUE, $js = FALSE, $type = 'print'){
		echo	$js		?	'<script type="text/javascript">console.log('.print_r($data, true).');</script>'	:						
				$type	==	'print'	?	'<pre>'.print_r($data, true).'</pre>'	:	var_dump($data);
		
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
	public function isArrayObject($array){
		return	(is_array($array) && is_object($array));
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

	public function setRootConfig($rootConfig = NULL){
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
				return	$this->getArrayObject($this->rootConfig[$key]);
			}
			
			return 	$this->getArrayObject($this->rootConfig['default']);
		}
		
		return $this->getArrayObject($this->rootConfig);
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
	
	public function setView($view = NULL){
		if($view !== NULL){
			$this->view	=	$view;
		}
		return $this;
	}	
	public function getView($currentLayout = FALSE){
		if($this->view	===	NULL){
			$this->setView($this->generateView($currentLayout));
		}
		return $this->view;
	}
	public function view($currentLayout = FALSE){
		return	$this->getView($currentLayout);
	}
	public function generateView($currentLayout = FALSE){
		$vars	=	$varsAjax	=	array(
			'isAjax'	=>	$this->viewPlugin()->isAjax(),
		);
				
		$vars['identity']	=	$this->getIdentity();

		if($currentLayout){
			$this->layout()->setTemplate($this->getService()->getLayout());
		}
				
		return	$this	->viewPlugin()
						->setVarsAjax($varsAjax)
						->setVars($vars);	
	}
	
	public function getServiceManager(){
		return	$this->getServiceLocator();
	}
	
	public function setTranslator($translator = NULL){
		if($translator !== NULL){
			$this->translator	=	$translator;
		}
		return $this;
	}
	public function getTranslator(){
		if($this->translator === NULL){
			$this->setTranslator($this->getServiceManager()->get('translator'));
		}
		return $this->translator;
	}
	public function translate($string = NULL){
		$translate	=	$string;
		
		if($string !== '' && $string !== NULL){
			$translate	=	$this->getTranslator()->translate($string);	
		}
		
		return $translate;
	}
	
	public function setModules($modules = NULL){
		if($modules !== NULL){
			$this->modules	=	$modules;
		}
		return $this;
	}
	public function getModules($arrayObject = FALSE){
		if($this->modules === NULL){
			$this->setModules($this->getSystems()->getModules());
		}
		return $arrayObject	?	$this->modules	:	$this->modules->getArrayCopy();
	}
	public function isModuleInitialized($moduleName){
		$moduleName	=	ucfirst(strtolower($moduleName));
		return in_array($moduleName, $this->getModules());
	}
	
	public function log($message){
		$this	->getSystems()
				->getEventService()
				->trigger('log', NULL, array('message' => $message));
				
		return $this;
	}
	public function getIdentity(){
		$identity 	=	NULL;
		$services	=	$this->getArrayObject([
							'authentication'	=>	$this->getExternalService('authentication'),
							'authorization'		=>	$this->getExternalService('authorization'),
						]);

		if($services->authorization !== NULL){
			$identity	=	$services->authentication->getIdentity();

			if($identity !== NULL){
				/**
				*	JOIN	:: 	AUTHORIZATION
				*/
				if($services->authorization !== NULL){
					$db 			=	$services->authentication->getRepository()->prepare()->getDb();
					$defaultColumns	=	$services->authorization->getRepository()->getColumns(FALSE);					

					$tables			=	$this->getArrayObject([
						'housecare'	=>	'housecareAuthorization',
						'villa'		=>	'villaAuthorization',
						'escape'	=>	'escapeAuthorization',
					]);

					$columns 		=	$this->getArrayObject([
						'housecare'	=>	[],
						'villa'		=>	[],
						'escape'	=>	[],
					]);

					$identifiants	=	$this->getArrayObject([
						'housecare'	=>	0,
						'villa'		=>	1,
						'escape'	=>	2,
					]);

					foreach($defaultColumns as $key => $column){
						$columns->housecare[$key]	=	$tables->housecare.ucfirst($column);
						$columns->villa[$key]		=	$tables->villa.ucfirst($column);
						$columns->escape[$key]		=	$tables->escape.ucfirst($column);
					}
					
					$type		=	'space';
					$identifiant=	1;
					$state		=	1;
					$global		=	0;
					$gt 		=	0;

					$identity->access 	=	$db	->leftJoin(
													[$services->authorization->getRepository()->getTableName()	=>	$tables->housecare],
													$db->expression(
														'IF(
															(SELECT COUNT(id) FROM '.$services->authorization->getRepository()->getTableName().' 
															WHERE '.$services->authorization->getRepository()->getTableName().'.type = ? 
															AND '.$services->authorization->getRepository()->getTableName().'.identifiant = ? 
															AND '.$services->authorization->getRepository()->getTableName().'.state = '.$services->authentication->getRepository()->getTableName().'.state 
															AND '.$services->authorization->getRepository()->getTableName().'.identity = '.$services->authentication->getRepository()->getTableName().'.id) > ?, '.
														$tables->housecare.'.identity = '.$services->authentication->getRepository()->getTableName().'.id, '. 
														$tables->housecare.'.identity = ?)',
														[$type, $identifiants->housecare, $gt, $global]
													),
													$columns->housecare
												)
												->leftJoin(
													[$services->authorization->getRepository()->getTableName()	=>	$tables->villa],
													$db->expression(
														'IF(
															(SELECT COUNT(id) FROM '.$services->authorization->getRepository()->getTableName().' 
															WHERE '.$services->authorization->getRepository()->getTableName().'.type = ? 
															AND '.$services->authorization->getRepository()->getTableName().'.identifiant = ? 
															AND '.$services->authorization->getRepository()->getTableName().'.state = '.$services->authentication->getRepository()->getTableName().'.state 
															AND '.$services->authorization->getRepository()->getTableName().'.identity = '.$services->authentication->getRepository()->getTableName().'.id) > ?, '.
														$tables->villa.'.identity = '.$services->authentication->getRepository()->getTableName().'.id, '. 
														$tables->villa.'.identity = ?)',
														[$type, $identifiants->villa, $gt, $global]
													),
													$columns->villa
												)
												->leftJoin(
													[$services->authorization->getRepository()->getTableName()	=>	$tables->escape],
													$db->expression(
														'IF(
															(SELECT COUNT(id) FROM '.$services->authorization->getRepository()->getTableName().' 
															WHERE '.$services->authorization->getRepository()->getTableName().'.type = ? 
															AND '.$services->authorization->getRepository()->getTableName().'.identifiant = ? 
															AND '.$services->authorization->getRepository()->getTableName().'.state = '.$services->authentication->getRepository()->getTableName().'.state 
															AND '.$services->authorization->getRepository()->getTableName().'.identity = '.$services->authentication->getRepository()->getTableName().'.id) > ?, '.
														$tables->escape.'.identity = '.$services->authentication->getRepository()->getTableName().'.id, '. 
														$tables->escape.'.identity = ?)',
														[$type, $identifiants->escape, $gt, $global]
													),
													$columns->escape
												)
												->where([
													$services->authentication->getRepository()->getTableName().'.id'	=>	$identity->id,
													$services->authentication->getRepository()->getTableName().'.state'	=>	1,
												])
												->execute()
												->getResult();
				}
			}
		}

		return $identity;				
	}
	public function getExternalService($namespace, $service = NULL){
		if($this->isModuleInitialized($namespace)){
			$config		=	$this->getModuleConfig('b');
			if($service == NULL){$service = $namespace;}
			$service	=	$config[$namespace][$service]['service'];
			return	$this->getServiceLocator()->get($service);
		}

		return NULL;
	}
	public function getExternalFormConfig($module, $namespace, $config){
		$root	=	$this->getModuleConfig('b');
		$root	=	$root[$module];
		$root	=	$root[$namespace];
		$root	=	$root['form'];
		$config	=	$root[$config];
		return	$config;
	}
}