<?php

namespace Conns\Yeslease\Controller\Checkout;
use Magento\Framework\App\Helper\Context;

class Verifylease extends \Magento\Framework\App\Action\Action
{
	/**
     * @var Data
     */
    protected $_dataHelper;
	 /**
     * Verify Lease constructor.
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession,
		\Conns\Yeslease\Helper\Data $dataHelper,
        Context $context
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $customerSession;
		$this->_dataHelper = $dataHelper;
		parent::__construct($context);
    }



    public function execute()
    {
		if($this->getRequest()->isAjax()){
			$returnArr	=	array();
			$errorResponse = false;
			
			if($this->getRequest()->getPost()){
				$leaseNumber	=	$this->getRequest()->getPost('applicant_lease_number');
				$last4ssn		=	$this->getRequest()->getPost('applicant_lastfour_ssn');
				$writer 		= 	new \Zend\Log\Writer\Stream(BP . '/var/log/support_report_progressive.log');
				$logger 		= 	new \Zend\Log\Logger();
				$logger->addWriter($writer);									
				if((strlen(trim($leaseNumber))==6) && (strlen(trim($last4ssn))==4)){
					
					$authResult =	$this->_dataHelper->__maketokenapicall();
					if ($authResult) {
						$authResult = json_decode($authResult);
						if (isset($authResult->access_token)) {
							$resobj = $this->_dataHelper->getVerifyLease($authResult->access_token, $customerID, $leaseNumber, $last4ssn);
							if (!is_string($resobj) || $resobj == "") {
								$errorResponse = true;
							}

							$decodedRespobj = json_decode($resobj);
							if (is_string($resobj) && $resobj != "") {
								if (!is_object($decodedRespobj) && !is_array($decodedRespobj)) {
									$errorResponse = true;
								}

								if (is_array($decodedRespobj)) {
									if (trim($decodedRespobj['code']) != "200" || trim($decodedRespobj['data']) == "" || trim($decodedRespobj['data']) == null) {
										$errorResponse = true;
									}
									if (trim($decodedRespobj['code']) == "204"){
										$returnArr['status']	=	"error";
										$returnArr['msg']		=	"We could not locate your approved lease ID. Please review the information provided and try again.";
										$errorResponse = false;
									}
								}

							}


							if ($errorResponse) {
								if (!empty($decodedRespobj->message)) {
									$returnArr['status']	=	"error";
									$returnArr['error']		= __($decodedRespobj->message);
								}else{
									$returnArr['status']	=	"error";
									$returnArr['msg']		=	"There is some error in processing your request. Please try again.";
								}
								
							}else{
								$resobj = json_decode($resobj);
								if ($resobj->code == '200') {
									$resp = $resobj->data;
									if($resp){
										$leaseStatus	=	isset($resp->Status)? $resp->Status : '';
										$leaseNumber	=	isset($resp->LeaseID)? $resp->LeaseID : '';
										$leaseDetails	=	isset($resp->LeaseDetails)? $resp->LeaseDetails : '';
										if(trim(strtolower($leaseStatus))=="approved"){
											$returnArr['status']	=	"success";
											$returnArr['msg']	=	"Your application has been verified. Your contracts will be presented after you submit the order. Please continue.";
										}
										if(trim(strtolower($leaseStatus))=="contracts"){
											$returnArr['status']	=	"error";
											$returnArr['msg']	=	"The Lease ID entered has already been used on an order. Contact us at 1-866-765-1513 for any questions.";
										}
										if(trim(strtolower($leaseStatus))=="contracts received"){
											$returnArr['status']	=	"error";
											$returnArr['msg']	=	"The Lease ID entered has already been used on an order. Contact us at 1-866-765-1513 for any questions.";
										}
										if($leaseDetails){
											$approvedLimit	=	isset($leaseDetails->ApprovalLimit)? $leaseDetails->ApprovalLimit : '';
										}
									}
								}
							}
						}
						
					}
				
				}else{
					$returnArr['status']	=	"error";
					$returnArr['msg']		=	"Please enter a valid 6 digit lease ID and last 4 digits of your SSN.";
				}
			}else{
				$returnArr['status']	=	"error";
				$returnArr['msg']		=	"Please enter the information requested.";
			}
		}
        
    }
}