<?php 
namespace Bakar\Filter;

use Bakar\Validator\Pictures as Validator;

use Zend\Filter\File\Rename;

class Pictures extends AbstractFilter{	
	public function generateFilter(){					
		$fileRename	=	new Rename(array(
							'target'			=>	'.\tmp\pictures',
							'randomize'			=>	TRUE,
							'use_upload_name'	=>	TRUE,
						));
						
		$this	->getFilterChain()
				->attach($fileRename);
		
		$this	->getFileInput()
				->setFilterChain($this->getFilterChain())
				->getValidatorChain()
				->attach($this->getValidator())
				->setArrowEmpty(TRUE);
		
		return $this->FileInput();	
	}
	public function generateValidator(){
		$validator	=	new Validator();
		$validator->setLabel($this->getLabel());
		$validator->setInputName($this->getInputName());
		$validator	=	$validator->getValidator();
		return $validator;
	}
}