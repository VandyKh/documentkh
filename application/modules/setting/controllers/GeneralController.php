<?php
class Setting_generalController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$id = $this->getRequest()->getParam("id");
		$db_gs = new Setting_Model_DbTable_DbGeneral();
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$db_gs->updateWebsitesetting($data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/setting/general");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$row =array();
		$row['client_company_name'] = $db_gs->geLabelByKeyName('client_company_name');
		$row['website'] = $db_gs->geLabelByKeyName('website');
		
		$row['footer_branch'] = $db_gs->geLabelByKeyName('footer_branch');
		$row['tel-client'] = $db_gs->geLabelByKeyName('tel-client');
		$row['email_client'] = $db_gs->geLabelByKeyName('email_client');
		
		$row['amount_alertday'] = $db_gs->geLabelByKeyName('amount_alertday');
		
		$this->view->logo = $db_gs->geLabelByKeyName('logo');
		
		$fm = new Setting_Form_FrmGeneral();
		$frm = $fm->FrmGeneral($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_general = $frm;
	}
	function refreshAction(){
		
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$param = $this->getRequest()->getParam("channy");
				$param = empty($param)?"":$param;
				$type=0;
				if (!empty($data['type_fomate'])){
					$type=$data['type_fomate'];
				}
				$dbglobal = new Application_Model_DbTable_DbGlobal();
				$return = $dbglobal->testTruncate($type,$param);
				if ($return==-1){
					Application_Form_FrmMessage::Sucessfull("Can not Clear Data", "/setting/general/refresh");
				}else{
					Application_Form_FrmMessage::Sucessfull("SUCCESSFULLY", "/setting/general/refresh");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$fm = new Setting_Form_FrmGeneral();
		$frm = $fm->FrmTruncate();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_general = $frm;
	}
	
}

