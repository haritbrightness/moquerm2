<?php

namespace Pc\Postcode\Api;

interface PostcodeManagementInterface
{    

    /**
    * @param string $postcode.
    * @param string $houseNumber
    * @param string $houseNumberAddition
    * @return string
    */
	public function getPostcodeInformation($postcode, $houseNumber, $houseNumberAddition);
	
}
