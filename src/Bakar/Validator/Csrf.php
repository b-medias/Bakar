<?php
namespace Bakar\Validator;

class Csrf extends AbstractValidator{
	
	public function generateValidator(){
		$csrf	=	new ZendCsrf();
		$csrf	->setMessages(array(
			ZendCsrf::NOT_SAME	=>	$this->getErrorMessage('NOT_SAME'),
		));
		
		$validatorChain	=	new ValidatorChain();
		$validatorChain	->addValidator($csrf);
						
		return $validatorChain;
	}
}