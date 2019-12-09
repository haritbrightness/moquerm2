<?php

namespace Swissup\DeliveryDate\Block\Adminhtml;

class DateField extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    public $quoteSession;

    /**
     * @var \Swissup\DeliveryDate\Helper\Data
     */
    public $helper;

    /**
     * @var \Swissup\DeliveryDate\Model\DeliverydateFactory
     */
    protected $deliverydateFactory;

    /**
     * @var \Swissup\DeliveryDate\Model\Deliverydate
     */
    protected $deliverydateModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Swissup\DeliveryDate\Helper\Data $helper
     * @param \Swissup\DeliveryDate\Model\DeliverydateFactory $deliverydateFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Swissup\DeliveryDate\Helper\Data $helper,
        \Swissup\DeliveryDate\Model\DeliverydateFactory $deliverydateFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->quoteSession = $quoteSession;
        $this->deliverydateFactory = $deliverydateFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Swissup\DeliveryDate\Model\Deliverydate
     */
    private function getDeliveryDateModel()
    {
        if (!$this->deliverydateModel) {
            $this->deliverydateModel = $this->deliverydateFactory
                ->create()
                ->loadByOrderId($this->quoteSession->getOrderId());
        }
        return $this->deliverydateModel;
    }

    /**
     * @return string
     */
    public function getDeliveryDate()
    {
        $date = '';
        $deliveryDate = $this->getDeliveryDateModel();
        if ($deliveryDate->getId()) {
            $date = $deliveryDate->getDate();
            $date = $this->helper->getFormattedDate($date);
        }
        return $date;
    }

    /**
     * @return string
     */
    public function getDeliveryTime()
    {
        $time = '';
        $deliveryDate = $this->getDeliveryDateModel();
        if ($deliveryDate->getId()) {
            $time = $deliveryDate->getTimerange();
        }
        return $time;
    }

    /**
     * @return string
     */
    public function getDeliveryDateFieldConfig()
    {
        return $this->escapeHtml(json_encode([
            'calendar' => [
                'minDate' => $this->helper->getMinDelayDays(),
                'maxDate' => $this->helper->getMaxDelayDays(),
                'dateFormat' => $this->helper->getDateFormat(true),
                'firstDay' => $this->helper->getFirstDay(),
                'showOn' => 'both',
                'buttonImageOnly' => false,
                'buttonText' => '',
            ]
        ]));
    }
}
