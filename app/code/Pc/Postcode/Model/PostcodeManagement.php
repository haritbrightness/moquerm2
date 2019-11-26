<?php

namespace Pc\Postcode\Model;

class PostcodeManagement 
{
    protected $postcodeHelper;
    
    public function __construct(
        \Pc\Postcode\Helper\Data $postcodeHelper
    ){
        $this->postcodeHelper = $postcodeHelper;
    }
   
   
    public function getPostcodeInformation($postcode, $houseNumber, $houseNumberAddition)
	{		
        $aResult = $this->postcodeHelper->lookupAddress($postcode, $houseNumber, $houseNumberAddition);
		
        return json_encode($aResult);
    }
    
}