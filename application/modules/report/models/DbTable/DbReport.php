<?php
class Report_Model_DbTable_DbReport extends Zend_Db_Table_Abstract
{
	
	function getAllDocument($search){
		try{
			$db = $this->getAdapter();
			$dbp = new Application_Model_DbTable_DbGlobal();
			$currentLang = $dbp->currentlang();
			$label = "name_en";
			if ($currentLang==1){
				$label = "name_kh";
			}
			$sql="SELECT
						d.*,
						(select title from dt_document_type where dt_document_type.id = d.document_type limit 1) as doc_type,
						(select title from dt_deptarment where dt_deptarment.id = d.from_dept limit 1) as from_department,
						(SELECT full_name FROM rms_users WHERE id=d.user_id LIMIT 1 ) AS user_name,
						(SELECT $label FROM `ln_view` WHERE TYPE =1 AND d.status=key_code LIMIT 1) AS status_label
					from 
						dt_document as d
					where	
						1
				";			
			$from_date =(empty($search['start_date']))? '1': "d.create_date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "d.create_date <= '".$search['end_date']." 23:59:59'";
			$where = " AND ".$from_date." AND ".$to_date;
			
			if(!empty($search['adv_search'])){
				$s_where = array();
				$s_search = addslashes(trim($search['adv_search']));
				$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
				$s_where[] = " d.subject LIKE '%{$s_search}%'";
				$s_where[] = " d.ministry_admin_no LIKE '%{$s_search}%'";
				$s_where[] = " d.department_admin_no LIKE '%{$s_search}%'";
				$s_where[] = " d.serial_code LIKE '%{$s_search}%'";
				$where .=' AND ('.implode(' OR ',$s_where).')';
			}
			$dbgb = new Application_Model_DbTable_DbGlobal();
			if(!empty($search['document_type'])){
				$condiction = $dbgb->getChildDocType($search['document_type']);
				if (!empty($condiction)){
					$where.=" AND d.document_type IN ($condiction)";
				}else{
					$where.=" AND d.document_type=".$search['document_type'];
				}
			}
			if(!empty($search['from_dept'])){
				$condiction = $dbgb->getChildDept($search['from_dept']);
				if (!empty($condiction)){
					$where.=" AND d.from_dept IN ($condiction)";
				}else{
					$where.=" AND d.from_dept=".$search['from_dept'];
				}
			}
			return $db->fetchAll($sql.$where);
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	function getAllScan($search){
		try{
			$db = $this->getAdapter();
			$dbp = new Application_Model_DbTable_DbGlobal();
			$currentLang = $dbp->currentlang();
			$label = "name_en";
			if ($currentLang==1){
				$label = "name_kh";
			}
			$sql="SELECT
						sd.*,
						d.subject,
						(select title from dt_document_type where dt_document_type.id = d.document_type limit 1) as doc_type,
						(select title from dt_deptarment where dt_deptarment.id = d.from_dept limit 1) as from_department,
						(select title from dt_deptarment where dt_deptarment.id = sd.department_scanner limit 1) as department_scanner,
						u.full_name AS scan_by,
						(SELECT $label FROM `ln_view` WHERE TYPE =4 AND sd.scan_type=key_code LIMIT 1) AS scan_type,
						(SELECT $label FROM `ln_view` WHERE TYPE =5 AND sd.doc_processing=key_code LIMIT 1) AS doc_processing
					from
						dt_scan_document as sd,					
						dt_document as d,
						rms_users as u
					where
						sd.document_id = d.id
						and sd.scan_by = u.id
				";
			$from_date =(empty($search['start_date']))? '1': "sd.create_date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "sd.create_date <= '".$search['end_date']." 23:59:59'";
			$where = " AND ".$from_date." AND ".$to_date;

			$dbgb = new Application_Model_DbTable_DbGlobal();
			if(!empty($search['adv_search'])){
				$s_where = array();
				$s_search = addslashes(trim($search['adv_search']));
				$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
				$s_where[] = " d.subject LIKE '%{$s_search}%'";
				$s_where[] = " d.ministry_admin_no LIKE '%{$s_search}%'";
				$s_where[] = " d.department_admin_no LIKE '%{$s_search}%'";
				$s_where[] = " d.serial_code LIKE '%{$s_search}%'";
				$where .=' AND ('.implode(' OR ',$s_where).')';
				}
			if(!empty($search['document_type'])){
				$condiction = $dbgb->getChildDocType($search['document_type']);
				if (!empty($condiction)){
					$where.=" AND d.document_type IN ($condiction)";
				}else{
					$where.=" AND d.document_type=".$search['document_type'];
				}
			}
			if(!empty($search['from_dept'])){
				$condiction = $dbgb->getChildDept($search['from_dept']);
				if (!empty($condiction)){
					$where.=" AND d.from_dept IN ($condiction)";
				}else{
					$where.=" AND d.from_dept=".$search['from_dept'];
				}
			}
			if(!empty($search['scan_type'])){
				$where.=" AND sd.scan_type= ".$search['scan_type'];
			}
			if($search['doc_process']>-1){
				$where.=" AND sd.doc_processing= ".$search['doc_process'];
			}
			$dbgb = new Application_Model_DbTable_DbGlobal();
			$where.=$dbgb->getAccessPermission("sd.department_scanner");
			
			return $db->fetchAll($sql.$where);
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function getDocumentyById($id){
		$db = $this->getAdapter();
		$this->_name="dt_document";
		$sql=" SELECT d.*,
			(SELECT dt.title FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentType,
			(SELECT dt.code FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentTypeCode,
			(SELECT dp.title FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDept,
			(SELECT dp.code FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDeptCode,
			(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = d.status AND v.type = 5 LIMIT 1) AS proccess
			 FROM $this->_name AS d  WHERE
		d.id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
}

