<?php 

namespace Pc\Postcode\Block\Checkout;
 
class LayoutProcessor extends \Magento\Framework\View\Element\AbstractBlock implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface{
    
    protected $scopeConfig;
	protected $logger;
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
		
        $this->scopeConfig = $context->getScopeConfig(); //$scopeConfig;
		$this->logger = $context->getLogger(); //$logger;
    }

	public function process($aResult)
	{
       
        if($this->scopeConfig->getValue('postcodecheckout/general/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE) &&
            isset($aResult['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'])
        ){
			
			$aShippingPostcodeFields = $this->getPostcodeFields('shippingAddress', 'shipping');
            
			$aShippingFields = $aResult['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['shipping-address-fieldset']['children'];

            if(isset($aShippingFields['street']))
			{
                unset($aShippingFields['street']['children'][1]['validation']);
                unset($aShippingFields['street']['children'][2]['validation']);
            }

            $aShippingFields = array_merge($aShippingFields, $this->getPostcodeFieldSet('shippingAddress', 'shipping'));

			$aResult['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['shipping-address-fieldset']['children'] = $aShippingFields;
						 					 
			$aResult = $this->getBillingFormFields($aResult);

        }
		
        return $aResult;
	}
	
	public function getBillingFormFields($aResult)
	{
        if(isset($aResult['components']['checkout']['children']['steps']['children']
        ['billing-step']['children']['payment']['children']
        ['payments-list'])) {

            $aPaymentForms = $aResult['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];

            foreach($aPaymentForms as $aPaymentMethodForm => $aPaymentMethodValue) 
			{
                $aPaymentMethodCode = str_replace('-form', '', $aPaymentMethodForm);

                if (!isset($aResult['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$aPaymentMethodCode . '-form'])) 
				{
                    continue;
                }

                $aBillingFields = $aResult['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$aPaymentMethodCode . '-form']['children']['form-fields']['children'];

                $billingPostcodeFields = $this->getPostcodeFields('billingAddress' . $aPaymentMethodCode, 'billing');

                $aBillingFields = array_merge($aBillingFields, $billingPostcodeFields);

                $aResult['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$aPaymentMethodCode . '-form']['children']['form-fields']['children'] = $aBillingFields;

            }
        }
		
		return $aResult;

	}

	public function getPostcodeFieldSet($sScope, $sAddressType)
	{
        return [
            'pc_postcode_fieldset'=>
                [
                    'component' => 'Pc_Postcode/js/view/form/postcode',
                    'type' => 'group',
                    'config' => [
                        "customerScope" => $sScope,
                        "template" => 'Pc_Postcode/form/group',
                        "additionalClasses" => "pc_postcode_fieldset",
                        "loaderImageHref" => $this->getViewFileUrl('images/loader-1.gif')
                    ],
                    'children' => $this->getPostcodeFields($sScope, $sAddressType),
                    'provider' => 'checkoutProvider',
                    'addressType'=> $sAddressType
                ]
        ];
    }
	
	public function getPostcodeFields($sScope, $sAddressType = 'shipping')
	{
		$aPostcodeFields =    
		[
		    'pc_postcode_postcode'=>
			[
				'component' => 'Magento_Ui/js/form/element/abstract',
				'config' => [
					"customerScope" => $sScope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/input',
					"id" => 'pc_postcode_postcode'
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $sScope . '.pc_postcode_postcode',
				'label' => 'Postcode',
				'sortOrder' => '1000',
				'validation' => [
					'required-entry' => false
				],
                'addressType'=> $sAddressType
			],
            'pc_postcode_housenumber'=>
			[
				'component' => 'Magento_Ui/js/form/element/abstract',
				'config' => [
					"customerScope" => $sScope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/input',
					"id" => 'pc_postcode_housenumber'
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $sScope . '.pc_postcode_housenumber',
				'label' => 'Huisnummer',
				'sortOrder' => '1001',
				'validation' => [
					'required-entry' => false
				],
                'addressType'=> $sAddressType
			],
			'pc_postcode_housenumber_addition'=>
			[
				'component' => 'Magento_Ui/js/form/element/select',
				'config' => [
					"customerScope" => $sScope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/select'
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $sScope . '.pc_postcode_housenumber_addition',
				'label' => 'Toevoeging',
				'sortOrder' => '1002',
				'validation' => [
					'required-entry' => false,
				],
				'options' => [],
				'visible' => false,
                'addressType'=> $sAddressType
			],
			'pc_postcode_disable'=>
			[
				'component' => 'Magento_Ui/js/form/element/abstract',
				'config' => [
					"customerScope" => $sScope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/checkbox'
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $sScope . '.pc_postcode_disable',
				'label' => 'Handmatige invoer',
				'sortOrder' => '1003',
				'validation' => [
					'required-entry' => false,
				],
                'addressType'=> $sAddressType
			]
		];
		
		return $aPostcodeFields;
	}
}