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
namespace Purpletree\Rma\Controller\Adminhtml\Resolution;

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
     * Resolution Factory
     *
     * @var \Purpletree\Rma\Model\ResolutionFactory
     */
    protected $_resolutionFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_jsonFactory = $jsonFactory;
        $this->_dataHelper = $dataHelper;
        $this->_resolutionFactory = $resolutionFactory;
        parent::__construct($context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::resolution');
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
        $resolutionItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($resolutionItems))) {
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
        foreach (array_keys($resolutionItems) as $resolutionId) {
            /** @var \Purpletree\Rma\Model\Resolution $resolution */
            $resolution = $this->_resolutionFactory->create()->load($resolutionId);
            if ($resolutionId == 1 || $resolutionId == 2 || $resolutionId == 3) {
                $messages[] = $this->getErrorWithResolutionId($resolution, 'Cannot be Edited');
                $error = true;
            } else {
                try {
                    $resolutionData = $resolutionItems[$resolutionId];//todo: handle dates
                    $resolution->addData($resolutionData);
                    $resolution->save();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messages[] = $this->getErrorWithResolutionId($resolution, $e->getMessage());
                    $error = true;
                } catch (\RuntimeException $e) {
                    $messages[] = $this->getErrorWithResolutionId($resolution, $e->getMessage());
                    $error = true;
                } catch (\Exception $e) {
                    $messages[] = $this->getErrorWithResolutionId(
                        $resolution,
                        __('Something went wrong while saving the Resolution.')
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
     * Add Resolution id to error message
     *
     * @param \Purpletree\Rma\Model\Resolution $resolution
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithResolutionId(\Purpletree\Rma\Model\Resolution $resolution, $errorText)
    {
        return '[Resolution ID: ' . $resolution->getPtsResolutionId() . '] ' . $errorText;
    }
}
