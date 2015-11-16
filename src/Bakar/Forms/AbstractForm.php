<?php
/**
* Bakar (http://www.bakar.be)
*
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Forms;

use Zend\Form\Form;
use Zend\Form\Element\Button;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Collection;
use Zend\Form\Element\Color;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Date;
use Zend\Form\Element\DateSelect;
use Zend\Form\Element\DateTime;
use Zend\Form\Element\DateTimeLocal;
use Zend\Form\Element\DateTimeSelect;
use Zend\Form\Element\Email;
use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Image;
use Zend\Form\Element\Month;
use Zend\Form\Element\MonthSelect;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Number;
use Zend\Form\Element\Password;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Range;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Time;
use Zend\Form\Element\Url;
use Zend\Form\Element\Week;

use ArrayObject;

abstract class AbstractForm{
	protected $config;
	protected $form;
	protected $arrayObject;
	
	public function test(){
		$this->debug('Hello world from '.get_called_class().' -> '.__FUNCTION__, true);
	}
	
	public function debug($data, $exit = TRUE, $js = FALSE){
		echo	$js	?	'<script type="text/javascript">console.log('.print_r($data, true).');</script>'	:
						'<pre>'.print_r($data, true).'</pre>';
		
		if($exit){exit;}
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
	
	public function setConfig($config = NULL){
		if($config !== NULL){
			$this->config = $config;
		}
		return $this;
	}
	public function getConfig($key = NULL, $ArrayObject = TRUE){
		$return	=	NULL;
		

		if($this->config !== NULL){
			$return	=	$this->config;
			
			if($key !== NULL && $this->config->offsetExists($key)){
				$return	=	$this->config->offsetGet($key);
			}
			
			if($ArrayObject && is_array($return)){
				$return	=	$this->getArrayObject($return);
			}
			
			if(!$ArrayObject && is_object($return)){
				$return	=	$return->getArrayCopy();
			}
		}
		
		return $return;
	}
	
	public function get($type, $name){
		switch($type){
			case 'button':
			return	$this->getButton($name);
			break;
			
			case 'captcha':
			return	$this->getCaptcha($name);
			break;
			
			case 'checkbox':
			return	$this->getCheckbox($name);
			break;
			
			case 'collection':
			return	$this->getCollection($name);
			break;
			
			case 'color':
			return	$this->getColor($name);
			break;
			
			case 'csrf':
			return	$this->getCsrf($name);
			break;
			
			case 'date':
			return	$this->getDate($name);
			break;
			
			case 'dateSelect':
			return	$this->getDateSelect($name);
			break;
			
			case 'dateTime':
			return	$this->getDateTime($name);
			break;
			
			case 'dateTimeLocal':
			return	$this->getDateTimeLocal($name);
			break;
			
			case 'dateTimeSelect':
			return	$this->getDateTimeSelect($name);
			break;
			
			case 'email':
			return	$this->getEmail($name);
			break;
			
			case 'file':
			return	$this->getFile($name);
			break;
			
			case 'hidden':
			return	$this->getHidden($name);
			break;
			
			case 'image':
			return	$this->getImage($name);
			break;
			
			case 'month':
			return	$this->getMonth($name);
			break;
			
			case 'monthSelect':
			return	$this->getMonthSelect($name);
			break;
			
			case 'multiCheckbox':
			return	$this->getMultiCheckbox($name);
			break;
			
			case 'number':
			return	$this->getNumber($name);
			break;
			
			case 'password':
			return	$this->getPassword($name);
			break;
			
			case 'radio':
			return	$this->getRadio($name);
			break;
			
			case 'range':
			return	$this->getRange($name);
			break;
			
			case 'select':
			return	$this->getSelect($name);
			break;
			
			case 'submit':
			return	$this->getSubmit($name);
			break;
			
			case 'text':
			return	$this->getText($name);
			break;
			
			case 'textarea':
			return	$this->getTextarea($name);
			break;
			
			case 'time':
			return	$this->getTime($name);
			break;
			
			case 'url':
			return	$this->getUrl($name);
			break;
			
			case 'week':
			return	$this->getWeek($name);
			break;
			
			case 'tel':
			return	$this	->getText($name)
							->setAttribute('type', 'tel');
			break;			
		}
	}
	public function getZfForm($name){
		return new Form($name);
	}
	public function getZendForm($name){
		return	$this->getZfForm($name);
	}
	
	public function setForm($form = NULL){
		if($form !== NULL){
			$this->form = $form;
		}
		return $this;
	}
	public function getForm(){
		if($this->form === NULL){
			$this->setForm($this->generateForm());
		}
		return $this->form;
	}
	public function generateForm(){
		return	 new Form();
	}
	
	public function getButton($name){
		return new Button($name);
	}
	public function getCaptcha($name){
		return new Captcha($name);
	}
	public function getCheckbox($name){
		return new Checkbox($name);
	}
	public function getCollection($name){
		return new Collection($name);
	}
	public function getColor($name){
		return new Color($name);
	}
	public function getCsrf($name){
		return new Csrf($name);
	}
	public function getDate($name){
		return new Date($name);
	}
	public function getDateSelect($name){
		return new DateSelect($name);
	}
	public function getDateTime($name){
		return new DateTime($name);
	}
	public function getDateTimeLocal($name){
		return new DateTimeLocal($name);
	}
	public function getDateTimeSelect($name){
		return new DateTimeSelect($name);
	}
	public function getEmail($name){
		return new Email($name);
	}
	public function getFile($name){
		return new File($name);
	}
	public function getHidden($name){
		return new Hidden($name);
	}
	public function getImage($name){
		return new Image($name);
	}
	public function getMonth($name){
		return new Month($name);
	}
	public function getMonthSelect($name){
		return new MonthSelect($name);
	}
	public function getMultiCheckbox($name){
		return new MultiCheckbox($name);
	}
	public function getNumber($name){
		return new Number($name);
	}
	public function getPassword($name){
		return new Password($name);
	}
	public function getRadio($name){
		return new Radio($name);
	}
	public function getRange($name){
		return new Range($name);
	}
	public function getSelect($name){
		return new Select($name);
	}
	public function getSubmit($name){
		return new Submit($name);
	}
	public function getText($name){
		return new Text($name);
	}
	public function getTextarea($name){
		return new Textarea($name);
	}
	public function getTime($name){
		return new Time($name);
	}
	public function getUrl($name){
		return new Url($name);
	}
	public function getWeek($name){
		return new Week($name);
	}

	public function getFormInput($name){
		return new Form($name);
	}
}