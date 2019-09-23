<?php

class Application_Model_DbTable_DbFront extends Zend_Db_Table_Abstract
{
	
	public static function getFrontUserId(){
		$session_user=new Zend_Session_Namespace("authfronts");
		return $session_user->user_id;
	}
	public function getUserInfo(){
		$session_user=new Zend_Session_Namespace("authfronts");
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
		if (!empty($check)){
		$sql.=" AND d.status IN (1,2) ";
		}
		
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
		$user_id = empty($user['user_id'])?0:$user['user_id'];
		$sql=" SELECT d.*,
		sd.id as scan_id,
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
		$sql.=" AND sd.scan_by = ".$user_id;
		$sql.=" ORDER BY sd.id DESC";
		$row=$db->fetchAll($sql);
		return $row;
	}
	
	function getDocumentScanHistory($search=null){
		$db = $this->getAdapter();
		$user = $this->getUserInfo();
		$user_id = empty($user['user_id'])?0:$user['user_id'];
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
		";
		$sql.=" AND sd.scan_by = ".$user_id;
		
		$from_date =(empty($search['start_date']))? '1': "sd.create_date >= '".date("Y-m-d",strtotime($search['start_date']))." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "sd.create_date <= '".date("Y-m-d",strtotime($search['end_date']))." 23:59:59'";
		$sql.= " AND ".$from_date." AND ".$to_date;
		
		if(!empty($search['search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['search']));
			$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
			$s_where[] = " REPLACE(d.subject,' ','') LIKE '%{$s_search}%'";
			$s_where[] = " REPLACE(d.ministry_admin_no,' ','') LIKE '%{$s_search}%'";
			$s_where[] = " REPLACE(d.department_admin_no,' ','') LIKE '%{$s_search}%'";
			$s_where[] = " REPLACE(sd.comment,' ','')  LIKE '%{$s_search}%'";
		
			$sql .=' AND ('.implode(' OR ',$s_where).')';
		}
		$dbgb = new Application_Model_DbTable_DbGlobal();
		if(!empty($search['document_type'])){
			$condiction = $dbgb->getChildDocType($search['document_type']);
			if (!empty($condiction)){
				$sql.=" AND d.document_type IN ($condiction)";
			}else{
				$sql.=" AND d.document_type=".$search['document_type'];
			}
		}
		if(!empty($search['from_dept'])){
			$condiction = $dbgb->getChildDept($search['from_dept']);
			if (!empty($condiction)){
				$sql.=" AND d.from_dept IN ($condiction)";
			}else{
				$sql.=" AND d.from_dept=".$search['from_dept'];
			}
		}
		
		$sql.=" ORDER BY sd.id DESC";
		$row=$db->fetchAll($sql);
		return $row;
	}
	
	public function isertScanDocument($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$checkScan = $this->getScanDocumentByTime($_data['document_id']);
			if(empty($checkScan)){
				$arr_old = array(
						"is_active"			=> 0
						);
				$this->_name = "dt_scan_document";
				$where = "document_id = ".$_data['document_id'];
				$this->update($arr_old, $where);
				
				$user = $this->getUserInfo();
				$user_id = empty($user['user_id'])?0:$user['user_id'];
				$_arr=array(
						'document_id'	  	=> $_data['document_id'],
						'scan_by'			=> $user_id,
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
			}
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function getScanDocumentByTime($document_id){
		$db = $this->getAdapter();
		$date = date("Y-m-d H:i");
		$sql='SELECT DATE_FORMAT(sc.create_date, "%Y-%m-%d %H:%i"),sc.* FROM `dt_scan_document` AS sc WHERE 1
			AND DATE_FORMAT(sc.create_date, "%Y-%m-%d %H:%i") = "'.$date.'" 
			AND sc.document_id =$document_id
			LIMIT 1';
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getScanDocumentyById($scan){
		$db = $this->getAdapter();
		$sql=" SELECT sd.*,
					d.subject,
					d.ministry_admin_no,
					d.department_admin_no,
					d.serial_code,
					(SELECT dt.title FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentType,
					(SELECT dt.code FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentTypeCode,
					(SELECT dp.title FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDept,
					(SELECT dp.code FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDeptCode
			 FROM `dt_scan_document` AS sd,
			 	  `dt_document` AS d
			 WHERE 
				  d.id = sd.document_id
				  AND sd.is_active =1
			AND sd.id =".$db->quote($scan)."";
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	
	public function isertCommentScanDocument($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$id = $_data['scan'];
			$arr_old = array(
					"comment"			=> $_data['comment'],
					"doc_processing"	=> $_data['process']
			);
			$this->_name = "dt_scan_document";
			$where = "id = ".$db->quote($id);
			$this->update($arr_old, $where);
			
			$arr_ = array(
					"status"	=> $_data['process']
			);
			$this->_name = "dt_document";
			$where_1 = " serial_code = ".$db->quote($_data['document']);
			$this->update($arr_, $where_1);
			
			$db->commit();
			return $id;
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}
?>