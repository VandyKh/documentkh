<?php

class Application_Form_FrmAdvanceSearch extends Zend_Dojo_Form
{
	protected $tr;
	protected $tvalidate =null;//text validate
	protected $filter=null;
	protected $t_num=null;
	protected $text=null;
	protected $tarea=null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->tarea = 'dijit.form.SimpleTextarea';
	}
	public function AdvanceSearch($data=null,$type=null){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_title = new Zend_Dojo_Form_Element_TextBox('adv_search');
		$_title->setAttribs(array('dojoType'=>$this->text,
				'onkeyup'=>'this.submit()',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")
				));
		$_title->setValue($request->getParam("adv_search"));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside'));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DEACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status"));
		
		$db = new Application_Model_DbTable_DbGlobal(); 
		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch',
				'label'=>'Search'
				
				));
		
		$opt_type=$db->getVewOptoinTypeByType(7,1);
		$type=new Zend_Dojo_Form_Element_FilteringSelect('type');
		$type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>true,
				'class'=>'fullside',
				'autoComplete'=>'false',
				'queryExpr'=>'*${0}*',
		));
		$type->setMultiOptions($opt_type);
		$type->setValue($request->getParam("type"));
		
		$from_date = new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$from_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'placeholder'=>$this->tr->translate("START_DATE"),
				'onchange'=>''));
		$_date = $request->getParam("start_date");
		
		if(empty($_date)){
			//$_date = date("Y-m-d");
		}
		$from_date->setValue($_date);
		
		
		$to_date = new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$to_date->setAttribs(array(
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'placeholder'=>$this->tr->translate("END_DATE"),
				'dojoType'=>'dijit.form.DateTextBox',
				'required'=>'true',
				'class'=>'fullside',
		));
		$_date = $request->getParam("end_date");
		
		if(empty($_date)){
			$_date = date("Y-m-d");
		}
		$to_date->setValue($_date);
		
		$user = new Zend_Dojo_Form_Element_FilteringSelect('user');
		$rows = $db ->getAllUser();
		$options=array(''=>$this->tr->translate("SELECT_USER"));
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['by_user'];
		$user->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>'false',
		));
		$user->setMultiOptions($options);
		$user->setValue($request->getParam('user'));
		
		$_document_type=  new Zend_Dojo_Form_Element_FilteringSelect('document_type');
		$_document_type->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','queryExpr'=>'*${0}*',
				'autoComplete'=>'false',
				'onChange'=>''
		));
		$_parent_opt = array(
				0=>$this->tr->translate("SELECT_DOCUMENT_TYPE"));
		$cate = $db->getAllDocumentType();
		if (!empty($cate)) foreach ($cate as $ct){
			$_parent_opt[$ct['id']]=$ct['name'];
		}
		$_document_type->setMultiOptions($_parent_opt);
		$_document_type->setValue($request->getParam('document_type'));
		
		$_from_dept=  new Zend_Dojo_Form_Element_FilteringSelect('from_dept');
		$_from_dept->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','queryExpr'=>'*${0}*',
				'autoComplete'=>'false',
		));
		$_parent_opt = array(0=>$this->tr->translate("SELECT_FROM_DEPARMENT"));
		$dept = $db->getAllDepartment();
		if (!empty($dept)) foreach ($dept as $ct){
			$_parent_opt[$ct['id']]=$ct['name'];
		}
		$_from_dept->setMultiOptions($_parent_opt);
		$_from_dept->setValue($request->getParam('from_dept'));
		
		
		$_department=  new Zend_Dojo_Form_Element_FilteringSelect('department');
		$_department->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','queryExpr'=>'*${0}*',
				'autoComplete'=>'false',
		));
		$_parent_opt = array(0=>$this->tr->translate("SELECT_DEPARMENT"));
		$dept = $db->getAllDepartment();
		if (!empty($dept)) foreach ($dept as $ct){
			$_parent_opt[$ct['id']]=$ct['name'];
		}
		$_department->setMultiOptions($_parent_opt);
		$_department->setValue($request->getParam('department'));
		
		
		$_scan_type_opt = array(0=>$this->tr->translate("SELECT_SCAN_TYPE"));
		$_scan_type=  new Zend_Dojo_Form_Element_FilteringSelect('scan_type');
		$_scan_type->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside'));
		$_data = $db->getDataFromView(4);
		if (!empty($_data)) foreach ($_data as $ct){
			$_scan_type_opt[$ct['id']]=$ct['name'];
		}
		$_scan_type->setMultiOptions($_scan_type_opt);
		$_scan_type->setValue($request->getParam("scan_type"));
		
		$_doc_process_opt = array(-1=>$this->tr->translate("Select Process"));
		$_doc_process=  new Zend_Dojo_Form_Element_FilteringSelect('doc_process');
		$_doc_process->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside'));
		$_data = $db->getDataFromView(5);
		if (!empty($_data)) foreach ($_data as $ct){
			$_doc_process_opt[$ct['id']]=$ct['name'];
		}
		$_doc_process->setMultiOptions($_doc_process_opt);
		$_doc_process->setValue($request->getParam("doc_process"));
		
		$this->addElements(array(
				$from_date,
				$to_date,
				$type,
				$_title,
				$_status,
				$_btn_search,
				$user,
				$_document_type,
				$_from_dept,
				$_department,
				$_scan_type,
				$_doc_process
			));
		return $this;
	}
	
}