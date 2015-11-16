<?php
namespace Bakar\Validator;

class Text extends AbstractValidator{
	public function generateValidator(){		
		$this	->getValidatorChain()
				->addValidator($this->getNotEmptyValidator())
				->addValidator($this->getStringLengthValidator());
						
		return $this->getValidatorChain();
	}
}