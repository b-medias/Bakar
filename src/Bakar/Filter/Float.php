<?php 
namespace Bakar\Filter;	

class Float extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setLabel($this->getLabel())
								->setInputName($this->getInputName())
								->getValidatorChain()
								->addValidator($this->getValidator()->getFloatValidator());
		
		$filter		=	$this	->getFilterChain()
								->attach($this->getStringTrimFilter())
								->attach($this->getStripNewLinesFilter())
								->attach($this->getStripTagsFilter())
								->attach($this->getNumberFormatFilter());
						
		return	$this	->getInput()
						->setFilterChain($filter)
						->setValidatorChain($validator);	
	}
}