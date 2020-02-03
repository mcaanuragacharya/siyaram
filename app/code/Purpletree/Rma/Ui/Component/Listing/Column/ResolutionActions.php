<?php
/**
 * Purpletree_Rma ResolutionActions
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

class ResolutionActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url path  to edit
     *
     * @var string
     */
    const URL_PATH_EDIT = 'purpletree_rma/resolution/edit';

    /**
     * Url path  to delete
     *
     * @var string
     */
    const URL_PATH_DELETE = 'purpletree_rma/resolution/delete';

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
        array $components = [],
        array $data = []
    ) {
    
        $this->_urlBuilder = $urlBuilder;
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
                if (isset($item['pts_resolution_id'])) {
                    if ($item['pts_resolution_id'] != 1 && $item['pts_resolution_id'] != 2 && $item['pts_resolution_id'] != 3) {
                        $item[$this->getData('name')] = [
                            'edit' => [
                                'href' => $this->_urlBuilder->getUrl(
                                    static::URL_PATH_EDIT,
                                    [
                                        'pts_resolution_id' => $item['pts_resolution_id']
                                    ]
                                ),
                                'label' => __('Edit')
                            ],
                            'delete' => [
                                'href' => $this->_urlBuilder->getUrl(
                                    static::URL_PATH_DELETE,
                                    [
                                        'pts_resolution_id' => $item['pts_resolution_id']
                                    ]
                                ),
                                'label' => __('Delete'),
                                'confirm' => [
                                    'title' => __('Delete "${ $.$data.name }"'),
                                    'message' => __('Are you sure you wan\'t to delete the Resolution "${ $.$data.name }" ?')
                                ]
                            ]
                        ];
                    }
                }
            }
        }
        return $dataSource;
    }
}
