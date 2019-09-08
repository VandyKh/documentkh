<?php

class Application_Form_FrmPopupGlobal extends Zend_Dojo_Form
{
	public function init()
	{
		
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
	
	
	function printFormDocument(){
		
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		
		$key = new Application_Model_DbTable_DbKeycode();
		$datasetting =$key->getKeyCodeMiniInv(TRUE);
		$printLogo = $baseUrl."/public"."/images/photo/print/sample-print.jpg";
		if (!empty($datasetting['background'])){
			if (file_exists(PUBLIC_PATH."/images/photo/print/".$datasetting['background'])){
				$printLogo = $baseUrl."/public"."/images/photo/print/".$datasetting['background'];
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
				    top: 164px;
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
    top: 142px;
    right: 0;
    min-width: 242px;
    text-align: left;
				}
				div#lbDay, div#lbMonth, div#lbYear {
				    position: absolute;
				    top: 146px;
				    text-align: center;
				}
				div#lbMonth {
					top: 142px;
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