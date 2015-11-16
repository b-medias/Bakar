<?php
/**
* Bakar (http://www.bakar.be)
*
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/

namespace Bakar\Navigation;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;

class LanguageNavigation extends DefaultNavigationFactory{

	protected function getPages(ServiceLocatorInterface $serviceLocator){
		$application	=	$serviceLocator->get('Application');
		$routeMatch		=	$application->getMvcEvent()->getRouteMatch();
		
		if($routeMatch !== NULL){
			if($this->pages	=== NULL){
				$configuration['navigation'][$this->getName()]	=	array(
					'francais'	=>	array(
						'label'		=>	'FR',
						'title'		=>	'Français',
						'route'		=>	$routeMatch->getMatchedRouteName(),
						'params'	=>	array_merge($routeMatch->getParams(), array('language' => 'fr')),
					),
					'nederlands'=>	array(
						'label'		=>	'NL',
						'title'		=>	'Nederlands',
						'route'		=>	$routeMatch->getMatchedRouteName(),
						'params'	=>	array_merge($routeMatch->getParams(), array('language' => 'nl')),
					),
					/*'english'	=>	array(
						'label'		=>	'En',
						'title'		=>	'English',
						'route'		=>	$routeMatch->getMatchedRouteName(),
						'params'	=>	array_merge($routeMatch->getParams(), array('language' => 'en')),
					),*/
				);
				
				if(!isset($configuration['navigation'])){
					throw new Exception\InvalidArgumentException('Could not find navigation configuration key');
				}
				if(!isset($configuration['navigation'][$this->getName()])){
					throw new Exception\InvalidArgumentException(sprintf(
						'Failed to find a navigation container by the name "%s"',
						$this->getName()
					));
				}

				$router			=	$application->getMvcEvent()->getRouter();
				$pages			=	$this->getPagesFromConfig($configuration['navigation'][$this->getName()]);
				$this->pages	=	$this->injectComponents($pages, $routeMatch, $router);
			}
			return $this->pages;
		}
	}
}