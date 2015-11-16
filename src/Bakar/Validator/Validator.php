<?php
	namespace Bakar\Validator;

	class Validator extends AbstractValidator{
		public function generateValidator(){		
			return $this->getValidatorChain();
		}
	}