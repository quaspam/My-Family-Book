<?php
class ErrorController extends Zend_Controller_Action{
	
	public function errorAction(){
		
		$e = $this->_getParam('error_handler');
		//echo "<pre style='background: #ECECEC;'>" . print_r($e->exception, true) . "<pre>";
		//exit;
		switch($e->type){
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
				$this->view->msg = "REQUESTED RESOURCE NOT FOUND";
				$this->_response->setHttpResponseCode(404);
			break;
			default:
				$this->view->msg = "APPLICATION ERROR";
				$this->_response->setHttpResponseCode(500);
			break;
		}
		$this->view->req = $e->request;
		$this->view->exception = $e->exception;
	}
}