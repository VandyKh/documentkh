<?php
class Home_IndexController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$dbglobal = new Application_Model_DbTable_DbGlobal();
		$userid = $dbglobal->getUserId();
		if (empty($userid)){
			$this->_redirect("/index");
		}
		$db_user=new Application_Model_DbTable_DbUsers();
		$user_info = $db_user->getUserInfo($userid);
		
		$db = new Home_Model_DbTable_DbDashboard();
		//$this->view->client = $db->getAllClient();
		///$this->view->clientActive = $db->getAllClient(1);
		//$this->view->clientDeactive = $db->getAllClient(-1);
	}
	
	
	
	function getalllandAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$action = (!empty($data['action'])?$data['action']:null);
			$propertytype= empty($data['property_type'])?null:$data['property_type'];
			$db = new Home_Model_DbTable_DbDashboard();
			$faculty = $db->getAllLand($data['branch_id'],1,$action,$propertytype);
			print_r(Zend_Json::encode($faculty));
			exit();
		}
	}
}

