<?php
class Other_PeriodController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
	protected $tr;
	protected $typetable;
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$this->typetable=3;//crm_verification
	}
	public function indexAction(){
		try{
			$db = new Other_Model_DbTable_DbPeriod();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'search_status' => -1);
			}
			$rs_rows= $db->getAllOther($search,$this->typetable);
			$list = new Application_Form_Frmtable();
			$collumns = array("TITLE","VALUE","NOTE","STATUS");
			$link=array(
					'module'=>'other','controller'=>'period','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('name_kh'=>$link,'value'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Other_Form_FrmPeriod();
		$frm_co=$frm->FrmaddPeriod();
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
				$db = new Other_Model_DbTable_DbPeriod();
				$db->addOther($_data,$this->typetable);
				if(!empty($_data['save_new'])){
					Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/period/add');
				}else{
					Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/period/index');
				}
			}catch(Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm = new Other_Form_FrmPeriod();
		$frm_co=$frm->FrmaddPeriod();
		Application_Model_Decorator::removeAllDecorator($frm_co);
		$this->view->frm_zone = $frm_co;
    }
    function editAction(){
		$db = new Other_Model_DbTable_DbPeriod();
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
	   			$db->addOther($_data,$this->typetable);
	   			Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'),self::REDIRECT_URL.'/period/index');
	   		}catch(Exception $e){
	   			Application_Form_FrmMessage::message($this->tr->translate('EDIT_FAIL'));
	   			$err =$e->getMessage();
	   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
	   		}
	   	}
	   	$id=$this->getRequest()->getParam('id');
	   	$id = empty($id)?0:$id;
	   	$row = $db->getOtherById($id,$this->typetable);
	   	if(empty($row)){
	   		Application_Form_FrmMessage::Sucessfull($this->tr->translate('NO_RECORD'),self::REDIRECT_URL.'/period/index');
	   		exit();
	   	}
	   	$frm = new Other_Form_FrmPeriod();
	   	$frm_co=$frm->FrmaddPeriod($row);
	   	Application_Model_Decorator::removeAllDecorator($frm_co);
	   	$this->view->frm_zone = $frm_co;
    }
}

