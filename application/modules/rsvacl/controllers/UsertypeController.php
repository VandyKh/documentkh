<?php

class RsvAcl_UserTypeController extends Zend_Controller_Action
{
	
	const REDIRECT_URL = '/rsvacl';
	
    public function init()
    {
        /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }

    public function indexAction()
    {
        // action body
    	try {
    		$db_tran=new Application_Model_DbTable_DbGlobal();
    		$db = new RsvAcl_Model_DbTable_DbUserType();
    		$result = $db->getAlluserType();
    		    		
    		$list = new Application_Form_Frmtable();
    		if(!empty($result)){
    			$glClass = new Application_Model_GlobalClass();
    			$result = $glClass->getImgActive($result, BASE_URL, true);
    		}
    		else{
    			$result = Application_Model_DbTable_DbGlobal::getResultWarning();
    		}
    		$collumns = array("USER_TYPE","PARENT","NOTE","STATUS");
    		$link=array(
    				'module'=>'rsvacl','controller'=>'usertype','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(10,$collumns, $result,array('user_type'=>$link,'title'=>$link));
    		
    		if (empty($result)){
    			$result = array('err'=>1, 'msg'=>'មិនទាន់មានទិន្នន័យនៅឡើយ!');
    		}		
    	} catch (Exception $e) {
    		$result = Application_Model_DbTable_DbGlobal::getResultWarning();
    	}
    }
    
    public function viewUserTypeAction()
    {   
    	/* Initialize action controller here */
    	if($this->getRequest()->getParam('id')){
    		$db = new RsvAcl_Model_DbTable_DbUserType();
    		$user_type_id = $this->getRequest()->getParam('id');
    		$rs=$db->getUserType($user_type_id);
    		$this->view->rs=$rs;
    	}  	 
    	
    }
	public function addAction(){
		if($this->getRequest()->isPost())
		{
			$db=new RsvAcl_Model_DbTable_DbUserType();	
			$post=$this->getRequest()->getPost();			
			if(!$db->isUserTypeExist($post['user_type'])){
				$id=$db->insertUserType($post);						
		    	Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", self::REDIRECT_URL.'/usertype/index');
			}else {
				Application_Form_FrmMessage::message('User type had existed already');
			}
		}
		$db=new Application_Model_DbTable_DbGlobal();
		$rs=$db->getGlobalDb('SELECT user_type_id,user_type FROM rms_acl_user_type WHERE status=1');
		$options=array(''=>$this->tr->translate('SELECT_PARENT'));
		foreach($rs as $read) $options[$read['user_type_id']]=$read['user_type'];
		$this->view->usertype_list= $options;
	}
	
    public function editAction()
    {	
    	if($this->getRequest()->getParam('id')){
    		$db = new RsvAcl_Model_DbTable_DbUserType();
    		$user_type_id = $this->getRequest()->getParam('id');
    		$rs=$db->getUserType($user_type_id);
    		$this->view->usertype=$rs;
    		$db1=new Application_Model_DbTable_DbGlobal();
    		$allusertype=$db1->getGlobalDb('SELECT user_type_id,user_type FROM rms_acl_user_type WHERE status=1 AND user_type_id <> '.$user_type_id);
    		$options=array(''=>$this->tr->translate('SELECT_PARENT'));
    		foreach($allusertype as $read) $options[$read['user_type_id']]=$read['user_type'];
    		$this->view->usertype_list= $options;
    	
    	}
    	else{
    		Application_Form_FrmMessage::message('User type had not existed');
    	}
    	
    	if($this->getRequest()->isPost())
		{
			$post=$this->getRequest()->getPost();
			//print_r($rs); exit;
			if($rs['user_type']==$post['user_type']){
				$db->updateUserType($post,$rs['user_type_id']);
	    		 Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS",self::REDIRECT_URL.'/usertype/index');
			}else{
				if(!$db->isUserTypeExist($post['user_type'])){
					$db->updateUserType($post,$rs['user_type_id']);
					Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS",self::REDIRECT_URL.'/usertype/index/add');
				}else {
					Application_Form_FrmMessage::message('User had existed already');
				}
			}
		}
    }
    function addusertypeAction(){
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_dbmodel = new RsvAcl_Model_DbTable_DbUserType();
    		$_data['user_type']=$_data['user_typename'];
    		$_data['parent_id']=$_data['parent_id'];
    		$_data['note']=$_data['usertype_note'];
    		$u_typeid = $_dbmodel->insertUserType($_data);
    		print_r(Zend_Json::encode($u_typeid));
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
    	$db = new RsvAcl_Model_DbTable_DbUserType();
    	$row = $db->getCheckUserTypeInUser($id);
    	if (!empty($row)){
    		Application_Form_FrmMessage::Sucessfull("UNAVAILABLE_TO_DELETE_THIS_RECORD","/rsvacl/usertype");
    		exit();
    	}else if ($id==1){
    		Application_Form_FrmMessage::Sucessfull("UNAVAILABLE_TO_DELETE_THIS_RECORD","/rsvacl/usertype");
    		exit();
    	}
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$delete_sms=$tr->translate('CONFIRM_DELETE');
    	echo "<script language='javascript'>
    		var txt;
    		var r = confirm('$delete_sms');
    			if (r == true) {";
    		echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/rsvacl/usertype/deleterecord/id/".$id."'";
    	echo"}";
    	echo"else {";
    			echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/rsvacl/usertype'";
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
	    if ($id==1){
	    Application_Form_FrmMessage::Sucessfull("UNAVAILABLE_TO_DELETE_THIS_RECORD","/rsvacl/usertype");
	    exit();
	    }
	     
	    $db = new RsvAcl_Model_DbTable_DbUserType();
	    try {
	    	$dbacc = new Application_Model_DbTable_DbUsers();
		    $rs = $dbacc->getAccessUrl($module,$controller,'delete');
		    if(!empty($rs)){
			    $db->deleteUserType($id);
			    Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/rsvacl/usertype");
		    	exit();
		    }
		    Application_Form_FrmMessage::Sucessfull("You no permission to delete","/rsvacl/usertype");
		    exit();
	    }catch (Exception $e) {
		    Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		    Application_Form_FrmMessage::message("DELETE_FAIL");
		    exit();
	   }
    }

}

