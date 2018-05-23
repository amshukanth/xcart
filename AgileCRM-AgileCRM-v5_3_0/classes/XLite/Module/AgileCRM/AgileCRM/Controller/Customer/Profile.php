<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\AgileCRM\AgileCRM\Controller\Customer;

use XLite\Module\AgileCRM\AgileCRM\Logic\Customer;
use XLite\Module\AgileCRM\AgileCRM\Logic\AgileCurl;

/**
 * Class Profile
 */
abstract class Profile extends \XLite\Controller\Customer\Profile implements \XLite\Base\IDecorator 
{
    /**
     * Postprocess register action (success)
     *
     * @return array
     */
    protected function postprocessActionRegisterSuccess()
    {
    	  $params = parent::postprocessActionRegisterSuccess();
    	
        if (\XLite\Module\AgileCRM\AgileCRM\Main::isSyncContacts()) {
        	
            /** @var \XLite\Model\Profile $profile */
            $profile = $this->getModelForm()->getModelObject();
            
            $profile_data = Customer::getData($profile);

            if ($profile) {
              $this->createAgileContact($profile_data);
            }        	
        }

        return $params;
    }

    public function createAgileContact($profile_data)
    {

        $result = AgileCurl::curl_wrap("contacts/search/email/".$profile_data['email_address'], null, "GET", "application/json");
        $result = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
        
        if(count($result)>0)
            $contact_id = $result->id;
        else
            $contact_id = "";

        if($contact_id == "")
        {   
            if(isset($profile_data['address']['firstname'])){
              $billing_address = array(
                    "address"=>$profile_data['address']['address1'],
                    "city"=>$profile_data['address']['city'],
                    "state"=>$profile_data['address']['province'],
                    "country"=>$profile_data['address']['country']
                  );
            }
            $contact_json = array(
                  "tags"=>array("XCart"),
                  "properties"=>array(
                    array(
                      "name"=>"first_name",
                      "value"=> isset($profile_data['address']['firstname']) ?  $profile_data['address']['firstname'] : "",
                      "type"=>"SYSTEM"
                    ),
                     array(
                      "name"=>"last_name",
                      "value"=> isset($profile_data['address']['lastname']) ?  $profile_data['address']['lastname'] : "",
                      "type"=>"SYSTEM"
                    ),
                    array(
                      "name"=>"email",
                      "value"=>$profile_data['email_address'],
                      "type"=>"SYSTEM"
                    ),  
                    array(
                        "name"=>"address",
                        "value"=> isset($profile_data['address']['firstname']) ? json_encode($billing_address) : "",
                        "type"=>"SYSTEM"
                    ), 
                    array(
                      "name"=>"phone",
                      "value"=> isset($profile_data['address']['firstname']) ? $profile_data['address']['phone'] : "",
                      "type"=>"SYSTEM"
                    )
                  )
                );
            $contact_json = json_encode($contact_json);
            $curln = AgileCurl::curl_wrap("contacts", $contact_json, "POST", "application/json");
        }

    }
}
