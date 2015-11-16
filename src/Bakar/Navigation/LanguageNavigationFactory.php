<?php
/**
* Bakar (http://www.bakar.be)
*
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Navigation;
 
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
class LanguageNavigationFactory implements FactoryInterface{
	public function createService(ServiceLocatorInterface $serviceLocator){
		$navigation	=	new LanguageNavigation();
		return $navigation->createService($serviceLocator);
	}
}