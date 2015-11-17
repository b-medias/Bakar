<?php
namespace Bakar\Navigation;
 
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
class LanguageNavigationFactory implements FactoryInterface{
    public function createService(ServiceLocatorInterface $serviceLocator){
        $navigation =  new LanguageNavigation();
	    return $navigation->createService($serviceLocator);
    }
}