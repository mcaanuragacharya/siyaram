<?php
include('common.php');
Orders::exportOrders();
exit;
class Orders{
    //Function for Configurable Product
    public static function exportOrders(){
        include('../app/bootstrap.php');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $url = $storeManager->getStore()->getBaseUrl();
        $connection = $resource->getConnection();
        $sqlOrder = "Select * FROM  sales_order ORDER BY entity_id DESC"; 
        $orderData = $connection->fetchAll($sqlOrder);
        if(count($orderData)<=0){
            echo 'No Orders Available';
            exit;
        }
        $orderResponseArray = array();
        for($i=0;$i<count($orderData);$i++){  
            $orderResponseArray[$i]['order'] = $orderData[$i];
            /*
            //Sales Order Item
            $sqlOrderItem = "Select * FROM sales_order_item WHERE order_id = '".$orderData[$i]['entity_id']."' ORDER BY item_id DESC"; 
            $orderItemData = $connection->fetchAll($sqlOrderItem);
            $orderResponseArray[$i]['orders_item'] = $orderItemData;
            */
            //sales_order_address 
            $sqlOrderAddress = "Select * FROM sales_order_address WHERE parent_id = '".$orderData[$i]['entity_id']."' AND address_type = 'billing' order by entity_id limit 1"; 
            $orderAddressData = $connection->fetchAll($sqlOrderAddress);
            $orderResponseArray[$i]['orders_billingaddress'] = $orderAddressData[0];
            $sqlOrderAddress = "Select * FROM sales_order_address WHERE parent_id = '".$orderData[$i]['entity_id']."' AND address_type = 'shipping' order by entity_id limit 1"; 
            $orderAddressData = $connection->fetchAll($sqlOrderAddress);
            $orderResponseArray[$i]['orders_shippingaddress'] = $orderAddressData[0];
            /*
            //sales_order_payment 
            $sqlOrderPayment = "Select * FROM sales_order_payment WHERE parent_id = '".$orderData[$i]['entity_id']."' ORDER BY entity_id DESC"; 
            $orderPaymentData = $connection->fetchAll($sqlOrderPayment);
            $orderResponseArray[$i]['sales_order_payment'] = $orderPaymentData;
            //sales_payment_transaction 
            $sqlOrderPaymentTransaction = "Select * FROM sales_payment_transaction WHERE order_id = '".$orderData[$i]['entity_id']."' ORDER BY transaction_id DESC"; 
            $orderPaymentTransactionData = $connection->fetchAll($sqlOrderPaymentTransaction);
            $orderResponseArray[$i]['orders_payment_payments_transaction'] = $orderPaymentTransactionData;
            //sales_shipment 
            $sqlOrderShipment = "Select * FROM sales_shipment WHERE order_id = '".$orderData[$i]['entity_id']."' ORDER BY entity_id DESC"; 
            $orderShipmentData = $connection->fetchAll($sqlOrderShipment);
            if(count($orderShipmentData)>0){
                $orderResponseArray[$i]['shipments'] = $orderShipmentData;
                //sales_shipment_item 
                $sqlOrderShipmentItem = "Select * FROM sales_shipment_item WHERE parent_id = '".$orderShipmentData[0]['entity_id']."' ORDER BY entity_id DESC"; 
                $orderShipmentItemData = $connection->fetchAll($sqlOrderShipmentItem);
                $orderResponseArray[$i]['shipments_shipments_item'] = $orderShipmentItemData;
                //sales_shipment_comment 
                $sqlOrderShipmentComment = "Select * FROM sales_shipment_comment WHERE parent_id = '".$orderShipmentData[0]['entity_id']."' ORDER BY entity_id DESC"; 
                $orderShipmentCommentData = $connection->fetchAll($sqlOrderShipmentComment);
                $orderResponseArray[$i]['shipments_shipments_comment'] = $orderShipmentCommentData;
                //shipments_shipments_track 
                $sqlOrderShipmentTrack = "Select * FROM sales_shipment_track WHERE parent_id = '".$orderShipmentData[0]['entity_id']."' ORDER BY entity_id DESC"; 
                $orderShipmentTrackData = $connection->fetchAll($sqlOrderShipmentTrack);
                $orderResponseArray[$i]['shipments_shipments_track'] = $orderShipmentTrackData;
            } else {
                $orderResponseArray[$i]['shipments'] = array();
                $orderResponseArray[$i]['shipments_shipments_item'] = array();
                $orderResponseArray[$i]['shipments_shipments_comment'] = array();
                $orderResponseArray[$i]['shipments_shipments_track'] = array();
            }*/
            //sales_invoice 
            $sqlOrderInvoice = "Select * FROM sales_invoice WHERE order_id = '".$orderData[$i]['entity_id']."' ORDER BY entity_id DESC"; 
            $orderInvoiceData = $connection->fetchAll($sqlOrderInvoice);
            if(count($orderInvoiceData)>0){
                $orderResponseArray[$i]['invoices'] = $orderInvoiceData;
                /*
                //sales_invoice_item 
                $sqlOrderInvoiceItem = "Select * FROM sales_invoice_item WHERE parent_id = '".$orderInvoiceData[0]['entity_id']."' ORDER BY entity_id DESC"; 
                $orderInvoiceItemData = $connection->fetchAll($sqlOrderInvoiceItem);
                $orderResponseArray[$i]['invoices_invoices_item'] = $orderInvoiceItemData;
                //sales_invoice_comment 
                $sqlOrderInvoiceComment = "Select * FROM sales_invoice_comment WHERE parent_id = '".$orderInvoiceData[0]['entity_id']."' ORDER BY entity_id DESC"; 
                $orderInvoiceCommentData = $connection->fetchAll($sqlOrderInvoiceComment);
                $orderResponseArray[$i]['invoices_invoices_comment'] = $orderInvoiceCommentData;    
                */
                
            } else {
                $orderResponseArray[$i]['invoices'] = '';
                /*$orderResponseArray[$i]['invoices_invoices_item'] = array();
                $orderResponseArray[$i]['invoices_invoices_comment'] = array();*/
            }
            /*
            //creditmemos 
            $sqlOrderCreditMemo = "Select * FROM sales_creditmemo WHERE order_id = '".$orderData[$i]['entity_id']."' ORDER BY entity_id DESC"; 
            $orderCreditMemoData = $connection->fetchAll($sqlOrderCreditMemo);
            if(count($orderCreditMemoData)>0){
                $orderResponseArray[$i]['creditmemos'] = $orderCreditMemoData;
                //sales_creditmemo_item 
                $sqlOrderCreditMemoItem = "Select * FROM sales_creditmemo_item WHERE order_id = '".$orderCreditMemoData[0]['entity_id']."' ORDER BY entity_id DESC"; 
                $orderCreditMemoItemData = $connection->fetchAll($sqlOrderCreditMemoItem);
                $orderResponseArray[$i]['creditmemos_creditmemos_item'] = $orderCreditMemoItemData;
                //sales_creditmemo_comment 
                $sqlOrderCreditMemoComment = "Select * FROM sales_creditmemo_comment WHERE order_id = '".$orderCreditMemoData[0]['entity_id']."' ORDER BY entity_id DESC"; 
                $orderCreditMemoCommentData = $connection->fetchAll($sqlOrderCreditMemoComment);
                $orderResponseArray[$i]['creditmemos_creditmemos_comment'] = $orderCreditMemoCommentData;    
            } else {
                $orderResponseArray[$i]['creditmemos'] = array();
                $orderResponseArray[$i]['creditmemos_creditmemos_item'] = array();
                $orderResponseArray[$i]['creditmemos_creditmemos_comment'] = array();
            }
            //taxs 
            $sqlOrderTax = "Select * FROM sales_order_tax WHERE order_id = '".$orderData[$i]['entity_id']."' ORDER BY tax_id DESC"; 
            $orderTaxData = $connection->fetchAll($sqlOrderTax);
            if(count($orderTaxData)>0){
                $orderResponseArray[$i]['taxs'] = $orderTaxData;
                //sales_order_tax_item 
                $sqlOrderTaxItem = "Select * FROM sales_order_tax_item WHERE tax_id = '".$orderTaxData[0]['tax_id']."' ORDER BY tax_item_id DESC"; 
                $orderTaxItemData = $connection->fetchAll($sqlOrderTaxItem);
                $orderResponseArray[$i]['taxs_taxs_item'] = $orderTaxItemData;
            } else {
                $orderResponseArray[$i]['taxs'] = array();
                $orderResponseArray[$i]['taxs_taxs_item'] = array();
            }*/
        }
        /*$fileName = 'orders.json';
        $fp = fopen('orders.json', 'w');
        fwrite($fp, json_encode($orderResponseArray));
        fclose($fp);
        /*if (file_exists($fileName)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($fileName));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileName));
        ob_clean();
        flush();
        readfile($fileName);
        exit;
        }*/

        //$jsonFilename = 'http://localhost/siyaram/importdata/orders.json';

        //Orders::jsonToCsv(json_encode($orderResponseArray),"D:\wamp\www\siyaram\importdata\output.csv");

        $array = $orderResponseArray;
        //$f = fopen('D:\wamp\www\siyaram\importdata\exportorder.csv', 'w');
        $f = fopen('/var/www/html/importdata/exportorder.csv', 'w');
        /*$indexArray = array('order','orders_item','orders_address','sales_order_payment','orders_payment_payments_transaction',
        'shipments','shipments_shipments_item','shipments_shipments_comment','shipments_shipments_track',
        'invoices','invoices_invoices_item','invoices_invoices_comment',
        'creditmemos','creditmemos_creditmemos_item','creditmemos_creditmemos_comment',
        'taxs','taxs_taxs_item');*/
        $indexArray = array('order','orders_billingaddress','orders_shippingaddress','invoices');
        $firstLineKeys = array();
        $headerStatus = 1;
        //echo '<pre>';print_r($array);exit;
        $m = 0;
        foreach ($array as $line)
        {
            if ($headerStatus==1)
            {
                for($j=0;$j<count($indexArray);$j++)
                {
                    if(count($line[$indexArray[$j]])>1){
                        $firstLineKeys[$m++] = array_keys($line[$indexArray[$j]]);
                    }
                }
                $finalTopArray = array();
                for($x=0;$x<count($firstLineKeys);$x++){
                    for($y=0;$y<count($firstLineKeys[$x]);$y++)
                        $finalTopArray[] = $firstLineKeys[$x][$y];
                    }
                fputcsv($f, $finalTopArray);
                $headerStatus = 0;
            }
            $line_array = array();
            for($i=0;$i<count($indexArray);$i++)
            {
                if(count($line[$indexArray[$i]])>1){
                    foreach ($line[$indexArray[$i]] as $value)
                    {
                        array_push($line_array,$value);    
                    }
                }
            }
            fputcsv($f, $line_array);
        }
        exit;
    }
    //Function to add Log
    public static function addLogData($data){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/product-'.date('Y-m-d').'.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data); // Simple Text Log
    }
}