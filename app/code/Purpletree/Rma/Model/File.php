<?php
/**
 * Purpletree_Rma File
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
 
namespace Purpletree\Rma\Model;

class File
{
    /**
     * Media sub folder
     *
     * @var string
     */
    protected $_subDir = 'purpletree/rma/messages';

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * File system model
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Filesystem $fileSystem
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Filesystem $fileSystem
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_fileSystem = $fileSystem;
    }

    /**
     * get images base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).$this->_subDir.'/file';
    }
    /**
     * get base file dir
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($this->_subDir.'/file');
    }
}
