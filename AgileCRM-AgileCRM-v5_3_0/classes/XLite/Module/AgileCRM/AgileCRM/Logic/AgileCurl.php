<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\AgileCRM\AgileCRM\Logic;

class AgileCurl{

	public static function curl_wrap($entity, $data, $method, $content_type) {

	    $agile_domain = \XLite\Module\AgileCRM\AgileCRM\Main::agile_domain();
	    $agile_email = \XLite\Module\AgileCRM\AgileCRM\Main::agile_email();
	    $agile_rest_api_key = \XLite\Module\AgileCRM\AgileCRM\Main::agile_rest_api_key();

	    if ($content_type == NULL) {
	        $content_type = "application/json";
	    }
	    
	    $agile_url = "https://" . $agile_domain . ".agilecrm.com/dev/api/" . $entity;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	    curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true);
	    switch ($method) {
	        case "POST":
	            $url = $agile_url;
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "GET":
	            $url = $agile_url;
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	            break;
	        case "PUT":
	            $url = $agile_url;
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "DELETE":
	            $url = $agile_url;
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	            break;
	        default:
	            break;
	    }
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        "Content-type : $content_type;", 'Accept : application/json'
	    ));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERPWD, $agile_email . ':' . $agile_rest_api_key);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}
}

