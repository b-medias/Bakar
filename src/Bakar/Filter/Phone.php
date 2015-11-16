<?php 
namespace Bakar\Filter;	

class Phone extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setLabel($this->getLabel())
								->setInputName($this->getInputName())
								->getValidatorChain()
								->addValidator($this->getValidator()->getPhoneValidator());
		
		$filter		=	$this	->getFilterChain();
						
		return	$this	->getInput()
						->setFilterChain($filter)
						->setValidatorChain($validator);	
	}
}