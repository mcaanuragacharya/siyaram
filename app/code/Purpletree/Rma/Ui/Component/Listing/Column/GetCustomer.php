<?php
/**
 * Purpletree_Rma GetCustomer
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Rma
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Rma\Ui\Component\Listing\Column;

class GetCustomer extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url path  to edit
     *
     * @var string
     */
    const URL_VIEW_EDIT = 'customer/index/edit';

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Model\Order $order,
        array $components = [],
        array $data = []
    ) {
    
        $this->_urlBuilder = $urlBuilder;
        $this->order = $order;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['pts_orderreturn_id'])) {
                    $order = $this->order->loadByIncrementId($item["pts_order_id"]);
                    if ($order->getCustomerId()) { //logged in customer
                        $customerId = $order->getCustomerId();
                        $item[$this->getData('name')] = "<a href='".$this->_urlBuilder->getUrl(static::URL_VIEW_EDIT, ['id' => $customerId])."' target='blank' title='".__('View Customer')."'>".$order->getCustomerFirstname()." ".$order->getCustomerLastname().'</a>';
                    } else { //not logged in customer
                        $item[$this->getData('name')] = $order->getBillingAddress()->getEmail();
                    }
                }
            }
        }
        return $dataSource;
    }
}
