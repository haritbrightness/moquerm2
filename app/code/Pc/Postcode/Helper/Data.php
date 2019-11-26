<?php

namespace Pc\Postcode\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $scopeConfig;
    protected $logger;
	protected $productMetadataInterface;
	protected $_moduleList;
    protected $developerHelper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
		\Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Developer\Helper\Data $developerHelper
    ){
		$this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
		$this->productMetadataInterface = $productMetadataInterface;
		$this->_moduleList = $moduleList;
        $this->developerHelper = $developerHelper;
	}



	public function getJsinit($getAdminConfig = false)
	{
		$aSettings = [
			"useStreet2AsHouseNumber" => $this->getBoolean('postcodecheckout/advanced_config/use_street2_as_housenumber'),
			"useStreet3AsHouseNumberAddition" => $this->getBoolean('postcodecheckout/advanced_config/use_street3_as_housenumberaddition'),
			"alwaysShowCountry" => $this->getBoolean('postcodecheckout/advanced_config/always_show_country'),
			"debug" => false,
			"translations"=> [
				"defaultError" =>  'Ongeldige Postcode + Huisnummer combinatie'
			]
		];

		return $aSettings;
	}

	

	/**
	 * Lookup information about a Dutch address by postcode, house number, and house number addition
	 *
	 * @param string $postcode
	 * @param string $houseNumber
	 * @param string $houseNumberAddition
	 *
	 * @return string
	 */
	public function lookupAddress($postcode, $houseNumber, $houseNumberAddition)
	{
		$bDebug = false;
		
		// Fix postcode
		$postcode = strtoupper($postcode); // Postcode		
	
		// Basic Postcode formatting check for The Netherlands with Postcode NL
		if(!preg_match('/^[1-9]([0-9]{3})[ ]?([A-Z]{2})$/', $postcode))
		{
			$aResponse['message'] = 'Postode is ongeldig, gebruik: 1234 AB';
			$aResponse['messageTarget'] = 'postcode';
			
			return $aResponse;
		}
		
		
		
		$aRequest = array();
		
		$aRequest['license_key'] = $this->getLicenseKey();
		$aRequest['website'] = $this->getRootUrl();
		
		$aRequest['addressdata']['postcode'] = $postcode;
		$aRequest['addressdata']['housenumber'] = $houseNumber;
		
		$sPostData = json_encode($aRequest);
		
		$sApiUrl = 'https://www.postcode-checkout.nl/postcode/';
	
		$sResponse = $this->doHttpRequest_curl($sApiUrl, $sPostData, true, 30, $bDebug, false);
		
		$aResponse = json_decode($sResponse, true);
		
		if(!is_array($aResponse))
		{
			$aResponse['message'] = 'Validatie mislukt, gebruik handmatige invoer.';
			$aResponse['messageTarget'] = 'housenumber';
			$aResponse['useManual'] = true;
		}
		else
		{
			$sPostcode = $aResponse['postcode'];
			
			// Response is valid but check the postcode for a valid format
			if(substr($sPostcode, -3, 1) != ' ') 
			{
				$sPostcode = substr($sPostcode, 0, strlen($sPostcode) - 2). ' ' . substr($sPostcode, -2);
			}
			
			$aResponse['postcode'] = $sPostcode;
			
		}
		
		return $aResponse;
		
	}
	
	protected function getLicenseKey()
	{
		return trim($this->getStoreConfig('postcodecheckout/general/api_key'));
	}
	
	// Retrieve ROOT url of script
	function getRootUrl($iParent = 0)
	{
		if(empty($_REQUEST['ROOT_URL']))
		{
			// Detect installation directory based on current URL
			$sRootUrl = '';

			// Detect scheme
			if(isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'ON') === 0))
			{
				$sRootUrl .= 'https://';
			}
			else
			{
				$sRootUrl .= 'http://';
			}

			// Detect domain
			$sRootUrl .= $_SERVER['HTTP_HOST'];

			 // Detect port
			if((strpos($_SERVER['HTTP_HOST'], ':') === false) && isset($_SERVER['SERVER_PORT']))
			{
				if(isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'ON') === 0))
				{
					if((strcmp($_SERVER['SERVER_PORT'], '443') !== 0) && (strcmp($_SERVER['SERVER_PORT'], '80') !== 0))
					{
						$sRootUrl .= ':' . $_SERVER['SERVER_PORT'];
					}
				}
				elseif(strcmp($_SERVER['SERVER_PORT'], '80') !== 0)
				{
					$sRootUrl .= ':' . $_SERVER['SERVER_PORT'];
				}
			}

			$sRootUrl .= '/';

			// Detect path
			if(isset($_SERVER['SCRIPT_NAME']))
			{
				$a = explode('/', substr($_SERVER['SCRIPT_NAME'], 1));

				while(sizeof($a) > ($iParent + 1))
				{
					$sRootUrl .= $a[0] . '/';
					array_shift($a);
				}
			}

			$_REQUEST['ROOT_URL'] = $sRootUrl;
		}

		return $_REQUEST['ROOT_URL'];
	}
	
	
	function mb_splitAddress($sAddress) 
	{
		// Get everything up to the first number with a regex
		$bHasMatch = preg_match('/^[^0-9]*/', $sAddress, $aMatch);

		// If no matching is possible, return the supplied string as the street
		if(!$bHasMatch) 
		{
			return array($sAddress, '', '');
		}

		// Remove the street from the sAddress.
		$sAddress = str_replace($aMatch[0], '', $sAddress);
		$sStreetname = trim($aMatch[0]);

		// Nothing left to split, return the streetname alone
		if(strlen($sAddress == 0)) 
		{
			return array($sStreetname, '', '');
		}

		// Explode sAddress to an array using a multiple explode function
		$aAddress = mb_multiExplodeArray(array(' ', '-', '|', '&', '/', '_', '\\'), $sAddress);

		// Shift the first element off the array, that is the house number
		$iHousenumber = array_shift($aAddress);

		// If the array is empty now, there is no extension.
		if(count($aAddress) == 0) 
		{
			return array($sStreetname, $iHousenumber, '');
		}

		// Join together the remaining pieces as the extension.
		$sExtension = substr(implode(' ', $aAddress), 0, 4);

		return array($sStreetname, $iHousenumber, $sExtension);
	}


	function mb_multiExplodeArray($aDelimiter, $sString) 
	{
		$sInput = str_replace($aDelimiter, $aDelimiter[0], $sString);
		$aArray = explode($aDelimiter[0], $sInput);
		
		return $aArray;
	}

	

	// doHttpRequest (Uses curl-library)
	function doHttpRequest_curl($sUrl, $sPostData = false, $bRemoveHeaders = false, $iTimeout = 30, $bDebug = false, $aAdditionalHeaders = false)
	{
		global $bIdealcheckoutCurlVerificationError;

		if(!isset($bIdealcheckoutCurlVerificationError))
		{
			$bIdealcheckoutCurlVerificationError = false;
		}

		$aUrl = parse_url($sUrl);

		$bHttps = false;
		$sRequestUrl = '';

		if(in_array($aUrl['scheme'], array('ssl', 'https')))
		{
			$sRequestUrl .= 'https://';
			$bHttps = true;

			if(empty($aUrl['port']))
			{
				$aUrl['port'] = 443;
			}
		}
		else
		{
			$sRequestUrl .= 'http://';

			if(empty($aUrl['port']))
			{
				$aUrl['port'] = 80;
			}
		}

		$sRequestUrl .= $aUrl['host'] . (empty($aUrl['path']) ? '/' : $aUrl['path']) . (empty($aUrl['query']) ? '' : '?' . $aUrl['query']);

		if(is_array($sPostData))
		{
			$sPostData = str_replace(array('%5B', '%5D'), array('[', ']'), http_build_query($sPostData));
		}


		if($bDebug === true)
		{
			$sRequest  = 'Requested URL: ' . $sRequestUrl . "\r\n";
			$sRequest .= 'Portnumber: ' . $aUrl['port'] . "\r\n";

			if($sPostData)
			{
				$sRequest .= 'Posted data: ' . $sPostData . "\r\n";
			}

			echo "\r\n" . "\r\n" . '<h1>SEND DATA:</h1>' . "\r\n" . '<code style="display: block; background: #E0E0E0; border: #000000 solid 1px; padding: 10px;">' . str_replace(array("\n", "\r"), array('<br>' . "\r\n", ''), htmlspecialchars($sRequest)) . '</code>' . "\r\n" . "\r\n";
		}


		$oCurl = curl_init();
		$oCertInfo = false;

		if($bHttps && $bDebug)
		{
			$oCertInfo = tmpfile();

			$sHostName = ($bHttps ? 'https://' : 'http://') . $aUrl['host'] . (empty($aUrl['port']) ? '' : ':' . $aUrl['port']);
			getUrlCertificate($sHostName);
		}

		curl_setopt($oCurl, CURLOPT_URL, $sRequestUrl);
		curl_setopt($oCurl, CURLOPT_PORT, $aUrl['port']);

		if($bHttps && ($bIdealcheckoutCurlVerificationError == false))
		{
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);

			if($oCertInfo)
			{
				curl_setopt($oCurl, CURLOPT_STDERR, $oCertInfo);
				curl_setopt($oCurl, CURLOPT_VERBOSE, true);
				curl_setopt($oCurl, CURLOPT_CERTINFO, true);
			}
		}

		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($oCurl, CURLOPT_TIMEOUT, $iTimeout);
		curl_setopt($oCurl, CURLOPT_HEADER, $bRemoveHeaders == false);


		if(substr($sPostData, 0, 1) == '{') // JSON string
		{
			if(!is_array($aAdditionalHeaders))
			{
				$aAdditionalHeaders = array();
			}

			$aAdditionalHeaders[] = 'Content-Type: application/json';
		}


		if(is_array($aAdditionalHeaders) && sizeof($aAdditionalHeaders))
		{
			curl_setopt($oCurl, CURLOPT_HTTPHEADER, $aAdditionalHeaders);
		}


		if($sPostData != false)
		{
			curl_setopt($oCurl, CURLOPT_POST, true);
			curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sPostData);
		}

		$sResponse = curl_exec($oCurl);


		// Capture certificate info
		if($bHttps && $oCertInfo)
		{
			fseek($oCertInfo, 0);

			$sCertInfo = '';

			while($s = fread($oCertInfo, 8192))
			{
				$sCertInfo .= $s;
			}

			fclose($oCertInfo);

			$this->logger->addNotice('cURL Retrieved SSL Certificate:' . "\r\n" . $sCertInfo);
		}

		if($bDebug)
		{
			if(curl_errno($oCurl) && (strpos(curl_error($oCurl), 'self signed certificate') !== false))
			{
				$this->logger->addNotice('cURL error #' . curl_errno($oCurl) . ': ' . curl_error($oCurl));
				$this->logger->addNotice(curl_getinfo($oCurl));
				$bIdealcheckoutCurlVerificationError = true;

				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($oCurl, CURLOPT_VERBOSE, false);
				curl_setopt($oCurl, CURLOPT_CERTINFO, false);

				// cURL Retry
				$sResponse = curl_exec($oCurl);
			}

			if(curl_errno($oCurl) == CURLE_SSL_CACERT)
			{
				$this->logger->addNotice('cURL error #' . curl_errno($oCurl) . ': ' . curl_error($oCurl));
				$this->logger->addNotice('ca-bundle.crt not installed?!');
				$this->logger->addNotice(curl_getinfo($oCurl));

				$sBundlePath = dirname(dirname(__FILE__)) . '/certificates/ca-bundle.crt';

				if(is_file($sBundlePath))
				{
					curl_setopt($oCurl, CURLOPT_CAINFO, $sBundlePath);

					// cURL Retry
					$sResponse = curl_exec($oCurl);
				}
			}

			if((curl_errno($oCurl) == CURLE_SSL_PEER_CERTIFICATE) || (curl_errno($oCurl) == 77))
			{
				$this->logger->addNotice('cURL error: ' . curl_error($oCurl));
				$this->logger->addNotice(curl_getinfo($oCurl));
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);

				// cURL Retry
				$sResponse = curl_exec($oCurl);
			}

			if(curl_errno($oCurl) && (strpos(curl_error($oCurl), 'error setting certificate verify locations') !== false))
			{
				$this->logger->addNotice('cURL error: ' . curl_error($oCurl));
				$this->logger->addNotice(curl_getinfo($oCurl));
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);

				// cURL Retry
				$sResponse = curl_exec($oCurl);
			}

			if(curl_errno($oCurl) && (strpos(curl_error($oCurl), 'certificate subject name ') !== false) && (strpos(curl_error($oCurl), ' does not match target host') !== false))
			{
				$this->logger->addNotice('cURL error: ' . curl_error($oCurl));
				$this->logger->addNotice(curl_getinfo($oCurl));
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);

				// cURL Retry
				$sResponse = curl_exec($oCurl);
			}
		}

		if(curl_errno($oCurl))
		{
			$this->logger->addNotice('cURL cannot rely on SSL verification. All SSL verification is disabled from this point.');
			$this->logger->addNotice(curl_getinfo($oCurl));
			$bIdealcheckoutCurlVerificationError = true;

			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_VERBOSE, false);
			curl_setopt($oCurl, CURLOPT_CERTINFO, false);

			// cURL Retry
			$sResponse = curl_exec($oCurl);
		}

		if(curl_errno($oCurl))
		{
			// cURL Failed
			$this->logger->addNotice('cURL error: ' . curl_error($oCurl));
			$this->logger->addNotice(curl_getinfo($oCurl));
		}

		curl_close($oCurl);


		if($bDebug === true)
		{
			echo "\r\n" . "\r\n" . '<h1>RECIEVED DATA:</h1>' . "\r\n" . '<code style="display: block; background: #E0E0E0; border: #000000 solid 1px; padding: 10px;">' . str_replace(array("\n", "\r"), array('<br>' . "\r\n", ''), htmlspecialchars($sResponse)) . '</code>' . "\r\n" . "\r\n";
		}


		if(empty($sResponse))
		{
			return '';
		}

		return $sResponse;
	}	
	
	// Curl verifcation error has occured
	function getUrlCertificate($sUrl, $bDebug = false)
	{
		if($bDebug)
		{
			if(version_compare(PHP_VERSION, '5.3.0') < 0)
			{
				$this->logger->addNotice('PHP version is to low for retrieving certificates.');
			}
			else
			{
				if($oStream = @stream_context_create(array('ssl' => array('capture_peer_cert' => true))))
				{
					$this->logger->addNotice('Fetching peer certificate for: ' . $sUrl);

					if($oHandle = @fopen($sUrl, 'rb', false, $oStream))
					{
						if(function_exists('stream_context_get_params'))
						{
							$aParams = stream_context_get_params($oHandle);

							if(isset($aParams['options'], $aParams['options']['ssl'], $aParams['options']['ssl']['peer_certificate']))
							{
								$oPeerCertificate = $aParams['options']['ssl']['peer_certificate'];

								$sTempPath = dirname(__DIR__) . '/temp';

								// Save certificate
								if(@openssl_x509_export_to_file($oPeerCertificate, $sTempPath . '/peer.' . time() . '.crt'))
								{
									return true;
								}
							}
							else
							{
								return false;
							}
						}
						else
						{
							$this->logger->addNotice('Stream function does not exist on this PHP version.');
						}
					}

					$this->logger->addNotice('Peer certificate capture failed for: ' . $sUrl);
				}
			}
		}

		return false;
	}
	
	protected function getBoolean($sConfigKey)
	{
		if($this->getStoreConfig($sConfigKey))
		{
			return true;
		}

		return false;
	}
	
	protected function getStoreConfig($sPath)
	{
		return $this->scopeConfig->getValue($sPath,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}


}