<?php 
namespace Bakar\Filter;

class Url extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setInputName($this->getInputName())
								->setLabel($this->getLabel())
								->getValidatorChain()
								->addValidator($this->getValidator()->getNotEmptyValidator())
								->addValidator($this->getValidator()->getUrlValidator());
								
		$filter		=	$this	->getFilterChain()
								->attach($this->getStringTrimFilter())
								->attach($this->getStripNewLinesFilter())
								->attach($this->getStripTagsFilter());
						
		return	$this	->getInput()
						->setFilterChain($filter)
						->setValidatorChain($validator);	
	}
}