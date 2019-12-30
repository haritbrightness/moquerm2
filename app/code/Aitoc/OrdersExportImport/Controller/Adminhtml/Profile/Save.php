<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;
use Aitoc\OrdersExportImport\Model\Profile;
use Aitoc\OrdersExportImport\Model\ProfileFactory;
use Aitoc\OrdersExportImport\Model\ExportFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::profile';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ProfileFactory
     */
    private $profileFactory;
    
    /**
     * @var ExportFactory
     */
    private $exportFactory;
    
    /**
     * Save constructor.
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ProfileFactory $profileFactory,
     * @param ExportFactory $exportFactory
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        ProfileFactory $profileFactory,
        ExportFactory $exportFactory
    ) {
        parent::__construct($context);
        
        $this->dataPersistor  = $dataPersistor;
        $this->profileFactory = $profileFactory;
        $this->exportFactory  = $exportFactory;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('profile_id');

            if (empty($data['profile_id'])) {
                $data['profile_id'] = null;
            }

            $model = $this->profileFactory->create();
            $model->load($id);
            
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This profile no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($this->scopeData($data));

            try {
                $model->save();
    
                $this->messageManager->addSuccess(__('You saved the profile.'));
                $this->dataPersistor->clear('orderexportimport_profile');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['profile_id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the profile.'));
            }

            $this->dataPersistor->set('orderexportimport_profile', $data);

            return $resultRedirect->setPath('*/*/edit', ['profile_id' => $this->getRequest()->getParam('profile_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function scopeData($data)
    {
        $newData               = [];
        $newData['name']       = $data['name'];
        $newData['profile_id'] = $data['profile_id'];
        $pattern               = '/\/$/mi';
        preg_match($pattern, $data['path_local'], $matches);
        if (empty($matches)) {
            $data['path_local'] .= "/";
        }
        if (isset($data['fields'])) {
            $flip = array_flip($data['fields']);
            if (isset($flip['on'])) {
                unset($flip['on']);
            }
            unset($data['fields']);
            $data['allowed_fields']['fields'] = array_keys($flip);
        }
        $newData['config']    = json_encode($data);
        $newData['date']      = date('Y-m-d H:i:s');

        return $newData;
    }
}
