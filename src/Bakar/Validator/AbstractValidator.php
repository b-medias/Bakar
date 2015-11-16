<?php
namespace Bakar\Validator;

use	Bakar\Validator\File\Extension;
use Bakar\Validator\File\Count;
use Bakar\Validator\NoSpace;

use Zend\Validator\File\Size;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Csrf;
use Zend\Validator\ValidatorChain;
use Zend\Validator\EmailAddress;
use Zend\Validator\HostName;
use Zend\I18n\Validator\PhoneNumber;
use Zend\Validator\Db\AbstractDb;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\Identical;
use Zend\Authentication\Validator\Authentication;
use Zend\I18n\Validator\DateTime as ZendDateTime;
use Zend\Validator\Date as DateValidator;
use Zend\I18n\Validator\Float;
use Zend\I18n\Validator\PostCode;
use Zend\Validator\Uri;


use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
	
use ArrayObject ;

abstract class AbstractValidator {
	private	$validatorChain;
	private	$extensionValidator;
	private $countValidator;
	private $notEmptyValidator;
	private $stringLengthValidator;
	private $csrfValidator;
	private $emailValidator;
	private	$hostNameValidator;
	private $phoneValidator;
	private $recordExistsValidator;
	private $noRecordExistsValidator;
	private $noSpaceValidator;
	private $authenticationValidator;
	private	$dateValidator;
	private $floatValidator;
	private $identicalValidator;
	private $postCodeValidator;
	private $urlValidator;
	
	private $extensionConfig;
	private $countConfig;
	private $sizeConfig;
	private $notEmptyConfig;
	private $stringLengthConfig;
	private $csrfConfig;
	private $emailConfig;
	private	$hostNameConfig;
	private $phoneConfig;
	private $recordExistsConfig;
	private $noRecordExistsConfig;
	private	$noSpaceConfig;
	private $authenticationConfig;
	private	$dateConfig;
	private $floatConfig;
	private $identicalConfig;
	private $postCodeConfig;
	private $urlConfig;
	
	protected $validator;
	protected $label;
	protected $inputName;
	protected $adapter;
	protected $service;
	
	
	public function getErrorMessage($type){
		/*$return	=	sprintf($this->getService()->get('ERROR::LABEL', FALSE), $this->getLabel()).'<br />'.
					$this->getService()->get($type, FALSE);*/
		$return	=	'ERROR';
					
		return $return;
	}
	
	public function setValidator($validator = NULL){
		if($validator !== NULL){
			$this->validator = $validator;
		}
		return $this;
	}
	public function getValidator(){
		if($this->validator === NULL){
			$this->setValidator($this->generateValidator());
		}
		return $this->validator;
	}
	public function generateValidator(){}
	
	public function setLabel($label = NULL){
		if($label !== NULL){
			$this->label = $label;
		}
		return $this;
	}
	public function getLabel(){
		if($this->label === NULL){
			$this->setLabel('');
		}
		return $this->label;
	}
	
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
			
	public function setValidatorChain($validatorChain = NULL){
		if($validatorChain !== NULL){
			$this->validatorChain = $validatorChain;
		}
		return $this;
	}
	public function getValidatorChain(){
		if($this->validatorChain === NULL){
			$this->setValidatorChain(new ValidatorChain());
		}
		return $this->validatorChain;
	}
	
	public function setAdapter($adapter = NULL){
		if($adapter !== NULL){
			$this->adapter	=	$adapter;
		}
		return $this;
	}
	public function getAdapter(){
		return	$this->adapter;
	}
	
	public function setService($service = NULL){
		if($service !== NULL){
			$this->service	=	$service;
		}
		return $this;
	}
	public function getService(){
		return $this->service;
	}
	
	/**
	*	VALIDATORS
	*/
	public function setExtensionValidator($extensionValidator = NULL){
		if($extensionValidator !== NULL){
			$this->extensionValidator = $extensionValidator;
		}
		return $this;
	}
	public function getExtensionValidator(){
		if($this->extensionValidator === NULL){
			$validator	=	new Extension(array());
			$validator	->setOptions($this->getExtensionConfig())
						->setMessages(array(
							Extension::FALSE_EXTENSION 	=>	$this->getErrorMessage('FALSE_EXTENSION'),
						));
			
			$this->setExtensionValidator($validator);
		}
		return $this->extensionValidator;
	}												
	
	public function setCountValidator($countValidator = NULL){
		if($countValidator !== NULL){
			$this->countValidator = $countValidator;
		}
		return $this;
	}
	public function getCountValidator(){
		if($this->countValidator === NULL){
			$validator	=	new Count();
			$validator	->setInputName($this->getInputName())
						->setOptions($this->getCountConfig())
						->setMessages(array(
							Count::TOO_MANY	=>	$this->getErrorMessage('TOO_MANY'),
							Count::TOO_FEW	=>	$this->getErrorMessage('TOO_FEW'),
						));
			
			$this->setCountValidator($validator);
		}
		return $this->countValidator;
	}
	
	public function setSizeValidator($sizeValidator = NULL){
		if($sizeValidator !== NULL){
			$this->sizeValidator = $sizeValidator;
		}
		return $this;
	}
	public function getSizeValidator(){
		if($this->sizeValidator === NULL){
			$validator	=	new Size();
			$validator	->setOptions($this->getSizeConfig())
						->setMessages(array(
							Size::TOO_BIG	=>	$this->getErrorMessage('TOO_BIG'),
						));
			$this->setSizeValidator($validator);
		}
		return $this->sizeValidator;
	}
	
	public function setNotEmptyValidator($notEmptyValidator = NULL){
		if($notEmptyValidator !== NULL){
			$this->notEmptyValidator	=	$notEmptyValidator;
		}
		return $this;
	}
	public function getNotEmptyValidator(){
		if($this->notEmptyValidator === NULL){
			$validator	=	new NotEmpty();
			$validator	->setOptions($this->getNotEmptyConfig())
						->setMessages(array(
							NotEmpty::IS_EMPTY	=>	$this->getErrorMessage('NOTEMPTY::IS_EMPTY'),
							NotEmpty::INVALID	=>	$this->getErrorMessage('NOTEMPTY::INVALID'),
						));
			
			$this->setNotEmptyValidator($validator);
		}
		return $this->notEmptyValidator;
	}
	
	public function setStringLengthValidator($stringLengthValidator = NULL){
		if($stringLengthValidator !== NULL){
			$this->stringLengthValidator = $stringLengthValidator;
		}
		return $this;
	}
	public function getStringLengthValidator(){
		if($this->stringLengthValidator === NULL){
			$validator	=	new StringLength();
			$validator	->setOptions($this->getStringLengthConfig())
						->setMessages(array(
							StringLength::TOO_SHORT	=> 	$this->getErrorMessage('STRINGLENGTH::TOO_SHORT'),
							StringLength::TOO_LONG	=>	$this->getErrorMessage('STRINGLENGTH::TOO_LONG'),
							StringLength::INVALID	=>	$this->getErrorMessage('STRINGLENGTH::INVALID'),
						));
			
			$this->setStringLengthValidator($validator);
		}
		return $this->stringLengthValidator;
	}
	
	public function setCsrfValidator($csrfValidator = NULL){
		if($csrfValidator !== NULL){
			$this->csrfValidator = $csrfValidator;
		}
		return $this;
	}
	public function getCsrfValidator(){
		if($this->csrfValidator === NULL){
			$validator	=	new Csrf();
			$validator	->setOptions($this->getCsrfConfig())
						->setMessages(array(
							Csrf::NOT_SAME	=>	$this->getErrorMessage('CSRF::NOT_SAME'),
						));
						
			$this->setCsrfValidator($validator);
		}
		return $this->csrfValidator;
	}
	
	public function setEmailValidator($emailValidator = NULL){
		if($emailValidator !== NULL){
			$this->emailValidator = $emailValidator;
		}
		return $this;
	}
	public function getEmailValidator(){
		if($this->emailValidator === NULL){
			$validator	=	new EmailAddress();
			$validator	->setOptions($this->getEmailConfig())
						->setMessages(array(
							EmailAddress::DOT_ATOM			=>	$this->getErrorMessage('EMAILADDRESS::DOT_ATOM'),
							EmailAddress::INVALID			=>	$this->getErrorMessage('EMAILADDRESS::INVALID'),
							EmailAddress::INVALID_FORMAT	=>	$this->getErrorMessage('EMAILADDRESS::INVALID_FORMAT'),
							EmailAddress::INVALID_HOSTNAME	=>	$this->getErrorMessage('EMAILADDRESS::INVALID_HOSTNAME'),
							EmailAddress::INVALID_LOCAL_PART=>	$this->getErrorMessage('EMAILADDRESS::INVALID_LOCAL_PART'),
							EmailAddress::INVALID_MX_RECORD	=>	$this->getErrorMessage('EMAILADDRESS::INVALID_MX_RECORD'),
							EmailAddress::INVALID_SEGMENT	=>	$this->getErrorMessage('EMAILADDRESS::INVALID_SEGMENT'),
							EmailAddress::LENGTH_EXCEEDED	=>	$this->getErrorMessage('EMAILADDRESS::LENGTH_EXCEEDED'),
							EmailAddress::QUOTED_STRING		=>	$this->getErrorMessage('EMAILADDRESS::QUOTED_STRING'),
						));
			$this->setEmailValidator($validator);
		}
		return $this->emailValidator;
	}
	
	public function setHostNameValidator($hostNameValidator	=	NULL){
		if($hostNameValidator !== NULL){
			$this->hostNameValidator	=	$hostNameValidator;
		}
		return $this;
	}
	public function getHostNameValidator(){
		if($this->hostNameValidator	=== NULL){
			$validator	=	new HostName();
			$validator	->setOptions($this->getHostNameConfig())
						->setMessages(array(
					        HostName::CANNOT_DECODE_PUNYCODE  => $this->getErrorMessage('HOSTNAME::CANNOT_DECODE_PUNYCODE'),
							HostName::INVALID                 => $this->getErrorMessage('HOSTNAME::INVALID'),
							HostName::INVALID_DASH            => $this->getErrorMessage('HOSTNAME::INVALID_DASH'),
							HostName::INVALID_HOSTNAME        => $this->getErrorMessage('HOSTNAME::INVALID_HOSTNAME'),
							HostName::INVALID_HOSTNAME_SCHEMA => $this->getErrorMessage('HOSTNAME::INVALID_HOSTNAME_SCHEMA'),
							HostName::INVALID_LOCAL_NAME      => $this->getErrorMessage('HOSTNAME::INVALID_LOCAL_NAME'),
							HostName::INVALID_URI             => $this->getErrorMessage('HOSTNAME::INVALID_URI'),
							HostName::IP_ADDRESS_NOT_ALLOWED  => $this->getErrorMessage('HOSTNAME::IP_ADDRESS_NOT_ALLOWED'),
							HostName::LOCAL_NAME_NOT_ALLOWED  => $this->getErrorMessage('HOSTNAME::LOCAL_NAME_NOT_ALLOWED'),
							HostName::UNDECIPHERABLE_TLD      => $this->getErrorMessage('HOSTNAME::UNDECIPHERABLE_TLD'),
							HostName::UNKNOWN_TLD             => $this->getErrorMessage('HOSTNAME::UNKNOWN_TLD'),					
						));
			$this->setHostNameValidator($validator);
		}
		return $this->hostNameValidator;
	}
	
	public function setPhoneValidator($phoneValidator = NULL){
		if($phoneValidator !== NULL){
			$this->phoneValidator = $phoneValidator;
		}
		return $this;
	}
	public function getPhoneValidator(){
		if($this->phoneValidator === NULL){
			$validator	=	new PhoneNumber();
			$validator	->setOptions($this->getPhoneConfig())
						->setMessages(array(
							PhoneNumber::NO_MATCH    =>	$this->getErrorMessage('PHONENUMBER::NO_MATCH'),
        					PhoneNumber::UNSUPPORTED => $this->getErrorMessage('PHONENUMBER::UNSUPPORTED'),
       					 	PhoneNumber::INVALID     => $this->getErrorMessage('PHONENUMBER::INVALID'),
						));
			$this->setPhoneValidator($validator);
		}
		return $this->phoneValidator;
	}
	
	public function setRecordExistsValidator($recordExistsValidator = NULL){
	    if($recordExistsValidator !== NULL){
	        $this->recordExistsValidator    =   $recordExistsValidator;
	    }
	    return $this;
	}
	public function getRecordExistsValidator(){
	    if($this->recordExistsValidator === NULL){
		    $validator  =   new RecordExists($this->getRecordExistsConfig());
			$validator	->setMessages(array(
							 NoRecordExists::ERROR_NO_RECORD_FOUND 	=>	$this->getErrorMessage('RECORD_EXISTS::ERROR_NO_RECORD_FOUND'),
							 NoRecordExists::ERROR_RECORD_FOUND		=>	$this->getErrorMessage('NO_RECORD_EXISTS::ERROR_RECORD_FOUND'),
						));
	        
	        $this->setRecordExistsValidator($validator);
	    }
	    return $this->recordExistsValidator;
	}
	
	public function setNoRecordExistsValidator($noRecordExistsValidator = NULL){
	    if($noRecordExistsValidator !== NULL){
	        $this->noRecordExistsValidator    =   $noRecordExistsValidator;
	    }
	    return $this;
	}
	public function getNoRecordExistsValidator(){
		if($this->noRecordExistsValidator === NULL){
		    $validator  =   new NoRecordExists($this->getNoRecordExistsConfig());
			$validator	->setMessages(array(
							 NoRecordExists::ERROR_NO_RECORD_FOUND 	=>	$this->getErrorMessage('RECORD_EXISTS::ERROR_NO_RECORD_FOUND'),
							 NoRecordExists::ERROR_RECORD_FOUND		=>	$this->getErrorMessage('NO_RECORD_EXISTS::ERROR_RECORD_FOUND'),
						));
	        
	        $this->setNoRecordExistsValidator($validator);
	    }
	    return $this->noRecordExistsValidator;
	}
	
	public function setNoSpaceValidator($noSpaceValidator	=	NULL){
		if($noSpaceValidator !== NULL){
			$this->noSpaceValidator	=	$noSpaceValidator;
		}
		return $this;
	}
	public function getNoSpaceValidator(){
		if($this->noSpaceValidator === NULL){
			$validator	=	new NoSpace();
			$validator	->setOptions($this->getNoSpaceConfig())
						->setMessages(array(
							NoSpace::SPACE	=>	$this->getErrorMessage('SPACE'),
						));
			$this->setNoSpaceValidator($validator);
		}
		return $this->noSpaceValidator;
	}
	
	public function setAuthenticationValidator($authenticationValidator = NULL){
		if($authenticationValidator !== NULL){
			$this->authenticationValidator	=	$authenticationValidator;
		}
		return $this;
	}
	public function getAuthenticationValidator(){
		if($this->authenticationValidator === NULL){
			$validator	=	new Authentication();
			$validator	->setOptions($this->getAuthenticationConfig())
						->setMessages(array(
							Authentication::IDENTITY_NOT_FOUND 	=>	'Invalid identity',							
							Authentication::IDENTITY_AMBIGUOUS 	=>	'Identity is ambiguous',
							Authentication::CREDENTIAL_INVALID 	=>	'Invalid password',
							Authentication::UNCATEGORIZED      	=>	'Authentication failed',
							Authentication::GENERAL            	=>	'Authentication failed',
						));
			$this->setAuthenticationValidator($validator);
		}
		return	$this->authenticationValidator;
	}
	
	public function setDateValidator($dateValidator = NULL){
		if($dateValidator !== NULL){
			$this->dateValidator 	=	$dateValidator;
		}
		return $this;
	}
	public function getDateValidator(){
		if($this->dateValidator === NULL){
			$validator	=	new DateValidator();
			$validator	->setOptions($this->getDateConfig())
						->setMessages(array(
							DateValidator::INVALID 		=>	$this->getErrorMessage('DATE::INVALID'),  
							DateValidator::INVALID_DATE	=> 	$this->getErrorMessage('DATE::INVALID_DATE'), 
							DateValidator::FALSEFORMAT 	=> 	$this->getErrorMessage('DATE::FALSEFORMAT'),
						));
						
			$this->setDateValidator($validator);
		}

		return $this->dateValidator;
	}
	
	public function setFloatValidator($floatValidator = NULL){
		if($floatValidator !== NULL){
			$this->floatValidator	=	$floatValidator;
		}
		return $this;
	}
	public function getFloatValidator(){
		if($this->floatValidator === NULL){
			$validator	=	new Float();
			$validator	->setOptions(array(
						))
						->setMessages(array(
							Float::INVALID 		=>	$this->getErrorMessage('FLOAT::INVALID'),
							Float::NOT_FLOAT	=>	$this->getErrorMessage('FLOAT::NOT_FLOAT'),
						));
			$this->setFloatValidator($validator);
		}
		return $this->floatValidator;
	}
	
	public function setIdenticalValidator($identicalValidator = NULL){
		if($identicalValidator !== NULL){
			$this->identicalValidator	=	$identicalValidator;
		}
		return $this;
	}
	public function getIdenticalValidator(){
		if($this->identicalValidator === NULL){
			$validator	=	new Identical($this->getIdenticalConfig());
			$validator	->setMessages(array(
							Identical::NOT_SAME			=>	$this->getErrorMessage('IDENTICAL::NOT_SAME'),
        					Identical::MISSING_TOKEN 	=>	$this->getErrorMessage('IDENTICAL::MISSING_TOKEN'),
						));
			$this->setIdenticalValidator($validator);
		}

		return $this->identicalValidator;
	}
	
	public function setPostCodeValidator($postCodeValidator = NULL){
		if($postCodeValidator !== NULL){
			$this->postCodeValidator	=	$postCodeValidator;
		}
		return $this;
	}
	public function getPostCodeValidator(){
		if($this->postCodeValidator === NULL){
			$validator	=	new PostCode();
			$validator	->setOptions($this->getPostCodeConfig())
						->setMessages(array(
							 PostCode::INVALID    		=>	$this->getErrorMessage('POSTCODE::INVALID'), 
        					 PostCode::NO_MATCH      	=>	$this->getErrorMessage('POSTCODE::NO_MATCH'), 
        					 PostCode::SERVICE        	=>	$this->getErrorMessage('POSTCODE::SERVICE'), 
					         PostCode::SERVICEFAILURE 	=>	$this->getErrorMessage('POSTCODE::SERVICEFAILURE'), 
						));
			$this->setPostCodeValidator($validator);
		}
		return	$this->postCodeValidator;
	}
	
	public function setUrlValidator($urlValidator = NULL){
		if($urlValidator !== NULL){
			$this->urlValidator	=	$urlValidator;
		}
		return $this;
	}
	public function getUrlValidator(){
		if($this->urlValidator === NULL){
			$validator	=	new Uri();
			$validator	->setOptions($this->getUrlConfig())
						->setMessages([
							Uri::INVALID	=> 	"Invalid type given. String expected",
        					Uri::NOT_URI	=>	"The input does not appear to be a valid Uri",
						]);
			
			$this->setUrlValidator($validator);
		}
		return $this->urlValidator;
	}
	
	/**
	*	Configuration of Validator
	*/
	public function setUrlConfig($urlConfig = NULL){
		if($urlConfig !== NULL){
			$this->urlConfig = $urlConfig;
		}
		return $this;
	}
	public function getUrlConfig(){
		if($this->urlConfig === NULL){
			$this->setUrlConfig($this->generateUrlConfig());
		}
		return $this->urlConfig;
	}
	public function generateUrlConfig(){
		return	[
			//'allowRelative'	=>	true,
			//'allowAbsolute'	=>	true,
			//'uriHandler'	=>	'Zend\Uri\UriHandler',
		];
	}
	
	public function setPostCodeConfig($postCodeConfig = NULL){
		if($postCodeConfig !== NULL){
			$this->postCodeConfig	=	array_merge($this->generatePostCodeConfig(), $postCodeConfig);
		}
		return $this;
	}
	public function getPostCodeConfig(){
		if($this->postCodeConfig === NULL){
			$this->setPostCodeConfig($this->generatePostCodeConfig());
		}
		return $this->postCodeConfig;
	}
	public function generatePostCodeConfig(){
		return	array(
			'locale'	=>	'fr_BE',
			//'service'	=>	'',
			//'format'	=>	'',
		);
	}
	
	public function setSizeConfig($sizeConfig = NULL){
		if($sizeConfig !== NULL){
			$this->sizeConfig = array_merge($this->generateSizeConfig(), $sizeConfig);
		}
		return $this;
	}
	public function getSizeConfig(){
		if($this->sizeConfig === NULL){
			$this->setSizeConfig($this->generateSizeConfig());
		}
		return $this->sizeConfig;
	}
	public function generateSizeConfig(){
		return array(
			'min'	=>	'1kB',
			'max'	=>	'10MB',
		);
	}
	
	public function setCountConfig($countConfig = NULL){
		if($countConfig !== NULL){
			$this->countConfig = array_merge($this->generateCountConfig(), $countConfig);
		}
		return $this;
	}
	public function getCountConfig(){
		if($this->countConfig === NULL){
			$this->setCountConfig($this->generateCountConfig());
		}
		return $this->countConfig;
	}
	public function generateCountConfig(){
		return array(
			'min'	=>	0,
			'max'	=>	1,
		);
	}
	
	public function setExtensionConfig($extensionConfig = NULL){
		if($extensionConfig	!==	NULL){
			$this->extensionConfig = array_merge($this->generateExtensionConfig(), $extensionConfig);
		}
		return $this;
	}
	public function getExtensionConfig(){
		if($this->extensionConfig === NULL){
			$this->setExtensionConfig($this->generateExtensionConfig());
		}
		return $this->extensionConfig;
	}
	public function generateExtensionConfig(){
		return	array();
	}
	
	public function setNotEmptyConfig($notEmptyConfig = NULL){
		if($notEmptyConfig !== NULL){
			$this->notEmptyConfig = array_merge($this->generateNotEmptyConfig(), $notEmptyConfig);
		}
		return $this;
	}
	public function getNotEmptyConfig(){
		if($this->notEmptyConfig === NULL){
			$this->setNotEmptyConfig($this->generateNotEmptyConfig());
		}
		return $this->notEmptyConfig;
	}
	public function generateNotEmptyConfig(){
		return array();
	}
	
	public function setStringLengthConfig($stringLengthConfig = NULL){
		if($stringLengthConfig !== NULL){
			$this->stringLengthConfig	=	array_merge($this->generateStringLengthConfig(), $stringLengthConfig);
		}
		return $this;
	}
	public function getStringLengthConfig(){
		if($this->stringLengthConfig === NULL){
			$this->setStringLengthConfig($this->generateStringLengthConfig());
		}
		return $this->stringLengthConfig;
	}
	public function generateStringLengthConfig(){
		return array(
			'encoding'	=>	'UTF-8',
			'max'		=>	500,
			'min'		=>	0,
		);
	}
	
	public function setCsrfConfig($csrfConfig = NULL){
		if($csrfConfig !== NULL){
			$this->csrfConfig	=	array_merge($this->generateCsrfConfig(), $csrfConfig);
		}
		return $this;
	}
	public function getCsrfConfig(){
		if($this->csrfConfig === NULL){
			$this->setCsrfConfig($this->generateCsrfConfig());
		}
		return $this->csrfConfig;
	}
	public function generateCsrfConfig(){
		return array();
	}
	
	public function setEmailConfig($emailConfig	=	NULL){
		if($emailConfig	!== NULL){
			$this->emailConfig	=	array_merge($this->generateEmailConfig(), $emailConfig);
		}
		return $this;
	}
	public function getEmailConfig(){
		if($this->emailConfig === NULL){
			$this->setEmailConfig($this->generateEmailConfig());
		}
		return $this->emailConfig;
	}
	public function generateEmailConfig(){
		return array(
			'hostNameValidator'	=>	$this->getHostNameValidator(),
		);
	}
	
	public function setHostNameConfig($hostNameConfig = NULL){
		if($hostNameConfig !== NULL){
			$this->hostNameConfig	=	array_merge($this->generateHostNameConfig(), $hostNameConfig);
		}
		return $this;
	}
	public function getHostNameConfig(){
		if($this->hostNameConfig === NULL){
			$this->setHostNameConfig($this->generateHostNameConfig());
		}
		return $this->hostNameConfig;
	}
	public function generateHostNameConfig(){
		return	array(
			//'allow'       => HostName::ALLOW_DNS, 
			//'useIdnCheck' => true,  
        	//'useTldCheck' => true,  
        	//'ipValidator' => null,  
		);
	}
	
	public function setPhoneConfig($phoneConfig	=	NULL){
		if($phoneConfig !== NULL){
			$this->phoneConfig  =   array_merge($this->generatePhoneConfig(), $phoneConfig);
		}
		return $this;
	}
	public function getPhoneConfig(){
	    if($this->phoneConfig === NULL){
	        $this->setPhoneConfig($this->generatePhoneConfig());
	    }
	    return $this->phoneConfig;
	}
	public function generatePhoneConfig(){
	    return  array(
			'country'	=>	'BE',						
		);
	}
	
	public function setRecordExistsConfig($recordExistsConfig = NULL){
	    if($recordExistsConfig !== NULL){
	        $this->recordExistsConfig   =   array_merge($this->generateRecordExistsConfig(), $recordExistsConfig);
	    }
	    return $this;
	}
    public function getRecordExistsConfig(){
        if($this->recordExistsConfig === NULL){
            $this->setRecordExistsConfig($this->generateRecordExistsConfig());
        }
        return $this->recordExistsConfig;
    }
    public function generateRecordExistsConfig(){
        return  array(
			'adapter'	=>	$this->getAdapter(),
		);
    }
	
	public function setNoRecordExistsConfig($noRecordExistsConfig = NULL){
	    if($noRecordExistsConfig !== NULL){
	        $this->noRecordExistsConfig   =   array_merge($this->generateNoRecordExistsConfig(), $noRecordExistsConfig);
	    }
	    return $this;
	}
    public function getNoRecordExistsConfig(){
        if($this->noRecordExistsConfig === NULL){
            $this->setNoRecordExistsConfig($this->generateNoRecordExistsConfig());
        }
        return $this->noRecordExistsConfig;
    }
    public function generateNoRecordExistsConfig(){
        return  array(
			'adapter'	=>	$this->getAdapter(),
		);
    }
	
	public function setNoSpaceConfig($noSpaceConfig = NULL){
		if($noSpaceConfig !== NULL){
			$this->noSpaceConfig	=	$noSpaceConfig;
		}
		return $this->noSpaceConfig;
	}
	public function getNoSpaceConfig(){
		if($this->noSpaceConfig === NULL){
			$this->setNoSpaceConfig($this->generateNoSpaceConfig());
		}
		return $this->noSpaceConfig;
	}
	public function generateNoSpaceConfig(){
		return array();
	}
	
	public function setAuthenticationConfig($authenticationConfig = NULL){
		if($authenticationConfig !== NULL){
			$this->authenticationConfig	=	array_merge($this->generateAuthenticationConfig(), $authenticationConfig);
		}
		return $this;
	}
	public function getAuthenticationConfig(){
		if($this->authenticationConfig === NULL){
			$this->setAuthenticationConfig($this->generateAuthenticationConfig());
		}
		return $this->authenticationConfig;
	}
	public function generateAuthenticationConfig(){
		return	array();
	}
	
	public function setDateConfig($dateConfig = NULL){
		if($dateConfig !== NULL){
			$this->dateConfig 	=	array_merge($this->generateDateConfig(), $dateConfig);
		}
		return $this;
	}
	public function getDateConfig(){
		if($this->dateConfig === NULL){
			$this->setDateConfig($this->generateDateConfig());
		}
		return $this->dateConfig;
	}
	public function generateDateConfig(){
		return	array(
			'format'	=>	'd/m/Y',
		);
	}
    
	public function setIdenticalConfig($identicalConfig = NULL){
		if($identicalConfig !== NULL){
			$this->identicalConfig	=	array_merge($this->generateIdenticalConfig(), $identicalConfig);
		}
		return $this;
	}
	public function getIdenticalConfig(){
		if($this->identicalConfig === NULL){
			$this->setIdenticalConfig($this->generateIdenticalConfig());
		}
		return $this->identicalConfig;
	}
	public function generateIdenticalConfig(){
		return array(
		
		);
	}
}