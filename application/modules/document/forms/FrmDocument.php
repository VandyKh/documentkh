<?php 
Class Document_Form_FrmDocument extends Zend_Dojo_Form {
	protected $tr;
	protected $tvalidate ;//text validate
	protected $filter;
	protected $text;
	protected $tarea=null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->tarea = 'dijit.form.SimpleTextarea';
	}
	public function FrmaddDocument($_data=null){
	
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_adv_search = new Zend_Dojo_Form_Element_TextBox('adv_search');
		$_adv_search->setAttribs(array('dojoType'=>$this->text,
				'onkeyup'=>'this.submit()',
				'class'=>"fullside",
				'placeholder'=>$this->tr->translate("SEARCH_ZONE_INFO")
		));
		$_adv_search->setValue($request->getParam("adv_search"));
		
		
		$_status_search=  new Zend_Dojo_Form_Element_FilteringSelect('search_status');
		$_status_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DEACTIVE"));
		$_status_search->setMultiOptions($_status_opt);
		$_status_search->setValue($request->getParam("search_status"));
		
		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch',
		));
		
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$_document_type=  new Zend_Dojo_Form_Element_FilteringSelect('document_type');
		$_document_type->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','queryExpr'=>'*${0}*',
				'autoComplete'=>'false',
				'onChange'=>''
				));
		$_parent_opt = array(
				0=>$this->tr->translate("SELECT_DOCUMENT_TYPE"),-1=>$this->tr->translate("ADD_NEW"));
		$cate = $dbgb->getAllDocumentType();
		if (!empty($cate)) foreach ($cate as $ct){
			$_parent_opt[$ct['id']]=$ct['name'];
		}
		$_document_type->setMultiOptions($_parent_opt);
		
		$_subject = new Zend_Dojo_Form_Element_Textarea('subject');
		$_subject->setAttribs(array('dojoType'=>'dijit.form.Textarea','required'=>'true','class'=>'fullside',));
		
		$_ministry_admin_no= new Zend_Dojo_Form_Element_TextBox('ministry_admin_no');
		$_ministry_admin_no->setAttribs(array('dojoType'=>$this->tvalidate,'required'=>'true','class'=>'fullside',));
		
		$_department_admin_no= new Zend_Dojo_Form_Element_TextBox('department_admin_no');
		$_department_admin_no->setAttribs(array('dojoType'=>$this->tvalidate,'required'=>'true','class'=>'fullside','onKeyUp'=>'checkTitle();'));
		
		$_from_dept=  new Zend_Dojo_Form_Element_FilteringSelect('from_dept');
		$_from_dept->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','queryExpr'=>'*${0}*',
				'autoComplete'=>'false',
				'onChange'=>'getpopupDepartment();'
		));
		$_parent_opt = array(0=>$this->tr->translate("SELECT_FROM_DEPARTMENT"),-1=>$this->tr->translate("ADD_NEW"));
		$dept = $dbgb->getAllDepartment();
		if (!empty($dept)) foreach ($dept as $ct){
			$_parent_opt[$ct['id']]=$ct['name'];
		}
		$_from_dept->setMultiOptions($_parent_opt);
		
		$_note = new Zend_Dojo_Form_Element_Textarea('note');
		$_note->setAttribs(array('dojoType'=>'dijit.form.Textarea','class'=>'fullside',
				'style'=>'width:99%;min-height:50px;'));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside',));
		$_status_opt = array();
		$opt = $dbgb->getVewOptoinTypeByType(5);
		if (!empty($opt)) foreach ($opt as $ct){
			$_status_opt[$ct['id']]=$ct['name'];
		}
		$_status->setMultiOptions($_status_opt);
		$_id = new Zend_Form_Element_Hidden('id');
		
		$issue_date= new Zend_Dojo_Form_Element_DateTextBox('issue_date');
		$issue_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'placeholder'=>$this->tr->translate("END_DATE")
		));
		$date = date("Y-m-d");
		$issue_date->setValue($date);
		
		if(!empty($_data)){
			$_id->setValue($_data['id']);
			$_document_type->setValue($_data['document_type']);
			$_subject->setValue($_data['subject']);
			$_ministry_admin_no->setValue($_data['ministry_admin_no']);
			$_department_admin_no->setValue($_data['department_admin_no']);
			$_from_dept->setValue($_data['from_dept']);
			$_note->setValue($_data['note']);
			$_status->setValue($_data['status']);
			$issue_date->setValue($_data['issue_date']);
		}
		$this->addElements(array($_btn_search,$_status_search,$_adv_search,
				$_id,
				$_document_type,
				$_subject,
				$_ministry_admin_no,
				$_department_admin_no,
				$_from_dept,
				$_note,
				$_status,
				$issue_date
			));
		return $this;
		
	}
	
}