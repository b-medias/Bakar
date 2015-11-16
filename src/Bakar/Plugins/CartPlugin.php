<?php
namespace Bakar\Plugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;
use ArrayObject;

class CartPlugin extends AbstractPlugin{
    const DISCOUNT_PERCENT      =   'PC';
    const DISCOUNT_QUANTITY     =   'QTY';
    const DISCOUNT_REDUCTION    =   'RED';
    const DISCOUNT_DIVISION     =   'DIV';
    
	private $serviceLocator;
	private $pluginManager;
	private $identifiant;
    private $session;
	private $systems;
	
	public function __construct($pm){
		$this->setPluginManager($pm);
		$this->setServiceLocator($pm->getServiceLocator());
	}
    
	public function setPluginManager($pluginManager = NULL){
		if($pluginManager !== NULL){
			$this->pluginManager = $pluginManager;
		}
		return $this;
	}
	public function getPluginManager(){
		return	$this->pluginManager;
	}
	public function setServiceLocator($serviceLocator = NULL){
		if($serviceLocator !== NULL){
			$this->serviceLocator	=	$serviceLocator;
		}
		return $this;
	}
	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	public function setSystems($systems = NULL){
		if($systems !== NULL){
			$this->systems	=	$systems;
		}
	}
	public function getSystems(){
		if($this->systems === NULL){
			$this->setSystems($this->getServiceLocator()->get('Bakar/Service/SystemsService'));
		}
		return $this->systems;
	}
	
	public function getService($name = 'product'){
		return	$this->getSystems()->getService($name);
	}
	
	public function setIdentifiant($identifiant = NULL){
        if($identifiant !== NULL){
            $this->identifiant = $identifiant;
        }
        return $this;
    }
    public function getIdentifiant(){
        if($this->identifiant === NULL){
            $this->setIdentifiant($this->generateIdentifiant());
        }
        return $this->identifiant;
    }
    public function generateIdentifiant(){
        return 'cart';
    }
    
    public function setSession($session = NULL){
        if($session !== NULL){
            $this->session = $session;
        }
        return $this;
    }
    public function getSession(){
        if($this->session === NULL){
            $this->setSession($this->generateSession());
        }
        return $this->session;
    }
    public function generateSession(){
        $session    =   new Container($this->getIdentifiant());
        return $session;
    }
        
    public function setItem($item = NULL){
		if($item !== NULL){
			$this->getSession()->offsetSet($item->key, $item);
		}
		return $this;
	}
	public function getItem($item){
		$return =   NULL;
		if($this->isExists($item)){
			$return =   $this->getSession()->offsetGet($item);    
		}
		return $return;
	}
	public function getReferences(){
		$references	=	new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
		$primaryKey	=	$this->getService()->getPrimaryKey();
		$sessionKey	=	$this->getService()->getSessionKey();

		foreach($this->getSession() as $item){
			$reference	=	new ArrayObject(array(
				'key'		=>	$item->item->$primaryKey,
				'sessionKey'=>	$item->item->$sessionKey,
				'quantity'	=>	$item->quantity,
			), ArrayObject::ARRAY_AS_PROPS);
			
			$references->offsetSet($item->item->$sessionKey, $reference);
		}
		return $references;
	}
   	public function getKeys(){
		$keys		=	array();
		$primaryKey	=	$this->getService()->getPrimaryKey();
		
		foreach($this->getSession() as $item){
			array_push($keys, $item->item->$primaryKey);
		}
		return $keys;
	}
    public function getItems(){
		$primaryKey	=	$this->getService()->getPrimaryKey();
		$sessionKey	=	$this->getService()->getSessionKey();
		
		$keyValues	=	$this->getKeys($primaryKey);
		$return		=	array();
		$items		=	array();
		if($keyValues > 0){
			$items	=	$this	->getService()
								->getItems(array($primaryKey => $keyValues))
								->getResults();
		}
		
		foreach($items as $key => $item){
			$sessionItem	=	$this->getItem($item->$sessionKey);
			if($sessionItem !== NULL){
				$item->quantity	=	$sessionItem->quantity;
				$item->discounts=	$this	->getService('discount')
											->getPlugin()
											->setConnecteur('product', $item)
											->getDiscounts();
			}
		}
		

		return $items;
	}
	
    public function getQuantity($item = NULL){
		$quantity   =   0;
        $items      =   $this->getSession();
    
        if($item !== NULL && $this->isExists($item)){
            $quantity	=	$items->offsetGet($item)->offsetExists('quantity')	?	$items->offsetGet($item)->quantity	:	0;
        }
        else{
            foreach($items as $item){
                $quantity	+=	$item->offsetExists('quantity')	?	$item->quantity	:	0;
            }
        }
        
        return $quantity;
    }
    public function quantity(){
        return $this->getQuantity();
    }
    
    public function getTotal($item = NULL, $one = FALSE){
        $total  =   0;
        $items  =   $this->getSession();
    
        if($item !== NULL && $this->isExists($item)){
            $total = $items->offsetGet($item)->price;
        }
        else{
            foreach($items as $item){
                if($one === TRUE){$total += ($item->price / $item->quantity);}
                else{$total +=  $item->price;}
            }
        }
        
        return $total;
    }
    public function total(){
        return $this->getTotal();
    }
	
    public function clear(){
		$this   ->getSession()
                ->getManager()
                ->getStorage()
                ->clear($this->getIdentifiant());
        return $this;
    }
    public function end(){
        return $this->clear();
    }
        
    public function isEmpty(){
        $this->getSession()->count() == 0 ? $return = TRUE : $return = FALSE;
        return $return;
    }
    public function isNotEmpty(){
        return !$this->isEmpty();
    }
    public function isExists($item){
        return $this    ->getSession()
                        ->offsetExists($item);
    }                           
    public function isNotExists($item){
        return  !$this->isExists($item);
    }
    
    public function add($item){
		$tmp	=	$item->getArrayCopy();
		$tmp	=	array_merge(array(
						'quantity'  =>  1,
						'key'       =>  'id',
						'item'      =>  NULL,
						'price'     =>  NULL,
						'merge'     =>  TRUE,			
					), $tmp);
					
		$item->exchangeArray($tmp);
		
		if($item->merge === TRUE){
			if($this->isExists($item->key)){
				$item->quantity	+=	$this->getQuantity($item->key);
			}
		}
		
		$this->setItem($item);
		
		return $item;
    }
    public function remove($item){
		$tmp	=	$item->getArrayCopy();
		$tmp	=	array_merge(array(
						'quantity'	=>	1,
						'key'		=>	'id',
					), $tmp);
		$item->exchangeArray($tmp);
		
		if($this->isExists($item->key)){
			if($this->getQuantity($item->key) > 	1){
				$quantity	=	$this->getQuantity($item->key) - $item->quantity;
				
				if($quantity >= 1){
					$item	=	$this->getItem($item->key);
					$item->quantity	=	$quantity;
					$this->setItem($item);
				}
				else{
					$this->delete($item->key);
				}
			}
			else{
				$this->delete($item->key);
			}
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function replace($item){
		if($this->isExists($item->key)){
			$tmp	=	$item->getArrayCopy();
			$tmp	=	array_merge(array(
							'quantity'	=>	$this->getQuantity($item->key),
							'key'		=>	$item->key,
						), $tmp);
			
			$item->exchangeArray($tmp);
			
			$quantity	=	$item->quantity;
			
			if($quantity > 0){
				$item				=	$this->getItem($item->key);
				$item->quantity		=	$quantity;
				$this->setItem($item);
			}
			else{
				$this->delete($item->key);
			}
			
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
    public function delete($key){
		if($this->isExists($key)){
			$this->getSession()->offsetUnset($key);
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
}