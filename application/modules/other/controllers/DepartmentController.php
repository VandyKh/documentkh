<?php
class Other_DepartmentController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
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
			$db = new Other_Model_DbTable_DbDepartment();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'search_status' => -1);
			}
			$rs_rows= $db->getAllDepartment($search);
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
   			$db = new Other_Model_DbTable_DbDepartment();
   			$db->addDepartment($_data);
   			if(!empty($_data['save_new'])){
   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/department/add');
   			}else{
   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/department/index');
   			}
   		}catch(Exception $e){
   			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
   			$err =$e->getMessage();
   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
   		}
   	}
   	$frm = new Other_Form_FrmDepartment();
   	$frm_co=$frm->FrmDepartment();
   	Application_Model_Decorator::removeAllDecorator($frm_co);
   	$this->view->frm_zone = $frm_co;
   }
   function editAction(){
   	$db = new Other_Model_DbTable_DbDepartment();
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
	   			$db->addDepartment($_data);
	   			Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'),self::REDIRECT_URL.'/department/index');
	   		}catch(Exception $e){
	   			Application_Form_FrmMessage::message($this->tr->translate('EDIT_FAIL'));
	   			$err =$e->getMessage();
	   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
	   		}
	   	}
	   	$id=$this->getRequest()->getParam('id');
	   	$id = empty($id)?0:$id;
	   	$row = $db->getDepartmentById($id);
	   	if(empty($row)){
	   		Application_Form_FrmMessage::Sucessfull($this->tr->translate('NO_RECORD'),self::REDIRECT_URL.'/department/index');
	   		exit();
	   	}
	   	$frm = new Other_Form_FrmDepartment();
	   	$frm_co=$frm->FrmDepartment($row);
	   	Application_Model_Decorator::removeAllDecorator($frm_co);
	   	$this->view->frm_zone = $frm_co;
   }
   public function addajaxAction(){
   	if($this->getRequest()->isPost()){
   		$data = $this->getRequest()->getPost();
   		$db_co = new Other_Model_DbTable_DbDepartment();
   		$data['parent']=$data['department_parent_id'];
   		$data['code']=$data['department_code'];
   		$data['title']=$data['department_title'];
   		$data['note']=$data['department_note'];
   		$id = $db_co->addDepartment($data);
   		
   		$dbgb = new Application_Model_DbTable_DbGlobal();
   		$cate = $dbgb->getAllDepartment();
   		array_unshift($cate, array ( 'id' => -1,'name' => $this->tr->translate('ADD_NEW')));
   		array_unshift($cate, array ( 'id' => 0,'name' => $this->tr->translate('PLEASE_SELECT_CATEGORY')));
   		$return = array('id'=>$id,'datastore'=>$cate);
   		print_r(Zend_Json::encode($return));
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
	   	$db = new Other_Model_DbTable_DbDepartment();
	   	$row = $db->getCheckDepartmentInDoc($id);
	   	$rowInuser = $db->getCheckDepartmentInUser($id);
	   	if (!empty($row)){
	   		Application_Form_FrmMessage::Sucessfull("UNAVAILABLE_TO_DELETE_THIS_RECORD","/other/department");
	   		exit();
	   	}else if (!empty($rowInuser)){
	   		Application_Form_FrmMessage::Sucessfull("UNAVAILABLE_TO_DELETE_THIS_RECORD","/other/department");
	   		exit();
	   	}
	   	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
	   	$delete_sms=$tr->translate('CONFIRM_DELETE');
	   	echo "<script language='javascript'>
	   	var txt;
	   	var r = confirm('$delete_sms');
	   	if (r == true) {";
   			echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/other/department/deleterecord/id/".$id."'";
   			echo"}";
   			echo"else {";
   			echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/other/department'";
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
	   $db = new Other_Model_DbTable_DbDepartment();
	   try {
		   $dbacc = new Application_Model_DbTable_DbUsers();
		   $rs = $dbacc->getAccessUrl($module,$controller,'delete');
		   if(!empty($rs)){
			   $db->deleteDepartment($id);
			   Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/other/department");
			   exit();
		   }
		   	Application_Form_FrmMessage::Sucessfull("You no permission to delete","/other/department");
		   	exit();
	   }catch (Exception $e) {
	  	 Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   		Application_Form_FrmMessage::message("DELETE_FAIL");
	   		exit();
	   }
   }
}

