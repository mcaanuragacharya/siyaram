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
namespace Purpletree\Rma\Controller\Adminhtml\Packagecondition;

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
     * Packagecondition Factory
     *
     * @var \Purpletree\Rma\Model\PackageconditionFactory
     */
    protected $_packageconditionFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_jsonFactory = $jsonFactory;
        $this->_dataHelper = $dataHelper;
        $this->_packageconditionFactory = $packageconditionFactory;
        parent::__construct($context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::packagecondition');
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
        $packageconditionItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($packageconditionItems))) {
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
        foreach (array_keys($packageconditionItems) as $packageconditionId) {
            /** @var \Purpletree\Rma\Model\Packagecondition $packagecondition */
            
                $packagecondition = $this->_packageconditionFactory->create()->load($packageconditionId);
            if ($packageconditionId == 1) {
                $messages[] = $this->getErrorWithPackageconditionId($packagecondition, 'Cannot be Edited');
                $error = true;
            } else {
                try {
                    $packageconditionData = $packageconditionItems[$packageconditionId];//todo: handle dates
                    $packagecondition->addData($packageconditionData);
                    $packagecondition->save();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messages[] = $this->getErrorWithPackageconditionId($packagecondition, $e->getMessage());
                    $error = true;
                } catch (\RuntimeException $e) {
                    $messages[] = $this->getErrorWithPackageconditionId($packagecondition, $e->getMessage());
                    $error = true;
                } catch (\Exception $e) {
                    $messages[] = $this->getErrorWithPackageconditionId(
                        $packagecondition,
                        __('Something went wrong while saving the Package Condition.')
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
     * Add Packagecondition id to error message
     *
     * @param \Purpletree\Rma\Model\Packagecondition $packagecondition
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithPackageconditionId(\Purpletree\Rma\Model\Packagecondition $packagecondition, $errorText)
    {
        return '[Package Condition ID: ' . $packagecondition->getPtsPackageconditionId() . '] ' . $errorText;
    }
}
