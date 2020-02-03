<?php
namespace BDPay\Billdesk\Controller\Response;

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
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$messageManager = $objectManager->get('\Magento\Framework\Message\ManagerInterface');
		$session = $objectManager->get('\Magento\Checkout\Model\Session');
		$successUrl = 'checkout/onepage/success';
		$failureUrl = 'checkout/onepage/failure';
		$bdresmsg = $_POST['msg'];
		$last_order_id = $session->getLastOrderId();

		if(isset($bdresmsg)){
			$msg_splitted = explode("|", $bdresmsg);
			$res_mercid = $msg_splitted[0];
			$res_custid = $msg_splitted[1];
			$res_bdtxnrefno = $msg_splitted[2];
			$res_amount = (int)$msg_splitted[4];
			$res_bankid = $msg_splitted[5];
			$res_paymode = $msg_splitted[7];
			$res_authcode = $msg_splitted[14];
			$res_description = $msg_splitted[24];
			$res_checksum = $msg_splitted[25];

			$bdres_wtcheck = substr($bdresmsg, 0, strrpos($bdresmsg,'|'));

			$newcheck = hash_hmac('sha256',$bdres_wtcheck,$this->model->getEncryptionKey(), false); 
			$newcheck = strtoupper($newcheck);
			$comment = 'BillDesk Transaction Id : '.$res_bdtxnrefno.'<br/>'.'Payment Mode : '.$res_paymode.'<br/>'.'Bank Id : '.$res_bankid.'<br/>'.'Transaction Response Code : '.$res_authcode.'<br/>'.'Transaction Response Message : '.$res_description;
			$order = $objectManager->get('\Magento\Sales\Api\OrderRepositoryInterface')->get($last_order_id);
			$order_id = $order->getIncrementId();
			$quote_id = $session->getLastQuoteId();
			$quote = $objectManager->get('\Magento\Quote\Api\CartRepositoryInterface')->get($quote_id);
			$messageManager = $objectManager->get('\Magento\Framework\Message\ManagerInterface');

			
			if($newcheck !== $res_checksum){
				$order->cancel()->setState(\Magento\Sales\Model\Order::STATUS_FRAUD)->save();
				//error_log("txn checksum mismach calculated checksum = " . $newcheck . " and checksum received = " . $res_checksum . " for orderid=" .$res_custid);
				$messageManager->addError('Sorry, We are unable to process the request. Please try again later');
				$this->resultRedirectFactory->create()->setPath($failureUrl);
				return;
			}

			$order_amount = $order->getGrandTotal();

			if((int)$res_amount*100 !== (int)$order_amount*100){
				//error_log("amount mismatch -- order amount=".$order_amount. " and res amount=" . $res_amount . " for orderid=" .$res_custid);
				$order->cancel()->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW)->save();
				$messageManager->addError(__('Sorry, We are unable to process the request. Please try again later'));
				$this->_redirect($failureUrl);
				return;
			}

			// success
	        if ($this->_validateResponse($res_authcode)) {
	            $quote->setIsActive(false)->save();
	            $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
				$order->setState($state);
				$order->setStatus($state);
	            // error_log("response comment=" . $comment. " for orderid=" .$res_custid);
	            $payment = $order->getPayment();
				$dummy_txn_id = 'BD_'.$res_custid;
				$payment->setLastTransId($dummy_txn_id);
				$payment->setTransactionId($dummy_txn_id);
				$key = \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS;
				$payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => $comment]
				);
				
				$trans = $objectManager->get('Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface');
				$transaction = $trans->setPayment($payment)
				->setOrder($order)
				->setTransactionId($dummy_txn_id)
				->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => $comment]
				)
				->setFailSafe(true)
				->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH);
				//echo "<script type='text/javascript'>alert('2');</script>";

				$payment->addTransactionCommentsToOrder($transaction, $comment);
	            $payment->setParentTransactionId(null);
				$payment->save();
				$order->save();
				//try
				//	{
				//	$order->sendNewOrderEmail();
				//	} catch (Exception $ex) {  }
				$last_order_id = $session->getLastOrderId();
				$messageManager->addSuccess('Transaction has been successful.');
				return $this->resultRedirectFactory->create()->setPath($successUrl);
	        } else {
				$order->cancel()->setState(\Magento\Sales\Model\Order::STATE_CANCELED)->save();
	            $messageManager->addError('Thank you for shopping with us. However, the transaction has been declined for reason :  ' . $res_description . ''); 
	            $this->_redirect($failureUrl);
				return;
	        }
		} else {
			//error_log("Response msg is not set...");
			$messageManager->addError('Sorry, We are unable to process the request. Please try again later.');
			$this->_redirect($failureUrl);
			return;
		}
    }

	protected function _validateResponse($res_authcode)
    {
    	//$postdata = Mage::app()->getRequest()->getPost();
    	//$session = Mage::getSingleton('checkout/session');
    	$flag = False;
    	//error_log('Response Code is ' . $res_authcode);
    	if($res_authcode == '0300') { // success
    		$flag = True;
    	}
    	return $flag;
    }
}
