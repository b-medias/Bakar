<?php 
	namespace Bakar\Filter;
	use Bakar\Validator\Avatar as Validator;

	use Zend\Filter\File\Rename;
	
	class Avatar extends AbstractFilter{	
		public function generateFilter(){					
			$fileRename	=	new Rename(array(
								'target'			=>	'.\tmp\avatars\avatar',
								'randomize'			=>	TRUE,
								'use_upload_name'	=>	TRUE,
							));
			
			$this	->getFilterChain()
					->attach($fileRename);
			
			
			$this	->getFileInput()
					->setAllowEmpty(TRUE)
					->setFilterChain($this->getFilterChain())
					->setValidatorChain($this->getValidator());
			
			return 	$this->getFileInput();			
		}
		public function generateValidator(){
			$validator	=	new Validator();
			$validator->setLabel($this->getLabel());
			$validator->setInputName($this->getInputName());
			$validator	=	$validator->getValidator();
			return $validator;
		}
	}