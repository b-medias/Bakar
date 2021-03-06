<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Repository;

use Zend\Stdlib\ArrayObject;
use DateTime;

abstract class AbstractRepository{
	const PATH 	=	'Bakar\Db\Adapter';
	private $serviceManager;
	private $pluginManager;
	private $dbPlugin;
	private $languagePlugin;
	private $tableName;
	private $columns;
	private $eventManager;
	private $globalConfig;
	private $modulesConfig;
	private $moduleConfig;	
	private $environment;
	
	public function prepare(){
		$this	->getDb()
				->setTableName($this->getTableName());
				
		return $this;
	}
	public function test(){
		$this->debug('Hello world from '.get_called_class(), true);
	}
	public function debug($data, $exit = TRUE, $js = FALSE){
		echo	$js	?	'<script type="text/javascript">console.log('.print_r($data, true).');</script>'	:
						'<pre>'.print_r($data, true).'</pre>';
		
		if($exit){exit;}
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
		return $this->getServiceManager();
	}
	
	public function setPluginManager($pluginManager = NULL){
		if($pluginManager !== NULL){
			$this->pluginManager = $pluginManager;
		}
		return $this;
	}
	public function getPluginManager(){
		if($this->pluginManager === NULL){
			$this->setPluginManager($this->getServiceManager()->get('controllerpluginmanager'));
		}
		return $this->pluginManager;
	}
	
	public function setDbPlugin($dbPlugin = NULL){
		if($dbPlugin !== NULL){
			$this->dbPlugin = $dbPlugin;
		}
		return $this;
	}
	public function getDbPlugin(){
		if($this->dbPlugin === NULL){
			$this->setDbPlugin($this->getPluginManager()->get('dbplugin')->setServiceLocator($this->getServiceManager()));
		}
		return $this->dbPlugin;
	}
	
	public function setDb($db = NULL){
		return $this->setDbPlugin($db);
	}
	public function getDb($adapterName = NULL){
		$db		=	$this->getDbPlugin();

		if($adapterName == NULL){
			$adapterName 	=	$this->getEnvironment();
		}

		$adapter=	$this->getServiceManager()->get(self::PATH.'\\'.ucfirst(strtolower($adapterName))); 
		$db->setAdapter($adapter);

		return $db;
	}
	
	public function getLanguagePlugin(){
		if($this->languagePlugin === NULL){
			$this->setLanguagePlugin($this->getPluginManager()->get('languageplugin'));
		}
		return $this->languagePlugin;
	}
	public function setLanguagePlugin($languagePlugin = NULL){
		if($languagePlugin !== NULL){
			$this->languagePlugin	=	$languagePlugin;
		}
		return $this;
	}
	
	public function setTableName($tableName = NULL){
		if($tableName !== NULL){
			$this->tableName = $tableName;
		}
		return $this;
	}
	public function getTableName(){
		if($this->tableName === NULL){
			$this->setTableName($this->generateTableName());
		}
		return $this->tableName;
	}
	protected function generateTableName(){
		$className	=	get_class($this);
		$className	=	substr($className, strripos($className, '\\') + 1);
		$className	=	preg_replace('/repository/i', '', $className);
		$tableName	=	strtolower($className);	
		return	$tableName;	
	}
	
	public function setColumns($columns = NULL){
		if($columns !== NULL){
			$this->columns	=	$columns;
		}
		return $this;
	}
	public function getColumns($prefix = TRUE){
		$this->setColumns($this->generateColumns($prefix));
		return $this->columns;
	}
	protected function generateColumns($prefix = TRUE){
		$columns	=	$this->getDb()->getStructure($this->getTableName());
		
		$return	=	array();
		
		if($prefix){
			foreach($columns as $key => $value){
				$return[$value]	=	$this->getTableName().ucfirst($value);
			}
		}
		else{
			foreach($columns as $key => $value){
				$return[$value]	=	$value;
			}
		}
		
		return $return;	
	}
	
	public function setEventManager($eventManager = NULL){
		if($eventManager !== NULL){
			$this->eventManager	=	$eventManager;
		}
		return $this;
	}
	public function getEventManager(){
		if($this->eventManager === NULL){
			$this->setEventManager($this->getServiceManager()->get('eventmanager'));
		}
		return $this->eventManager;
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

	public function update($update){
		$update	=	array_merge(array('predicate' => 'AND'), $update);
						
		return	$this	->getDb()
						->setTableName($this->getTableName())
						->update($update['data'])
						->where($update['conditions'], $update['predicate'])
						->execute();
	}
	public function count($where, $predicate = 'AND'){
		return	$this	->getDb()
						->setTableName($this->getTableName())
						->count()
						->where($where, $predicate)
						->execute();
	}
	public function findOne($params){
		$default	=	array(
			'predicate'	=>	'AND',
			'order'		=>	'id DESC',
		);
				
		$params	=	array_merge($default, $params);
		
		return	$this	->getDb()
						->setTableName($this->getTableName())
						->find()
						->where($params['conditions'], $params['predicate'])
						->order($params['order'])
						->limit(0, 1)
						->execute();
	}
	public function find($params){
		$default	=	array(
			'predicate'	=>	'AND',
			'order'		=>	'id DESC',
			'pagination'=>	NULL,
			'conditions'=>	true,
		);
				
		$params	=	array_merge($default, $params);
		
		$this	->getDb()
				->setTableName($this->getTableName())
				->find()
				->where($params['conditions'], $params['predicate'])
				->order($params['order']);
		
		if($params['pagination'] !== NULL){
			$this	->getDb()
					->pagination($params['pagination']);
		}
		
		
		return	$this	->getDb()
						->execute();
	}
	public function save($data){
		return	$this	->getDbPlugin()
						->setTableName($this->getTableName())
						->insert($data)
						->execute();
	}
	public function insert($data){
		return	$this->save($data);
	}
}