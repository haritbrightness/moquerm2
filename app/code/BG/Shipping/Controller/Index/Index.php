<?php

namespace BG\Shipping\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CollectionFactory;

class Index extends Action {

    /**
     * @var CollectionFactory
     */
    protected $tablerate;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param TablerateFactory $tablerate
     * @param ResultFactory $resultFactory
     */
    public function __construct(Context $context, ResultFactory $resultFactory, CollectionFactory $tablerate) {

        $this->resultFactory = $resultFactory;
        $this->tablerate = $tablerate;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {

        $country = $this->getRequest()->getParam('country_id');
        $number = $this->getRequest()->getParam('number');
        if ($country == "") {
            $data = ["", ""];
        } else {
            $data = $this->checkShippingCost($country, $number);
        }

        $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                ->setData([
            'status' => 400,
            'price' => abs($data[0]),
            'eta' => $data[1]
        ]);
        return $response;
    }

    public function getAllRates() {

        $array = array();
        $tablerateColl = $this->tablerate->create();
        foreach ($tablerateColl as $tablerate) {
            array_push($array, $tablerate->debug());
        }
        return $array;
    }

    public function checkShippingCost($country, $number) {
        $array = $this->getAllRates();
        $finalValue = 0;
        $conditionMax = array();
        foreach ($array as $val) {
            if ($val['dest_country_id'] == $country && $val['website_id'] == 12 && intval($number) >= intval($val['condition_value'])) {
                $conditionMax[$val["price"]] = $val['condition_value'];
                $eta = $val["eta"];
            }
        }
        arsort($conditionMax);
        $finalValue = key($conditionMax);
        $data = [$finalValue, $eta];
        return $data;
    }

}
