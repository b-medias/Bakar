<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Event;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;

abstract class AbstractEvent implements EventManagerAwareInterface{
	private $serviceManager;
	private $eventManager;
	private $event;
	
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
			$this->serviceManager 	=	$serviceManager;
		}
		return $this;
	}
	public function getServiceManager(){
		return	$this->serviceManager;
	}
	
	public function setEventManager(EventManagerInterface $eventManager){
		if($eventManager !== NULL){
			$eventManager->addIdentifiers(array(
				get_called_class()
			));
			
			$this->eventManager	=	$eventManager;
		}
		return $this;
	}
	public function getEventManager(){
		if($this->eventManager === NULL){
			$this->setEventManager(new EventManager());
		}
		return $this->eventManager;
	}
	
	public function setEvent($event = NULL){
		if($event !== NULL){
			$this->event	=	$event;
		}
		return $this;
	}
	public function getEvent(){
		return	$this->event;
	}
	
	public function create($event = NULL, $target = NULL, $argv = NULL, $callback = NULL){
		if($event == NULL){
			$event	=	__FUNCTION__;
		}
		if($target == NULL){
			$target	=	$this;
		}
		if($argv == NULL){
			$argv = array();
		}
		
		$e	=	new Event($event, $target, $argv, $callback);
		
		$this->setEvent($e);
		
		return $this->getEvent();
	}
	public function trigger($event = NULL, $target = NULL, $argv = NULL, $callback = NULL){
		$this->create($event, $target, $argv, $callback);

		$this	->getEventManager()
				->trigger($this->getEvent());
		
		return $this;
	}
}