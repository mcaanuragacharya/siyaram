<?php
namespace BDPay\Billdesk\Controller\Request;

use \Magento\Framework\App\Action\Context;
use BDPay\Billdesk\Model\Billdesk;


class Index extends \Magento\Framework\App\Action\Action 
{
	public function __construct(
	    Context $context,
	   	Billdesk $model
		) 
	{
	    $this->model = $model;

	    parent::__construct($context);
	}
    /**
     * @override
     * @see \Magento\Framework\App\Action\Action::execute()
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute() {
	
		return $this->resultRedirectFactory->create()->setUrl($this->generateUrl());
    }

    public function generateUrl(){
		//echo "generateUrl";
		$baseUrl = "https://pgi.billdesk.com/pgidsk/PGIMerchantPayment";
		$queryData = [
            'command' => 'initiateTransaction',
			'msg' => $this->model->getEncryptedData(),
            'access_code' => $this->model->getAccessCode()
        ];

        return $baseUrl.'?'. http_build_query($queryData);
    }



}