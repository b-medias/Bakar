<?php

/**
*	-	Corriger
		La fonction delete ajout de la condition data != null pour le where intégrer
		La fonction isNotNull 
*	-	Bug à corriger
*		La fonction between ne marche pas lorsqu'on utilise un array pour min et max
*/
namespace Bakar\Plugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Predicate\IsNotNull;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\Between;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Having;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Metadata\Metadata;

use PDO;
use ArrayObject;

class DbPlugin extends AbstractPlugin{

	const PATH_ADAPTER			=	'Zend\Db\Adapter\Adapter';
	const FETCH_MODE			=	'FETCHMODE';
	const WHERE					=	'WHERE';
	const HAVING				=	'HAVING';
	const VALUES				=	'VALUES';
	const COLUMNS				=	'COLUMNS';
	const INTO					=	'INTO';
	const FLAG					=	'FLAG';
	const INSERT				=	'INSERT';
	const LIMIT					=	'LIMIT';
	const OFFSET				=	'OFFSET';
	const JOIN					=	'JOIN';
	const ON					=	'ON';
	const UPDATE				=	'UPDATE';
	const IS_NULL				=	'IS NULL';
	const IS_NOT_NULL			=	'IS NOT NULL';
	const GROUP_BY				=	'GROUP BY';
	const PAGINATION			=	'PAGINATION';
	const FROM					=	'FROM';
	const ORDER					=	'ORDER';
	const TYPE_OF_REQUEST		=	'TYPE OF REQUEST';
	const TABLE_NAME			=	'TABLE NAME';
	const ORIGINAL_TABLE_NAME	=	'ORIGINAL TABLE NAME';
	const ALLIAS_TABLE_NAME		=	'ALLIAS TABLE NAME';
	const TRANSACTION			=	'TRANSACTION';
	const DISTINCT				=	'DISTINCT';

	protected $doctrine				=	NULL;
	
	protected $result				=	array();
	protected $results				=	array();
	protected $lastValues			=	array();
	protected $resultsTotal			=	array();
	protected $resultsLength		=	array();
	protected $resultsPagination	=	array();
	protected $paginatorPlugin		=	NULL;
	protected $requestObject		=	NULL;

	protected $paginationAdapter	=	NULL;
	protected $serviceLocator		=	NULL;
	protected $adapter				=	NULL;
	protected $pathAdapter			=	NULL;
	protected $tableName			=	NULL;
	protected $originalTableName	=	NULL;
	protected $alliasTableName		=	NULL;
	protected $sqlObject			=	NULL;

	protected $predicateOperator	=	array('>','<','>=','<=','!=','=');

	protected $keyRequest			=	NULL;
	protected $keyValues			=	NULL;
	protected $keyResult			=	NULL;
	protected $keyJoin				=	NULL;
	protected $factoryRequest		=	array();
	protected $currentFactoryRequest=	array(
		self::PAGINATION			=> 	array('isClaim' => FALSE),
		self::TABLE_NAME			=> 	NULL,
		self::ORIGINAL_TABLE_NAME	=> 	NULL,
		self::ALLIAS_TABLE_NAME		=> 	NULL,
		self::GROUP_BY				=> 	array(),
		self::COLUMNS				=> 	array(),
		self::WHERE					=> 	array(),
		self::INSERT				=> 	array(),
		self::UPDATE				=> 	array(),
		self::HAVING				=>	array(),
		self::ORDER					=> 	array(),
		self::JOIN					=> 	array(),
		self::VALUES				=> 	array(),
		self::LIMIT					=> 	NULL,
		self::OFFSET				=> 	NULL,
		self::FROM					=> 	NULL,	
		self::ON					=> 	NULL,
		self::DISTINCT 				=>	'',
		self::FETCH_MODE			=> 	PDO::FETCH_OBJ,
		self::INTO					=> 	'',
		self::FLAG					=> 	'SET',
		self::TYPE_OF_REQUEST		=> 	NULL,
		self::TRANSACTION			=>	FALSE,
	);

	public function isValid(){}
	public function setMessage(){}
	public function getMessage(){}
	public function transaction(){
		$this->setFactoryValue('transaction', TRUE);
		$this->getAdapter()->getDriver()->getConnection()->beginTransaction();
		return $this;
	}
	public function rollback(){
		$this->getAdapter()->getDriver()->getConnection()->rollback;
		return $this;
	}
	public function commit(){
		$$this->getAdapter()->getDriver()->getConnection()->commit();	
	}
	public function isTransaction(){
		return	$this->getFactoryValues(self::TRANSACTION);
	}
	public function getAdapters(){}
	public function setAdapters(){}
	public function getRealSqlRequest(){}
	public function getService(){}
	


	/**
	*	CORE
	*/
	public function setSqlObject($sqlObject = NULL){
		if($sqlObject !== NULL){
			$this->sqlObject = $sqlObject;
		}
		return $this;
	}
	public function getSqlObject($new = FALSE){
		if($this->sqlObject === NULL OR $new = TRUE){
			$this->setSqlObject(new Sql($this->getAdapter()));
		}
		return $this->sqlObject;
	}
	public function setServiceLocator($serviceLocator = NULL){
		if($serviceLocator !== NULL){
			$this->serviceLocator = $serviceLocator;
		}
		return $this;
	}
	public function getServiceLocator(){
		if($this->serviceLocator === NULL){
			$this->setServiceLocator($this->getController()->getServiceLocator());
		}
		return $this->serviceLocator;
	}
	public function setAdapter($adapter = NULL){
		if($adapter !== NULL){
			$this->adapter = $adapter;
		}
		return $this;
	}
	public function getAdapter(){
		if($this->adapter === NULL){
			$this->setAdapter($this->getServiceLocator()->get($this->getPathAdapter()));
		}
		return $this->adapter;
	}
	public function setPathAdapter($pathAdapter = NULL){
		if($pathAdapter !== NULL){
			$this->pathAdapter = $pathAdapter;
		}
		return $this;
	}	
	public function getPathAdapter(){
		if($this->pathAdapter === NULL){
			$this->setPathAdapter(self::PATH_ADAPTER);
		}
		return $this->pathAdapter;
	}
	public function setTableName($tableName = NULL){
		if($tableName !== NULL){
			$this->setFactoryValues(self::TABLE_NAME, $tableName);
		}
		return $this;
	}
	public function getTableName(){
		if($this->getFactoryValues(self::TABLE_NAME) === NULL){
			$this->setTableName($this->getController()->getEvent()->getRouteMatch()->getMatchedRouteName());
		}
		return $this->getFactoryValues(self::TABLE_NAME);
	}
	public function setOriginalTableName($originalTableName = NULL){
		if($originalTableName !== NULL){
			$this->setFactoryValues(self::ORIGINAL_TABLE_NAME, $originalTableName);
		}
		return $this;
	}
	public function getOriginalTableName(){
		if($this->getFactoryValues(self::ORIGINAL_TABLE_NAME) === NULL){
			$this->setOriginalTableName($this->getTableName());
		}
		return $this->getFactoryValues(self::ORIGINAL_TABLE_NAME);
	}
	public function setAlliasTableName($alliasTableName = NULL){
		if($alliasTableName !== NULL){
			$this->setFactoryValues(self::ALLIAS_TABLE_NAME, $alliasTableName);
		}
		return $this;
	}
	public function getAlliasTableName(){
		if($this->getFactoryValues(self::ALLIAS_TABLE_NAME) === NULL){
			$this->setAlliasTableName($this->getOriginalTableName());
		}
		return $this->getFactoryValues(self::ALLIAS_TABLE_NAME);
	}
	public function renameTable($renameTable){
		$this->setAlliasTableName($renameTable);
		return $this;
	}
	public function setName($name = NULL){
		$this->setTableName($name);
		return $this;
	}
	public function getName(){
		return $this->getTableName();
	}
	public function setOriginalName($originalName = NULL){
		$this->setOriginalTableName($originalName);
		return $this;
	}
	public function getOriginalName(){
		return $this->getOriginalTableName();
	}	
	public function setAllias($allias = NULL){
		$this->setAlliasTableName($allias);
		return $this;
	}	
	public function getAllias(){
		return $this->getAlliasTableName();
	}
	public function setFetchMode($mode = 'obj'){
		$allias	=	array(
			//Spécifie que la méthode de récupération doit retourner chaque ligne en tant qu'objet PDORow avec les noms de variables correspondants aux noms des colonnes retournées dans le jeu de résultats. PDO::FETCH_LAZY crée les noms des variables de l'objet uniquement lorsqu'ils sont utilisés.
			'lazy'		=> 	PDO::FETCH_LAZY,
			//Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par les noms des colonnes comme elles sont retournées dans le jeu de résultats correspondant. Si le jeu de résultats contient de multiples colonnes avec le même nom, PDO::FETCH_ASSOC retourne une seule valeur par nom de colonne.
			'assoc'		=> 	PDO::FETCH_ASSOC,
			//Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par les noms des colonnes comme elles sont retournées dans le jeu de résultats correspondant. Si le jeu de résultats contient de multiples colonnes avec le même nom, PDO::FETCH_NAMED retourne un tableau de valeurs par nom de colonne.
			'named'		=>	PDO::FETCH_NAMED,
			//Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par le numéro des colonnes comme elles sont retournées dans le jeu de résultats correspondant, en commençant à 0.
			'num'		=>	PDO::FETCH_NUM,
			//Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par les noms des colonnes ainsi que leurs numéros, comme elles sont retournées dans le jeu de résultats correspondant, en commençant à 0.
			'both'		=>	PDO::FETCH_BOTH, 
			//Spécifie que la méthode de récupération doit retourner chaque ligne dans un objet avec les noms de propriétés correspondant aux noms des colonnes comme elles sont retournées dans le jeu de résultats.
			'obj'		=> 	PDO::FETCH_OBJ,
			//Spécifie que la méthode de récupération doit retourner TRUE et assigner les valeurs des colonnes du jeu de résultats dans les variables PHP auxquelles elles sont liées avec la méthode PDOStatement::bindParam() ou la méthode PDOStatement::bindColumn().
			'bound'		=>	PDO::FETCH_BOUND,
			//Spécifie que la méthode de récupération doit retourner uniquement une seule colonne demandée depuis la prochaine ligne du jeu de résultats.
			'column'	=>	PDO::FETCH_COLUMN,
			//Spécifie que la méthode de récupération doit retourner une nouvelle instance de la classe demandée, liant les colonnes aux membres de la classe.
			'class'		=>	PDO::FETCH_CLASS,
			//Spécifie que la méthode de récupération doit mettre à jour une instance existante de la classe demandée, liant les colonnes aux propriétés nommées dans la classe.
			'into'		=> 	PDO::FETCH_INTO,
			//Spécifie que la méthode de récupération doit appeler une fonction de callback qui traitera les résultats.
			'func'		=>	PDO::FETCH_FUNC,
			'group'		=>	PDO::FETCH_GROUP,
			'unique'	=>	PDO::FETCH_UNIQUE,
			//Récupère un résultat sur deux colonnes dans un tableau où la première colonne est la clé, et la seconde colonne est la valeur. Disponible depuis PHP 5.2.3.
			'pair'		=>	PDO::FETCH_KEY_PAIR,
			//Détermine le nom de la classe depuis la valeur de la première colonne.
			'classtype'	=>	PDO::FETCH_CLASSTYPE,
			//Identique à PDO::FETCH_INTO, mais l'objet est fourni sous la forme d'une chaîne linéarisée. Disponible depuis PHP 5.1.0.
			'serialize'	=>	PDO::FETCH_SERIALIZE,
			'props_late'=>	PDO::FETCH_PROPS_LATE,
		);
		
		if(!array_key_exists($mode, $allias)){
			$mode	=	'obj';	
		}
		$this->setFactoryValues(self::FETCH_MODE, $allias[$mode]);
		return $this;
	}	
	public function getFetchMode(){
		return $this->getFactoryValues(self::FETCH_MODE);
	}	
	public function protectForLike($data){
		$data = str_replace(array('%','_','[',']'), array('\%','\_','\[','\]'), $data);
		return $data;
	}	
	public function setFactoryRequest($factoryRequest = NULL){
		if($factoryRequest !== NULL){
			array_push($this->factoryRequest, $factoryRequest);
		}
		return $this;
	}
	public function getFactoryRequest($index = NULL){
		$return =	$this->factoryRequest;
		if($index !== NULL AND array_key_exists($index, $this->factoryRequest)){
			$return = $return[$index];
		}
		return $return;
	}
	
	/**
	*	KEY
	*
	*	keyRequest	=>	key of current request
	*	keyValues	=>	key of current value for current request
	*	keyResult	=>	key of current result
	*	keyJoin		=>	key of join
	*/
	public function setKeyRequest($keyRequest = NULL){
		if($keyRequest !== NULL){
			$this->keyRequest = $keyRequest;
		}
		return $this;
	}
	public function getKeyRequest(){
		if($this->keyRequest === NULL){
			$this->setKeyRequest(0);
		}
		return $this->keyRequest;
	}
	public function setKeyValues($keyValues = NULL){
		if($keyValues !== NULL){
			$this->keyValues = $keyValues;
		}
		return $this;
	}
	public function getKeyValues(){
		if($this->keyValues === NULL){
			$this->setKeyValues(0);
		}
		return $this->keyValues;
	}
	public function setKeyResult($keyResult = NULL){
		if($keyResult !== NULL){
			$this->keyResult = $keyResult;
		}
		return $this;
	}
	public function getKeyResult(){
		return $this->keyResult;
	}
	public function setKeyJoin($keyJoin = NULL){
		if($keyJoin !== NULL){
			$this->keyJoin = $keyJoin;
		}
		return $this;
	}
	public function getKeyJoin(){
		if($this->keyJoin === NULL){
			$this->setKeyJoin(0);
		}
		return $this->keyJoin;
	}

	protected function convertPredicate($predicate){
		if(is_string($predicate)){
			$predicate = strtoupper($predicate);
			switch($predicate){
				case 'OR':
				$predicate	=	PredicateSet::OP_OR;
				break;
				
				case 'AND':
				$predicate	=	PredicateSet::OP_AND;
				break;
				
				default:
				$predicate	=	PredicateSet::OP_AND;
				break;
			}
		}
		return $predicate;
	}
	protected function convertValues($type, $data){
		$type	=	strtoupper($type);
		$return	=	array();
		switch($type){
			case self::WHERE:
			//'string'
			//array('object')
			//array('string' => 'object')
			$association=	$data[0];
			$data		=	$data[1];
			if(is_array($data)){
				foreach($data as $k1 => $v1){
					if(is_int($k1)){
						$value 	=	$this->convertValues($type, $v1);
						$return	= 	array_merge($return, $value);
					}
					else{
						$predicate	=	new Operator();
						$predicate->setOperator('=');
						$predicate->setLeft(trim($k1));
						$predicate->setRight(new Predicate\Expression(':value'.$this->getKeyValues()));
						$this->setFactoryValues(self::VALUES, array(':value'.$this->getKeyValues() => $v1));
						$this->setKeyValues($this->getKeyValues()+1);
						array_push($return, $predicate);
					}
				}
			}
			else if(is_object($data)){
				array_push($return, array($data, $association));
			}
			else{
				$predicate	=	new Operator();
				$operator	=	$this->extractOperator($data);
				$data		=	explode($operator, $data);
				$predicate->setOperator($operator);
				$predicate->setLeft(trim($data[0]));
				$predicate->setRight(new Predicate\Expression(':value'.$this->getKeyValues()));
				$this->setFactoryValues(self::VALUES, array(':value'.$this->getKeyValues() => trim($data[1])));
				$this->setKeyValues($this->getKeyValues()+1);
				array_push($return, array($predicate, $association));
			}
			return $return;
			break;
			
			case self::COLUMNS:
			$return	= $data;
			if(is_object($data)){
				$return = array($return);
			}
			return $return;
			break;
			
			case self::FROM:
			$return	= $data;
			if(is_object($data)){
				$return = array($return);
			}
			return $return;
			break;
			
			case self::UPDATE:
			if(is_array($data)){
				foreach($data as $k1 => $v1){
					if(is_int($k1)){
						$value	=	$this->convertValues($type, $v1);
						array_push($return, $value);
					}
					else{
						$expression	=	new Predicate\Expression(':value'.$this->getKeyValues());
						$this->setFactoryValues(self::VALUES, array(':value'.$this->getKeyValues() => $v1));
						$this->setKeyValues($this->getKeyValues()+1);
						array_push($return, array($k1 => $expression));
					}
				}
			}
			else if(is_object($data)){
				$return = array($data);
			}
			else{
				$expression	=	new Predicate\Expression(':value'.$this->getKeyValues());
				$this->setFactoryValues(self::VALUES, array(':value'.$this->getKeyValues() => $data));
				$this->setKeyValues($this->getKeyValues()+1);
				$return	=	$expression;
			}
			return $return;
			break;
			
			case self::INSERT:
			if(is_array($data)){
				foreach($data as $k1 => $v1){
					if(is_int($k1)){
						$value	=	$this->convertValues($type, $v1);
						array_push($return, $value);
					}
					else{
						$expression	=	new Predicate\Expression(':value'.$this->getKeyValues());
						$this->setFactoryValues(self::VALUES, array(':value'.$this->getKeyValues() => $v1));
						$this->setKeyValues($this->getKeyValues()+1);
						array_push($return, array($k1 => $expression));
					}
				}
			}
			else if(is_object($data)){
				$return = array($data);
			}
			else{
				$expression	=	new Predicate\Expression(':value'.$this->getKeyValues());
				$this->setFactoryValues(self::VALUES, array(':value'.$this->getKeyValues() => $data));
				$this->setKeyValues($this->getKeyValues()+1);
				$return	=	$expression;
			}
			return $return;
			break;
		
			case self::IS_NULL:
			$return	=	array();
			if(is_array($data)){
				foreach($data as $array){
					foreach($array as $key => $value){
						array_push($return, new isNull(trim($value)));
					}
				}
			}
			return $return;
			break;
			
			case self::IS_NOT_NULL:
			$return	=	array();
			if(is_array($data)){
				foreach($data as $array){
					foreach($array as $key => $value){
						array_push($return, new isNotNull(trim($value)));
					}
				}
			}
			return $return;
			break;
		}
	}
	protected function extractOperator($data){
		$operator	=	NULL;
		if(preg_match('/[[<]|[>]|[=]|[>=]|[<=]|[!=]]/', $data) > 0){
			if(preg_match('/>=/', $data) > 0){
				$operator = '>=';
			}
			else if(preg_match('/<=/', $data) > 0){
				$operator = '<=';
			}
			else if(preg_match('/!=/', $data) > 0){
				$operator = '!=';
			}
			else if(preg_match('/>/', $data) > 0){
				$operator = '>';
			}
			else if(preg_match('/</', $data) > 0){
				$operator = '<';
			}
			else if(preg_match('/=/', $data) > 0){
				$operator = '=';
			}
		}
		return $operator;
	}
	
	public function setFactoryValues($key, $value){
		$key	=	strtoupper($key);
		
		switch($key){
			case self::DISTINCT:
			$this->currentFactoryRequest[self::DISTINCT]	.=	$value;
			break;
			
			case self::WHERE:
			$value		=	$this->convertValues($key, $value);
			foreach($value as $array){
				array_push($this->currentFactoryRequest[self::WHERE], $array);
			}
			break;
			
			case self::HAVING:
			array_push($this->currentFactoryRequest[self::HAVING], $value);
			break;
			
			case self::VALUES:
			$this->currentFactoryRequest[self::VALUES]	=	array_merge($this->currentFactoryRequest[self::VALUES], $value);
			break;
			
			case self::ORDER:
			array_push($this->currentFactoryRequest[self::ORDER], $value);
			break;
			
			case self::COLUMNS:
			$value	=	$this->convertValues(self::COLUMNS, $value);
			$this->currentFactoryRequest[self::COLUMNS]	=	array_merge($this->currentFactoryRequest[self::COLUMNS], $value);
			break;
			
			case self::FROM:
			$value	=	$this->convertValues(self::FROM, $value);
			$this->currentFactoryRequest[self::FROM] = $value;
			break;
			
			case self::INSERT:
			$value	=	$this->convertValues($key, $value);
			$this->currentFactoryRequest[self::INSERT] = array_merge($this->currentFactoryRequest[self::INSERT], $value);
			break;
			
			case self::UPDATE:
			$value	=	$this->convertValues($key, $value);
			$this->currentFactoryRequest[self::UPDATE] = array_merge($this->currentFactoryRequest[self::UPDATE], $value);
			break;
			
			case self::ON:
			if(array_key_exists($this->getKeyJoin() -1 , $this->getFactoryValues(self::JOIN))){
				$join	=	$this->getFactoryValues(self::JOIN);
				$join	=	$join[$this->getKeyJoin() -1];
				if($join['on'] == NULL){
					$join['on']	=	$value;
					$this->currentFactoryRequest[self::JOIN][$this->getKeyJoin() -1] = $join;
				}
				else{
					$this->currentFactoryRequest[self::JOIN][$this->getKeyJoin()]['on']	= $value;
				}
			}
			else{
				$this->currentFactoryRequest[self::JOIN][$this->getKeyJoin()]['on']	= $value;
			}
			break;
			
			case self::JOIN:
			if(array_key_exists($this->getKeyJoin(), $this->currentFactoryRequest[self::JOIN]) AND !array_key_exists('tableName', $this->currentFactoryRequest[self::JOIN][$this->getKeyJoin()])){
				$this->currentFactoryRequest[self::JOIN][$this->getKeyJoin()] = array_merge($value, $this->currentFactoryRequest[self::JOIN][$this->getKeyJoin()]);
			}
			else{
				$this->currentFactoryRequest[self::JOIN][$this->getKeyJoin()] = $value;
			}
			$this->setKeyJoin($this->getKeyJoin() + 1);
			break;
			
			case self::PAGINATION:
			$this->currentFactoryRequest[self::PAGINATION] = array_merge($this->currentFactoryRequest[self::PAGINATION], $value);
			break;
			
			default:
			$this->currentFactoryRequest[$key] = $value;
			break;
		}
	}
	public function getFactoryValues($key = NULL){
		$return	=	$this->currentFactoryRequest;
		if($key !== NULL AND array_key_exists($key, $return)){
			$key	=	strtoupper($key);
			$return = 	$return[$key];
		}
		else{
			$return = NULL;
		}
		return $return;
	}

	/**
	*	WHERE
	*	----- IS STRING -----
	*	-	where('id != 4')
	*		WHERE id != 4
	*	-	where('id = 1, id > 2, id < 3, id >= 4, id <= 5, id != 6')
	*		WHERE id = 1 AND id > 2 AND id < 3 AND id >= 4 AND id <= 5 AND id != 6
	*	-	where('id = 1,2,3', 'AND', TRUE)
	*		WHERE id = 1 AND id = 2 AND id = 3
	*	-	where('id = 4, id = 5, id = 6', 'AND', TRUE)
	*		WHERE id = 4 AND id = 5 AND id = 6
	*	-	where('id > 4, id < 5, id >= 8', 'OR', TRUE)
	*		WHERE id > 4 OR id < 5 OR id >= 8
	*
	*	----- IS ARRAY -----
	*	- 	where(array('id' => 4, array('id' => 4)))
	*	-	where(array('id' => 1))
	*		WHERE id = 1
	*	-	where(array('id' => array('!=' 	=> 	4)))
	*		WHERE id != 4
	*	-	where(array('id' => array('!='	=>	array(1, 2, 3, 4))))
	*		WHERE id != 1 AND id != 2 AND id != 3 AND id != 4
	*	-	where(array('id' => array('1','2','>4')))
	*		WHERE id = 1 AND id = 2 AND id > 4
	*	-	where(array('id' => PREDICATE))
	*	-	where(array('id' => EXPRESSION))	
	*	-	where(PREDICATE)
	*	-	where(EXPRESSION)
	*
	*	RETURN
	*	$predicate as object
	*	array($predicate, $data);
	*/
	public function where($data, $predicate = 'AND', $convert = FALSE){
		$data	=	$this->extractWhere($data, $predicate, $convert);
		foreach($data as $item){
			$item	=	$this->setFactoryValues(self::WHERE, $item);
		}
		return $this;
	}
	public function whereOR($data, $convert = FALSE){
		$this->where($data, 'OR', $convert);
		return $this;
	}
	public function whereAND($data, $convert = FALSE){
		$this->where($data, 'AND', $convert);
		return $this;
	}
	public function extractWhere($data, $predicate = 'AND', $convert = FALSE){
		$return		=	array();
		$predicate	=	$this->convertPredicate($predicate);
		if(is_array($data)){
			foreach($data as $k1 => $v1){
				if(is_array($v1)){
					if(is_int($k1)){
						$return	=	array_merge($this->extractWhere($v1, $predicate), $return);
					}
					else{
						foreach($v1 as $k2 => $v2){
							if(is_string($k2) AND in_array($k2, $this->predicateOperator)){
								if(is_array($v2)){
									foreach($v2 as $k3 => $v3){
										$return = array_merge($this->extractWhere($k1.' '.$k2.' '.$v3, $predicate), $return);
									}
								}
								else{$return = array_merge($this->extractWhere($k1.' '.$k2.' '.$v2, $predicate), $return);}
							}
							else{$return = array_merge($this->extractWhere(array($k1 => $v2), $predicate), $return);}
						}
					}
				}		
				else if(is_object($v1)){
					if(is_int($k1)){
						$return = array_merge($this->extractWhere($v1, $predicate), $return);
					}
					else{
						$factoryData	=	array($predicate, array($k1 => $v1));
						array_push($return, $factoryData);
					}
				}
				else{
					if(is_int($k1)){$return = array_merge($this->extractWhere($v1, $predicate), $return);}
					else{$return = array_merge($this->extractWhere($k1.' = '.$v1, $predicate), $return);}
				}
			}
		}
		else if(is_object($data)){
			$factoryData	=	array($predicate, $data);
			array_push($return, $factoryData);
		}
		else if(is_string($data)){
			if(preg_match('/,/', $data) > 0 && $convert == TRUE){
				if(preg_match('/=/', $data) > 0){
					$data	=	explode('=', $data);
					$return = array_merge($this->extractWhere(array($data[0] => explode(',', $data[1])), $predicate), $return);
				}
				else{
					$data	=	explode(',', $data);
					$return = array_merge($this->extractWhere($data, $predicate, $convert), $return);
				}
			}
			else{
				$data	=	trim($data);
				array_push($return, array($predicate, $data));
			}
		}
		return $return;
	}

	/**
	*	FIND
	*	-	find('a')
	*		COLUMNS a
	*	-	find('a, b, c')
	*		COLUMNS a, b, c
	*	-	find(array('a','b','c'))
	*		COLUMNS a, b, c
	*	-	find('id = 1')
	*		WHERE id = 1
	*	-	find(array('id' => 1))
	*		WHERE id = 1
	*	-	find('id as newId')
			COLUMNS id as newId
	*	-	find(array('a','b','c', array('id' => 1), array('d, e, f', 'g', 'h')
	*		COLUMNS a, b, c, d, e, f, g, h WHERE id = 1
	*/
	public function find($data = '*', $predicate = 'AND', $convert = TRUE, $select = FALSE){
		$this->setFactoryValues(self::TYPE_OF_REQUEST, 'select');
		$predicate	=	$this->convertPredicate($predicate);
		if(is_array($data)){
			foreach($data as $k1 => $v1){
				if(is_int($k1)){$this->find($v1, $predicate, $convert, $select);}
				else{
					if($select == FALSE){$this->where(array($k1 => $v1));}
					else{$this->columns(array($k1 => $v1));}
				}
			}
		}
		else if(is_object($data)){
			if($select = TRUE){$this->columns($data);}
			else{$this->where($data);}
		}
		else if(is_string($data)){
			if(preg_match('/,/', $data) > 0 && $convert == TRUE){
				$data	=	explode(',', $data);
				$this->find($data, $predicate, $convert, $select);
			}
			else if(preg_match('/ as /i', $data) > 0){
				$this->columns($data);
			}
			else if($this->extractOperator($data) !== NULL && $convert == TRUE){
				if($select == FALSE){$this->where($data);}
				else{$this->columns($data);}
			}
			else{
				$this->columns($data);
			}
		}
		return $this;
	}
	public function select($data = '*', $predicate = 'AND', $convert = TRUE, $select = TRUE){
		$this->find($data, $predicate, $convert, $select);
		return $this;
	}
	
	/**
	*	COLUMNS
	*
	*	-	columns(array('a, b, c'))
	*	-	columns(array('a','b','c'))
	*	-	columns(array('a' => 'b'))
	*	-	columns(array('a as b, a = b, a, b, c'))
	*/
	public function columns($data = '*'){
		$data	=	$this->extractColumns($data);
		foreach($data as $array){
			$this->setFactoryValues(self::COLUMNS, $array);
		}
		return $this;
	}
	public function extractColumns($data = '*'){
		$return =	array();
		if(is_array($data)){
			foreach($data as $k1 => $v1){
				if(is_array($v1)){$this->extractColumns($v1);}
				else{
					if(is_int($k1)){
						$return =	array_merge($this->extractColumns($v1), $return);
					}
					else{
						if(is_object($v1)){array_push($return, array($k1 => $v1));}
						else{$return = array_merge($this->extractColumns($k1.' as '.$v1), $return);}
					}
				}
			}
		}
		else if(is_object($data)){
			array_push($return, $data);
		}
		else{
			if(preg_match('/,/', $data) > 0){
				$data	=	explode(',', $data);
				$return = 	array_merge($data, $return);
			}
			else if(preg_match('/ as /i', $data) > 0){
				$data	=	preg_replace('/ as /i',' as ', $data);
				$data	=	explode(' as ', $data);
				array_push($return, array(trim($data[1]) => trim($data[0])));
			}
			else if(preg_match('/=/', $data) > 0){
				$data	=	explode('=', $data);
				$return =	array_merge($this->extractColumns($data[0].' as '.$data[1]), $return);
			}
			else{
				if($data == '*'){array_push($return, array('*')) ;}
				else{$return = array_merge($this->extractColumns($data.' as '.$data), $return);}
			}
		}
		return $return;
	}
	
	/**
	*	INSERT
	*	
	*	-	insert(array(1, 'a', 'b'))
	*	-	insert(array('id' => 4, 'test' => 5))
	*	-	insert(array('1, a, b, c'))
	*	-	insert('1, a, b, c, d')
	*/
	public function insert($data, $flag = 'SET', $convert = FALSE){
		$this->setFactoryValues(self::TYPE_OF_REQUEST, 'insert');
		$this->setFactoryValues(self::FLAG, strtoupper($flag));
		
		if(is_array($data)){
			foreach($data as $k1 => $v1){
				if(is_int($k1)){$this->insert($v1);}
				else{$this->setFactoryValues(self::INSERT, array($k1 => $v1));}
			}
		}
		else if(is_object($data)){
			$this->setFactoryValues(self::INSERT, $data);
		}
		else{
			if(preg_match('/,/', $data) > 0 AND $convert == TRUE){
				$data	=	explode(',', $data);
				$this->insert($data);
			}
			else if(preg_match('/=/', $data) > 0 AND $convert == TRUE){
				$data	=	explode('=', $data);
				$this->insert(array($data[0] => $data[1]));
			}
			else{
				$this->setFactoryValues(self::INSERT, array($data));
			}
		}
		return $this;
	}
	public function add($data, $convert = FALSE){
		$this->insert($data, 'SET', $convert);
		return $this;
	}
	public function values($data, $flag = 'SET', $convert = FALSE){
		$this->insert($data, $flag, $convert);
		return $this;
	}
	
	/**
	*	LIMIT	OFFSET
	*
	*	offset(0) limit(5)
	*	limit(0, 5)
	*/
	public function limit($offset, $limit = NULL){
		if($limit === NULL){
			$this->setFactoryValues(self::LIMIT, $offset);
			if($this->getFactoryValues(self::OFFSET) === NULL){
				$this->setFactoryValues(self::OFFSET, 0);
			}
		}
		else{
			$this->setFactoryValues(self::LIMIT, $limit);
			$this->setFactoryValues(self::OFFSET, $offset);
		}
		return $this;
	}
	public function offset($offset = 0){
		if($this->getFactoryValues(self::LIMIT) === NULL){
			$limit = 0;
		}
		$this->limit($offset, $limit);
		return $this;
	}
	
	public function parenthese($data, $predicate = 'AND', $convert = FALSE){
		$parenthese	=	new PredicateSet();
		$data		=	$this->extractWhere($data, $predicate, $convert);
		foreach($data as $item){
			$item	=	$this->convertValues(self::WHERE, $item);
			$item	=	$item[0];
			$parenthese->addPredicate($item[0], $item[1]);
		}
		return $parenthese;
	}
	public function into($data = NULL){
		if($into === NULL){
			if($this->getFactoryValues(self::INTO) === ''){
				$this->setFactoryValues(self::INTO, $this->getOriginalTableName());
			}
		}
		else{
			$this->setFactoryValues(self::INTO, $data);
		}
		return $this;
	}

	/**
	*	JOIN
	*
	*	-	join('table as allias')
	*	-	join('table')
	*	-	join(array('table' => 'allias'))
	*	-	join('table, allias')
	*/
	public function join($tables, $on = NULL, $columns = '*', $type = 'inner'){
		$columnsOfFirstTable	=	$this->getFactoryValues(self::COLUMNS);
		if(empty($columnsOfFirstTable)){$this->find('*');}
		
		if(is_array($tables)){
			if($columns !== '*'){
				$columns = $this->extractColumns($columns);
				$cols		=	array();
				foreach($columns as $c){
					$cols	=	array_merge($cols, $c);
				}
			}
			else{
				$cols = '*';
			}
			
			$data	=	array(
				'tables'	=>	array_flip($tables),
				'columns'	=>	$cols,
				'type'		=>	$type,
			);
			$this->on($on);
			$this->setFactoryValues(self::JOIN, $data);
		}
		else if(is_object($tables)){
			$this->setFactoryValues(self::JOIN, $tables);
		}
		else{		
			if(preg_match('/,/', $tables) > 0){
				$tables	= explode(',', $tables);
			}
			else if(preg_match('/=/', $tables) > 0){
				$tables	=	explode('=', $tables);
			}			
			else if(preg_match('/ as /i', $tables) > 0){
				$tables	=	preg_replace('/ as /i', ' as ', $tables);
				$tables	=	explode(' as ', $tables);
			}
			else{
				$tables	=	array($tables, $tables);
			}
			
			$this->join(array(trim($tables[0]) => trim($tables[1])), $on, $columns, $type);
		}
		return $this;
	}
	public function leftJoin($tables, $on = NULL, $columns = '*'){
		$this->join($tables, $on, $columns, 'left');
		return $this;
	}
	public function rightJoin($tables, $on = NULL, $columns = '*'){
		$this->join($tables, $on, $columns, 'right');
		return $this;
	}	
	public function innerJoin($tables, $on = NULL, $columns = '*'){
		$this->join($tables, $on, $columns, 'inner');
		return $this;
	}	
	public function outerJoin($tables, $on = NULL, $columns = '*'){
		$this->join($tables, $on, $columns, 'outer');
		return $this;
	}
	public function fullJoin($tables, $on = NULL, $columns = '*'){
		$this->join($tables, $on, $columns, 'full');
		return $this;
	}	
	
	/**
	*	On
	*
	*	-	on('id as test')
	*	-	on('table')
	*	-	on(array('id' => 'test'))
	*	-	on('id, test')
	*/
	public function on($tables){
		if(is_array($tables)){
			foreach($tables as $key => $value){
				if(is_int($key)){
					$this->on($value);
				}
				else{
					$this->setFactoryValues(self::ON, trim($key).' = '.trim($value));
				}
			}
		}
		else if(is_object($tables)){
			$this->setFactoryValues(self::ON, $tables);
		}
		else{
			if(preg_match('/,/', $tables) > 0){
				$tables	= explode(',', $tables);
			}
			else if(preg_match('/=/', $tables) > 0){
				$tables	=	explode('=', $tables);
			}			
			else if(preg_match('/ as /i', $tables) > 0){
				$tables	=	preg_replace('/ as /i', ' as ', $tables);
				$tables	=	explode(' as ', $tables);
			}
			else{
				$tables	=	array($tables, $tables);
			}
			$this->on(array($tables[0] => $tables[1]));
		}
		return $this;
	}	
	
	/**
	*	UPDATE
	*
	*	INSERT EXPRESSION
	*/
	public function update($data, $flag = 'SET', $convert = FALSE){
		$this->setFactoryValues(self::TYPE_OF_REQUEST, 'update');
		$this->setFactoryValues(self::FLAG, strtoupper($flag));

		if(is_array($data)){
			foreach($data as $k1 => $v1){
				if(is_int($k1)){$this->update($v1);}
				else{
					$this->setFactoryValues(self::UPDATE, array($k1 => $v1));
				}			
			}
		}
		else if(is_object($data)){
			$this->setFactoryValues(self::UPDATE, $data);
		}
		else{
			if(preg_match('/,/', $data) > 0 AND $convert == TRUE){
				$data	=	explode(',', $data);
				$this->update($data);
			}
			else if(preg_match('/=/', $data) > 0 AND $convert == TRUE){
				$data	=	explode('=', $data);
				$this->update(array($data[0] => $data[1]));
			}
			else{
				$this->setFactoryValues(self::UPDATE, array($data));
			}
		}
		return $this;
	}
	public function setFlag($flag){
		$flag = strtoupper($flag);
		switch($flag){
			case 'SET':
			$flag = 'SET';
			break;
			
			case 'MERGE':
			$flag = 'MERGE';
			break;
			
			default:
			$flag = 'SET';
			break;
		}
		$this->setFactoryValues(self::FLAG, $flag);
		return $this;
	}
	public function merge($data){
		$this->setFlag('MERGE');
		$typeOfRequest	=	$this->getFactoryValues(self::TYPE_OF_REQUEST);
		switch($typeOfRequest){
			case 'insert':
			$this->insert($data);
			break;
			
			case 'update':
			$this->update($data);
			break;
		}
		return $this;
	}
	public function set($data){
		$this->update($data);
		return $this;
	}
	
	public function expression($expression, $params = NULL){
		return new Expression($expression, $params);
	}
	public function literal($expression, $params = NULL){
		return $this->expression($expression, $params);
	}
	public function meta(){
		return new Metadata($this->getAdapter());
	}
	public function getStructure($tableName = NULL){
		if($tableName !== NULL){
			return $this->meta()->getColumnNames($tableName);
		}
		else{
			return $this->meta()->getColumnNames($this->getOriginalTableName());
		}
	}
	
	public function groupBy($data){
		$this->setFactoryValues(self::GROUP_BY, $data);
		return $this;
	}
	public function group($data){
		$this->groupBy($data);
		return $this;
	}
	
	/**
	*	IS NULL
	*	IS NOT NULL
	*	COLUMNS EXPRESSION
	*/
	public function isNull($data){
		$data	=	$this->extractColumns($data);
		$data	=	$this->convertValues(self::IS_NULL, $data);
		return $data;
	}
	public function isNotNull($data){
		$data	=	$this->extractColumns($data);
		$data	=	$this->convertValues(self::IS_NOT_NULL, $data);
		return $data;
	}

	public function extractOperation($identifier, $value = NULL){
		$return	=	array();
		if(is_array($identifier)){
			foreach($identifier as $k	=>	$v){
				if(is_int($k)){
					$return = array_merge($return, $this->extractOperation($v, $value));
				}
				else{
					if(preg_match('/,/', $k) > 0){
						$k 	= 	explode(',', $k);
						$return	=	array_merge($this->extractOperation($k, $v), $return);
					}
					else{
						$return	= 	array_merge($this->extractOperation($k, $v), $return);
					}
				}
			}
		}
		else if(is_object($identifier)){
			array_push($return, $identifier);
		}
		else{
			if(preg_match('/,/', $identifier) > 0){
				$identifier	=	explode(',', $identifier);
				$return		=	array_merge($return, $this->extractOperation($identifier, $value));
			}
			else{
				if(is_array($value)){
					foreach($value as $k => $v){
						$return = array_merge($return, $this->extractOperation($identifier, $v));
					}
				}
				else{
					if(preg_match('/,/', $value) > 0){
						$value	=	explode(',', $value);
						$return =	array_merge($return, $this->extractOperation($identifier, $value));
					}
					else{
						array_push($return, array($identifier, $value));
					}
				}
			}
		}
		return $return;
	}
	
	public function in($identifier, $value = NULL){
		$value 	=	$this->extractOperation($identifier, $value);
		$return	=	array();
		foreach($value as $array){
			array_push($return, new In($array));
		}
		return $return;
	}
	public function equalTo($left, $right = NULL){
		$return	=	$this->extractOperation($left, $right);
		return $return;
	}
	public function lessThan($left, $right = NULL){
		$value	=	$this->extractOperation($left, $right);
		$return	=	array();
		foreach($value as $array){
			array_push($return, $array[0].' < '.$array[1]);
		}
		return $return;
	}
	public function greaterThan($left, $right = NULL){
		$value	=	$this->extractOperation($left, $right);
		$return	=	array();
		foreach($value as $array){
			array_push($return, $array[0].' > '.$array[1]);
		}
		return $return;
	}
	public function greaterThanOrEqualTo($left, $right = NULL){
		$value	=	$this->extractOperation($left, $right);
		$return	=	array();
		foreach($value as $array){
			array_push($return, $array[0].' >= '.$array[1]);
		}
		return $return;
	}
	public function lessThanOrEqualTo($left, $right = NULL){
		$value	=	$this->extractOperation($left, $right);
		$return	=	array();
		foreach($value as $array){
			array_push($return, $array[0].' <= '.$array[1]);
		}
		return $return;
	}
	public function notEqual($left, $right = NULL){
		$value	=	$this->extractOperation($left, $right);
		$return	=	array();
		foreach($value as $array){
			array_push($return, $array[0].' != '.$array[1]);
		}
		return $return;
	}
	public function notEqualTo($left, $right = NULL){
		return $this->notEqual($left, $right);
	}
	public function having($expression, $data){	
		$this->setFactoryValues(self::HAVING, $this->expression($expression, $data));
		return $this;
	}
	/**
	*	Between
	*	between(array('identifier' => array('min','max')))
	*	between(array('identifier, test' => 'test, test'))
	*	between(array('identifier', 'test' => array('min', 'max')))
	*/
	public function between($identifier, $min = NULL, $max = NULL){
		$return	=	array();
		if(is_string($identifier)){
			if(preg_match('/,/', $identifier) > 0){
				$identifier = explode(',', $identifier);
				$return = array_merge($return, $this->between($identifier, $min, $max));
			}
			else{
				array_push($return, new Between($identifier, $min, $max));
			}
		}
		else if(is_array($identifier)){
			foreach($identifier as $key => $value){
				if(is_int($key)){
					$return = array_merge($return, $this->between($value, $min, $max));
				}
				else{
					$return = array_merge($return, $this->between($key, $value[0], $value[1]));
				}
			}
		}
		else if(is_object($identifier)){
			array_push($return, $identifier);
		}
		return $return;
	}
	
	/**
	*	Pagination
	*/
	public function pagination($factoryPagination = NULL){
		$this->setFactoryPagination($factoryPagination);
		return $this;
	}	
	public function setFactoryPagination($factoryPagination = NULL){
		$this->currentFactoryRequest[self::PAGINATION]['isClaim'] = TRUE;

		if($factoryPagination !== NULL AND is_array($factoryPagination)){
			$this->setFactoryValues(self::PAGINATION, $factoryPagination);
		}
		return $this;
	}
	public function getFactoryPagination($index = NULL){
		$return = $this->getFactoryValues(self::PAGINATION);		
		if($index !== NULL AND array_key_exists($index, $return)){
			$return	= $return[$index];
		}
		return $return;
	}
	public function setPaginatorPlugin($paginatorPlugin = NULL){
		if($paginatorPlugin !== NULL){
			$this->paginatorPlugin = $paginatorPlugin;
		}
		return $this;
	}
	public function getPaginatorPlugin(){
		if($this->paginatorPlugin === NULL){
			$this->getController()->paginatorPlugin()->setAdapter($this->getAdapter());
			$this->setPaginatorPlugin($this->getController()->paginatorPlugin());
		}
		return $this->paginatorPlugin;
	}

	/**
	*	FROM
	*
	*	COLUMNS EXPRESSION
	*/
	public function from($data){
		$data	=	$this->extractColumns($data);
		foreach($data as $array){
			$this->setFactoryValues(self::FROM, $data);
		}
		return $this;
	}
	
	/**
	*	ORDER
	*
	*	-	order('id DESC')
	*	-	order('id desc')
	*	-	order(array('id' => 'DESC'))
	*	-	order(array('id' => 'DESC', 'name' => 'ASC'))
	*	-	order(array('id', 'name'), 'DESC')
	*/
	public function order($key, $order = NULL){
		if(is_array($key)){
			foreach($key as $k1 => $v1){
				if(is_int($k1)){
					$this->order($v1, $order);
				}
				else{
					$this->order($k1, $v1);
				}
			}
		}
		else if(is_object($key)){
			$this->setFactoryValues(self::ORDER, $key);
		}
		else{
			if(preg_match('/,/', $key) > 0){
				$key	=	explode(',', $key);
				$this->order($key, $order);
			}
			else if(preg_match('/asc/i', $key) > 0){
				$key 	=	preg_replace('/asc/i', 'ASC', $key);
				$key	=	explode('ASC', $key);
				$this->order($key[0], 'ASC');
			}
			else if(preg_match('/desc/i', $key) > 0){
				$key 	=	preg_replace('/DESC/i', 'DESC', $key);
				$key	=	explode('DESC', $key);
				$this->order($key[0], 'DESC');				
			}
			else if(preg_match('/>/', $key) > 0){
				$key 	=	preg_replace('/>/i', 'DESC', $key);
				$key	=	explode('DESC', $key);
				$this->order($key[0], 'DESC');
			}
			else if(preg_match('/</', $key) > 0){
				$key 	=	preg_replace('/</i', 'ASC', $key);
				$key	=	explode('ASC', $key);
				$this->order($key[0], 'ASC');
			}
			else{
				$this->setFactoryValues(self::ORDER, trim($key).' '.strtoupper(trim($order)));
			}
		}
		return $this;
	}
	public function orderBy($key, $order = NULL){
		$this->order($key, $order);
		return $this;
	}
	
	/**
	*	DELETE
	*
	*	WHERE EXPRESSION
	*/
	public function delete($data = NULL, $predicate = 'AND', $convert = FALSE){
		$this->setFactoryValues(self::TYPE_OF_REQUEST, 'delete');
		if($data != NULL){
			$this->where($data, $predicate, $convert = FALSE);
		}
		return $this;
	}
	
	public function clear(){
		$this->setFactoryValues(self::TABLE_NAME, $this->getTableName());
		$this->setFactoryValues(self::ORIGINAL_TABLE_NAME, $this->getOriginalTableName());
		$this->setFactoryValues(self::ALLIAS_TABLE_NAME, $this->getAlliasTableName());
		if($this->getFactoryValues(self::TYPE_OF_REQUEST) === NULL){
			$this->find();
		}
		
		$this->setFactoryRequest($this->currentFactoryRequest);
		$this->resetCurrentFactory();
		return $this;
	}
	public function resetCurrentFactory(){
		$this->currentFactoryRequest=	array(
			self::PAGINATION			=> array('isClaim' => FALSE),
			self::TABLE_NAME			=> NULL,
			self::ORIGINAL_TABLE_NAME	=> NULL,
			self::ALLIAS_TABLE_NAME		=> NULL,
			self::GROUP_BY				=> array(),
			self::COLUMNS				=> array(),
			self::WHERE					=> array(),
			self::HAVING				=> array(),
			self::INSERT				=> array(),
			self::UPDATE				=> array(),
			self::ORDER					=> array(),
			self::JOIN					=> array(),
			self::VALUES				=> array(),
			self::DISTINCT				=> NULL,
			self::LIMIT					=> NULL,
			self::OFFSET				=> NULL,
			self::FROM					=> NULL,	
			self::ON					=> NULL,
			self::FETCH_MODE			=> PDO::FETCH_OBJ,
			self::INTO					=> '',
			self::FLAG					=> 'SET',
			self::TYPE_OF_REQUEST		=> NULL,
		);
	}
	public function resetResult(){
		$this->result			=	array();
		$this->results			=	array();
		$this->resultsTotal		=	array();
		$this->resultsLength	=	array();
		$this->resultsPagination=	array();
		$this->lastValues		=	array();
		return $this;
	}
	public function reset(){
		$this->factoryRequest	=	array();
		$this->keyValues		=	NULL;
	}
	public function renderFactory($factory = NULL){
		$factories	=	$this->getFactoryRequest();
		if($factory !== NULL){
			$factories = array($factory);
		}
		
		foreach($factories as $key => $factory){
			if($factory[self::TYPE_OF_REQUEST] !== NULL){
				$sqlObject	=	$this->getSqlObject(TRUE);
				if($factory[self::INTO] === ''){
					$factory[self::INTO] = $factory[self::ORIGINAL_TABLE_NAME];
				}
				if($factory[self::TYPE_OF_REQUEST] === 'insert'){
					$values	=	array();
				
					foreach($factory[self::INSERT] as $insert){
						$values	=	array_merge($values, $insert);
					};
					
					$sqlObject	=	$sqlObject	->insert()
												->values($values, $factory[self::FLAG])
												->into($factory[self::INTO]);
					if(!empty($factory[self::COLUMNS])){
						$sqlObject->columns($factory[self::COLUMNS]);
					}
				}
				else{
					if($factory[self::FROM]	===	NULL){
						$factory[self::FROM] = array($factory[self::ALLIAS_TABLE_NAME]	=>	$factory[self::ORIGINAL_TABLE_NAME]);
					}
					
					switch($factory[self::TYPE_OF_REQUEST]){
						case 'select':
						if(empty($factory[self::COLUMNS])){$factory[self::COLUMNS]	=	array('*');}

						$sqlObject	=	$sqlObject	->select()
													->from($factory[self::FROM])
													->columns($factory[self::COLUMNS]);
													
						if($factory[self::DISTINCT] != ''){
							$sqlObject->quantifier($factory[self::DISTINCT]);
						}
						break;
						
						case 'update':
						$sqlObject	=	$sqlObject->update($factory[self::INTO]);
						foreach($factory[self::UPDATE] as $array){
							$sqlObject->set($array, $factory[self::FLAG]);							
						}
						break;
						
						case 'delete':
						$sqlObject	=	$sqlObject	->delete()
													->from($factory[self::ORIGINAL_TABLE_NAME]);
						break;
					}
					
					$w	=	new Where();
					$factory[self::WHERE]	=	array_reverse($factory[self::WHERE]);
					foreach($factory[self::WHERE] as $where){
						$w->addPredicate($where[0], $where[1]);	
					}
					$sqlObject->where($w);
					
					if(!empty($factory[self::ORDER])){
						$sqlObject->order($factory[self::ORDER]);
					}
					
					if($factory[self::LIMIT] !== NULL AND $factory[self::OFFSET] !== NULL){
						$sqlObject->offset($factory[self::OFFSET]);
						$sqlObject->limit($factory[self::LIMIT]);
					}
					
					if(!empty($factory[self::JOIN])){
						foreach($factory[self::JOIN] as $join){
							$sqlObject->join($join['tables'], $join['on'], $join['columns'], $join['type']);
						}
					}
					
					if(!empty($factory[self::GROUP_BY])){
						$sqlObject->group($factory[self::GROUP_BY]);
					}
					
					if(!empty($factory[self::HAVING])){
						foreach($factory[self::HAVING] as $having){
							$sqlObject->having($having);
						}
					}
				}
				$factories[$key]['sqlObject'] = $sqlObject;
			}
		}
		return $factories;
	}
	
	/**
	*	Count
	*	count()
	*	count('t, d, s')
	*	count(array('id','test','name'))
	*	count(array('id' => 'test', 'name' => 'x'))
	*/
	public function count($data = NULL){
		$this->setFactoryValues(self::TYPE_OF_REQUEST, 'select');
		if(is_null($data)){
			$this->count(array('*' => 'total'));
		}
		else if(is_array($data)){
			foreach($data as $key => $value){
				if(is_int($key)){
					$this->count(array($value => $value));
				}
				else{
					$this->setFactoryValues(self::COLUMNS, array($value => new Expression('COUNT('.$key.')')));
				}
			}
		}
		else if(is_string($data)){
			$array	=	explode(',', $data);
			$this->count($array);
		}
		
		return $this;
	}	
	public function execute($sql = NULL){
		$this->clear();
		$this->resetResult();
		$factories	=	$this->renderFactory();
		
		foreach($factories as $factory){
			if($factory[self::PAGINATION]['isClaim']	===	TRUE){
				$plugin								=	$this->getPaginatorPlugin();
				$factoryPagination					=	$factory[self::PAGINATION];
				$factoryPagination['requestObject']	=	$factory['sqlObject'];
				$factoryPagination['values']		=	$factory[self::VALUES];

				$plugin->setFactory($factoryPagination);
								
				$this->setResults($plugin->getItems());
				$this->setResultsTotal($plugin->count());
				$this->setResultsLength($plugin->length());
				$this->setLastValues(NULL);
				$this->setResultsPaginator($plugin);
			}
			else{
				$statement	=	$this->getAdapter()->createStatement();
				if($sql !== NULL){
					$statement->prepare($sql);				
					$statement->execute();
				}
				else{
					$statement->prepare($this->getSqlObject(TRUE)->getSqlStringForSqlObject($factory['sqlObject']));
					$statement->getResource()->setFetchMode($factory[self::FETCH_MODE]);
					$statement->execute($factory[self::VALUES]);
				}
				
				if($factory[self::TYPE_OF_REQUEST] === 'insert' OR $factory[self::TYPE_OF_REQUEST] === 'update' OR $factory[self::TYPE_OF_REQUEST] === 'delete'){
					$this->setLastValues($this->getAdapter()->getDriver()->getLastGeneratedValue());
					$this->setResults(NULL);
					$this->setResultsTotal(1);
				}
				else{
					$results	=	$statement->getResource()->fetchAll();					
					$this->setResults($results);
					$this->setLastValues(NULL);
					$this->setResultsTotal($this->getCount($factory));
					$this->setResultsLength(count($results));
				}
				$this->setResultsPaginator(NULL);
			}
		}
		$this->reset();
		return $this;
	}
	public function exe(){
		$this->execute();
		return $this;
	}
	
	public function one(){
		$this->limit(0, 1);
		return $this;
	}
	
	public function getResult($index = 1, $array = FALSE){
		return $this->getResults($index, $array);
	}
	public function result($index = 1, $array = FALSE){
		return $this->getResults($index, $array);
	}
	
	public function getResults($index = NULL, $array = TRUE){
		$retour	=	$this->results;
		if(is_bool($index)){
			$array = $index;
			$index = NULL;
		}
			
		if($index === NULL){
			if(count($retour) === 1){
				$retour = $retour[0];
				if(count($retour) === 1 AND $array === FALSE){
					$retour = $retour[0];
				}
			}
		}
		else{
			$index <= 0 ? $index = 1 : $index;
			$index = $index - 1;
			$this->setKeyResult((int) $index);
			if(array_key_exists($index, $retour)){
				$retour = $retour[$index];
				if($array === FALSE && array_key_exists(0, $retour))
					{$retour = $retour[0];}
				else{
					$retour = NULL;
				}
			}
		}
		return $retour;
	}
	public function setResults($data){
		array_push($this->results, $data);
		return $this;
	}
	public function results($index = NULL, $array = TRUE){
		return $this->getResults($index, $array);
	}
	public function getResultsMerge(){
		$results	=	$this->getResults();
		$return		=	array();
		foreach($results as $array){
			foreach($array as $value){
				array_push($return, $value);
			}
		}
		return $return;
	}
	
	protected function setResultsTotal($data){
		array_push($this->resultsTotal, $data);
		return $this;
	}
	protected function getResultsTotal($array = FALSE){
		$return	=	$this->resultsTotal;
		if($this->getKeyResult() !== NULL){
			$return = $return[$this->getKeyResult()];
			if($array === TRUE){
				$return = array($return);
			}
		}
		return $return;
	}
	public function total(){
		$total	=	array_sum($this->getResultsTotal(TRUE));
		return $total;
	}
	public function getTotal(){
		return $this->total();
	}
	
	protected function setResultsLength($data){
		array_push($this->resultsLength, $data);
		return $this;
	}
	protected function getResultsLength($array = FALSE){
		$return	=	$this->resultsLength;
		if($this->getKeyResult() !== NULL){
			$return = $return[$this->getKeyResult()];
			if($array === TRUE){
				$return = array($return);
			}
		}
		return $return;
	}
	public function length(){
		$total	=	array_sum($this->getResultsLength(TRUE));
		return $total;
	}
	public function getLength(){
		return $this->length();
	}

	
	protected function setLastValues($data){
		array_push($this->lastValues, $data);
		return $this;
	}
	public function getLastValues($array = TRUE){
		$return	=	$this->lastValues;
		if($this->getKeyResult() === NULL){
			if(count($return) == 1 AND $array === FALSE){
				$return = $return[0];
			}
		}
		else{
			$return = $return[$this->getKeyResult()];
		}
		return $return;
	}
	public function getLastInsertId($array = FALSE){
		return (int) $this->getLastValues($array);
	}

	protected function setResultsPaginator($data){
		array_push($this->resultsPagination, $data);
	}
	protected function getResultsPagination($index = NULL, $array = TRUE){
		$return	=	$this->resultsPagination;
		if(array_key_exists($index, $return)){
			$return = $return[$index];
			if($array === TRUE){
				$return = array($return);
			}
		}
		return $return;
	}
	public function getPagination($index = NULL, $render = TRUE){
		$index === NULL ? $index = $this->getKeyResult() : $index;
		$return = $this->getResultsPagination($index);
		if(count($return) === 1){
			$return = $return[0];
			if(is_object($return)){
				$return = $return->getPaginator($render);
			}
		}
		else{
			$r = array();
			foreach($return as $paginator){
				array_push($r, $paginator->getPaginator($render));
			}
			$return = $r;
		}
		return $return;
	}
	
	/**
	* Returns the total number of rows in the result set.
	*
	* @return int
	*/
	public function getCount($factory){
		$sqlObject	=	$this->getSqlObject(TRUE);
		if($factory[self::FROM]	===	NULL){
			$factory[self::FROM] = array($factory[self::ALLIAS_TABLE_NAME]	=>	$factory[self::ORIGINAL_TABLE_NAME]);
		}
		
		switch($factory[self::TYPE_OF_REQUEST]){
			case 'select':
			$sqlObject	=	$sqlObject	->select()
										->from($factory[self::FROM])
										->columns(array('all' => new Expression('COUNT(1)')));
			break;

		}
		
		$w	=	new Where();
		$factory[self::WHERE]	=	array_reverse($factory[self::WHERE]);
		foreach($factory[self::WHERE] as $where){
			$w->addPredicate($where[0], $where[1]);	
		}
		$sqlObject->where($w);
				
		if(!empty($factory[self::JOIN])){
			foreach($factory[self::JOIN] as $join){
				$sqlObject->join($join['tables'], $join['on'], $join['columns'], $join['type']);
			}
		}
		
		if(!empty($factory[self::GROUP_BY])){
			$sqlObject->group($factory[self::GROUP_BY]);
		}
		
		$statement	=	$this->getAdapter()->createStatement();
		$statement->prepare($this->getSqlObject(TRUE)->getSqlStringForSqlObject($sqlObject));
		$statement->getResource()->setFetchMode(PDO::FETCH_OBJ);
		$statement->execute($factory[self::VALUES]);
		$results	=	$statement->getResource()->fetchAll();
		$total		=	0;
		if(!empty($factory[self::GROUP_BY])){
			$total	=	count($results);
		}
		else{
			foreach($results as $result){
				$total	+=	$result->all;
			}
		}
		return $total;
	}
	public function getResultObject($flag = TRUE){
		return new ArrayObject($this->getResult($flag));
	}

	/**
	*	Like
	*	like(In Expression)
	*/
	public function like($identifier = NULL, $like = NULL){
		$return	=	array();
		if($identifier === NULL OR $identifier === ''){
			$identifier = $this->getStructure();
		}
		$value 	=	$this->extractOperation($identifier, $like);
		$return	=	array();
		foreach($value as $array){
			$like	=	new Like();
			$like->setIdentifier($array[0]);
			$like->setLike($array[1]);
			array_push($return, $like);
		}
		return $return;
	}
	public function search($identifier, $like = NULL, $options = array()){
		$columns = $this->getFactoryValues(self::COLUMNS);
		if(empty($columns)){
			$this->find('*');
		}
		
		$options = array_merge(array('engine' => 2, 'minLength' => 0), $options);
		
		if(!is_array($identifier)){
			if(preg_match('/,/',$identifier) > 0){
				$identifier = explode(',', $identifier);
				$this->search($identifier, $like, $options);
			}
			else{
				if(!is_array($like) AND $like !== NULL){
					if(preg_match('/,/', $like) > 0){
						$like = explode(',',$like);
						$this->search($identifier, $like, $options);
					}
					else{
						switch($options['engine']){
							case 0:
							//la phrase exact
							$search = $this->protectForLike($like);
							$search = '%'.$search.'%';
							$this->where($this->like($identifier, $search));
							break;
				
							case 1:
							//tous les mots
							$search = explode(' ', $like);
							foreach($search as $word){
								if(strlen($word) > $options['minLength']){
									$word = $this->protectForLike($word);
									$word = trim($word);
									$this->where($this->like($identifier, '%'.$word.'%'));
								}	
							}
							break;
				
							case 2:
							//au moins un des mot
							$search = explode(' ', $like);
							foreach($search as $word){
								if(strlen($word) > $options['minLength']){
									$word = $this->protectForLike($word);
									$word = trim($word);
									$this->where($this->like($identifier, '%'.$word.'%'), 'OR');
								}	
							}
							break;
														
							case 3:
							//commencant par la phrase complete
							$search = $this->protectForLike($like);
							$search = '%'.$search;
							$this->where($this->like($identifier, $search));
							break;
							
							case 4:
							//Commencant par au moin un des mots suivant
							$search = explode(' ', $like);
							foreach($search as $word){
								if(strlen($word) > $options['minLength']){
									$word = $this->protectForLike($word);
									$word = trim($word);
									$this->where($this->like($identifier, '%'.$word), 'OR');
								}	
							}
							break;
							
							case 5:
							//commencant part tous les mots suivant
							$search = explode(' ', $like);
							foreach($search as $word){
								if(strlen($word) > $options['minLength']){
									$word = $this->protectForLike($word);
									$word = trim($word);
									$this->where($this->like($identifier, '%'.$word));
								}	
							}
							break;
							
							case 6:
							/**
							*	Commence par au moins une des expressions mais avec un espace avant
							*/
							$search = explode(' ', $like);
							foreach($search as $word){
								if(strlen($word) > $options['minLength']){
									$word = $this->protectForLike($word);
									$word = trim($word);
									$this->where($this->like($identifier, $word.'%'), 'AND');
								}	
							}
							break;
							
							case 7:
							/**
							*	Commence par au moins une des expressions mais avec un espace avant
							*/
							$search = explode(' ', $like);
							foreach($search as $word){
								if(strlen($word) > $options['minLength']){
									$word = $this->protectForLike($word);
									$word = trim($word);
									$this->where($this->expression($identifier.' REGEXP ?', '^[ ]{1}'.$word), 'AND');
								}	
							}
							break;
							
							case 8:
							/**
							*	Commence par au moins une des expressions mais avec un espace avant
							*/
							$search = explode(' ', $like);
							$key	=	count($search) - 1;
							$word	=	'';
							foreach($search as $k => $v){
								if($k == $key){
									$word .= '+'.$v.'* ';
								}
								else{
									$word .= '+'.$v.' ';
								}
							}

							$this	->select(array('PERTINENCE_'.$identifier => $this->expression('MATCH('.$identifier.') AGAINST(? IN BOOLEAN MODE)', array($word))))
									->where($this->parenthese($this->expression('MATCH('.$identifier.') AGAINST(? IN BOOLEAN MODE)', array($word))), 'OR')
									->order('PERTINENCE_'.$identifier.' DESC');

							break;
							
							case 9:
							//tous les mots
							$search = explode(' ', $like);
							$predicate	=	array();
							foreach($search as $word){
								if(strlen($word) > $options['minLength']){
									$word = $this->protectForLike($word);
									$word = trim($word);
									array_push($predicate, $this->like($identifier, '%'.$word.'%'));
								}	
							}

							$this->where($this->parenthese($predicate, 'AND'),'OR');
							break;
							
							case 10:
							//la phrase exact
							$search = $this->protectForLike($like);
							$search = $search.'%';
							$this->where($this->like($identifier, $search), 'OR');
							break;
						}
					}
				}
				else if(is_array($like)){
					foreach($like as $val){
						$this->search($identifier, $val, $options);
					}
				}
				else{
					$this->search($this->getStructure(), $identifier, $options);
				}
			}
		}
		else if(is_array($identifier)){
			foreach($identifier as $key => $val){
				if(is_int($key)){
					$this->search($val, $like, $options);
				}
				else{
					if(preg_match('/,/',$key) > 0){
						$key = explode(',', $key);
						$this->search($key, $val, $options);
					}
					else{
						$this->search($key, $val, $options);
					}
				}
			}
		}
		else if(is_object($identifier)){
		}
		return $this;			
	}
	public function render(){
		$factories = $this->renderFactory($this->currentFactoryRequest);
		foreach($factories as $factory){
			return new Expression($this->getSqlObject()->getSqlStringForSqlObject($factory['sqlObject']), $factory[self::VALUES]);
		}
	}
	
	
	public function dumpSql(){
		$this->clear();
		$factories	=	$this->renderFactory();
		foreach($factories as $factory){
			var_dump($factory[self::VALUES]);
			var_dump($this->getSqlObject()->getSqlStringForSqlObject($factory['sqlObject']));	
		}
		return $this;
	}
	public function preSql(){
		$this->clear();
		$factories	=	$this->renderFactory();
		foreach($factories as $factory){
			echo('<pre>'.print_r($factory[self::VALUES], true).'</pre>');
			echo('<pre>'.print_r($this->getSqlObject()->getSqlStringForSqlObject($factory['sqlObject']), true).'</pre>');	
		}
		return $this;
	}
	
	public function distinct($expression){
		$this->setFactoryValues(self::DISTINCT, 'DISTINCT('.$expression.'),');
		return $this;
	}
		
	/**
	*	DOCTRINE
	*/
	public function setDoctrine($doctrine){
		if($doctrine !== NULL){
			$this->doctrine = $doctrine;
		}
		return $this;
	}
	public function getDoctrine(){
		if($this->doctrine === NULL){
			$this->setDoctrine($this->getController()->doctrinePlugin());
		}
		return $this->doctrine;
	}
	public function with(){
	}
}