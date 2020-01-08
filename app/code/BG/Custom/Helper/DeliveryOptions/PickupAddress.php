<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace BG\Custom\Helper\DeliveryOptions;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote\AddressFactory;

class PickupAddress extends \TIG\PostNL\Helper\DeliveryOptions\PickupAddress
{
    const PG_ADDRESS_TYPE = 'pakjegemak';

    /**
     * @var bool|AddressInterface
     */
    private $pickupAddress = false;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @param Session        $checkoutSession
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        Session $checkoutSession,
        AddressFactory $addressFactory
    ) {
        parent::__construct($checkoutSession,$addressFactory);
    }
    private function create($pgData, $quote)
    {
        $address = $this->addressFactory->create();

        $address->setQuoteId($quote->getId());
        $address->setAddressType(self::PG_ADDRESS_TYPE);
        $address->setCompany($pgData['Name']);
        $address->setCity($pgData['City']);
        $address->setCountryId($pgData['Countrycode']);
        $address->setStreet($this->getStreet($pgData));
        $address->setPostcode($pgData['Zipcode']);
        $address->setFirstname($pgData['customer']['firstname']);
        $address->setLastname($pgData['customer']['lastname']);
        if($pgData['customer']['telephone']){
            $address->setTelephone($pgData['customer']['telephone']);
        }        
        $address->save();

        return $address;
    }
}
