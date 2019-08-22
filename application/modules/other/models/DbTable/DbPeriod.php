<?php

class Other_Model_DbTable_DbPeriod extends Zend_Db_Table_Abstract
{

    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    }
    function tableIndex($type=1){
    	if ($type==1){
    		return "crm_other";
    	}else if ($type==2){
    		return "crm_floor";
    	}else if ($type==3){
    		return "crm_line";
    	}else if ($type==4){
    		return "crm_side";
    	}else if ($type==5){
    		return "crm_start_direction";
    	}else if ($type==6){
    		return "crm_verification";
    	}
    }
	public function addOther($_data,$type){
		$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		//$this->_name = $this->tableIndex($type);
    		$this->_name = "ln_view";
    		
			$_arr=array(
					'name_en'	  => $_data['title'],
					'name_kh'	  => $_data['title'],
					'value'	  	  => $_data['value'],
					'type'	  	  => $type,
					'note'	  	  => $_data['note'],
			);
			if(!empty($_data['id'])){
				$id = $_data['id'];
				$_arr['status'] = $_data['status'];
				$where = 'id = '.$id;
				$this->update($_arr, $where);
			}else{
				$key_code = $this->getKeyCode($type);
				$_arr['key_code'] = $key_code;
				$_arr['status'] = 1;
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
	public function getOtherById($id,$type){
		$db = $this->getAdapter();
		//$this->_name = $this->tableIndex($type);
		$this->_name = "ln_view";
		$sql=" SELECT * FROM $this->_name WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getAllOther($search=null,$type){
		//$this->_name = $this->tableIndex($type);
		$this->_name = "ln_view";
		$db = $this->getAdapter();
		$sql = "SELECT
				id,
				name_kh,
				value,
				note
			";
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->caseStatusShowImage("status");
		$sql.=" FROM $this->_name";
				
		$where = " WHERE name_kh != '' and type=$type";
		if($search['search_status']>-1){
			$where.= " AND status = ".$search['search_status'];
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$searches = addslashes(trim($search['adv_search']));
			$s_where[] = " title LIKE '%{$searches}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		$order = " ORDER BY id DESC";
		return $db->fetchAll($sql.$where.$order);	
	}	
	public function getKeyCode($type){
		$db = $this->getAdapter();
		$sql="SELECT key_code FROM ln_view where type=$type ORDER BY key_code DESC LIMIT 1 ";
		$key_code = $db->fetchOne($sql);
		$new_acc_no= (int)$key_code + 1;
		return $new_acc_no;
	}
}

