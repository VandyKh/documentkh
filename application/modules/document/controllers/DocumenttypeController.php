<?php
class Document_DocumenttypeController extends Zend_Controller_Action {
	const REDIRECT_URL='/document';
	protected $tr;
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Document_Model_DbTable_DbDocumentType();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'search_status' => -1);
			}
			$rs_rows= $db->getAllCategory($search);
			$this->view->row= $rs_rows;
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
			$frm = new Document_Form_FrmDocumentType();
   			$frm_co=$frm->FrmaddDocumentType();
   			Application_Model_Decorator::removeAllDecorator($frm_co);
   			$this->view->frm_zone = $frm_co;
	}
   function addAction(){
   	if($this->getRequest()->isPost()){
   		try{
   			// Check Session Expire
   			$dbgb = new Application_Model_DbTable_DbGlobal();
   			$checkses = $dbgb->checkSessionExpire();
   			if (empty($checkses)){
   				$dbgb->reloadPageExpireSession();
   				exit();
   			}
   			$_data = $this->getRequest()->getPost();
   			$db = new Document_Model_DbTable_DbDocumentType();
   			$db->addDocumentType($_data);
   			if(!empty($_data['save_new'])){
   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/documenttype/add');
   			}else{
   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/documenttype/index');
   			}
   		}catch(Exception $e){
   			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
   			$err =$e->getMessage();
   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
   		}
   	}
   	$frm = new Document_Form_FrmDocumentType();
   	$frm_co=$frm->FrmaddDocumentType();
   	Application_Model_Decorator::removeAllDecorator($frm_co);
   	$this->view->frm_zone = $frm_co;
   }
   function editAction(){
   	$db = new Document_Model_DbTable_DbDocumentType();
	   	if($this->getRequest()->isPost()){
	   		// Check Session Expire
	   		$dbgb = new Application_Model_DbTable_DbGlobal();
	   		$checkses = $dbgb->checkSessionExpire();
	   		if (empty($checkses)){
	   			$dbgb->reloadPageExpireSession();
	   			exit();
	   		}
	   		
	   		try{
	   			$_data = $this->getRequest()->getPost();
	   			$db->addDocumentType($_data);
	   			Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'),self::REDIRECT_URL.'/documenttype/index');
	   		}catch(Exception $e){
	   			Application_Form_FrmMessage::message($this->tr->translate('EDIT_FAIL'));
	   			$err =$e->getMessage();
	   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
	   		}
	   	}
	   	$id=$this->getRequest()->getParam('id');
	   	$id = empty($id)?0:$id;
	   	$row = $db->getCategoryById($id);
	   	if(empty($row)){
	   		Application_Form_FrmMessage::Sucessfull($this->tr->translate('NO_RECORD'),self::REDIRECT_URL.'/documenttype/index');
	   		exit();
	   	}
	   	$frm = new Document_Form_FrmDocumentType();
	   	$frm_co=$frm->FrmaddDocumentType($row);
	   	Application_Model_Decorator::removeAllDecorator($frm_co);
	   	$this->view->frm_zone = $frm_co;
   }
   public function addajaxAction(){
   	if($this->getRequest()->isPost()){
   		$data = $this->getRequest()->getPost();
   		$db_co = new Document_Model_DbTable_DbDocumentType();
   		$data['parent']=$data['doc_type_parentid'];
   		$data['code']=$data['doc_type_code'];
   		$data['title']=$data['doc_type_title'];
   		$data['note']=$data['doc_type_note'];
   		$id = $db_co->addDocumentType($data);
   		
   		$dbgb = new Application_Model_DbTable_DbGlobal();
   		$cate = $dbgb->getAllDocumentType();
   		array_unshift($cate, array ( 'id' => -1,'name' => $this->tr->translate('ADD_NEW')));
   		array_unshift($cate, array ( 'id' => 0,'name' => $this->tr->translate('PLEASE_SELECT_CATEGORY')));
   		$return = array('id'=>$id,'datastore'=>$cate);
   		print_r(Zend_Json::encode($return));
   		exit();
   	}
   }
}

