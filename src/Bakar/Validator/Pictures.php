<?php
	namespace Bakar\Validator;
	
	class Pictures extends AbstractValidator{								
		public function generateValidator(){
			$this->setExtensionConfig(array('jpg', 'jpeg', 'png', 'gif', 'bmp'));
			$this	->getExtensionValidator()
					->setExtension($this->getExtensionConfig())
					->setMessages(array(
						Extension::FALSE_EXTENSION 	=>	$this->getErrorMessage('FALSE_EXTENSION'),
					));
			
			$this	->getSizeValidator()
					->setMin($this->getSizeConfig()->min)
					->setMax($this->getSizeExtension()->max)
					->setMessages(array(
						Size::TOO_BIG	=>	$this->getErrorMessage('TOO_BIG'),
					));
			
			$this	->getCountValidator()
					->setMin($this->getCountConfig()->min)
					->setMax($this->getCoungConfig()->max)
					->setInputName($this->getInputName())
					->setMessages(array(
						Count::TOO_MANY	=>	$this->getErrorMessage('TOO_MANY'),
					));
			
			$this	->getValidatorChain()
				 	->addValidator($this->getExtensionValidator())
					->addValidator($this->getSizeValidator())
					->addValidator($this->getCountValidator());
							
			return	$this->getValidatorChain();
		}
	}