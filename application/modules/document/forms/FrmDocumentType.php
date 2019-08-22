<?php 
Class Document_Form_FrmDocumentType extends Zend_Dojo_Form {
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
	public function FrmaddDocumentType($_data=null){
	
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
		$current_id = empty($_data['id'])?null:$_data['id'];
		$_parent=  new Zend_Dojo_Form_Element_FilteringSelect('parent');
		$_parent->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','queryExpr'=>'*${0}*',
				'autoComplete'=>'false',
				));
		$_parent_opt = array(
				0=>$this->tr->translate("SELECT_DOCUMENT_TYP_PARENT"));
		$cate = $dbgb->getAllDocumentType($current_id);
		if (!empty($cate)) foreach ($cate as $ct){
			$_parent_opt[$ct['id']]=$ct['name'];
		}
		$_parent->setMultiOptions($_parent_opt);
		
		$_title= new Zend_Dojo_Form_Element_TextBox('title');
		$_title->setAttribs(array('dojoType'=>$this->tvalidate,'required'=>'true','class'=>'fullside',));
		
		$_code= new Zend_Dojo_Form_Element_TextBox('code');
		$_code->setAttribs(array('dojoType'=>$this->tvalidate,'required'=>'true','class'=>'fullside',));
		
		$_note = new Zend_Dojo_Form_Element_Textarea('note');
		$_note->setAttribs(array('dojoType'=>'dijit.form.Textarea','class'=>'fullside',
				'style'=>'width:99%;min-height:50px;'));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DEACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_id = new Zend_Form_Element_Hidden('id');
		
		//For Popup Form
		$_parent_doc=  new Zend_Dojo_Form_Element_FilteringSelect('parent_doc');
		$_parent_doc->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','queryExpr'=>'*${0}*',
				'autoComplete'=>'false',
		));
		$_parent_opt = array(
				0=>$this->tr->translate("SELECT_DOCUMENT_TYP_PARENT"));
		$cate = $dbgb->getAllDocumentType($current_id);
		if (!empty($cate)) foreach ($cate as $ct){
			$_parent_opt[$ct['id']]=$ct['name'];
		}
		$_parent_doc->setMultiOptions($_parent_opt);
		
		$_title_doc= new Zend_Dojo_Form_Element_TextBox('title_doc');
		$_title_doc->setAttribs(array('dojoType'=>$this->tvalidate,'required'=>'true','class'=>'fullside',));
		
		$_code_doc= new Zend_Dojo_Form_Element_TextBox('code_doc');
		$_code_doc->setAttribs(array('dojoType'=>$this->tvalidate,'required'=>'true','class'=>'fullside',));
		
		$_note_doc = new Zend_Dojo_Form_Element_Textarea('note_doc');
		$_note_doc->setAttribs(array('dojoType'=>'dijit.form.Textarea','class'=>'fullside',
				'style'=>'width:99%;min-height:50px;'));
		
		if(!empty($_data)){
			$_id->setValue($_data['id']);
			$_parent->setValue($_data['parent']);
			$_title->setValue($_data['title']);
			$_code->setValue($_data['code']);
			$_note->setValue($_data['note']);
			$_status->setValue($_data['status']);
		}
		$this->addElements(array($_btn_search,$_status_search,$_adv_search,
				$_id,
				$_parent,
				$_title,$_code,$_note,$_status,
				
				$_parent_doc,
				$_title_doc,
				$_code_doc,
				$_note_doc
				));
		return $this;
		
	}
	
}