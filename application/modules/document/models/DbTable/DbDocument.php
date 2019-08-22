<?php

class Document_Model_DbTable_DbDocument extends Zend_Db_Table_Abstract
{

    protected $_name = 'dt_document';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    }
    
    function getAllDocument($search=null,$parent = 0, $spacing = '', $cate_tree_array = ''){
    	$db = $this->getAdapter();
    	$sql = "SELECT
			    	id,
			    	subject,
			    	ministry_admin_no,
			    	department_admin_no,
			    	(select title from dt_deptarment as d where d.id = from_dept) as from_dept,
			    	(select title from dt_document_type as dt where dt.id = document_type) as document_type,
			    	issue_date,
			    	note,
			    	create_date,
			    	(SELECT full_name FROM rms_users WHERE id=user_id LIMIT 1) As user_name
    		";
    	$dbp = new Application_Model_DbTable_DbGlobal();
    	$sql.=$dbp->caseStatusShowImage("status");
    	$sql.=" FROM $this->_name";
    	
    	$where = " WHERE subject!=''  ";
    	if($search['status']>-1){
    		$where.= " AND status = ".$search['status'];
    	}
    	if($search['from_dept']>0){
    		$where.= " AND from_dept = ".$search['from_dept'];
    	}
    	if($search['document_type']>0){
    		$where.= " AND document_type = ".$search['document_type'];
    	}
    	
    	$from_date =(empty($search['start_date']))? '1': " create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date   = (empty($search['end_date']))? '1': " create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	if(!empty($search['adv_search'])){
    		$s_where = array();
    		$searches = addslashes(trim($search['adv_search']));
    		$s_where[] = " subject LIKE '%{$searches}%'";
    		$s_where[] = " ministry_admin_no LIKE '%{$searches}%'";
    		$s_where[] = " department_admin_no LIKE '%{$searches}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order = " ORDER BY id DESC";
    	return $db->fetchAll($sql.$where.$order);
    }
    
    function generateDocumentCode(){
    	$db = $this->getAdapter();
    	$sql = " SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1";
    	$last = $db->fetchOne($sql);
    	$code =($last+1).date("Y").date("m").date("d").time();
    	return $code;
    }
	public function addDocument($_data){
		$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
			$_arr=array(
					'subject'	  		=> $_data['subject'],
					'ministry_admin_no'	=> $_data['ministry_admin_no'],
					'department_admin_no'=> $_data['department_admin_no'],
					'from_dept'	  		=> $_data['from_dept'],
					'document_type'	  	=> $_data['document_type'],
					'issue_date'	 	=> $_data['issue_date'],
					'note'	 	 		=> $_data['note'],
					'modify_date' 		=> date('Y-m-d H:i:s'),
					'user_id'	  		=> $this->getUserId()
			);
			if(!empty($_data['id'])){
				$id = $_data['id'];
				$_arr['status'] = $_data['status'];
				$where = 'id = '.$id;
				$this->update($_arr, $where);
			}else{
				$_arr['serial_code'] = $this->generateDocumentCode();
				$_arr['status'] = 1;
				$_arr['create_date'] = date('Y-m-d H:i:s');
				$id =  $this->insert($_arr);
			}
			$db->commit();
			
			$record = $this->getDocumentyById($id);
			if (!empty($record['serial_code'])){
			$this->generateQRCode($record['serial_code']);
			}
			return $id;
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
		}
	}
	public function getDocumentyById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name WHERE
			  id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	
	public function CheckTitle($data){
		$db =$this->getAdapter();
		$sql = "SELECT  * FROM `dt_document` WHERE department_admin_no = '".$data['department_admin_no']."' AND from_dept = ".$data['from_dept'] ;
		return $db->fetchRow($sql);
	}	
	function generateQRCode($_serial_code){
		
		$part= PUBLIC_PATH.'/images/photo/qrcode/';
		if (!file_exists($part)) {
			mkdir($part, 0777, true);
		}
		
		$phblicpart = PUBLIC_PATH;
		$errorCorrectionLevel = 'L';
		$matrixPointSize = 4;
		//html PNG location prefix
// 		$PNG_WEB_DIR = $this->baseUrl().'/images/photo/qrcode/';
		$PNG_TEMP_DIR = $phblicpart.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."photo".DIRECTORY_SEPARATOR."qrcode".DIRECTORY_SEPARATOR;
		include $phblicpart.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."phpqrcode".DIRECTORY_SEPARATOR."qrlib.php";
		$filename="";
		if (!empty($_serial_code)){
			$filename = $PNG_TEMP_DIR.md5($_serial_code.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
			if (file_exists($filename)){
		
			}else{
				QRcode::png($_serial_code, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			}
			
			$_arr=array(
					'qrcode_image'	  		=> md5($_serial_code.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png',
			);
			$where = 'serial_code = '.$_serial_code;
			$this->_name ="dt_document";
			$this->update($_arr, $where);
		}
	}
}
