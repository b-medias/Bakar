<?php 
namespace Bakar\Filter;	

class StreetNumber extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setLabel($this->getLabel())
								->setInputName($this->getInputName())
								->setStringLengthConfig(array(
									'min'	=>	1,
									'max'	=>	50,
								))
								->getValidatorChain()
								->addValidator($this->getValidator()->getNotEmptyValidator())
								->addValidator($this->getValidator()->getStringLengthValidator());
		
		$filter		=	$this	->getFilterChain()
								->attach($this->getStripTagsFilter());
						
		return	$this	->getInput()
						->setFilterChain($filter)
						->setValidatorChain($validator);	
	}
}