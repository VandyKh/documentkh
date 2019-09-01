<?php
class Report_indexController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	function indexAction(){
	}
	function rptDocumentAction(){
		$db  = new Report_Model_DbTable_DbReport();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}
		else{
			$search = array(
				'adv_search'	=> '',
				'document_type'	=> 0,
				'from_dept'		=> 0,
				'start_date'	=> date('Y-m-d'),
				'end_date'		=> date('Y-m-d'),
				'doc_process'	=> '-1',
			);
		}
		$this->view->search = $search;
		$this->view->rs =$db->getAllDocument($search);
		 
		$form = new Application_Form_FrmAdvanceSearch();
		$frm = $form->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		 
	}
	
	function rptScanAction(){
		$db  = new Report_Model_DbTable_DbReport();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}
		else{
			$search = array(
					'adv_search'	=> '',
					'document_type'	=> 0,
					'from_dept'		=> 0,
					'scan_type'		=> 0,
					'doc_process'	=> -1,
					'start_date'	=> date('Y-m-d'),
					'end_date'		=> date('Y-m-d')
			);
		}
		$this->view->search = $search;
		$this->view->rs =$db->getAllScan($search);
			
		$form = new Application_Form_FrmAdvanceSearch();
		$frm = $form->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
			
	}
	function printsAction(){
		$id = $this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		
		$db  = new Report_Model_DbTable_DbReport();
		$row = $db->getDocumentyById($id);
		$this->view->row=$row;
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->printsDoc = $frmpopup->printFormDocument();
	}
	function rptDocAlertAction(){
		$db  = new Report_Model_DbTable_DbReport();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}
		else{
			$search = array(
					'adv_search'	=> '',
					'document_type'	=> 0,
					'from_dept'		=> 0,
					'scan_type'		=> 0,
					'doc_process'	=> -1,
					'start_date'	=> date('Y-m-d'),
					'end_date'		=> date('Y-m-d')
			);
		}
		$this->view->search = $search;
		$this->view->rs =$db->getAllDocumentAlert($search);
			
		$form = new Application_Form_FrmAdvanceSearch();
		$frm = $form->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
			
	}
}