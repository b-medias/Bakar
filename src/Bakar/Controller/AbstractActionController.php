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

	public function test(){
		$this->debug('Hello world from '.get_called_class().' -> '.__FUNCTION__, true);
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
	public function getView(){
		if($this->view	===	NULL){
			$this->setView($this->generateView());
		}
		return $this->view;
	}
	public function view(){
		return	$this->getView();
	}
	public function generateView(){
		$vars	=	$varsAjax	=	array(
			'isAjax'	=>	$this->viewPlugin()->isAjax(),
		);
				
		$vars['identity']	=	$this->getIdentity();
				
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
	

	////////////////////////////////////		
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
							'administrator'		=>	$this->getExternalService('user', 'administrator'),
						]);	

		if($services->authentication !== NULL){
			$identity	=	$services->authentication->getIdentity();
			if($identity !== NULL){
				$db 		=	$services->authentication->getRepository()->prepare()->getDb();
				$db->where([
					$services->authentication->getRepository()->getTableName().'.id' 	=>	$identity->id,
					$services->authentication->getRepository()->getTableName().'.state'	=>	1,
				]);	


				if($services->administrator !== NULL){
					$db->leftJoin(
						$services->administrator->getRepository()->getTableName(),
						$db->expression(
							$services->administrator->getRepository()->getTableName().'.id = '.$services->authentication->getRepository()->getTableName().'.id
							AND '.$services->authentication->getRepository()->getTableName().'.state = ?
							AND '.$services->administrator->getRepository()->getTableName().'.state = ?',
							[1, 1]
						),
						$services->administrator->getRepository()->getColumns()
					);
				}

				$identity	=	$db		->execute()
										->getResult();

				
				if($services->authorization !== NULL){
					$db	=	$services	->authorization
										->getRepository()
										->prepare()
										->getDb();	

					$spaceAccess	=	$this->getArrayObject([
											'housecare'	=>	'IF((SELECT COUNT(id) FROM '.$services->authorization->getRepository()->getTableName().' WHERE type = ? AND identifiant = ? AND state = ? AND identity = ? AND _access = ?) > ?, ?, ?) AS haveAccessHousecare',
											'villa'		=>	'IF((SELECT COUNT(id) FROM '.$services->authorization->getRepository()->getTableName().' WHERE type = ? AND identifiant = ? AND state = ? AND identity = ? AND _access = ?) > ?, ?, ?) AS haveAccessVilla',
											'escape'	=>	'IF((SELECT COUNT(id) FROM '.$services->authorization->getRepository()->getTableName().' WHERE type = ? AND identifiant = ? AND state = ? AND identity = ? AND _access = ?) > ?, ?, ?) AS haveAccessEscape',
											'uk'		=>	'',
										]);

					$identity->access	=	$db	->select($db->expression($spaceAccess->housecare, ['space', 0, 1, $identity->id, 1, 0, 1, 0]))
												->select($db->expression($spaceAccess->villa, ['space', 1, 1, $identity->id, 1, 0, 1, 0])) 
												->select($db->expression($spaceAccess->escape, ['space', 1, 2, $identity->id, 1, 0, 1, 0]))
												->where([
													'identity'	=>	$identity->id,
													'state'		=>	1,
												])
												->group($services->authorization->getRepository()->getTableName().'.identity')
												->execute()
												->getResult();
				}
			}
		}

		return $identity;				
	}
	/*
	public function getIdentity(){	
		$e	=	$this	->getSystems()
						->getEventService()
						->trigger('identity', $this, array('identity' => NULL))
						->getEvent();
						
		$identity	=	$e->getParam('identity');

		return $identity;				
	}*/
	public function getExternalService($namespace, $service = NULL){
		$config		=	$this->getModuleConfig('b');
		if($service == NULL){$service = $namespace;}
		$service	=	$config[$namespace][$service]['service'];

		return	$this->getServiceLocator()->get($service);
	}
	public function getExternalFormConfig($module, $namespace, $config){
		$root	=	$this->getModuleConfig('b');
		$root	=	$root[$module];
		$root	=	$root[$namespace];
		$root	=	$root['form'];
		$config	=	$root[$config];
		return	$config;
	}
	public function access(){		
		$e		=	$this	->getSystems()
							->getEventService()
							->trigger('access', $this, array('access' => NULL, 'identity' => $this->getIdentity()))
							->getEvent();
							
		$access		=	$e->getParam('access');
		$identity	=	$e->getParam('identity');
			
		if($access != NULL){
			if(!$access->isGranted){
				return	$this->redirect()->toRoute($access->redirect->route, $access->redirect->params);
			}
		}
				
		return $identity;
	}
}