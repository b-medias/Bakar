<?php 
namespace Bakar\Filter;	

class PostCode extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setLabel($this->getLabel())
								->setInputName($this->getInputName())
								->getValidatorChain()
								->addValidator($this->getValidator()->getPostCodeValidator());
		
		$filter		=	$this	->getFilterChain()
								->attach($this->getStripTagsFilter());
						
		return	$this	->getInput()
						->setFilterChain($filter)
						->setValidatorChain($validator);	
	}
}