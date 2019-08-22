<?php

class Other_Model_DbTable_DbStreet extends Zend_Db_Table_Abstract
{

    protected $_name = 'crm_street';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    }
	public function addStreet($_data){
		$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
			$_arr=array(
					'str_no'	  => $_data['str_no'],
					'str_title'	  => $_data['str_title'],
					'note'	  => $_data['note'],
					'modify_date' => date('Y-m-d H:i:s'),
					'user_id'	  => $this->getUserId()
			);
			if(!empty($_data['id'])){
				$id = $_data['id'];
				$_arr['status'] = $_data['status'];
				$where = 'id = '.$id;
				$this->update($_arr, $where);
			}else{
				$_arr['status'] = 1;
				$_arr['create_date'] = date('Y-m-d H:i:s');
				$id =  $this->insert($_arr);
			}
			$db->commit();
			return $id;
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
		}
	}
	public function getZoneById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name WHERE
			  id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getAllStreet($search=null){
		$db = $this->getAdapter();
		$sql = "SELECT
				id,
				str_no,
				str_title,
				modify_date
				 ";
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->caseStatusShowImage("status");
		$sql.=",(SELECT full_name FROM rms_users WHERE id=user_id LIMIT 1) As user_name
				FROM $this->_name";
		$where = ' WHERE 1 ';
		if($search['search_status']>-1){
			$where.= " AND status = ".$search['search_status'];
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$search = addslashes(trim($search['adv_search']));
			$s_where[] = " str_no LIKE '%{$search}%'";
			$s_where[] = " str_title LIKE '%{$search}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		$order = " ORDER BY id DESC";
		return $db->fetchAll($sql.$where.$order);	
	}	
}

