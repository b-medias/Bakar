<?php
namespace Bakar\Validator\File;
use Zend\Validator\File\Count as ZendCount;

class Count extends ZendCount{
	protected $inputName;
	protected $totalFiles;
	protected $plurielMax	=	'';
	protected $plurielFiles	=	'';
	
	protected $messageVariables = array(
        'min'   		=> 	array('options' => 'min'),
        'max'   		=> 	array('options' => 'max'),
        'count' 		=> 	'count',
    	'totalFiles'	=>	'totalFiles',
		'plurielMax'	=>	'plurielMax',
		'plurielFiles'	=>	'plurielFiles',
	);
		
	public function setInputName($inputName = NULL){
		if($inputName !== NULL){
			$this->inputName = $inputName;
		}
		return $this;
	}
	public function getInputName(){
		if($this->inputName === NULL){
			$this->setInputName('');
		}
		return $this->inputName;
	}
	
	public function isValid($value, $file = null){
		$this->getMax() > 1	?	$this->plurielMax	=	's'	:	$this->plurielMax	=	'';
		
		if($this->getInputName() !== '' && array_key_exists($this->getInputName(), $file)){
			$this->totalFiles	=	count($file[$this->getInputName()]);
			$this->totalFiles > 1 ? $this->plurielFiles	=	's'	:	$this->plurielFiles = '';
		}
			
		if(($file !== null) && !array_key_exists('destination', $file)){
			$file['destination'] = dirname($value['tmp_name']);
        }

        if(($file !== null) && array_key_exists('tmp_name', $value)){
            $value = $file['destination'] . DIRECTORY_SEPARATOR . $value['name'];
        }

         if (($file !== null) || !empty($file['tmp_name'])) {
       	 	$this->addFile($value);
    	}

        $this->count = count($this->files);
		
		if(($this->getMax() !== null) && ($this->count > $this->getMax())){
            return $this->throwError($file, self::TOO_MANY);
        }

        if(($this->getMin() !== null) && ($this->count < $this->getMin())){
            return $this->throwError($file, self::TOO_FEW);
        }

        return true;
    }
}
	
	