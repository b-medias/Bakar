<?php 
namespace Bakar\Validator;
use Zend\Validator\AbstractValidator as ZendAbstractValidator;
	
class NoSpace extends ZendAbstractValidator{
	const SPACE		=	'space';
	
	protected $messageTemplates	=	array(
		self::SPACE		=>	"%value% contains space",
	);
	
	public function isValid($value){
		$this->setValue($value);
		if(strpos($value, " ") !== FALSE){
			$this->error(self::SPACE);
			return false;
		}
		
		return true;
	}
}