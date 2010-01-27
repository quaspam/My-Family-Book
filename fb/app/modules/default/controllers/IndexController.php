<?php

class IndexController extends Zend_Controller_Action {

	/**
	 * The default action - show the home page
	 */
	public function indexAction(){

	}


	public function caddAction(){

		$this->listCentriqPdts();
		$tbl_pdt = new CentriqTable();
		$tbl_pdt->getNextPdtID();
		if( $this->_getParam('name','') && $this->_getParam('price',0) ){
			$data['prod_amt']   = $this->_getParam('price');
			$data['prod_desc']  = (string)$this->_getParam('name');
			$data['is_centriq'] = 1;
			$data['data_id'] = 1;
			$data['active'] = 1;
			$data['prod_id'] = $tbl_pdt->getNextPdtID();

			$tbl_pdt->insert($data);
		}

		$this->view->pdts = $tbl_pdt->getList();
		$this->renderScript('index/centriq.phtml');
	}

	protected function getCentriqProducts(){

		/*@var $db Zend_Db_Adapter_Abstract */
		$db = Zend_Registry::get('db_adapter');
		$pdts = $db->query("")
		->fetchAll(Zend_Db::FETCH_OBJ);


	}

	public function memAction(){
		$mem = new stdClass();
		$mem->fname = "Arthur";
		$mem->sname = "Mlauzi";
		$mem->id	= "BN403088";
		$mem->work	= "Computer Programmer";
		$this->view->mem = $mem;
	}

	protected function listCentriqPdts(){
		$sql = "
		SELECT
			C.PROD_ID, C.IS_FAMILY F, C.DEP_UPTO_65_CNT U65,
			C.DEP_OVER_65_CNT O65, COVER, PROD_AMT, MSG
		FROM tbl_centriq_pdts C
		INNER JOIN Product_MAster PM
		ON PM.data_id=1 AND C.PROD_ID = PM.PROD_ID";

		/*@var $db Zend_Db_Adapter_SqlSrv */
		$db = Zend_Registry::get('db_adapter');
		$db->setFetchMode(Zend_Db::FETCH_OBJ);
		$rs = $db->fetchAll($sql);

		for($i = 0, $l = count($rs); $i < $l; $i++ ){
			$pdt = $rs[$i];
			$code = ($pdt->F ? 'F' : 'M') . $pdt->COVER;
			$code.= $pdt->U65 ? 'U' . $pdt->U65 : '';
			$code.= $pdt->O65 ? 'O' . $pdt->O65 : '';
			$rs[$code] = $pdt;
			unset($rs[$i]);
		}

		ob_clean();
		echo "<pre>" . json_encode($rs) ."</pre>";
		exit;
	}

	public function cdrAction(){

		$dsn = "odbc:DSN=CDR;Driver={Microsoft Access}";
		$db = new PDO($dsn);
		$cid = $this->_request->getParam('cid', 0);
		if( !$cid ){
			$this->getCdr($db);
		}else{
			$this->rmCall($cid, $db);
		}
	}

	protected function rmCall( $cid, PDO $db ){
		$sql = "DELETE FROM LOG WHERE LOGID=$cid";
		$db->query($sql);
		$this->_helper->viewRenderer->setNoRender(true);
		Zend_Layout::getMvcInstance()->disableLayout();
		echo "{success: true, cid: $cid}";
	}

	protected function getCdr( PDO $db ){
		$min_date = date("1/m/Y");
		$stsmt = $db->query("
			SELECT
				LOGID AS ID, LOGDATETIME AS DT, DURATION AS DUR, CALLCOST AS COST, NUMBERDIALED AS NUM
			FROM LOG WHERE PIN IN('2577') AND LOGDATETIME > #$min_date# ORDER BY LOGID DESC
		");
		if( (int)$db->errorCode() ){
			$this->view->log = "There was error with error code: " . print_r($db->errorInfo(), true);
			$this->view->calls =  array();
		}else{
			$rs = $stsmt->fetchAll(PDO::FETCH_OBJ);
			$this->view->calls = $rs;
		}
	}

}