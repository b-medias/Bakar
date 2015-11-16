<?php
/**
*	Paginator Adapter
*	Version 0.5
*/
namespace Bakar\Plugins;

use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Paginator\Adapter\ArrayAdapter as ArrayPaginator;
use Zend\Paginator\Paginator;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;

class PaginatorPlugin extends AbstractPlugin implements AdapterInterface{
	protected $paginator		=	NULL;
	protected $length			=	NULL;
	protected $rowCount			=	NULL;
	protected $result			=	NULL;
	protected $factory			=	NULL;
	protected $adapter			=	NULL;
	protected $sqlObject		=	NULL;
	protected $resultSet		=	NULL;
	protected $defaultFactory	=	array(
		'page'				=>	NULL,
		'itemsPerPage'		=>	12,
		'pageRange'			=>	10,
		'template'			=>	'paginator',
		'scrollingStyle'	=>	'Sliding',
		'styles'			=>	array('All', 'Elastic', 'Jumping', 'Sliding'),
		'requestObject'		=>	NULL,
		'values'			=>	NULL,
		'identifiantRequest'=>	'p',
	);
	
	public function setFactory($factory = NULL){
		$this->result = NULL;
		if(!is_array($factory)){
			$factory = array($factory);
		}
		$this->factory = array_merge($this->defaultFactory, $factory);
	}
	public function getFactory(){
		if($this->factory === NULL){
			$this->setFactory($this->defaultFactory);
		}
		return $this->factory;
	}
	public function setPage($page = NULL){
		if($page != NULL AND $page >= 0){
			$this->factory['page'] = $page;
		}
		return $this;
	}
	public function getPage(){
		if($this->factory['page'] === NULL){
			$page = $this->getController()->params()->fromRoute($this->getIdentifiantRequest());
			$page == NULL ? $this->setPage(1) : $this->setPage($page);
		}
		return $this->factory['page'];
	}
	public function setIdentifiantRequest($identifiantRequest = NULL){
		if($identifiantRequest !== NULL){
			$this->factory['identifiantRequest'] = $identifiantRequest;
		}
		return $this;
	}
	public function getIdentifiantRequest(){
		if($this->factory['identifiantRequest'] === NULL){
			$this->setIdentifiantRequest($this->defaultFactory['identifiantRequest']);
		}
		return $this->factory['identifiantRequest'];
	}
	public function setPageRange($pageRange = NULL){
		if($pageRange !== NULL){
			$this->factory['pageRange'] = $pageRange;
		}
		return $this;
	}
	public function getPageRange(){
		if($this->factory['pageRange'] === NULL){
			$this->setPageRange($this->defaultFactory['pageRange']);
		}
		return $this->factory['pageRange'];
	}
	public function setItemsPerPage($itemsPerPage = NULL){
		if($itemsPerPage !== NULL){
			$this->factory['itemsPerPage'] = $itemsPerPage;
		}
		return $this;
	}
	public function getItemsPerPage(){
		if($this->factory['itemsPerPage'] === NULL){
			$this->setItemsPerPage($this->defaultFactory['itemsPerPage']);
		}
		return $this->factory['itemsPerPage'];
	}
	public function setTemplate($template = NULL){
		if($template !== NULL){
			$this->factory['template'] = $template;
		}
		return $this;
	}
	public function getTemplate(){
		if($this->factory['template'] === NULL){
			$this->setTemplate($this->defaultFactory['template']);
		}
		return $this->factory['template'];
	}
	public function setScrollingStyle($scrollingStyle = NULL){
		if($scrollingStyle !== NULL AND array_key_exists($scrollingStyle, $this->defaultFactory['styles'])){
			$this->factory['scrollingStyle'] = $scrollingStyle;
		}
		return $this;
	}
	public function getScrollingStyle(){
		if($this->factory['scrollingStyle'] === NULL){
			$this->setScrollingStyle($this->defaultFactory['scrollingStyle']);
		}
		return $this->factory['scrollingStyle'];
	}
	public function setValues($values = NULL){
		if($values !== NULL){
			$this->factory['values'] = $values;
		}
		return $this;
	}
	public function getValues(){
		return $this->factory['values'];
	}
	
	public function setRequestObject($requestObject = NULL){
		if($requestObject !== NULL){
			$this->factory['requestObject'] = $requestObject;
		}
	}
	public function getRequestObject(){
		return $this->factory['requestObject'];
	}
	public function getSqlObject(){
		if($this->sqlObject === NULL){
			$this->setSqlObject(new Sql($this->getAdapter()));
		}
		return $this->sqlObject;
	}
	public function setSqlObject($sqlObject = NULL){
		if($sqlObject !== NULL){
			$this->sqlObject = $sqlObject;
		}
		return $this;
	}
	public function setAdapter($adapter = NULL){
		if($adapter !== NULL){
			$this->adapter	=	$adapter;
		}
		return $this;
	}
	public function getAdapter(){
		if($this->adapter === NULL){
			$this->setAdapter($this->getController()->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->adapter;
	}
	public function setResultSet($resultSet = NULL){
		if($resultSet !== NULL){
			$this->resultSet = $resultSet;
		}
		return $this;
	}
	public function getResultSet(){
		if($this->resultSet === NULL){
			$this->setResultSet(new ResultSet);
		}
		return $this->resultSet;
	}
				
	public function setPaginator($paginator = NULL){
		if($paginator !== NULL){
			$this->paginator = $paginator;
		}
		return $this;
	}
	public function getPaginator($render = TRUE){
		if($this->paginator === NULL){
			$this->setPaginator(new Paginator($this));
		}
		
		$this->paginator->setCurrentPageNumber($this->getPage());
		$this->paginator->setPageRange($this->getPageRange());
		$this->paginator->setItemCountPerPage($this->getItemsPerPage());
		$render === TRUE ? $return = $this->render() : $return = $this->paginator;
		return $return;
	}
	
	private function render(){
		$params[$this->getIdentifiantRequest()]	=	$this->getPage();
		$params['params']						=	$this->getController()->params()->fromRoute();
		$params['route']						=	$this->getController()->getEvent()->getRouteMatch()->getMatchedRouteName();
		$p 										= 	$this->getController()->getServiceLocator()->get('viewrenderer');
		$p 										= 	$p->paginationControl($this->paginator, $this->getScrollingStyle(), $this->getTemplate(), $params);
		return $p;
	}
	
	public function length(){
		$return = $this->getLength();
		if($return === NULL){
			$return = 0;
		}
		return $return;
	}
	public function setLength($length = NULL){
		if($length !== NULL){
			$this->length = $length;
		}
		return $this;
	}
	public function getLength(){
		if($this->length === NULL){
			$this->setLength(count($this->getItems()));
		}
		return $this->length;
	}
	
	/**
	* Returns the total number of rows in the result set.
	*
	* @return int
	*/
	public function count(){
		if ($this->rowCount !== null) {
			return $this->rowCount;
		}
		$select 	= 	clone $this->getRequestObject();
		$columns	=	$select->getRawState(Select::COLUMNS);
		$joins 		=	$select->getRawState(Select::JOINS);
		$_columns	=	array();
		
		$select->reset(Select::JOINS);
		$select->reset(Select::COLUMNS);
		$select->reset(Select::LIMIT);
		$select->reset(Select::OFFSET);
		$select->reset(Select::ORDER);
		$select->reset(Select::GROUP);
		
		foreach($columns as $key => $column){
			if(is_object($column)){
				$_columns[$key]	=	$column;
			}
		}
		
		$_columns['all']	=	new Expression('COUNT(1)');		
		$select->columns($_columns);

		
		foreach ($joins as $join) {
			$select->join($join['name'], $join['on'], array(), $join['type']);
		}
		
		//$select->columns(array('all' => new Expression('COUNT(1)')));
		

		//echo($this->getSqlObject()->getSqlStringForSqlObject($select));
		
		$statement = $this->getSqlObject()->prepareStatementForSqlObject($select);
		$result    = $statement->execute($this->getValues());
		$row       = $result->current();
		

		$this->rowCount = $row['all'];
				
		return $this->rowCount;
	}
		
	/**
	* Returns an array of items for a page.
	*
	* @param  int $offset           Page offset
	* @param  int $itemCountPerPage Number of items per page
	* @return array
	*/
	public function getItems($offset = NULL, $itemCountPerPage = NULL){
		if($this->result === NULL){
			if($offset === NULL){
				$offset = ($this->getPage()-1) * $this->getItemsPerPage();
			}
			if($itemCountPerPage === NULL){
				$itemCountPerPage = $this->getItemsPerPage();
			}
			$select = clone $this->getRequestObject();
			$select->offset($offset);
			$select->limit($itemCountPerPage);
	
			$statement = $this->getSqlObject()->prepareStatementForSqlObject($select);
			$result    = $statement->execute($this->getValues());
	
			$resultSet = clone $this->getResultSet();
			$resultSet->initialize($result);
			$this->result = $resultSet;
		}
		return $this->result;
	}
}