<?php
/**
* Bakar (http://www.bakar.be)
*
* @link         http://www.bakar.be
* @copyright    Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version 		3.0
*/
namespace	Bakar\Plugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ViewPlugin extends AbstractPlugin{
	private $view;
	private $vars		=	array();
	private $varsAjax	=	array();
	private $jsonModel;
	private $viewModel;
	private $request;
	private $template;
	private $ajaxTemplate;
	
	public function setLayout($layout){
		$this->getView()->layout	=	$layout;
		return $this;
	}
	
	public function template($lower = FALSE){
		$namespace	=	$this->getController()->getNameSpace();
		$controller	=	$this->getController()->params()->fromRoute('controller');
		$action		=	$this->getController()->params()->fromRoute('action');
		$controller	=	substr($controller, strrpos($controller, '\\')+1);
		
		return	$lower	===	TRUE	?	strtolower($namespace.'/'.$controller.'/'.$action)	:	$namespace.'/'.$controller.'/'.$action;
	}
	
	public function setView($view = NULL){
		if($view !== NULL){
			$this->view	=	$view;
		}
		return $this;
	}
	public function getView(){
		$this->setView($this->generateView());
		return	$this->view;
	}
	public function generateView(){
		$view	=	$this	->getViewModel()
							->setVariables($this->getVars());
		
		if($this->isAjax()){
			$view->setTemplate($this->getTemplate());
			$html	=	$this->render($view);
			
			if($this->getVarAjax('html') === NULL){
				$this->setVarAjax('html', $html);
			}
			
			$return	=	$this	->getJsonModel()
								->setVariables($this->getVarsAjax());
			
		}
		else{
			$return	=	$this->getTemplate() !== 'ajax'	?	$view->setTemplate($this->getTemplate())	:	$view->setTemplate($this->template(TRUE));
		}
		
		return $return;
	}
	
	
	public function setVars($vars, $replace = FALSE){
		$this->vars	=	$replace !== FALSE	?	$vars	:	array_merge($this->vars, $vars);
		return $this;
	}
	public function getVars(){
		return $this->vars;
	}
	
	public function setVar($key, $value, $replace = FALSE){
		$this->setVars(array($key => $value), $replace);
		return $this;
	}
	public function getVar($key = NULL){
		$value	=	NULL;
		
		if($key !== NULL && array_key_exists($key, $this->getVars())){
			$vars	=	$this->getVars();
			$value	=	$vars[$key];
		}
		
		return $value;
	}
	
	public function setVarsAjax($vars, $replace = FALSE){
		$this->varsAjax	=	$replace !== FALSE ? $vars	:	array_merge($this->varsAjax, $vars);
		return $this;
	}
	public function getVarsAjax(){
		return $this->varsAjax;
	}
	
	public function setVarAjax($key, $value, $replace = FALSE){
		$this->setVarsAjax(array($key => $value), $replace);
		return $this;
	}
	public function getVarAjax($key = NULL){
		$value	=	NULL;
		
		if($key !== NULL && array_key_exists($key, $this->getVarsAjax())){
			$vars	=	$this->getVarsAjax();
			$value	=	$vars[$key];
		}
		
		return $value;
	}
	
	public function setJsonModel($jsonModel = NULL){
		if($jsonModel !== NULL){
			$this->jsonModel	=	$jsonModel;
		}
		
		return $this;
	}
	public function getJsonModel(){
		if($this->jsonModel === NULL){
			$this->setJsonModel($this->getNewJsonModel());
		}
		
		return $this->jsonModel;
	}
	public function getNewJsonModel(){
		return new JsonModel();
	}
	
	public function setViewModel($viewModel = NULL){
		if($viewModel !== NULL){
			$this->viewModel	=	$viewModel;
		}
		return $this;
	}
	public function getViewModel(){
		if($this->viewModel === NULL){
			$this->setViewModel($this->getNewViewModel());
		}
		return $this->viewModel;
	}
	public function getNewViewModel(){
		return new ViewModel();
	}
	
	public function setRequest($request = NULL){
		if($request !== NULL){
			$this->request	=	$request;
		}
		return $this;
	}
	public function getRequest(){
		if($this->request === NULL){
			$this->setRequest($this->getController()->getRequest());
		}
		return $this->request;
	}
	
	public function render($view){
		return	$this	->getController()
						->getServiceLocator()
						->get('viewrenderer')
						->render($view);
	}
	
	public function isAjax(){
		return	$this->getRequest()->isXmlHttpRequest();
	}
	public function isNotAjax(){
		return !$this->isAjax();
	}
	
	public function isGet(){
		return $this->getRequest()->isGet();
	}
	public function isNotGet(){
		return !$this->isGet();
	}
	
	public function isPost(){
		return $this->getRequest()->isPost();
	}
	public function isNotPost(){
		return !$this->isPost();
	}

	public function setTemplate($template = NULL){
		if($template !== NULL){
			$this->template	=	$template;
		}
		return $this;
	}
	public function getTemplate(){
		if($this->template === NULL){
			$this->setTemplate($this->getAjaxTemplate());
		}
		return $this->template;
	}
	
	public function setAjaxTemplate($ajaxTemplate){
		if($ajaxTemplate !== NULL){
			$this->ajaxTemplate	=	$ajaxTemplate;
		}
		return $this;
	}
	public function getAjaxTemplate(){
		if($this->ajaxTemplate === NULL){
			$this->setAjaxTemplate('ajax');
		}
		return $this->ajaxTemplate;
	}
}
	