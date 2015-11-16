<?php 
namespace Bakar\Filter;	

class Filter extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setLabel($this->getLabel())
								->setInputName($this->getInputName())
								->getValidatorChain();
		
		$filter		=	$this	->getFilterChain()
								->attach($this->getStringTrimFilter())
								->attach($this->getStripNewLinesFilter())
								->attach($this->getStripTagsFilter());
						
		return	$this	->getInput()
						->setFilterChain($filter)
						->setValidatorChain($validator);
	}
}