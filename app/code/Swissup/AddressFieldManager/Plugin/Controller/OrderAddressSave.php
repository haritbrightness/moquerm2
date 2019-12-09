<?php
namespace Swissup\AddressFieldManager\Plugin\Controller;

class OrderAddressSave
{
    /**
     * @var \Swissup\AddressFieldManager\Model\Address\Order
     */
    protected $orderAddressModel;

    /**
     * @param \Swissup\AddressFieldManager\Model\Address\OrderFactory $orderAddressFactory
     */
    public function __construct(
        \Swissup\AddressFieldManager\Model\Address\OrderFactory $orderAddressFactory
    ) {
        $this->orderAddressModel = $orderAddressFactory->create();
    }

    /**
     * Save order address
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function beforeExecute(
        \Magento\Sales\Controller\Adminhtml\Order\AddressSave $subject
    ) {
        $addressId = $subject->getRequest()->getParam('address_id');
        $data = $subject->getRequest()->getPostValue();

        $this->orderAddressModel
            ->addData($data)
            ->setId($addressId)
            ->save();
    }
}
