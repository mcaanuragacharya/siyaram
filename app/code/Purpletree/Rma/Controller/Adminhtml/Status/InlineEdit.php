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
namespace Purpletree\Rma\Controller\Adminhtml\Status;

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
     * Status Factory
     *
     * @var \Purpletree\Rma\Model\StatusFactory
     */
    protected $_statusFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Purpletree\Rma\Model\StatusFactory $statusFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Purpletree\Rma\Model\StatusFactory $statusFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_jsonFactory = $jsonFactory;
        $this->_dataHelper = $dataHelper;
        $this->_statusFactory = $statusFactory;
        parent::__construct($context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::status');
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
        $statusItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($statusItems))) {
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
        foreach (array_keys($statusItems) as $statusId) {
            /** @var \Purpletree\Rma\Model\Status $status */
            $status = $this->_statusFactory->create()->load($statusId);
            if ($statusId == 1 || $statusId == 2 || $statusId == 3) {
                $messages[] = $this->getErrorWithStatusId($status, 'Cannot be Edited');
                $error = true;
            } else {
                try {
                    $statusData = $statusItems[$statusId];//todo: handle dates
                    $status->addData($statusData);
                    $status->save();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messages[] = $this->getErrorWithStatusId($status, $e->getMessage());
                    $error = true;
                } catch (\RuntimeException $e) {
                    $messages[] = $this->getErrorWithStatusId($status, $e->getMessage());
                    $error = true;
                } catch (\Exception $e) {
                    $messages[] = $this->getErrorWithStatusId(
                        $status,
                        __('Something went wrong while saving the Status.')
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
     * Add Status id to error message
     *
     * @param \Purpletree\Rma\Model\Status $status
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithStatusId(\Purpletree\Rma\Model\Status $status, $errorText)
    {
        return '[Status ID: ' . $status->getPtsStatusId() . '] ' . $errorText;
    }
}
