<?php

/**
 * CentriqTable
 *
 * @author Administrator
 * @version
 */

require_once 'Zend/Db/Table/Abstract.php';

class CentriqTable extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'product_master';
	protected $_primary = array('DATA_ID','PROD_ID');

	/**
	 * This function retrives all centriq products
	 * @return array
	 */
	public function getList(){

		$sql = "SELECT prod_id AS id, prod_amt amt, prod_desc name
				FROM Product_Master WHERE is_centriq = '1' and data_id = 1";
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
		return $this->getAdapter()->fetchAll($sql);
	}

	public function getNextPdtID(){
		$sql = "SELECT MAX(prod_id) + 1 AS id
				FROM Product_Master WHERE data_id = 1";
		$rs = $this->getAdapter()->fetchRow($sql);
		return (int) $rs['id'];
	}

}

