<?php
/**
* Bakar (http://www.bakar.be)
*
* @link         http://www.bakar.be
* @copyright    Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version 		02032015.1633
*/

namespace Bakar\Plugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DistancePlugin extends AbstractPlugin{
	const RAYON	=	6366;
	
	private $db;
	private $tableName;
	
	public function setTableName($tableName = NULL){
		if($tableName !== NULL){
			$this->tableName	=	$tableName;
		}
		return $this;
	}
	public function getTableName(){
		if($this->tableName === NULL){
			$this->setTableName('coordscp');
		}
		return $this->tableName;
	}
	
	public function setDb($db = NULL){
		if($db !== NULL){
			$this->db	=	$db;
		}
		return $this;
	}
	public function getDb(){
		if($this->db === NULL){
			$this->setDb($this->getController()->dbPlugin());
		}
		return $this->db;
	}
	
	public function getCoordsByDb($zip = NULL){
		$return	=	array();
		
		if($zip !== NULL){
			$return	=	$this	->getDb()
								->setTableName($this->getTableName())
								->select(array(
									'Lat'	=>	'latitude',
									'Lng'	=>	'longitude',
								))
								->where(array('zip' => $zip))
								->execute()
								->getResult();
		}
		
		return $return;
	}

	public function getDistance($start, $end){
		$coordsA	=	$this->getCoordsByDb($start);
		$coordsB	=	$this->getCoordsByDb($end);
		
		$coordsA->latitude	=	deg2rad($coordsA->latitude);
		$coordsA->longitude	=	deg2rad($coordsA->longitude);
		$coordsB->latitude	=	deg2rad($coordsB->latitude);
		$coordsB->longitude	=	deg2rad($coordsB->longitude);
		
		$latitude	=	($coordsA->latitude - $coordsB->latitude) / 2;
		$longitude	=	($coordsA->longitude - $coordsB->longitude) / 2;
		
		
		
		$distance	=	2 * asin(sqrt(pow(sin($latitude), 2) + cos($coordsA->latitude) * cos($coordsB->latitude) * pow(sin($longitude), 2)));
		$km			=	$distance	*	self::RAYON;
		
		return $km;
	}
}