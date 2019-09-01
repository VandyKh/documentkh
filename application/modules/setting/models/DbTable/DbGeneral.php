<?php

class Setting_Model_DbTable_DbGeneral extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_setting';
    
    public function geLabelByKeyName($keyName){
    	$db = $this->getAdapter();
    	$sql = " SELECT s.`code`,s.keyName,s.keyValue 
				FROM `rms_setting` AS s
				WHERE s.status=1 
				AND s.`keyName` ='$keyName' LIMIT 1";
    	return $db->fetchRow($sql);
    }
	public function updateWebsitesetting($data){
		try{
			$dbg = new Application_Model_DbTable_DbGlobal();
			
			$arr = array('keyValue'=>$data['client_company_name'],);
			$where=" keyName= 'client_company_name'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['footer_branch'],);
			$where=" keyName= 'footer_branch'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['telClient'],);
			$where=" keyName= 'tel-client'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['email_client'],	);
			$where=" keyName= 'email_client'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['website'],	);
			$where=" keyName= 'website'";
			$this->update($arr, $where);
			
			$rows = $this->geLabelByKeyName('amount_alertday');
			if (empty($rows)){
				$arr = array('keyValue'=>$data['amount_alertday'],'keyName'=>"amount_alertday",'user_id'=>$dbg->getUserId());
				$this->insert($arr);
			}else{
				$arr = array('keyValue'=>$data['amount_alertday'],);
				$where=" keyName= 'amount_alertday'";
				$this->update($arr, $where);
			}
			
			$part= PUBLIC_PATH.'/images/photo/logo/';
			if (!file_exists($part)) {
				mkdir($part, 0777, true);
			}
			$photo_name = $_FILES['photo']['name'];
			if (!empty($photo_name)){
				$tem =explode(".", $photo_name);
				$image_name_stu = "profile_".date("Y").date("m").date("d").time().".".end($tem);
				$tmp = $_FILES['photo']['tmp_name'];
				if(move_uploaded_file($tmp, $part.$image_name_stu)){
					move_uploaded_file($tmp, $part.$image_name_stu);
					$photo = $image_name_stu;
					
					$arr = array('keyValue'=>$photo,);
					$where=" keyName= 'logo'";
					$this->update($arr, $where);
				}
			}
			
			$part= PUBLIC_PATH.'/images/photo/print/';
			if (!file_exists($part)) {
				mkdir($part, 0777, true);
			}
			$photo_name = $_FILES['photo_print']['name'];
			if (!empty($photo_name)){
				$tem =explode(".", $photo_name);
				$image_name_stu = "background_".date("Y").date("m").date("d").time().".".end($tem);
				$tmp = $_FILES['photo_print']['tmp_name'];
				if(move_uploaded_file($tmp, $part.$image_name_stu)){
					move_uploaded_file($tmp, $part.$image_name_stu);
					$photo = $image_name_stu;
						
					$arr = array('keyValue'=>$photo,);
					$where=" keyName= 'background'";
					$this->update($arr, $where);
				}
			}
			
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
}

