<?php

class Home_Model_DbTable_DbDashboard extends Zend_Db_Table_Abstract
{
	public function getAllClient($available=null){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(p.`id`) AS totalProperty 
			FROM `ln_client` AS p 
			WHERE 1  ";
		if (!empty($available)){
			if ($available==-1){ $available = 0;}
			$sql.=" AND p.`status` =$available";
		}
		return $db->fetchOne($sql);
	}
	
	
}