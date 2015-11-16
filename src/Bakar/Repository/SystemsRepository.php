<?php
/**
* Bakar (http://www.bakar.be)
*
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Repository;

use Bakar\Repository\AbstractRepository;

class SystemsRepository extends AbstractRepository{
	const TABLENAME	=	'systems';
	
	protected function generateTableName(){
		return self::TABLENAME;
	}
	
	public function logs($data){
		$this	->getDb()
				->setTableName($this->getTableName())
				->insert($data)
				->execute();
	}
}