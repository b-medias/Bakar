<?php 
namespace Bakar\Filter;

class Csrf extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setLabel($this->getLabel())
								->setInputName($this->getInputName())
								->getValidatorChain()
								->addValidator($this->getValidator()->getCsrfValidator());
		
		return	$this	->getInput()
						->setValidatorChain($validator);
	}
}