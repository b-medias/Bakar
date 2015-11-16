<?php 
namespace Bakar\Filter;	

class Login extends AbstractFilter{
	public function generateFilters(){
		$validator	=	$this	->getValidator()
								->setLabel($this->getLabel())
								->setInputName($this->getInputName())
								->setStringLengthConfig(array(
									'min'	=>	1,
									'max'	=>	100,
								))
								->getValidatorChain()
								->addValidator($this->getValidator()->getNotEmptyValidator())
								->addValidator($this->getValidator()->getStringLengthValidator());
		
		$filter		=	$this	->getFilterChain()
								->attach($this->getStringTrimFilter())
								->attach($this->getStripNewLinesFilter())
								->attach($this->getStripTagsFilter());
						
		return	$this	->getInput()
						->setFilterChain($filter)
						->setValidatorChain($validator);	
	}
}