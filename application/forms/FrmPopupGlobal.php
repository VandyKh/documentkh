<?php

class Application_Form_FrmPopupGlobal extends Zend_Dojo_Form
{
	public function init()
	{
		
	}
	public function frmPopupDistrict(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Other_Form_FrmDistrict();
		$frm = $frm->FrmAddDistrict();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_district" >
				<form id="form_district" >';
		$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>'.$tr->translate('DISTRICT_KH').'</td>
						<td>'.$frm->getElement('pop_district_namekh').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate('DISTRICT_ENG').'</td>
						<td>'.$frm->getElement('pop_district_name').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate('PROVINCE').'</td>
						<td>'.$frm->getElement('province_names').'</td>
					</tr>
					
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="'.$tr->translate('GO_SAVE').'" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewDistrict();"/>
						</td>
				    </tr>
				</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	public function frmPopupCommune(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_commune" >
					<form id="form_commune" >';
			$str.='<table style="margin: 0 auto; width:500px;" cellspacing="7">
					<tr>
						<td>'.$tr->translate('COMMUNE_NAME_KH').'</td>
						<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" class="fullside" id="commune_namekh" name="commune_namekh" value="" type="text">'.'</td>
					</tr>
					<tr>
						<td>'.$tr->translate('COMUNE_NAME_EN').'</td>
						<td>'.'<input dojoType="dijit.form.ValidationTextBox" class="fullside" id="commune_nameen" name="commune_nameen" value="" type="text">'.'</td>
					</tr>
					<tr>
						<td></td>
						<td>'.'<input dojoType="dijit.form.TextBox" required="true" class="fullside" id="district_nameen" name="district_nameen" value="" type="hidden">'.'</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="'.$tr->translate('GO_SAVE').'" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewCommune();"/>
						</td>
					</tr>
				</table>';
		$str.='</form></div>
			</div>';
		return $str;
	}
	public function frmPopupVillage(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_village" >
					<form id="form_village" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
		 <script type="dojo/method" event="onSubmit">			
			if(this.validate()) {
				return true;
			} else {
				return false;
			}
        </script>
		';
		$str.='<table style="margin: 0 auto;  width:500px" cellspacing="10">
					    <tr>
							<td>'.$tr->translate("VILLAGE_KH").'</td>
							<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" missingMessage="Invalid Module!" class="fullside" id="village_namekh" name="village_namekh" value="" type="text">'.'</td>
						</tr>
						<tr>
							<td>'.$tr->translate("VILLAGE_NAME").'</td>
							<td>'.'<input dojoType="dijit.form.ValidationTextBox" missingMessage="Invalid Module!" class="fullside" id="village_name" name="village_name" value="" type="text">'.'</td>
						</tr>
						<tr>
							<td>'. $tr->translate("DISPLAY_BY").'</td>
							<td>'.'<select name="display" id="display" dojoType="dijit.form.FilteringSelect" class="fullside">
									    <option value="1" label="áž�áŸ’áž˜áŸ‚ážš">áž�áŸ’áž˜áŸ‚ážš</option>
									    <option value="2" label="English">English</option>
									</select>'.'</td>
						</tr>
						<tr>
							<td>'.'<input dojoType="dijit.form.TextBox" class="fullside" id="province_name" name="province_name" value="" type="hidden">
								<input dojoType="dijit.form.TextBox" id="district_name" name="district_name" value="" type="hidden">
							'.'</td>
							<td>'.'<input dojoType="dijit.form.TextBox" id="commune_name" name="commune_name" value="" type="hidden">'.'</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
											<input type="reset" value="ážŸáŸ†áž¢áž¶áž�" label='.$tr->translate('CLEAR').' dojoType="dijit.form.Button" iconClass="dijitIconClear"/>
											<input type="button" value="save_close" name="save_close" label="'. $tr->translate('SAVE').'" dojoType="dijit.form.Button" 
												iconClass="dijitEditorIcon dijitEditorIconSave" Onclick="addVillage();"  />
							</td>
						</tr>
					</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	
	public function frmPopupLoanTye(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
	
		$fm = new Other_Form_FrmVeiwType();
		$frm = $fm->FrmViewType();
		Application_Model_Decorator::removeAllDecorator($frm);
	
		$str='<div class="dijitHidden">
		<div data-dojo-type="dijit.Dialog"  id="frm_loantype" >
		<form id="form_loantype" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
		<script type="dojo/method" event="onSubmit">
		if(this.validate()) {
		return true;
	} else {
	return false;
	}
	</script>
	';
		$str.='<table style="margin: 0 auto; width: 95%;" cellspacing="10">
		<tr>
		<td>'.$tr->translate("TITLE_KH").'</td>
		<td>'.$frm->getElement('title_kh').'</td>
		</tr>
		<tr>
		<td>'.$tr->translate("TITLE_EN").'</td>
		<td>'.$frm->getElement('title_en').'</td>
		</tr>
		<tr>
		<td>'. $tr->translate("DISPLAY_BY").'</td>
		<td>'.$frm->getElement('display_by').'</td>
		</tr>
		<tr>
		<td>'.$tr->translate("STATUS").'</td>
		<td>'. $frm->getElement('status').'</td>
		</tr>
		<tr>
		<td colspan="2" align="center">
		<input type="reset" value="ážŸáŸ†áž¢áž¶áž�" label='.$tr->translate('CLEAR').' dojoType="dijit.form.Button" iconClass="dijitIconClear"/>
		<input type="button" value="save_close" name="save_close" label="'. $tr->translate('SAVE').'" dojoType="dijit.form.Button"
		iconClass="dijitEditorIcon dijitEditorIconSave" Onclick="addNewloanType();"  />
		</td>
		</tr>
		</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	
	
	function getFooterReport(){
		$key = new Application_Model_DbTable_DbKeycode();
		$data=$key->getKeyCodeMiniInv(TRUE);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<table align="center" width="100%">
				   <tr style="font-size: 14px;">
				        <td style="width:20%;text-align:center;  font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate('APPROVED BY').'</td>
				        <td></td>
				        <td style="width:20%;text-align:center; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate('VERIFYED BY').'</td>
				        <td></td>
				        <td style="width:20%;text-align:center; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate('PREPARE BY').'</td>
				   </tr>';
		$str.='</table>';
		return $str;
	}
	
	public function frmPopupOther(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		
		$frm = new Other_Form_FrmOther();
		$frm=$frm->FrmaddOther();
		Application_Model_Decorator::removeAllDecorator($frm);
		$string='
			<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_datapop" data-dojo-props="title:'."'".$tr->translate("ADD_NEW_DATA")."'".'">
					<form id="form_popup" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
						<div class="card-box">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
				                   <label class="control-label col-md-12 col-sm-12 col-xs-12 title-blog bold" for="first-name">
				                   	<i class="fa fa-hand-o-right" aria-hidden="true"></i> 
				                   	<span id="title_form"></span> 
				                   </label>
				                </div>
								<div class="form-group">
								   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
								   </label>
								   <div class="col-md-7 col-sm-7 col-xs-12">
								   '.$frm->getElement("title").$frm->getElement("type").'
								   </div>
								</div>
								<div class="form-group">
								   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
								   </label>
								   <div class="col-md-7 col-sm-7 col-xs-12">
								   '.$frm->getElement("note").'
								   </div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
										<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button" 
							 	iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addData();"/>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		';
		return $string;
		
	}
	public function frmPopupService(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Group_Form_FrmService();
		$frm=$frm->FrmAddService();
		Application_Model_Decorator::removeAllDecorator($frm);
		$string='
		<div class="dijitHidden">
			<div data-dojo-type="dijit.Dialog"  id="frm_datapop_service" data-dojo-props="title:'."'".$tr->translate("CREATE_SERVICE")."'".'">
				<form id="form_popup_service" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<div class="card-box">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("title_service").'
							   </div>
							</div>
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("note_service").'
							   </div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
									<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button" 
						 	iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addService();"/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		';
		return $string;
	}
	public function frmPopupProduct(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Group_Form_FrmProduct();
		$frm=$frm->FrmAddProduct();
		Application_Model_Decorator::removeAllDecorator($frm);
		$string='
			<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_datapop_product" data-dojo-props="title:'."'".$tr->translate("CREATE_PRODUCT")."'".'">
					<form id="form_popup_product" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
						<div class="card-box">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
								   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
								   </label>
								   <div class="col-md-7 col-sm-7 col-xs-12">
								   	 '.$frm->getElement("title_product").'
								   </div>
								</div>
								<div class="form-group">
								   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
								   </label>
								   <div class="col-md-7 col-sm-7 col-xs-12">
								   '.$frm->getElement("note_product").'
								   </div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
										<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button" 
							 	iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addProduct();"/>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		';
		return $string;
	}
	public function frmPopupStandard(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Group_Form_FrmStandard();
		$frm=$frm->FrmAddStandard();
		Application_Model_Decorator::removeAllDecorator($frm);
		$string = '
		<div class="dijitHidden">
			<div data-dojo-type="dijit.Dialog"  id="frm_datapop_standard" data-dojo-props="title:'."'".$tr->translate("CREATE_STANDARD")."'".'">
				<form id="form_popup_standard" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<div class="card-box">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("title_standard").'
							   </div>
							</div>
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("note_standard").'
							   </div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
									<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button" 
						 	iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addStandard();"/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		
		';
		return $string;
	}
	public function frmPopupMadeBy(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Group_Form_FrmMadeBy();
		$frm=$frm->FrmaddMadeBy();
		Application_Model_Decorator::removeAllDecorator($frm);
		
		$string = '
		<div class="dijitHidden">
			<div data-dojo-type="dijit.Dialog"  id="frm_datapop_madeby" data-dojo-props="title:'."'".$tr->translate("CREATE_MADE_BY")."'".'">
				<form id="form_popup_madeby" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<div class="card-box">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   	'.$frm->getElement("title_madeby").'
							   </div>
							</div>
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("note_madeby").'
							   </div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
									<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button" 
						 	iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addMadeBy();"/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		';
		return $string;
	}
	public function frmPopupStreet(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Other_Form_FrmStreet();
		$frm=$frm->FrmAddStreet();
		Application_Model_Decorator::removeAllDecorator($frm);
		
		$string = '
		<div class="dijitHidden">
			<div data-dojo-type="dijit.Dialog"  id="frm_datapop_street" data-dojo-props="title:'."'".$tr->translate("ADD_STREET")."'".'">
				<form id="form_popup_street" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<div class="card-box">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('STREET_NO').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("str_no").'
							   </div>
							</div>
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('STREET_TITLE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   	'.$frm->getElement("str_title").'
							   </div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
									<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button" 
						 	iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addStreetd();"/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		';
		return $string;
	}
	public function frmPopupZone(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Other_Form_FrmZone();
		$frm=$frm->FrmAddZone();
		Application_Model_Decorator::removeAllDecorator($frm);
		$string = '
		<div class="dijitHidden">
			<div data-dojo-type="dijit.Dialog"  id="frm_datapop_zone" data-dojo-props="title:'."'".$tr->translate("ADD_ZONE")."'".'">
				<form id="form_popup_zone" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<div class="card-box">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("title_zone").'
							   </div>
							</div>
							<div class="form-group">
							   <label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
							   </label>
							   <div class="col-md-7 col-sm-7 col-xs-12">
							   '.$frm->getElement("note_zone").'
							   </div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
									<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button" 
						 	iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addZone();"/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		';
		return $string;
	}
	
	public function frmPopupCate(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Group_Form_FrmCategory();
		$frm=$frm->FrmAddCategory();
		Application_Model_Decorator::removeAllDecorator($frm);
		$string = '
		<div class="dijitHidden">
			<div data-dojo-type="dijit.Dialog"  id="frm_datapop_category" data-dojo-props="title:'."'".$tr->translate("ADD_CATEGORY")."'".'">
				<form id="form_popup_category" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<div class="card-box">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
								</label>
								<div class="col-md-7 col-sm-7 col-xs-12">
								'.$frm->getElement("title_category").'
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
								</label>
								<div class="col-md-7 col-sm-7 col-xs-12">
								'.$frm->getElement("note_category").'
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
							<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button"
							iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addCategory();"/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		';
		return $string;
	}
	
	////////////////////////////////////
	public function frmPopupDocumentType(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Group_Form_FrmCategory();
		$frm=$frm->FrmAddCategory();
		Application_Model_Decorator::removeAllDecorator($frm);
		$string = '
		<div class="dijitHidden">
			<div data-dojo-type="dijit.Dialog"  id="frm_datapop_category" data-dojo-props="title:'."'".$tr->translate("CREATE_DOCUMENT_TYPE")."'".'">
				<form id="form_popup_category" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<div class="card-box">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('PARENT').' :
								</label>
								<div class="col-md-7 col-sm-7 col-xs-12">
								'.$frm->getElement("parent_doc").'
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('CODE').' :
								</label>
								<div class="col-md-7 col-sm-7 col-xs-12">
								'.$frm->getElement("code_doc").'
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('TITLE').' :
								</label>
								<div class="col-md-7 col-sm-7 col-xs-12">
								'.$frm->getElement("title_doc").'
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-5 col-sm-5 col-xs-12" for="first-name">'.$tr->translate('NOTE').' :
								</label>
								<div class="col-md-7 col-sm-7 col-xs-12">
								'.$frm->getElement("note_doc").'
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 border-top mt-20 ptb-10 text-center">
								<input type="button"  label="'.$tr->translate("SAVE").'" dojoType="dijit.form.Button"
								iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addDocumentType();"/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		';
		return $string;
	}
	
	function printFormDocument(){
		
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		
		$key = new Application_Model_DbTable_DbKeycode();
		$datasetting =$key->getKeyCodeMiniInv(TRUE);
		$printLogo = $baseUrl."/images/photo/print/sample-print.jpg";
		if (!empty($datasetting['background'])){
			if (file_exists(PUBLIC_PATH."/images/photo/print/".$datasetting['background'])){
				$printLogo = $baseUrl."/images/photo/print/".$datasetting['background'];
			}
		}
		$string="
			<style>
				.print-bg{
					background-image: url('".$printLogo."');background-size: 100%;
				}
				.qrBlog {
				    position: absolute;
				    top: 470px;
				    left: 32px;
				    width: 85px;
				    height: 85px;
				}
				img.qrImg {
					width: 100%;
					height: auto;
				}
				div#lbSubj {
				    position: absolute;
				    top: 165px;
				    left: 114px;
				    vertical-align: bottom;
				    width: 575px;
				    line-height: 23px;
				}
				div#lbDocNo {
				    position: absolute;
				    top: 146px;
				    left: 95px;
				    width: 230px;
				    text-align: center;
				}
				div#lbFromDept {
				    position: absolute;
				    top: 146px;
				    right: 42px;
				    width: 200px;
				    text-align: center;
				}
				div#lbDay, div#lbMonth, div#lbYear {
				    position: absolute;
				    top: 146px;
				    text-align: center;
				}
				div#lbDay {
				    width: 30px;
				    left: 362px;
				}
				div#lbMonth {
				    width: 47px;
				    left: 406px;
				}
				div#lbYear {
				    width: 60px;
				    left: 464px;
				}
			</style>
			
		";
		$string.='
			<div class="print-bg" style=" font-size: 12px; font-family: '."'Times New Roman'".','."'Khmer OS Battambang'".';  color: #000; width: 21cm; height: 15cm;padding: 0px;margin: 0 auto;position: relative; top:0; margin-top:-18px;" >
				<div id="lbDocNo"></div>
				<div id="lbSubj"></div>
				
				<div id="lbDay"></div>
				<div id="lbMonth"></div>
				<div id="lbYear"></div>
				
				<div id="lbFromDept"></div>
				<div id="qrBlog" class="qrBlog">
					
				</div>
			</div>
		';
		return $string;
	}
}