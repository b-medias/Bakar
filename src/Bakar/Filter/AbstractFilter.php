<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Filter;

use Bakar\Validator\Validator;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\Filter\FilterChain;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\Filter\StripTags;
use Zend\I18n\Filter\Alnum;
use Zend\Filter\Digits;
use Zend\I18n\Filter\NumberFormat;

use NumberFormatter;

abstract class AbstractFilter{
	private $alnumFilter;
	private $stripTagsFilter;
	private $stripNewLinesFilter;
	private $stringTrimFilter;
	private	$digitsFilter;
	private $numberFormatFilter;
	private $filterChain;
	private $inputFilter;
	private $fileInput;
	private $input;
	private $inputName;
	private $label;
	private $filters;
	private	$validator;
	private $form;
	private $value;
	private $service;
	
	public function setService($service = NULL){
		if($service !== NULL){
			$this->service	=	$service;
		}
		return $this;
	}
	public function getService(){
		return	$this->service;
	}
	
	public function setValidator($validator = NULL){
		if($validator !== NULL){
			$this->validator = $validator;
		}
		return $this;
	}
	public function getValidator(){
		if($this->validator === NULL){
			$this->setValidator(new Validator());
		}
		return $this->validator;
	}
	
	public function setInputFilter($inputFilter = NULL){
		if($inputFilter !== NULL){
			$this->inputFilter = $inputFilter;
		}
		return $this;
	}
	public function getInputFilter(){
		if($this->inputFilter === NULL){
			$this->setInputFilter(new InputFilter());
		}
		return $this->inputFilter;
	}
		
	public function setFilters($filters = NULL){
		if($filters !== NULL){
			$this->filters = $filters;
		}
		return $this;
	}
	public function getFilters(){
		if($this->filters === NULL){
			$this->setFilters($this->generateFilters());
		}
		return $this->filters;
	}
	public function generateFilters(){}
				
	public function setLabel($label = NULL){
		if($label !== NULL){
			$this->label = $label;
		}
		return $this;
	}
	public function getLabel(){
		if($this->label === NULL){
			$this->setLabel($this->getForm()->get($this->getInputName())->getLabel());
		}
		return $this->label;
	}
	
	public function setValue($value = NULL){
		if($value !== NULL){
			$this->value	=	$value;
		}
		return $this;
	}
	public function getValue(){
		if($this->value === NULL){
			$this->setValue($this->getForm()->get($this->getInputName())->getValue());
		}
		return $this->value;
	}
	
	public function setInput($input = NULL){
		if($input !== NULL){
			$this->input = $input;
		}
		return $this;
	}
	public function getInput(){
		if($this->input === NULL){
			$this->setInput(new Input($this->getInputName()));
		}
		return $this->input;
	}
	
	public function setFileInput($fileInput = NULL){
		if($fileInput !== NULL){
			$this->fileInput = $fileInput;
		}
		return $this;
	}
	public function getFileInput(){
		if($this->fileInput === NULL){
			$this->setFileInput(new FileInput($this->getInputName()));
		}
		return $this->fileInput;
	}
	
	public function setForm($form = NULL){
		if($form !== NULL){
			$this->form	=	$form;
		}
		return $this;
	}
	public function getForm(){
		return $this->form;
	}
	
	
	/**
	*	Filters
	*/
	public function setFilterChain($filterChain = NULL){
		if($filterChain !== NULL){
			$this->filterChain = $filterChain;
		}
		return $this;
	}
	public function getFilterChain(){
		if($this->filterChain === NULL){
			$this->setFilterChain(new FilterChain());
		}
		return $this->filterChain;
	}
	
	public function setAlnumFilter($alnumFilter = NULL){
		if($alnumFilter !== NULL){
			$this->alnumFilter = $alnumFilter;
		}
		return $this;
	}
	public function getAlnumFilter(){
		if($this->alnumFilter === NULL){
			$this->setAlnumFilter(new Alnum());
		}
		return $this->alnumFilter;
	}
	
	public function setStripTagsFilter($stripTagsFilter = NULL){
		if($stripTagsFilter !== NULL){
			$this->stripTagsFilter = $stripTagsFilter;
		}
		return $this;
	}
	public function getStripTagsFilter(){
		if($this->stripTagsFilter === NULL){
			$this->setStripTagsFilter(new StripTags());
		}
		return $this->stripTagsFilter;
	}
	
	public function setStripNewLinesFilter($stripNewLinesFilter = NULL){
		if($stripNewLinesFilter !== NULL){
			$this->stripNewLinesFilter = $stripNewLinesFilter;
		}
		return $this;
	}
	public function getStripNewLinesFilter(){
		if($this->stripNewLinesFilter === NULL){
			$this->setStripNewLinesFilter(new StripNewlines());
		}
		return $this->stripNewLinesFilter;
	}
	
	public function setStringTrimFilter($stringTrimFilter = NULL){
		if($stringTrimFilter !== NULL){
			$this->stringTrimFilter = $stringTrimFilter;
		}
		return $this;
	}
	public function getStringTrimFilter(){
		if($this->stringTrimFilter === NULL){
			$this->setStringTrimFilter(new StringTrim());
		}
		return $this->stringTrimFilter;
	}
	
	public function setDigitsFilter($digitsFilter	= NULL){
		if($digitsFilter !== NULL){
			$this->digitsFilter	=	$digitsFilter;
		}
		return $this;
	}
	public function getDigitsFilter(){
		if($this->digitsFilter === NULL){
			$this->setDigitsFilter(new Digits());
		}
		return $this->digitsFilter;
	}
	
	public function setNumberFormatFilter($numberFormatFilter = NULL){
		if($numberFormatFilter !== NULL){
			$this->numberFormatFilter	=	$numberFormatFilter;
		}
		return $this;
	}
	public function getNumberFormatFilter(){
		if($this->numberFormatFilter === NULL){
			$this->setNumberFormatFilter(new NumberFormat());
		}
		return $this->numberFormatFilter;
	}
		
	public function setInputName($inputName = NULL){
		if($inputName !== NULL){
			$this->inputName = $inputName;
		}
		return $this;
	}
	public function getInputName(){
		if($this->inputName === NULL){
			$this->setInputName('');
		}
		return $this->inputName;
	}
}