<?php

class Application_Model_DbTable_DbGlobal extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		return $session_user->user_id;
	}
	public function getUserInfo(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$userName=$session_user->user_name;
		$GetUserId= $session_user->user_id;
		$level = $session_user->level;
		$location_id = $session_user->branch_id;
		$info = array("user_name"=>$userName,"user_id"=>$GetUserId,"level"=>$level,"branch_id"=>$location_id);
		return $info;
	}
	function currentlang(){
		$session_lang=new Zend_Session_Namespace('lang');
		return $session_lang->lang_id;
	}
	function getUserIP()
	{ // get current ip
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		else
		{
			$ip = $remote;
		}
		return $ip;
	}
	function addActivityUser($_data){
		try{
			$id = $this->getUserId();
			$row = $this->getUserInfo($id);
			$ipaddress = $this->getUserIP();
			$_arr=array(
					'user_id'	  => $id,
					'branch_id'	      => $row['branch_id'],
					'user_name'	      => $row['user_name'],
					'date_time'  => date("Y-m-d H:i:s"),
					'description'=> $_data['description'],
					'activityold'=> empty($_data['activityold'])?"":$_data['activityold'],
					'after_edit_info'=> empty($_data['after_edit_info'])?"":$_data['after_edit_info'],
					'ipaddress'=> empty($ipaddress)?"":$ipaddress,
			);
			$this->_name="rns_user_activity";
			$this->insert($_arr);
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function getLaguage(){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `ln_language` AS l WHERE l.`status`=1 ORDER BY l.ordering ASC";
		return $db->fetchAll($sql);
	}
	public  function caseStatusShowImage($status="status"){
		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		$imgnone='<img src="'.$base_url.'/public/images/icon/cross.png"/>';
		$imgtick='<img src="'.$base_url.'/public/images/icon/apply2.png"/>';
		$string=", CASE
		WHEN  $status = 1 THEN '$imgtick'
		WHEN  $status = 0 THEN '$imgnone'
		END AS status ";
		return $string;
	}
	function monthKh($index=null){
		$month = array("01"=>"មករា","02"=>"កុម្ភៈ","03"=>"មីនា","04"=>"មេសា","05"=>"ឧសភា","06"=>"មិថុនា","07"=>"កក្កដា","08"=>"សីហា","09"=>"កញ្ញា","10"=>"តុលា","11"=>"វិច្ឆិកា","12"=>"ធ្នូ",);
		if (!empty($month[$index])){
			return $month[$index];
		}
		return "";
	}
	function dayKh($index=null){
		$day = array(
				"Mon" =>'ច័ន្ទ',
				"Tue" =>'អង្គារ',
				"Wed" =>'ពុធ',
				"Thu" =>'ព្រហស្បតិ៍',
				"Fri" =>'សុក្រ',
				"Sat" =>'សៅរ៍',
				"Sun" =>'អាទិត្យ',
		);
		if (!empty($day[$index])){
			return $day[$index];
		}
		return "";
	}
	function getNumberInkhmer($number){
		$khmernumber = array("០","១","២","៣","៤","៥","៦","៧","៨","៩");
		$spp = str_split($number);
		$num="";
		foreach ($spp as $ss){
				
			if (!empty($khmernumber[$ss])){
				$num.=$khmernumber[$ss];
			}else{
				$num.=$ss;
			}
		}
		return $num;
	}
	function  getAllBranchByUser(){
		$db = $this->getAdapter();
		$sql = 'select br_id as id,project_name as name from ln_project where status=1 and project_name!="" ORDER BY br_id DESC ';
		return $db->fetchAll($sql);
	}
	function  getAllBranchInfoByID($id){
		$db = $this->getAdapter();
		$sql = "select * from ln_project where 1 and project_name!='' AND br_id = $id ORDER BY br_id DESC ";
		return $db->fetchRow($sql);
	}
	public static function GlobalgetUserId(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		return $session_user->user_id;
	}
	function getAllCustomer(){
		return array();
	}
	
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function getGlobalDb($sql)
  	{
  		$db=$this->getAdapter();
  		$row=$db->fetchAll($sql);  		
  		if(!$row) return NULL;
  		return $row;
  	}
  	public function getGlobalDbRow($sql)
  	{
  		$db=$this->getAdapter();  		
  		$row=$db->fetchRow($sql);
  		if(!$row) return NULL;
  		return $row;
  	}
  	public static function getActionAccess($action)
    {
    	$arr=explode('-', $action);
    	return $arr[0];    	
    }     
    public function isRecordExist($conditions,$tbl_name){
		$db=$this->getAdapter();		
		$sql="SELECT * FROM ".$tbl_name." WHERE ".$conditions." LIMIT 1"; 
		$row= count($db->fetchRow($sql));
		if(!$row) return NULL;
		return $row;	
    }
    /*for select 1 record by id of earch table by using params*/
    public function GetRecordByID($conditions,$tbl_name){
    	$db=$this->getAdapter();
    	$sql="SELECT * FROM ".$tbl_name." WHERE ".$conditions." LIMIT 1";
    	$row = $this->fetchRow($sql);
    	return $row;
    	$row= $db->fetchRow($sql);
    	return $row;
    }
    /**
     * insert record to table $tbl_name
     * @param array $data
     * @param string $tbl_name
     */
    public function addRecord($data,$tbl_name){
    	$this->setName($tbl_name);
    	return $this->insert($data);
    }
    public function updateRecord($data,$id,$tbl_name){
    	$this->setName($tbl_name);
    	$where=$this->getAdapter()->quoteInto('id=?',$id);
    	$this->update($data,$where);    	
    }   
    public function DeleteRecord($tbl_name,$id){
    	$db = $this->getAdapter();
		$sql = "UPDATE ".$tbl_name." SET status=0 WHERE id=".$id;
		return $db->query($sql);
    } 
     public function DeleteData($tbl_name,$where){
    	$db = $this->getAdapter();
		$sql = "DELETE FROM ".$tbl_name.$where;
		return $db->query($sql);
    } 
    public function convertStringToDate($date, $format = "Y-m-d H:i:s")
    {
    	if(empty($date)) return NULL;
    	$time = strtotime($date);
    	return date($format, $time);
    }  
    
	public static function getResultWarning(){
          return array('err'=>1,'msg'=>'មិន​ទាន់​មាន​ទន្និន័យ​នូវ​ឡើយ​ទេ!');	
   }
   /*@author Mok Channy
    * for use session navigetor 
    * */
   public static function SessionNavigetor($name_space,$array=null){
   	$session_name = new Zend_Session_Namespace($name_space);
   	return $session_name;   	
   }
   public function getAllProvince(){
   	$this->_name='ln_province';
   	$sql = " SELECT province_id,(province_kh_name) province_en_name FROM $this->_name WHERE status=1 AND province_en_name!='' ORDER BY province_id DESC";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getAllDistrict(){
   	$this->_name='ln_district';
   	$sql = " SELECT dis_id,pro_id,CONCAT(district_name,'-',district_namekh) district_name FROM $this->_name WHERE status=1 AND district_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getAllDistricts(){
   	$this->_name='ln_district';
   	$sql = " SELECT dis_id AS id,pro_id,CONCAT(district_name,'-',district_namekh) name FROM $this->_name WHERE status=1 AND district_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getCommune(){
   	$this->_name='ln_commune';
   	$sql = " SELECT com_id,com_id AS id,commune_name,CONCAT(commune_name,'-',commune_namekh) AS name,district_id FROM $this->_name WHERE status=1 AND commune_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getVillage(){
   	$this->_name='ln_village';
   	$sql = " SELECT vill_id,vill_id AS id,village_name,CONCAT(village_namekh,'-',village_name) AS name,commune_id FROM $this->_name WHERE status=1 AND village_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   
   
   public function getClientByType($type=null,$client_id=null ,$row=null){
   $this->_name='ln_client';
   $session_lang=new Zend_Session_Namespace('lang');
   $lang_id=$session_lang->lang_id;
   $prvoince_str='province_kh_name';
   $district_str='district_namekh';
   $commune_str ='commune_namekh';
   $village_str='village_namekh';
   if($lang_id!=1){
   	$prvoince_str='province_en_name';
   	$district_str='district_name';
   	$commune_str ='commune_name';
   	$village_str='village_name';
   }
   $where='';
   	$sql = " SELECT client_id,name_en,client_number,
   				(SELECT `ln_village`.$village_str FROM `ln_village` WHERE (`ln_village`.`vill_id` = `ln_client`.`village_id`)) AS `village_name`,
				(SELECT `c`.$commune_str FROM `ln_commune` `c` WHERE (`c`.`com_id` = `ln_client`.`com_id`) LIMIT 1) AS `commune_name`,
				(SELECT `d`.$district_str FROM `ln_district` `d` WHERE (`d`.`dis_id` = `ln_client`.`dis_id`) LIMIT 1) AS `district_name`,
				(SELECT $prvoince_str FROM `ln_province` WHERE province_id= ln_client.pro_id  LIMIT 1) AS province_en_name

   	FROM $this->_name WHERE status=1  ";
   	$db = $this->getAdapter();
   	if($row!=null){
   		if($client_id!=null){ $where.=" AND client_id  =".$client_id ." LIMIT 1";}
   		return $db->fetchRow($sql.$where);
   	}
   	return $db->fetchAll($sql.$where);
   }
   
   public function getAllSituation($id = null){
   	$_status = array(
   			1=>$this->tr->translate("Single"),
   			2=>$this->tr->translate("Married"),
   			3=>$this->tr->translate("Windowed"),
   			4=>$this->tr->translate("Mindowed")
   	);
   	if($id==null)return $_status;
   	else return $_status[$id];
   }
   public function GetAllIDType($id = null){
   	$_status = array(
   			1=>$this->tr->translate("National ID"),
   			2=>$this->tr->translate("Family Book"),
   			3=>$this->tr->translate("Resident Book"),
   			4=>$this->tr->translate("Other")
   	);
   	if($id==null)return $_status;
   	else return $_status[$id];
   }
  
  function countDaysByDate($start,$end){
  	$first_date = strtotime($start);
  	$second_date = strtotime($end);
  	$offset = $second_date-$first_date;
  	return floor($offset/60/60/24);
  
  }
  function getAllUser(){
  	$db=$this->getAdapter();  	 
  	$sql="SELECT id,full_name AS by_user,full_name AS name FROM `rms_users` WHERE active=1 ORDER BY id DESC ";
  	return $db->fetchAll($sql);
  }
  public function getVewOptoinTypeByType($type=null,$option = null,$limit =null,$first_option =null){
  	$db = $this->getAdapter();
  	$tr= Application_Form_FrmLanguages::getCurrentlanguage();
  	$string = "name_kh";
  	if($this->currentlang()==2 OR $this->currentlang()==3){
  		$string = "name_kh";//$string = "name_en";
  	}
  	
  	$sql="SELECT key_code AS id, $string AS name ,displayby FROM `ln_view` WHERE status =1 AND name_kh!='' ";//just concate
  	if($type!=null){
  		$sql.=" AND type = $type ";
  	}
  	if($limit!=null){
  		$sql.=" LIMIT $limit ";
  	}
  	$rows = $db->fetchAll($sql);
  	if($option!=null){
  		$options=array();
  		if($first_option==null){//if don't want to get first select
  			$options=array(''=>$tr->translate("PLEASE_SELECT"));
  		}
  		if(!empty($rows))foreach($rows AS $row){
  			$options[$row['id']]=$row['name'];
  		}
  		return $options;
  	}else{
  		return $rows;
  	}
  }
  
 public function setReportParam($arr_param,$file){
  	$contents = file_get_contents('.'.$file);
  	if($arr_param!=null){
  		foreach($arr_param as $key=>$read){
  			$contents=str_replace('@'.$key, $read, $contents);
  		}
  	}
  	$info=pathinfo($file);
  	$newfile=$info['dirname'].'/_'.$info['basename'];
  	file_put_contents('.'.$newfile, $contents);
  	return $newfile;
  }
  public function getHeadBudgetList($type,$start){
  	$heads=$this->getDibursementInYear($type, $start);
  	$str='<tr>';
  	foreach($heads as $value){
  		$str.='<td class="tdheader">'.$value.'</td>';
  	}
  	return $str.'</tr>';
  }
  public function getSubDaysByPaymentTerm($pay_term,$amount_collect = null){
  	if($pay_term==3){
  		$amount_days =30;
  	}elseif($pay_term==2){
  		$amount_days =7;
  	}else{
  		$amount_days =1;
  	}
  	return $amount_days;//;*$amount_collect;//return all next day collect laon form customer
  }
  public function getSystemSetting($keycode){
  	$db = $this->getAdapter();
  	$sql = "SELECT * FROM `ln_system_setting` WHERE keycode ='".$keycode."'";

  	return $db->fetchRow($sql);
  }
  
	function getAllViewType($opt=null,$filter=null){
  		$db = $this->getAdapter();
  		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
	  	$sql ="SELECT * FROM `ln_view_type`";
	  	if($filter!=null){
	  		$sql.=" WHERE id=12 OR id=13";
	  	}
	  	$result = $db->fetchAll($sql);
	  	$options=array('-1'=>$tr->translate("SELECT_TYPE"));
	  	if($opt!=null){
	  		if(!empty($result))foreach($result AS $row){
	  			    $options[$row['id']]=$row['name'];
	  		}
	  		return $options;
	  	}else{
	  		return $result;
	  	}
  	
  }

  public function getNewClientIdByBranch($branch_id=null){// by vandy get new client no by branch
  	$this->_name='ln_client';
  	$db = $this->getAdapter();
  	$sql=" SELECT count(id)  FROM $this->_name WHERE 1 LIMIT 1 ";
  	$acc_no = $db->fetchOne($sql);
  	
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  	$prefix="";
  	$pre= "";
  	for($i = $acc_no;$i<6;$i++){
  		$pre.='0';
  	}
  	return $prefix.$pre.$new_acc_no;
  }
  
  
  function testTruncate($type=0,$param=null){
//   	# truncate data from all table
//   	# $sql = "SHOW TABLES IN 1hundred_2011";
//   	# or,
//   	$connection = $this->getAdapter();
//   	$sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE 'db_borey'";
  	
//   	# use the instantiated db connection object from the init.php, to process the query
//   	$tables = $connection ->fetchAll($sql);// fetch_all($sql);
  	
//   	foreach($tables as $table)
//   	{
//   		//echo $table['TABLE_NAME'].'<br/>';
  	
//   		# truncate data from this table
//   		# $sql = "TRUNCATE TABLE `developer_configurations_cms`";
  	
//   		# use the instantiated db connection object from the init.php, to process the query
//   		# $result = $connection -> query($sql);
  	
//   		# truncate data from this table
//   		$sql = "TRUNCATE TABLE `".$table['TABLE_NAME']."`";
  	
//   		# use the instantiated db connection object from the init.php, to process the query
//   		$result = $connection -> query($sql);
//   	}
		if ($param != "123456"){
			return -1;
  		}
	  	$connection = $this->getAdapter();
	  	$arr_table = array();
	  	if ($type==1 || $type==0){
	  		//Property
	  		$arr_table = array_merge($arr_table, array(
	  			'ln_properties',
	  			'ln_properties_type',
	  			'ln_property_price',)
	  				);
	  	}
	  	if ($type==2 || $type==0){
	  		//Customer,Supplier,Staff agency..
	  		$arr_table = array_merge($arr_table, 
	  			array(
	  				'ln_client',
		  			'ln_client_document',
		  			'ln_supplier',
		  			'ln_staff',
		  			'ln_supplier',)
	  		);
	  	}
	  	if ($type==3 || $type==0){
	  		//Investment Module
	  		$arr_table = array_merge($arr_table,
	  				array(
	  			'rms_investment',
	  			'rms_investment_detail',
	  			'rms_investment_detail_broker',
	  			'rms_investor',
	  			'rms_investor_withdraw',
	  			'rms_investor_withdraw_broker',
	  			'rms_investor_withdraw_broker_detail',
	  			'rms_investor_withdraw_detail',)
	  		);
	  	}
	  	
	  	if ($type==4 || $type==0){
	  		//Other Income/Expense
	  		$arr_table = array_merge($arr_table,
	  				array(
		  			'ln_expense',
		  			'ln_income',
		  			'ln_comission',
		  			'ln_otherincome',
		  			'ln_otherincome_detail',
		  			'ln_otherincomepayment',)
	  		);
	  	}
	  	if ($type==5 || $type==0){
	  		//Plong
	  		$arr_table = array_merge($arr_table,
	  				array(
	  				'ln_issueplong',
		  			'ln_processing_plong',
		  			'ln_processing_plong_detail',
		  			'ln_receiveplong',)
	  		);
	  	}
	  	if ($type==6 || $type==0){
	  		//Sale & Payment,Change House/Owner
	  		$arr_table = array_merge($arr_table,
	  				array(
	  				'ln_sale_cancel',
		  			'ln_sale',
		  			'ln_saleschedule',
		  			'ln_client_receipt_money',
		  			'ln_client_receipt_money_detail',
		  			'ln_reschedule',
	  				'ln_change_house',
	  				'ln_change_owner',
	  						)
	  		);
	  	}
	  if (!empty($arr_table)) foreach ($arr_table as $rs){
		  	$sql = "TRUNCATE TABLE `".$rs."`";
		  	# use the instantiated db connection object from the init.php, to process the query
		  	$result = $connection -> query($sql);
	  }
	  return 1;
  }
  
  public function checkSessionExpire()
  {
  	$user_id = $this->getUserId();
  	$tr= Application_Form_FrmLanguages::getCurrentlanguage();
  	if (empty($user_id)){
  		return false;
  	}else{
  		return true;
  	}
  }
  function reloadPageExpireSession(){
  	$url="";
  	$tr= Application_Form_FrmLanguages::getCurrentlanguage();
  	$msg = $tr->translate("Session Expire");
  	echo '<script language="javascript">
  	alert("'.$msg.'");
  	window.location = "'.Zend_Controller_Front::getInstance()->getBaseUrl().$url.'";
  	</script>';
  }
  
  function getAllCategory($parent = 0, $spacing = '', $cate_tree_array = ''){
  	$db = $this->getAdapter();
  	$currentLang = $this->currentlang();
  	$column = "title";
  	if ($currentLang==1){
  		$column = "title";
  	}
  	$sql = "SELECT
	  	id,
	  	$column AS name,
	  	parent
  	FROM crm_category ";
  	$where = " WHERE title!='' AND parent = $parent  AND status =1 ";
  	$order = " ORDER BY id ASC";
  	$rows = $db->fetchAll($sql.$where.$order);
  	if (!is_array($cate_tree_array))
  		$cate_tree_array = array();
  	if (count($rows) > 0) {
  		foreach ($rows as $row){
  			$cate_tree_array[] = array("id" => $row['id'],"parent" => $row['parent'], "name" => $spacing . $row['name']);
  			$cate_tree_array = $this->getAllCategory($row['id'], $spacing . ' - ', $cate_tree_array);
  		}
  	}
  	return $cate_tree_array;
  }
  
  
  //////
  function getAllDocumentType($current_id=null,$parent = 0, $spacing = '', $cate_tree_array = ''){
  	$db = $this->getAdapter();
  	$currentLang = $this->currentlang();
  	$column = "title";
  	if ($currentLang==1){
  		$column = "title";
  	}
  	$sql = "SELECT
  	id,
  	$column AS name,
  	parent
  	FROM dt_document_type ";
  	$where = " WHERE title!='' AND parent = $parent  AND status =1 ";
  	if (!empty($current_id)){
  		$where.=" AND id != $current_id";
  	}
  	$order = " ORDER BY id ASC";
  	$rows = $db->fetchAll($sql.$where.$order);
  	if (!is_array($cate_tree_array)){$cate_tree_array = array();}
  	if (count($rows) > 0) {
	  	foreach ($rows as $row){
		  	$cate_tree_array[] = array("id" => $row['id'],"parent" => $row['parent'], "name" => $spacing . $row['name']);
		  	$cate_tree_array = $this->getAllDocumentType($current_id,$row['id'], $spacing . ' - ', $cate_tree_array);
		}
	}
  	return $cate_tree_array;
  }
  function getAllDepartment($current_id=null,$parent = 0, $spacing = '', $cate_tree_array = ''){
  	$db = $this->getAdapter();
  	$currentLang = $this->currentlang();
  	$column = "title";
  	if ($currentLang==1){
  		$column = "title";
  	}
  	$sql = "SELECT
  				id,
  				CONCAT(COALESCE(code,''),'-',COALESCE($column,'')) AS name,
  				parent
  			FROM 
  				dt_deptarment 
  		";
  	$where = " WHERE title!='' AND parent = $parent  AND status =1 ";
  	if (!empty($current_id)){
  		$where.=" AND id != $current_id";
  	}
  	$order = " ORDER BY id ASC";
  	$rows = $db->fetchAll($sql.$where.$order);
  	if (!is_array($cate_tree_array)){$cate_tree_array = array();}
  	if (count($rows) > 0) {
	  	foreach ($rows as $row){
		  	$cate_tree_array[] = array("id" => $row['id'],"parent" => $row['parent'], "name" => $spacing . $row['name']);
		  	$cate_tree_array = $this->getAllDepartment($current_id,$row['id'], $spacing . ' - ', $cate_tree_array);
	  	}
  	}
  	return $cate_tree_array;
  }
	function getDataFromView($type){
	  	$db = $this->getAdapter();
	  	$currentLang = $this->currentlang();
	  	$column = "name_en";
	  	if ($currentLang==1){
	  		$column = "name_kh";
	  	}
	  	$sql = "SELECT
				  	key_code as id,
				  	$column AS name
				 FROM
				  	ln_view
				 WHERE
				 	status=1
				 	and type = $type	
	  		";
	  	$order = " ORDER BY key_code ASC";
  		return $db->fetchAll($sql.$order);;
  	}
  	
  	public function getAccessPermission($string='department_scanner'){
  		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
  		$department = $session_user->department;
  		$department = empty($department)?0:$department;
  		$departmentList = $this->getChildDept($department);
  		$result="";
  		if(!empty($departmentList)){
  			$level = $session_user->level;
  			if($level==1 ){
  				$result.= "";
  			}
  			else{
  				$result.= " AND $string IN ($departmentList)";
  			}
  		}
  		return $result;
  		 
  	}
  	
  	function getChildDept($id,$idetity=null){
  		$where='';
  		$db = $this->getAdapter();
  		$sql=" SELECT c.`id` FROM `dt_deptarment` AS c WHERE c.`parent` = $id AND c.`status`=1 ";
  		$child = $db->fetchAll($sql);
  		foreach ($child as $va) {
  			if (empty($idetity)){
  				$idetity=$id.",".$va['id'];
  			}else{$idetity=$idetity.",".$va['id'];
  			}
  			$idetity = $this->getChildDept($va['id'],$idetity);
  		}
  		return $idetity;
  	}
  	function getChildDocType($id,$idetity=null){
  		$where='';
  		$db = $this->getAdapter();
  		$sql=" SELECT c.`id` FROM `dt_deptarment` AS c WHERE c.`parent` = $id AND c.`status`=1 ";
  		$child = $db->fetchAll($sql);
  		foreach ($child as $va) {
  			if (empty($idetity)){
  				$idetity=$id.",".$va['id'];
  			}else{$idetity=$idetity.",".$va['id'];
  			}
  			$idetity = $this->getChildDocType($va['id'],$idetity);
  		}
  		return $idetity;
  	}
  	
  	function getAllPeriodOptionSearch($addNew=null){
  		$db = $this->getAdapter();
  		
  		$sql="SELECT DISTINCT v.value AS id,
			v.name_kh AS `name`  FROM `ln_view` AS v WHERE v.type=3
			 ORDER BY v.value ASC ";
  		 
  		$row=  $db->fetchAll($sql);
  		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
  		if (!empty($addNew)){
  			$addNewArr = Array ( -1 => Array (  'id' => -1 ,'name' => $tr->translate('ADD_NEW') ) );
  			$row = array_merge( $addNewArr , $row);
  		}
  		return $row;
  	}
  	
  	function getNotificationPendingScan($limit =null){
  		$db = $this->getAdapter();
  		
  		$key = new Application_Model_DbTable_DbKeycode();
  		$datasetting =$key->getKeyCodeMiniInv(TRUE);
  		$amount_day = empty($datasetting['amount_alertday'])?1:$datasetting['amount_alertday'];
  		$end_date= date('Y-m-d');
  		if ($amount_day>1){
  			$end_date= date('Y-m-d',strtotime("-$amount_day day"));
  		}
  		$sql="
  		
  		SELECT d.*,
			sd.id AS scan_id,
			sd.comment,
			sd.create_date AS scanDate,
			(SELECT dt.title FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentType,
			(SELECT dt.code FROM `dt_document_type` AS dt WHERE dt.id = d.document_type LIMIT 1) documentTypeCode,
			(SELECT dp.title FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDept,
			(SELECT dp.code FROM `dt_deptarment` AS dp WHERE dp.id = d.from_dept LIMIT 1) fromDeptCode,
			
			(SELECT dp.title FROM `dt_deptarment` AS dp WHERE dp.id = sd.department_scanner LIMIT 1) scanDept,
			(SELECT dp.code FROM `dt_deptarment` AS dp WHERE dp.id = sd.department_scanner LIMIT 1) scanDeptCode,

			(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = sd.scan_type AND v.type = 4 LIMIT 1) AS scanType,
			(SELECT v.name_kh FROM `ln_view` AS v WHERE v.key_code = d.status AND v.type = 5 LIMIT 1) AS proccess,
			(SELECT u.full_name FROM rms_users AS u WHERE u.id=sd.scan_by LIMIT 1) AS scanner
		 FROM `dt_scan_document` AS sd,
			`dt_document` AS d
		WHERE d.id = sd.document_id 
			AND sd.is_active = 1
			AND d.status IN (1,2)
  		";
  		
  		$to_date = (empty($end_date))? '1': " sd.create_date <= '".$end_date." 23:59:59'";
  		$sql.= " AND ".$to_date;
  		if (!empty($limit)){
  			$sql.=" LIMIT $limit";
  		}
  		return $db->fetchAll($sql);
  	}
  	
  	function getCountAllNotificationPendingScan(){
  		$db = $this->getAdapter();
  		$key = new Application_Model_DbTable_DbKeycode();
  		$datasetting =$key->getKeyCodeMiniInv(TRUE);
  		$amount_day = empty($datasetting['amount_alertday'])?1:$datasetting['amount_alertday'];
  		$end_date= date('Y-m-d');
  		if ($amount_day>1){
  			$end_date= date('Y-m-d',strtotime("-$amount_day day"));
  		}
  		$sql="
  		SELECT COUNT(d.id) as countss  		
  		FROM `dt_scan_document` AS sd,
  		`dt_document` AS d
  		WHERE d.id = sd.document_id
  		AND sd.is_active = 1
  		AND d.status IN (1,2)
  		";
  		$to_date = (empty($end_date))? '1': " sd.create_date <= '".$end_date." 23:59:59'";
  		$sql.= " AND ".$to_date;
  		$sql.=" LIMIT 1";
  		return $db->fetchOne($sql);
  	}
  
}
?>