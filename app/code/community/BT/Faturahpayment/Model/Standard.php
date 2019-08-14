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


class BT_Faturahpayment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
	
   protected $_code = 'faturahpayment';
 
		
	 /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('faturahpayment/index/redirect', array('_secure' => true));
    }

			
	
	 /**
     * Return form field array
     *
     * @return array
     */
	
	
	public function getToken(){
	
	try
			{//*** Initialize
	   $client = new SoapClient('https://Services.faturah.com/TokenGeneratorWS.asmx?wsdl');
	// $client = new SoapClient('TokenGeneratorWS.wsdl'); 
	// From the locally downloaded file	$strMyMerchantCode = "xxxxxxxxxxxxxxxxxxxxxxxxxxxx1001"; //*** Put your merchant Code
	$strMyMerchantCode = $this->getMerchantCode();
	
	$faturahParams = array('GenerateNewMerchantToken'=>array("merchantCode"=>$strMyMerchantCode));
	$result = $client->__soapCall('GenerateNewMerchantToken', $faturahParams);
	$TokenGUID = $result->GenerateNewMerchantTokenResult;
	$returnURL = $this->RedirectBuyerToPaymentGatwayPage($strMyMerchantCode, $TokenGUID);	//*** This Function is illustrated in Appendix III
	return $returnURL ; 
	}catch(Exception $ex)
	{
	
	Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('faturahpayment/index/error'));
	Mage::app()->getResponse()->sendResponse();		
    exit;
    //return;
	
	}

	
	}
	
	function RedirectBuyerToPaymentGatwayPage($strMerchantCode, $TokenGUID)
{
	
	 $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
     $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
	 
	 $sandboxMode = Mage::getStoreConfig('payment/faturahpayment/sandbox_flag');
	 
	 if(!$sandboxMode){
		$FaturahPaymentPageURL = "https://gateway.faturah.com/TransactionRequestHandler.aspx"; 
	}else{
		$FaturahPaymentPageURL = "https://gatewaytest.faturah.com/TransactionRequestHandler.aspx";
	}
	
	
	 $strTransactionPrice = $order->getBaseGrandTotal(); 
	 $shippingAmount = $order->getShippingAmount();
	 $mobile = $order->getShippingAddress()->getTelephone();
	 
	 $email = $order->getCustomerEmail();
	 $customerName = $order->getCustomerName();
	

	//**** Append Mandatory fields to URL
	$FaturahPaymentPageURL .= "?" . "mc=" . $strMerchantCode; //*** Merchant Code
	$FaturahPaymentPageURL .= "&" . "mt=" . $TokenGUID; //*** Token Genrated
	$FaturahPaymentPageURL .= "&" . "dt=" . date('m/d/Y h:i:s A'); //*** Transaction date & time
	$FaturahPaymentPageURL .= "&" . "a=" . $strTransactionPrice; //*** Merchant Code
    
	$items = $order->getAllVisibleItems();
	$count = count($items);
 
        $productIds = '' ;
        $productName = '' ;
        $productQty = '' ;
        $productPrice = '' ;
        foreach ($items as $itemId => $item)
        {
			
							
		     $productIds[] = $item->getId();
		     $productName[] = $item->getName();
		     $productQty[] =  (int) $item->getQtyOrdered();      
             $productPrice[]  = round($item->getPrice(),2);
	    } 
	    
	    $productIdsString = implode("|",$productIds) ;
	    $productPriceString = implode("|",$productPrice);
	    $productQtyString = implode("|",$productQty) ;
	    $productNameString = implode(" | ",$productName) ;
	    
	       
   
	//******************************************
	//*** These parameters should be passed aslo
	//******************************************
	$FaturahPaymentPageURL .= "&" . "ProductID=" .$productIdsString;          
	if(strlen($productNameString) <= 1000) 
	{
		$FaturahPaymentPageURL .= "&" . "ProductName=" . $productNameString; 
	}
	if(strlen($productNameString) <= 1000) //*** Check on lenght of List of products Descriptions
	{
		$FaturahPaymentPageURL .= "&" . "ProductDescription=" . $productNameString; //*** List of products Descriptions
	}
	$FaturahPaymentPageURL .= "&" . "ProductQuantity=" . $productQtyString; //*** List of products Quanitites
	$FaturahPaymentPageURL .= "&" . "ProductPrice=" . $productPriceString; //*** List of products Prices
	$FaturahPaymentPageURL .= "&" . "DeliveryCharge=" . $shippingAmount; //*** Delivery Charge
	$FaturahPaymentPageURL .= "&" . "CustomerName=" . $customerName;
	
	

	//*********************************************************************
	//*** Here you can pass more fields as per your specific configuration
	//*********************************************************************
	$FaturahPaymentPageURL .= "&" . "EMail=" .  $email; //*** Customer Email
	$FaturahPaymentPageURL .= "&" . "lang=" . "en"; //*** Preferred Language (ar=Arabic, en=English) 
	$FaturahPaymentPageURL .= "&" . "PhoneNumber=" . $mobile; //*** Customer Email
	
	return $FaturahPaymentPageURL ;
	
	}

		
		public function  getMerchantCode(){
			 
			 return Mage::getStoreConfig('payment/faturahpayment/merchant_code');
			 
		}
   
		
				 
		 public function getCheckout() 
		{
			return Mage::getSingleton('checkout/session');
		}
		
		
		
		
			
}
