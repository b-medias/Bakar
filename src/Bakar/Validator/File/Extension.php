<?php
namespace Bakar\Validator\File;
use Zend\Validator\File\Extension as ZendExtension;

class Extension extends ZendExtension{
	protected $fileExtention;
	
	protected $messageVariables	=	array(
		'extension' => array('options' => 'extension'),
		'fileExtension' => 'fileExtension',
	);
	
    public function isValid($value, $file = null){
		if(is_string($value) && is_array($file)){
            $filename = $file['name'];
            $file     = $file['tmp_name'];
        } 
		elseif(is_array($value)){
        	if(!isset($value['tmp_name']) || !isset($value['name'])) {
            	throw new Exception\InvalidArgumentException(
                    'Value array must be in $_FILES format'
                );
            }
            $file     = $value['tmp_name'];
            $filename = $value['name'];
        } 
		else{
            $file     = $value;
            $filename = basename($file);
        }
        
		$this->setValue($filename);

        if(false === stream_resolve_include_path($file)){
            $this->error(self::NOT_FOUND);
            return false;
        }

        $extension  = substr($filename, strrpos($filename, '.') + 1);
        $extensions = $this->getExtension();
		
		$this->fileExtension	=	$extension;
		$this->messageVariables['fileExtension'] = 'fileExtension';
		
        if($this->getCase() && (in_array($extension, $extensions))){
            return true;
        } 
		elseif(!$this->getCase()){
        	foreach($extensions as $ext){
                if (strtolower($ext) == strtolower($extension)) {
                    return true;
                }
            }
        }

        $this->error(self::FALSE_EXTENSION);
        return false;
    }
}
