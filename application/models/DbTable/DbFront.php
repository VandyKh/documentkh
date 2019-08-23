<?php

class Application_Model_DbTable_DbFront extends Zend_Db_Table_Abstract
{
	
	public function getUserInfo(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES_FRONT);
		$userName=$session_user->user_name;
		$GetUserId= $session_user->user_id;
		$department = $session_user->department;
		$info = array("user_name"=>$userName,"user_id"=>$GetUserId,"department"=>$department);
		return $info;
	}
	
	public function getDocumentyByQr($qr,$check=null){
		$db = $this->getAdapter();
		$this->_name="dt_document";
		$sql=" SELECT d.*,
		(SELECT dt.title FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentType,
		(SELECT dt.code FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentTypeCode,
		(SELECT dp.title FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDept,
		(SELECT dp.code FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDeptCode,
		(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = d.status AND v.type = 5 LIMIT 1) AS proccess
		FROM $this->_name AS d  WHERE
		d.serial_code = ".$db->quote($qr)."";
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		if (!empty($check)){
			if (!empty($row)){
				return true;
			}else{
				return false;
			}
		}else {
			return $row;
		}
	}
	
	public function getScanDocumentByDocId($id){
		$db = $this->getAdapter();
		$this->_name="dt_scan_document";
		$sql=" SELECT sd.*,
		(SELECT dp.title FROM `dt_deptarment` AS dp WHERE dp.id = sd.department_scanner LIMIT 1) department,
		(SELECT dp.code FROM `dt_deptarment` AS dp WHERE dp.id = sd.department_scanner LIMIT 1) departmentCode,
		(SELECT u.full_name FROM `rms_users` AS u WHERE u.id = sd.scan_by LIMIT 1) AS scanBy,
		(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = sd.scan_type AND v.type = 4 LIMIT 1) AS scanType,
		(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = sd.doc_processing AND v.type = 5 LIMIT 1) AS proccess
		 FROM $this->_name AS sd
		 WHERE sd.document_id = ".$db->quote($id)."";
		$sql.=" ORDER BY sd.id DESC";
		$row=$db->fetchAll($sql);
		return $row;
	}
	function getDocumentInHand(){
		$db = $this->getAdapter();
		
		$user = $this->getUserInfo();
		
		$sql=" SELECT d.*,
		sd.comment,
		sd.create_date as scanDate,
		(SELECT dt.title FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentType,
		(SELECT dt.code FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentTypeCode,
		(SELECT dp.title FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDept,
		(SELECT dp.code FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDeptCode,
		(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = sd.scan_type AND v.type = 4 LIMIT 1) AS scanType,
		(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = d.status AND v.type = 5 LIMIT 1) AS proccess
		
		 FROM `dt_scan_document` AS sd,
			`dt_document` AS d
			WHERE d.id = sd.document_id 
			AND sd.is_active = 1";
		$sql.=" AND sd.scan_by = ".$user['user_id'];
		$sql.=" ORDER BY sd.id DESC";
		$row=$db->fetchAll($sql);
		return $row;
	}
	
	public function isertScanDocument($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr_old = array(
					"is_active"			=> 0
					);
			$this->_name = "dt_scan_document";
			$where = "document_id = ".$_data['document_id'];
			$this->update($arr_old, $where);
			
			$user = $this->getUserInfo();
			$_arr=array(
					'document_id'	  	=> $_data['document_id'],
					'scan_by'			=> $user['user_id'],
					'department_scanner'=> $user['department'],
					'comment'	  		=> "",
					'doc_processing'	=> $_data['doc_processing'],
					'scan_type'	 		=> $_data['scan_type'],
					'create_date'	 	=> date('Y-m-d H:i:s'),
					'modify_date' 		=> date('Y-m-d H:i:s'),
					'status'	  		=> 1,
					"is_active"			=> 1
			);
			$this->_name = "dt_scan_document";
			$id =  $this->insert($_arr);
			$db->commit();
			return $id;
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}
?>