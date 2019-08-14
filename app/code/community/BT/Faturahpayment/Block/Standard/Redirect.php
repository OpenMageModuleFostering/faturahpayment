<?php
/****************************************************************
 * 
 * @Company Name: Bluethink IT Consulting
 * @Author: BT Team
 * @Date: 23 August 2014
 * @Description: Redirect on payment gateway.
 * @Support : http://ticket.bluethink.in/
 *  
 ***************************************************************/ 
class BT_faturahpayment_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
	
	
     protected function _toHtml()
    {
        $standard = Mage::getModel('faturahpayment/standard');
        $action = $standard->getToken();        
        $html = '<html><body>';
		$html.= $this->__('You will be redirected to the faturah website in a few seconds.');
		$html .= '<form id ="faturah_standard_checkout" method ="post" action ="'.$action.'" >';
		$html .= '<form>';
		$html.= '<script type="text/javascript">document.getElementById("faturah_standard_checkout").submit();</script>';
        $html.= '</body></html>';
		return $html;
    }
}
