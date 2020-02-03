<?php
namespace Bluedart\Shipment\Block\Adminhtml\Order;

/**
 * Product View block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Sales\Block\Adminhtml\Order\View
{

    public $order_id;

    protected function _construct()
    {
        $itemscount = 0;
        //$totalWeight = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_order = $objectManager->create('Magento\Sales\Model\Order')->load($this->getOrder()->getId());
        //$this->order_id = $this->getOrder()->getId();
        $itemsv = $_order->getAllVisibleItems();
        foreach ($itemsv as $itemvv) {
          // echo  $itemvv->getQtyShipped()."--";
            if ((int)$itemvv->getQtyOrdered() > (int)$itemvv->getQtyShipped()) {
                $itemscount += (int)$itemvv->getQtyOrdered() - (int)$itemvv->getQtyShipped();
            }
            /*if ($itemvv->getWeight() != 0) {
                $weight = $itemvv->getWeight() * $itemvv->getQtyOrdered();
            } else {
                $weight = 0.5 * $itemvv->getQtyOrdered();
            }
            $totalWeight += $weight;*/
        }

        /*$history = array();
        if ($_order->getShipmentsCollection()->count()) {
            foreach ($_order->getShipmentsCollection() as $_shipment) {
                if ($_shipment->getCommentsCollection()->count()) {
                    foreach ($_shipment->getCommentsCollection() as $_comment) {
                        $history[] = $_comment->getComment();
                    }
                }
            }
        }

        $bluedart_return_button = false;
        if (count($history)) {
            foreach ($history as $_history) {
                $awbno = strstr($_history, "- Order No", true);
                $awbno = trim($awbno, "AWB No.");
                break;
            }
            if (isset($awbno)) {
                if ((int)$awbno)
                    $bluedart_return_button = true;
            }
        }*/

        /*if ($itemscount == 0) {
            $this->buttonList->add('print_bluedart_label', array(
                'label' => __('Bluedart Print Label'),
                'onclick' => "myObj.printLabel()",
                'class' => 'go'
            ));
        }*/

        if ($_order->canShip()) {
            $this->buttonList->add('create_bluedart_shipment', array(
                'label' => __('Prepare Blue Dart Shipment'),
                'onclick' => 'bluedartpop(' . $itemscount . ')',
                'class' => 'go'
            ));
        } /*elseif (!$_order->canShip() && $bluedart_return_button) {
            $this->buttonList->add('create_bluedart_shipment', array(
                'label' => __('Return Bluedart Shipment'),
                'onclick' => 'bluedartreturnpop(' . $itemscount . ')',
                'class' => 'go'
            ));
        }*/
        parent::_construct();
    }
}
