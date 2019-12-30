<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Class Upload
 */
class Upload extends Action
{
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::import';

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * List of allowed file extensions
     *
     * @var array
     */
    private $allowedExtensions = ['xml', 'csv'];

    /**
     * Id of component name on form
     *
     * @var string
     */
    private $fileId = 'file_name';

    /**
     * @var FileProcessor
     */
    public $fileProcessor;

    /**
     * @param Action\Сontext $context
     * @param Uploader $uploader
     * @param UploaderFactory $uploader
     */
    public function __construct(
        Action\Context $context,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory
    ) {
        parent::__construct($context);
        $this->fileSystem      = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $destinationPath = $this->getDestinationPath();
        try {
            $uploader = $this->uploaderFactory
                ->create(['fileId' => $this->fileId])
                ->setAllowCreateFolders(true)
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions($this->allowedExtensions)
                ->addValidateCallback('validate', $this, 'validateFile');
            $result   = $uploader->save($destinationPath);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * Return full path
     *
     * @return string
     */
    public function getDestinationPath()
    {
        return $this->fileSystem
            ->getDirectoryWrite(DirectoryList::TMP)
            ->getAbsolutePath('/');
    }
}
