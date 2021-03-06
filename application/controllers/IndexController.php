<?php

class IndexController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/home';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }
    public function indexAction()
    {
    	
    }
    public function administratorAction()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	$username = $session_user->full_name;
    	$user_id = $session_user->user_id;
    	if (!empty($user_id)){
    		$this->_redirect("/document");
    	}
    	$this->_helper->layout()->disableLayout();
		$form=new Application_Form_FrmLogin();				
		$form->setAction('index');		
		$form->setMethod('post');
		$form->setAttrib('accept-charset', 'utf-8');
		$this->view->form=$form;
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);		
		$vdgb = new Application_Model_DbTable_DbGlobal();
		$this->view->alllang =  $vdgb->getLaguage();
        
		if($this->getRequest()->isPost())		
		{
			$formdata=$this->getRequest()->getPost();
			if($form->isValid($formdata))
			{
				$session_lang=new Zend_Session_Namespace('lang');
				$session_lang->lang_id=$formdata["lang"];//for creat session
				Application_Form_FrmLanguages::getCurrentlanguage($session_lang->lang_id);//for choose lang for when login
				$user_name=$form->getValue('txt_user_name');
				$password=$form->getValue('txt_password');
				$db_user=new Application_Model_DbTable_DbUsers();
				if($db_user->userAuthenticate($user_name,$password)){					
					
					$session_user=new Zend_Session_Namespace(SYSTEM_SES);
					$user_id=$db_user->getUserID($user_name);
					$user_info = $db_user->getUserInfo($user_id);
					
					$arr_acl=$db_user->getArrAcl($user_info['user_type']);
					//$session_user->url_report=$db_user->getArrAclReport($user_info['user_type']);
					$session_user->user_id=$user_id;
					$session_user->user_name=$user_name;
					$session_user->pwd=$password;		
					$session_user->level= $user_info['user_type'];
					$session_user->full_name= $user_info['full_name'];
					$session_user->department= $user_info['department'];
					$session_user->timeout= time();
					$a_i = 0;
					$arr_actin = array();	
					for($i=0; $i<count($arr_acl);$i++){
						$arr_module[$i]=$arr_acl[$i]['module'];
					}
					$arr_module=(array_unique($arr_module));
					$arr_actin=(array_unique($arr_actin));
					$arr_module=$this->sortMenu($arr_module);
					
					$session_user->arr_acl = $arr_acl;
					$session_user->arr_module = $arr_module;
					$session_user->arr_actin = $arr_actin;
						
					$session_user->lock();
					$dbgb = new Application_Model_DbTable_DbGlobal();
					$_datas = array('description'=>'Login to System');
					$dbgb->addActivityUser($_datas);
					
					$log=new Application_Model_DbTable_DbUserLog();
					$log->insertLogin($user_id);	
					foreach ($arr_module AS $i => $d){
						if($d !== 'transfer'){
							$url = '/' . $arr_module[0];
						}
						else{
							$url = self::REDIRECT_URL;
							break;
						}
					}
					Application_Form_FrmMessage::redirectUrl("/document");	
					exit();
				}
			else{		
					$this->view->msg = $tr->translate("USER_NAME_PASSORD_WRONG");
				}
			}
			else{				
				$this->view->msg = 'លោកអ្នកមិនមានសិទ្ធិប្រើប្រាស់ទេ!';
			}				
		}	
		$session_lang=new Zend_Session_Namespace('lang');
		$this->view->rslang = $session_lang->lang_id;
    }
    protected function sortMenu($menus){
    	$menus_order = Array ( 'home','document','group','other','report','rsvacl','setting');
    	$temp_menu = Array();
    	$menus=array_unique($menus);
    	foreach ($menus_order as $i => $val){
    		foreach ($menus as $k => $v){
    			if($val == $v){
    				$temp_menu[] = $val;
    				unset($menus[$k]);
    				break;
    			}
    		}
    	}
    	return $temp_menu;    	
    }
    public function logoutAction()
    {
        if($this->getRequest()->getParam('value')==1){        	
        	$aut=Zend_Auth::getInstance();
        	$aut->clearIdentity();  
        	$dbgb = new Application_Model_DbTable_DbGlobal();
        	$_datas = array('description'=>'Log out From System');
        	$dbgb->addActivityUser($_datas);
        	
        	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
        	$log=new Application_Model_DbTable_DbUserLog();
			$log->insertLogout($session_user->user_id);
			
        	$session_user->unsetAll();       	
        	Application_Form_FrmMessage::redirectUrl("/index/administrator");
        	exit();
        } 
    }

    public function reloadrAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    		$session_user->timeout= time();
    		print_r(Zend_Json::encode($session_user->timeout));
    		exit();
    	}
    }
    public function errorAction()
    {
    }
    public static function start(){
    	return ($this->getRequest()->getParam('limit_satrt',0));
    }
    function changelangeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$session_lang=new Zend_Session_Namespace('lang');
    		$session_lang->lang_id=$data['lange'];
    		Application_Form_FrmLanguages::getCurrentlanguage($data['lange']);
    		print_r(Zend_Json::encode(2));
    		exit();
    	}
    }
    
    
    public function loginAction()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	
    	$session_user=new Zend_Session_Namespace("authfronts");
    	$username = $session_user->full_name;
    	$user_id = $session_user->user_id;
    	if (!empty($user_id)){
    		$this->_redirect("/index/home");
    	}
    	if($this->getRequest()->isPost())
    	{
    		
    		$formdata=$this->getRequest()->getPost();
    			$session_lang=new Zend_Session_Namespace('lang');
    			$session_lang->lang_id=1;//for creat session
    			Application_Form_FrmLanguages::getCurrentlanguage($session_lang->lang_id);//for choose lang for when login
    			
    			$user_name=$formdata['email'];
    			$password=$formdata['password'];
    			$db_user=new Application_Model_DbTable_DbUsers();
    			if($db_user->userAuthenticate($user_name,$password)){
    					
    				$session_user=new Zend_Session_Namespace("authfronts");
    				$user_id=$db_user->getUserID($user_name);
    				$user_info = $db_user->getUserInfo($user_id);
    					
    				$session_user->user_id=$user_id;
    				$session_user->user_name=$user_name;
    				$session_user->pwd=$password;
    				$session_user->level= $user_info['user_type'];
    				$session_user->full_name= $user_info['full_name'];
    				$session_user->department= $user_info['department'];
    				$session_user->lock();
    					
    				$log=new Application_Model_DbTable_DbUserLog();
    				$log->insertLogin($user_id);
    				Application_Form_FrmMessage::redirectUrl("/index/home");
    				exit();
    			}
    			else{
    				$this->view->msg = $tr->translate("USER_NAME_PASSORD_WRONG");
    			}
    	}
    }
    public function changepasswordAction()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	// action body
    	if ($this->getRequest()->isPost()){
    		$session_user=new Zend_Session_Namespace("authfronts");
    		$pass_data=$this->getRequest()->getPost();
    		if ($pass_data['password'] == $session_user->pwd){
    			 
    			$db_user = new Application_Model_DbTable_DbUsers();
    			try {
    				$db_user->changePassword($pass_data['new_password'], $session_user->user_id);
    				$session_user->unlock();
    				$session_user->pwd=$pass_data['new_password'];
    				$session_user->lock();
    				Application_Form_FrmMessage::Sucessfull($tr->translate("CHANGING_SUCCESSFULLY"), "/index/home");
    			} catch (Exception $e) {
    				Application_Form_FrmMessage::message($tr->translate("CHANGING_FAIL"));
    				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			}
    		}
    		else{
    			$this->view->msg = $tr->translate("CHANGING_FAIL");
    		}
    	}
    }
    public function logoutsAction()
    {
    		$aut=Zend_Auth::getInstance();
    		$aut->clearIdentity();
    		$session_user=new Zend_Session_Namespace("authfronts");
    		$session_user->unsetAll();
    		Application_Form_FrmMessage::redirectUrl("/index/login");
    		exit();
    }
    public function homeAction()
    {
    }
    public function scanViewAction()
    {
    }
    public function scanReceiveAction()
    {
    }
    public function scanCloseAction()
    {
    }
    
    public function scanningAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$document = empty($data['keyword'])?0:$data['keyword'];
    		$scantype = empty($data['scantype'])?0:$data['scantype'];
    		$dbdoc = new Application_Model_DbTable_DbFront();
    		
    		$check = $dbdoc->getDocumentyByQr($document,1);
    		if (!empty($check)){
	    		$row = $dbdoc->getDocumentyByQr($document);
	    		$arr = array('document_id'=>$row['id'],'scan_type'=>$scantype,'doc_processing'=>$row['status']);
	    		if ($scantype==1 || $scantype==3){
	    			$dbdoc->isertScanDocument($arr);
					$return =1;
	    		}
	    		
    		}else{
    			$return =0;
    		}
    		print_r(Zend_Json::encode($return));
    		exit();
    	}
    }
    public function viewAction()
    {
    	
    	$document = $this->getRequest()->getParam("document");
    	$document = empty($document)?0:$document;
    	$db = new Application_Model_DbTable_DbFront();
    	$row = $db->getDocumentyByQr($document);
    	$this->view->row=$row;
    	if (empty($row)){
    		Application_Form_FrmMessage::Sucessfull('NO_RECORD', "/index/home");
    		exit();
    	}
    	$this->view->scanRecord = $db->getScanDocumentByDocId($row['id']);
    	
    }
    public function documentAction()
    {
    	$db = new Application_Model_DbTable_DbFront();
    	$row = $db->getDocumentInHand();
    	$this->view->row=$row;
    }
    public function commentAction()
    {
    	
    	$scan = $this->getRequest()->getParam("scan");
    	$scan = empty($scan)?0:$scan;
    	$db = new Application_Model_DbTable_DbFront();
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$db->isertCommentScanDocument($_data);
    		Application_Form_FrmMessage::Sucessfull('SUCCESS', "/index/home");
    	}
    	$row = $db->getScanDocumentyById($scan);
    	$this->view->row=$row;
    	if (empty($row)){
    		Application_Form_FrmMessage::Sucessfull('NO_RECORD', "/index/home");
    		exit();
    	}
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$this->view->opt = $dbgb->getVewOptoinTypeByType(5);
    }
    public function reportAction()
    {
    	
    	if($this->getRequest()->isPost()){
    		$search=$this->getRequest()->getPost();
    	}else{
    		$search = array(
    				'search'=>"",
    				'document_type'=>0,
    				'from_dept'=>0,
    				'start_date'=> date('Y-m-d'),
    				'end_date'=>date('Y-m-d'));
    	}
    	$this->view->search = $search;
    	$db = new Application_Model_DbTable_DbFront();
    	$row = $db->getDocumentScanHistory($search);
    	$this->view->row=$row;
    	
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$department = $dbgb->getAllDepartment();
		array_unshift($department, array('id'=>0,'name'=>$tr->translate('SELECT_FROM_DEPARTMENT')));
		$this->view->opt_department = $department;
		
		$doc_type = $dbgb->getAllDocumentType();
		array_unshift($doc_type, array('id'=>0,'name'=>$tr->translate('SELECT_DOCUMENT_TYPE')));
		$this->view->opt_doctype = $doc_type;
    }
}