<?php
	namespace Bakar\Validator;
		
	abstract class AbstractDoctrineValidator extends Zend\Validator\AbstractValidator{
		/**
		* @var Doctrine\ORM\EntityManager
		*/
		private $entityManager;
		
		public function __construct(\Doctrine\ORM\EntityManager $entityManager){
			$this->entityManager = $entityManager;
		}
		public function getEntityManager(){
			return $this->entityManager;
		}
	
	}