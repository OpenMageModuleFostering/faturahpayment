<?php
/****************************************************************
 * 
 * @Company Name: Bluethink IT Consulting
 * @Author: BT Team
 * @Date: 23 August 2014
 * @Description: Used for process faturah payment
 * @Support : http://ticket.bluethink.in/
 *  
 ***************************************************************/
 
class BT_Faturahpayment_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {		
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    
    public function errorAction()
    {		
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    
     public function redirectAction()
    {
		$session = Mage::getSingleton('checkout/session');
		$session->setBilldeskStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('faturahpayment/standard_redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }
    
    
    public function callbackprocessAction(){
	  
	try {

	
	$strReturnedToken = $_GET["token"];
	$strTransactionCode = '';
	
	
	if(isset($_GET["ignore"]) && $_GET["ignore"] == 0) 
	{
	    	
		if(isset($_GET["Response"]) && !empty($_GET["Response"]) && strtolower($_GET["Response"]) == "0") //**** Error happened
		{
			//*** Handle error messages & update my system that there is an error
			if(isset($_GET["ResponseText"]) && !empty($_GET["ResponseText"]) != null)
			{
				switch(strtolower($_GET["ResponseText"]))
				{
					case "system error":
						//*******************************************
						//**** Handle this error case into my System
						//*******************************************
						
						$this->_redirect('checkout/onepage/failure');
						
						break;
					
					case "invalid configuration":
						//*******************************************
						//**** Handle this error case into my System
						//*******************************************
						$this->_redirect('checkout/onepage/failure');
						
						break;
					
					case "merchant code(mc) is not found":
						//*******************************************
						//**** Handle this error case into my System
						//*******************************************
						$this->_redirect('checkout/onepage/failure');
						break;
					
					case "...": //**** Repeat case for all other error messages and as per your logic
						//**************************************
						//**** Handle error case into my System
						//**************************************
						$this->_redirect('checkout/onepage/failure');
						break;
				}
			}
		}else //*** No error happened
		{
			//*** Initaialize
			$strTransactionCode = $_GET["code"];
						
			//*** Handle returned status message                       
			if(isset($_GET["status"]) && !empty($_GET["status"]))
			{
				switch(strtolower($_GET["status"]))
				{
					case '15': //**** Transaction is accepted by bank
						//*******************************************
						//**** Handle this status case into my System
						//*******************************************
						$this->_redirect('checkout/onepage/success');
						break;
					
					case '30': //**** Transaction is approved/accepted by Faturah Team
						//*******************************************
						//**** Handle this status case into my System
						//*******************************************
						$this->_redirect('checkout/onepage/success');
						break;
					
					case '22': //**** Transaction is rejected
						//*******************************************
						//**** Handle this status case into my System
						//*******************************************
						$this->_redirect('checkout/onepage/failure');
						break;
					
					case '6': //**** Transaction is NOT approved/accepted by Faturah Team
						//*******************************************
						//**** Handle this status case into my System
						//*******************************************
						$this->_redirect('checkout/onepage/failure');
						break;
					
					case '1': //**** Transaction is under processing on the bank
						//*******************************************
						//**** Handle this status case into my System
						//*******************************************
						$this->_redirect('checkout/onepage/success');
						break;
					
					case '18': //**** Transaction is under processing on Faturah Team for approval/acceptance
						//*******************************************
						//**** Handle this status case into my System
						//*******************************************
						
						
						$this->_redirect('checkout/onepage/success');

						break;
				}
			}
		}
	}//*****************************************************
//*** Second handle second message come from Faturah
	//*** Only display message to buyer with this trsnaction status
	//*****************************************************
	else //*** This is second message comes from faturah
	{
		//**** Check if there is error or not
		if(isset($_GET["Response"]) && !empty($_GET["Response"]) && strtolower($_GET["Response"]) == "0") //**** Error happened
		{
			//*** Handle error messages & update my system that there is an error
			if(isset($_GET["ResponseText"]) && !empty($_GET["ResponseText"]))
			{
				//**** Display Error message to buyer
				if(isset($_GET["lang"]) && !empty($_GET["lang"]) && strtolower($_GET["lang"]) == "ar")
					print("حدث خطأ اثناء العملية!");
				else
					print("An error occured while handle this payment!");
			}
		}else //*** No error happened
		{
			//*** Initaialize
			$strTransactionCode = $_GET["code"]; 
		
			//*** Display status message based on returned status message                       
			if(isset($_GET["status"]) && !empty($_GET["status"]))
			{
				switch($_GET["status"])
				{
					case '15': //**** Transaction is accepted by bank
						//**** Display Error message to buyer
						if(isset($_GET["lang"]) && !empty($_GET["lang"]) && strtolower(
								$_GET["lang"]) == "ar")
							print("العملية بكود: " . $strTransactionCode . " قبلت من البنك");
						else
							print("Your transaction with code: " . $strTransactionCode . " is accepted by bank");
						break;
					
					case '30': //**** Transaction is approved/accepted by Faturah Team
						//**** Display Error message to buyer
						if(isset($_GET["lang"]) && !empty($_GET["lang"]) && strtolower(
								$_GET["lang"]) == "ar")
							print("العملية بكود: " . $strTransactionCode . " قبلت من فاتورة");
						else
							print
									("Your transaction with code: " . $strTransactionCode . " is approved/accepted by Faturah Team");
						break;
					
					case '22': //**** Transaction is rejected
						//**** Display Error message to buyer
						if(isset($_GET["lang"]) && !empty($_GET["lang"]) && strtolower(
								$_GET["lang"]) == "ar")
							print("العملية بكود: " . $strTransactionCode . " رفضت");
						else
							print("Your transaction with code: " . $strTransactionCode . " is rejected");
						break;
					
					case '6': //**** Transaction is NOT approved/accepted by Faturah Team
						//**** Display Error message to buyer
						if(isset($_GET["lang"]) && !empty($_GET["lang"]) && strtolower(
								$_GET["lang"]) == "ar")
							print("العملية بكود: " . $strTransactionCode . " رفضت من فاتورة");
						else
							print
									("Your transaction with code: " . $strTransactionCode . " is NOT approved/accepted by Faturah Team");
						break;
					
					case '1': //**** Transaction is under processing on the bank
						//**** Display Error message to buyer
						if(isset($_GET["lang"]) && !empty($_GET["lang"]) && strtolower(
								$_GET["lang"]) == "ar")
							print("العملية بكود: " . $strTransactionCode . " العملية قيد التنفيذ وتحت المراجعة من البنك");
						else
							
							print(
									"Your transaction with code: " . $strTransactionCode . " is under processing on the bank");
						break;
					
					case '18': //**** Transaction is under processing on Faturah Team for approval/acceptance
						//**** Display Error message to buyer
						if(isset($_GET["lang"]) && !empty($_GET["lang"]) && strtolower(
								$_GET["lang"]) == "ar")
							print("العملية بكود: " . $strTransactionCode . " العملية قيد التنفيذ");
						else
							print("Your transaction with code: " . $strTransactionCode . " is under processing on Faturah Team for approval/acceptance");
						break;
				}
			}
		}
	}
}catch(Exception $ex)
{
	print("An Error Occured!");
}
  
	  
	}
    
    
}
