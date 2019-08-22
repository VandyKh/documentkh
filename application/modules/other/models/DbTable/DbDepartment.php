<?php

class Other_Model_DbTable_DbDepartment extends Zend_Db_Table_Abstract
{

    protected $_name = 'dt_deptarment';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    }
	public function addDepartment($_data){
		$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
			$_arr=array(
					'parent'	 	=> $_data['parent'],
					'code'	  		=> $_data['code'],
					'title'	  		=> $_data['title'],
					'note'	  		=> $_data['note'],
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
    		//echo $e->getMessage();
    		$db->rollBack();
		}
	}
	public function getDepartmentById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name WHERE
			  id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getAllDepartment($search=null,$parent = 0, $spacing = '', $cate_tree_array = ''){
		$db = $this->getAdapter();
		$sql = "SELECT
				id,
				code,
				parent,
				title,
				modify_date
				 ";
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->caseStatusShowImage("status");
		$sql.=",(SELECT full_name FROM rms_users WHERE id=user_id LIMIT 1) As user_name
				FROM $this->_name";
		$where = " WHERE title!='' AND parent = $parent ";
		if($search['search_status']>-1){
			$where.= " AND status = ".$search['search_status'];
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$searches = addslashes(trim($search['adv_search']));
			$s_where[] = " title LIKE '%{$searches}%'";
			$s_where[] = " title_kh LIKE '%{$searches}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		$order = " ORDER BY id DESC";
		$rows = $db->fetchAll($sql.$where.$order);
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		if (count($rows) > 0) {
			foreach ($rows as $row){
				$cate_tree_array[] = array("id" => $row['id'],"code" => $row['code'],"parent" => $row['parent'], "title" => $spacing . $row['title'],"modify_date" => $row['modify_date'],"status" => $row['status'],"user_name" => $row['user_name']);
				$cate_tree_array = $this->getAllDepartment($search,$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}	
}

