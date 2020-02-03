<?php
/**
 * Purpletree_Rma InlineEdit
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
namespace Purpletree\Rma\Controller\Adminhtml\Reason;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_jsonFactory;

    protected $_dataHelper;
    /**
     * Reason Factory
     *
     * @var \Purpletree\Rma\Model\ReasonFactory
     */
    protected $_reasonFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Purpletree\Rma\Model\ReasonFactory $reasonFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Purpletree\Rma\Model\ReasonFactory $reasonFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_jsonFactory = $jsonFactory;
        $this->_dataHelper = $dataHelper;
        $this->_reasonFactory = $reasonFactory;
        parent::__construct($context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::reason');
    }
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_jsonFactory->create();
        $error = false;
        $messages = [];
        $reasonItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($reasonItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        if (!($this->_dataHelper->isEnabled())) {
             return $resultJson->setData([
                'messages' => [__('Module is disabled. Please enable the module to Continue')],
                'error' => true,
             ]);
        }
        foreach (array_keys($reasonItems) as $reasonId) {
            /** @var \Purpletree\Rma\Model\Reason $reason */
            $reason = $this->_reasonFactory->create()->load($reasonId);
            if ($reasonId == 1) {
                $messages[] = $this->getErrorWithReasonId($reason, 'Cannot be Edited');
                $error = true;
            } else {
                try {
                    $reasonData = $reasonItems[$reasonId];//todo: handle dates
                    $reason->addData($reasonData);
                    $reason->save();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messages[] = $this->getErrorWithReasonId($reason, $e->getMessage());
                    $error = true;
                } catch (\RuntimeException $e) {
                    $messages[] = $this->getErrorWithReasonId($reason, $e->getMessage());
                    $error = true;
                } catch (\Exception $e) {
                    $messages[] = $this->getErrorWithReasonId(
                        $reason,
                        __('Something went wrong while saving the Reason.')
                    );
                    $error = true;
                }
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Reason id to error message
     *
     * @param \Purpletree\Rma\Model\Reason $reason
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithReasonId(\Purpletree\Rma\Model\Reason $reason, $errorText)
    {
        return '[Reason ID: ' . $reason->getPtsReasonId() . '] ' . $errorText;
    }
}
