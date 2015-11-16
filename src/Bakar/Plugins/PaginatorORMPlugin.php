<?php
	namespace Bakar\Plugins;
	
	use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
	use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
	
	use Zend\Paginator\Adapter\AdapterInterface;
	use Zend\Paginator\Adapter\Iterator as paginatorIterator;
	use Zend\Paginator\Adapter\ArrayAdapter as ArrayPaginator;
	use Zend\Paginator\Paginator;
	use Zend\Mvc\Controller\Plugin\AbstractPlugin;
	
	class PaginatorORMPlugin extends AbstractPlugin implements AdapterInterface{
	protected $paginator		=	NULL;
	protected $length			=	NULL;
	protected $rowCount			=	NULL;
	protected $result			=	NULL;
	protected $factory			=	NULL;
	protected $resultSet		=	NULL;
	protected $query			=	NULL;
	protected $ormPaginator		=	NULL;
	protected $doctrinePaginator=	NULL;
	protected $total			=	NULL;
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
		if(array_key_exists('pageRange', $this->factory) === FALSE){
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
		if(array_key_exists('itemsPerPage', $this->factory) === FALSE){
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
		if(array_key_exists('template', $this->factory) === FALSE){
			$this->setTemplate($this->defaultFactory['template']);
		}
		return $this->factory['template'];
	}
	public function setScrollingStyle($scrollingStyle = NULL){
		if($scrollingStyle !== NULL AND in_array($scrollingStyle, $this->defaultFactory['styles'])){
			$this->factory['scrollingStyle'] = $scrollingStyle;
		}
		return $this;
	}
	public function getScrollingStyle(){
		if(array_key_exists('scrollingStyle', $this->factory) === FALSE){
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
	
	public function setQuery($query = NULL){
		if($query !== NULL){
			$this->query = $query;
		}
		return $this;
	}
	public function getQuery(){
		return $this->query;
	}
	public function setDoctrinePaginator($doctrinePaginator = NULL){
		if($doctrinePaginator !== NULL){
			$this->doctrinePaginator = $doctrinePaginator;
		}
		return $this;
	}
	public function getDoctrinePaginator(){
		if($this->doctrinePaginator === NULL){
			$this->setDoctrinePaginator(new DoctrinePaginator($this->getORMPaginator()));
		}
		return $this->doctrinePaginator;
	}
	public function setORMPaginator($ormPaginator = NULL){
		if($ormPaginator !== NULL){
			$this->ormPaginator = $ormPaginator;
		}
		return $this;
	}
	public function getORMPaginator(){
		if($this->ormPaginator === NULL){
			$this->setORMPaginator(new ORMPaginator($this->getQuery()));
		}
		return $this->ormPaginator;
	}
	

	
	public function setResultSet($resultSet = NULL){
		if($resultSet !== NULL){
			$this->resultSet = $resultSet;
		}
		return $this;
	}
	public function setPaginator($paginator = NULL){
		if($paginator !== NULL){
			$this->paginator = $paginator;
		}
		return $this;
	}
	public function getPaginator($render = TRUE){
		if($this->paginator === NULL){
			$this->setPaginator(new Paginator($this->getDoctrinePaginator()));
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
	public function total(){
		return $this->getTotal();
	}
	public function setTotal($total = NULL){
		if($total !== NULL){
			$this->total = $total;
		}
		return $this;
	}
	public function getTotal(){
		if($this->total === NULL){
			$this->setTotal($this->count());
		}
		return $this->total;
	}
	
	/**
	* Returns the total number of rows in the result set.
	*
	* @return int
	*/
	public function count(){
		return $this->getDoctrinePaginator()->count();
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
			
			$this->result = $this->getDoctrinePaginator()->getItems($offset, $itemCountPerPage);
		}
		return $this->result;
	}
}