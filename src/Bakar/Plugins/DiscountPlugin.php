<?php
namespace Bakar\Plugins;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ArrayObject;


class DiscountPlugin extends AbstractPlugin{
	const DISCOUNT_PERCENT      =   '%';
	const DISCOUNT_QUANTITY     =   '+';
	const DISCOUNT_REDUCTION    =   '-';
	const DISCOUNT_DIVISION     =   '/';	
	protected $connecteurs;

	public function setConnecteur($key, $value){
		if($key !== NULL && $value !== NULL){
			$this->getConnecteurs()->$key	=	$value;
		}
		return $this;
	}
	public function getConnecteur($key){
		$connecteur	=	NULL;
		if($this->getConnecteurs()->offsetExists($key)){
			$connecteur	=	$this->getConnecteurs()->$key;
		}
		return $connecteur;
	}
	public function setConnecteurs(ArrayObject $connecteurs){
		if($connecteurs !== NULL){
			$this->connecteurs	=	$connecteurs;
		}
		return $this;
	}
	public function getConnecteurs(){
		if($this->connecteurs === NULL){
			$this->setConnecteurs(new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS));
		}
		return $this->connecteurs;
	}
	public function getViewType($type){
		switch($type){
			case self::DISCOUNT_DIVISION:
			return '';
			
			case self::DISCOUNT_QUANTITY:
			return '';
			
			case self::DISCOUNT_PERCENT:
			return '%';
			
			case self::DISCOUNT_REDUCTION:
			return '€';
		}
	}
	public function sort($data){
		$filter	=	new ArrayObject();
		$data->ksort();
		$tmp	=	$data->getArrayCopy();
		$tmp	=	array_reverse($tmp);
		$data->exchangeArray($tmp);

		foreach($data as $priority => $array){
			foreach($array as $discount){
				if($discount->cumul == TRUE){
					$filter->append($discount);
				}
				else{
					break;
				}
			}
		}
		return $filter;
	}
	public function fx($type, $value){
		$price	=	$this->getConnecteur('product')->PP;

		switch($type){
			case	self::DISCOUNT_PERCENT:
			$price	=	($price / 100) * $value;
			break;
			
			case	self::DISCOUNT_REDUCTION:
			$price	=	$price - $value;
			break;
			
			case	self::DISCOUNT_DIVISION:
			$price	=	$price / 2;
			break;
		}

		return number_format($price, 2, '.', '');
	}
	public function calcul($discounts){
		$this->sort($discounts);
		$filter	=	$discounts;
		
		$return	=	new ArrayObject(array(
			'price'				=>	$this->getConnecteur('product')->PP,
			'priceWithDiscount'	=>	$this->getConnecteur('product')->PP,
			'allView'			=>	'',
			'userView'			=>	'',
			'globalView'		=>	'',
			'categoryView'		=>	'',
			'productView'		=>	'',
			'discounts'			=>	new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS),
		), ArrayObject::ARRAY_AS_PROPS);
				
		foreach($filter as $discount){
			foreach($discount as $underDiscount){
				if($discount->cumul === TRUE OR $return->discounts->count() == 0){	
					$calculed					=	$this->fx($underDiscount->type, $underDiscount->value);
					$_discount					=	clone($underDiscount);
					$_discount->valueDiscount	=	$calculed;
					$_discount->valueCalculed	=	$return->price - $calculed;
					$_discount->view			=	'<div class="b-discount">- '.$_discount->value.$this->getViewType($_discount->type).'</div>';
					$return->priceWithDiscount	=	$return->priceWithDiscount - $calculed;
					$return->allView			.=	$_discount->view;
					
					switch($discount->table){
						case 'user':
						$return->userView		.=	$_discount->view;
						break;
						
						case 'category':
						$return->categoryView	.=	$_discount->view;
						break;
						
						case 'global':
						$return->globalView		.=	$_discount->view;
						break;
						
						case 'product':
						$return->productView	.=	$_discount->view;
						break;
					}
					
					$return->discounts->append($_discount);
				}
				
				if($discount->cumul == FALSE){
					break;
				}
			}
		}	
		return $return;
	}
	
	public function getDiscounts(){
		$return	=	new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
		$array	=	new ArrayObject(array(
			'user'		=>	$this->getConnecteur('user'),
			'product'	=>	$this->getConnecteur('product'),
			'category'	=>	$this->getConnecteur('category'),
			'global'	=>	$this->getConnecteur('global'),
		), ArrayObject::ARRAY_AS_PROPS);


		foreach($array as $table => $connecteurs){
			if(is_array($connecteurs)){
				foreach($connecteurs as $connecteur){
					if($connecteur->discountDiscounts !== NULL){
						$discounts	=	$connecteur->discountDiscounts;
						
						foreach($discounts as $discount){
							if(!$return->offsetExists($discount->offsetGet('level'))){
								$return->offsetSet($discount->level, new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS));
							}
							$discount->table	= 	$table;
							$return->offsetGet($discount->offsetGet('level'))->offsetSet(NULL, $discount);
						}
					}
				}
			
			}
			else{			
				if($connecteurs != NULL && $connecteurs->discountDiscounts !== NULL){
					$discounts	=	$connecteurs->discountDiscounts;
					if(is_string($discounts)){
						$discounts	=	unserialize($discounts);
					}
					foreach($discounts as $discount){						
						if(!$return->offsetExists($discount->offsetGet('level'))){
							$return->offsetSet($discount->offsetGet('level'), new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS));
						}
						$discount->table	=	$table;
						$return->offsetGet($discount->offsetGet('level'))->offsetSet(NULL, $discount);
					}
				}
			}
		}
		
		return $this->calcul($return);
	}
	


	public function getDiscountsBy($key){
		$return	=	new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
		$array	=	new ArrayObject(array(
			$key		=>	$this->getConnecteur($key),
		), ArrayObject::ARRAY_AS_PROPS);

		$connecteurs	=	$this->getConnecteur($key);
		if(is_array($connecteurs)){
			foreach($connecteurs as $connecteur){
				if($connecteur->discountDiscounts !== NULL){
					$discounts	=	$connecteur->discountDiscounts;
					foreach($discounts as $discount){
						if(!$return->offsetExists($discount->offsetGet('level'))){
							$return->offsetSet($discount->offsetGet('level'), new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS));
						}
						$discount->offsetSet('table', $key);
						$return->offsetGet($discount->offsetGet('level'))->offsetSet(NULL, $discount);
					}
				}
			}
		}
		else{
			if($connecteurs != NULL && $connecteurs->discountDiscounts !== NULL){
				$discounts	=	$connecteurs->discountDiscounts;
				foreach($discounts as $discount){
					if(!$return->offsetExists($discount->offsetGet('level'))){
						$return->offsetSet($discount->offsetGet('level'), new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS));
					}
					$discount->offsetSet('table', $key);
					$return->offsetGet($discount->offsetGet('level'))->offsetSet(NULL, $discount);
				}
			}
		}
		return $this->calcul($return);
	}
	public function getDiscountBy($key){
		$return	=	new ArrayObject();
		$array	=	new ArrayObject(array(
			$key		=>	$this->getConnecteur($key),
		));

		$connecteurs	=	$this->getConnecteur($key);
		if(is_array($connecteurs)){
			foreach($connecteurs as $connecteur){
				if($connecteur->discountDiscounts !== NULL){
					$discounts	=	$connecteur->discountDiscounts;
					foreach($discounts as $discount){
						if(!$return->offsetExists($discount->offsetGet('level'))){
							$return->offsetSet($discount->offsetGet('level'), new ArrayObject());
						}
						$discount->offsetSet('table', $key);
						$return->offsetGet($discount->offsetGet('level'))->offsetSet(NULL, $discount);
						break;
					}
				}
			}
		}
		else{
			if($connecteurs != NULL && $connecteurs->discountDiscounts !== NULL){
				$discounts	=	$connecteurs->discountDiscounts;
				foreach($discounts as $discount){
					if(!$return->offsetExists($discount->offsetGet('level'))){
						$return->offsetSet($discount->offsetGet('level'), new ArrayObject());
					}
					$discount->offsetSet('table', $key);
					$return->offsetGet($discount->offsetGet('level'))->offsetSet(NULL, $discount);
					break;
				}
			}
		}
		return $this->calcul($return);
	}
}



