<?php
namespace Bluedart\Shipment\Block\Adminhtml\Shipment;

class View extends \Magento\Shipping\Block\Adminhtml\View {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
        ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->buttonList->add('print_bluedart_label', array(
            'label' => __('Blue Dart Print Label'),
            'onclick' => 'setLocation(\'' . $this->getBluedartPrintLabelUrl() . '\')',
            'class' => 'go'
        ));
        parent::_construct();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    public function getBluedartPrintLabelUrl()
    {
        return $this->getUrl('shipment/index/printlabel', ['shipment_id' => $this->getShipment()->getId()]);
    }
}
