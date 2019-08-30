<?php
class Document_indexController extends Zend_Controller_Action {
	const REDIRECT_URL = '/document/index';
	public function init()
	{
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Document_Model_DbTable_DbDocument();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search = array(
					'adv_search' 	=> '',
					'doc_process' 		=> -1,
					'from_dept' 	=> 0,
					'document_type' => 0,
					'period_option'=>'',
					'start_date'	=> date('Y-m-d'),
					'end_date'		=> date('Y-m-d'));
			}
			$rs_rows= $db->getAllDocument($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("SUBJECT","MINISTRY_ADMIN_NO","DEPARTMENT_ADMIN_NO","FROM_DEPARTMENT","DOCUMENT_TYPE","ISSUE_DATE","STATUS","DATE","BY_USER");
			$link=array(
					'module'=>'document','controller'=>'index','action'=>'edit',);
			$link1=array(
					'module'=>'document','controller'=>'index','action'=>'view',);
			$this->view->list=$list->getCheckList(10, $collumns, $rs_rows,array('subject'=>$link,'ministry_admin_no'=>$link,'department_admin_no'=>$link,'from_dept'=>$link,'document_type'=>$link,));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	
		$form = new Application_Form_FrmAdvanceSearch();
		$frm = $form->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
// 		$this->view->result=$search;	
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			try{
				// Check Session Expire
				$dbgb = new Application_Model_DbTable_DbGlobal();
				$checkses = $dbgb->checkSessionExpire();
				if (empty($checkses)){
					$dbgb->reloadPageExpireSession();
					exit();
				}
				$data = $this->getRequest()->getPost();
				$db = new Document_Model_DbTable_DbDocument();
				$id= $db->addDocument($data);
				if(!empty($data['save_new'])){
					Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/add');
				}else{
					Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL);
				}
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_SUCCESS"));
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$fm = new Document_Form_FrmDocument();
		$frm = $fm->FrmaddDocument();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_doc = $frm;
		
		$db  = new Application_Model_DbTable_DbGlobal();
		$department = $db->getAllDepartment();
		$this->view->department_list = $department;
		array_unshift($department, array('id'=>-1,'name'=>$this->tr->translate('ADD_NEW')));
		$this->view->department = $department;
		
		$doc_type = $db->getAllDocumentType();
		$this->view->doc_type_list = $doc_type;
		array_unshift($doc_type, array('id'=>-1,'name'=>$this->tr->translate('ADD_NEW')));
		$this->view->doc_type = $doc_type;
		
	}
	public function editAction(){
		$db = new Document_Model_DbTable_DbDocument();
		$id = $this->getRequest()->getParam("id");
		if($this->getRequest()->isPost()){
			try{
				// Check Session Expire
				$dbgb = new Application_Model_DbTable_DbGlobal();
				$checkses = $dbgb->checkSessionExpire();
				if (empty($checkses)){
					$dbgb->reloadPageExpireSession();
					exit();
				}
				$data = $this->getRequest()->getPost();
				$id= $db->addDocument($data);
				Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'), self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		$row = $db->getDocumentyById($id);
	    $this->view->row=$row;
	    
		$fm = new Document_Form_FrmDocument();
		$frm = $fm->FrmaddDocument($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_doc = $frm;
		
		$db  = new Application_Model_DbTable_DbGlobal();
		$department = $db->getAllDepartment();
		$this->view->department_list = $department;
		array_unshift($department, array('id'=>-1,'name'=>$this->tr->translate('ADD_NEW')));
		$this->view->department = $department;
		
		$doc_type = $db->getAllDocumentType();
		$this->view->doc_type_list = $doc_type;
		array_unshift($doc_type, array('id'=>-1,'name'=>$this->tr->translate('ADD_NEW')));
		$this->view->doc_type = $doc_type;
	}
	
	function checkTitleAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Document_Model_DbTable_DbDocument();
			$return=$db->CheckTitle($data);
			print_r(Zend_Json::encode($return));
			exit();
		}
	}
	
	function adddocumentAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Document_Model_DbTable_DbDocument();
			$id = $db->addDocument($data);
			$row = $db->getDocumentyById($id);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function deleteAction(){
	
		// Check Session Expire
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$checkses = $dbgb->checkSessionExpire();
		if (empty($checkses)){
			$dbgb->reloadPageExpireSession();
			exit();
		}
	
		$id = $this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		$db = new Document_Model_DbTable_DbDocument();
		$row = $db->getCheckDocumentInScan($id);
		if (!empty($row)){
			Application_Form_FrmMessage::Sucessfull("UNAVAILABLE_TO_DELETE_THIS_RECORD","/document/index");
			exit();
		}
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$delete_sms=$tr->translate('CONFIRM_DELETE');
		echo "<script language='javascript'>
		var txt;
		var r = confirm('$delete_sms');
		if (r == true) {";
		echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/document/index/deleterecord/id/".$id."'";
		echo"}";
		echo"else {";
		echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/document/index'";
		echo"}
		</script>";
	}
	function deleterecordAction(){
	
		// Check Session Expire
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$checkses = $dbgb->checkSessionExpire();
		if (empty($checkses)){
			$dbgb->reloadPageExpireSession();
			exit();
		}
	
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$action=$request->getActionName();
		$controller=$request->getControllerName();
		$module=$request->getModuleName();
	
		$id = $this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		$db = new Document_Model_DbTable_DbDocument();
		try {
			$dbacc = new Application_Model_DbTable_DbUsers();
			$rs = $dbacc->getAccessUrl($module,$controller,'delete');
			if(!empty($rs)){
			$db->deleteDocument($id);
			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/document/index");
				exit();
			}
			Application_Form_FrmMessage::Sucessfull("You no permission to delete","/document/index");
			exit();
		}catch (Exception $e) {
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			Application_Form_FrmMessage::message("DELETE_FAIL");
			exit();
		}
	}
	
	
	
}