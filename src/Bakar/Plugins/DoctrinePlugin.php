<?php
namespace Bakar\Plugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

	
class DoctrinePlugin extends AbstractPlugin{
	protected $serviceLocator;
	protected $entityManager;
	protected $hydrator;
	protected $repository;
	protected $repositories	=	array();
	protected $pathEntities	=	array();
	protected $paginator;
	protected $pagination;
	protected $pathEntity;
		
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
	public function setEntityManager($entityManager = NULL){
		if($entityManager !== NULL){
			$this->entityManager = $entityManager;
		}
		return $this;
	}
	public function getEntityManager(){
		if($this->entityManager === NULL){
			$this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
		}
		return $this->entityManager;
	}	
	public function setRepository($repository = NULL, $name = NULL){
		if($repository !== NULL){
			$this->repository = $this->getEntityManager()->getRepository($repository);
			if($name !== NULL){
				$this->repositories[$name] 	=	$this->repository;
				$this->setPathEntity($repository, $name);
			}
		}
		
		return $this;
	}
	public function getRepository($name = NULL){
		$return  = $this->repository;
		if($name !== NULL AND array_key_exists($name, $this->repositories)){
			$return = $this->repositories[$name];
		}
		
		return $return;
	}
	public function setPathEntity($pathEntity = NULL, $name = NULL){
		if($pathEntity !== NULL){
			$this->pathEntity = $pathEntity;
			if($name !== NULL){
				$this->pathEntities[$name] = $pathEntity;
			}
		}
		return $this;
	}
	public function getPathEntity($name = NULL){
		$return = $this->pathEntity;
		if($name !== NULL AND array_key_exists($name, $this->pathEntities)){
			$return = $this->pathEntities[$name];
		}
		return $return;
	}
	
	public function setPaginator($paginator = NULL){
		if($paginator !== NULL){
			$this->paginator = $paginator;
		}
		return $this;
	}
	public function getPaginator(){
		if($this->paginator === NULL){
			$this->setPaginator($this->getController()->paginatorORMPlugin());
		}
		return $this->paginator;
	}
	
	public function pagination($query = NULL){
		$return  = NULL;
		if($query !== NULL){
			$this	->getPaginator()
					->setQuery($query);
					
			$this->setPagination($this->getPaginator());
		}
		return $return;
	}
	public function setPagination($pagination = NULL){
		if($pagination !== NULL){
			$this->pagination = $pagination;
		}
		return $this;
	}
	public function getPagination($query = NULL){
		if($query !== NULL){
			$this->pagination($query);
		}
		return $this->pagination;
	}
	public function setHydrator($hydrator = NULL){
		if($hydrator !== NULL){
			$this->hydrator = $hydrator;
		}
		return $this;
	}
	public function getHydrator($name = NULL){
		$entity	=	$this->getPathEntity($name);
		
		if($this->hydrator === NULL){
			$this->setHydrator(new DoctrineHydrator($this->getEntityManager(), $entity));
		}
		return $this->hydrator;
	}	
}