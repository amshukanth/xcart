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
abstract class AddressBook extends \XLite\Controller\Customer\AddressBook implements \XLite\Base\IDecorator 
{
	 /**
     * Save address
     *
     * @return boolean
     */
    protected function doActionSave()
    {
    	$result = parent::doActionSave();

    	if (\XLite\Module\AgileCRM\AgileCRM\Main::isSyncContacts()) {
        	
            /** @var \XLite\Model\Profile $profile */
            $profile = $this->getModelForm()->getModelObject()->getProfile();

            $profile_data = Customer::getData($profile);

            if($profile){

                $billing_address = array(
                      "address"=>$profile_data['address']['address1'],
                      "city"=>$profile_data['address']['city'],
                      "state"=>$profile_data['address']['province'],
                      "country"=>$profile_data['address']['country']
                    );
            	
            	$result = AgileCurl::curl_wrap("contacts/search/email/".$profile_data['email_address'], null, "GET", "application/json");
                $result = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
                
                if(count($result)>0)
                    $contact_id = $result->id;
                else
                    $contact_id = "";

                $contact_agile = array(
                                  "tags"=>array("XCart"),
                                  "properties"=>array(
                                    array(
                                      "name"=>"first_name",
                                      "value"=> $profile_data['address']['firstname'],
                                      "type"=>"SYSTEM"
                                    ),
                                     array(
                                      "name"=>"last_name",
                                      "value"=>$profile_data['address']['lastname'],
                                      "type"=>"SYSTEM"
                                    ),
                                    array(
                                      "name"=>"email",
                                      "value"=>$profile_data['email_address'],
                                      "type"=>"SYSTEM"
                                    ), 
                                    array(
                                      "name"=>"address",
                                      "value"=>json_encode($billing_address),
                                      "type"=>"SYSTEM"
                                    ), 
                                    array(
                                      "name"=>"phone",
                                      "value"=>$profile_data['address']['phone'],
                                      "type"=>"SYSTEM"
                                    )
                                  )
                                );

                if($contact_id == "")
                {   
                    $contact_json = json_encode($contact_agile);
                    $curln = AgileCurl::curl_wrap("contacts", $contact_json, "POST", "application/json");
                }
                else{
                    $contact_agile['id'] = $contact_id;
                    $contact_json = json_encode($contact_agile);
                    $curlupdate = AgileCurl::curl_wrap("contacts/edit-properties", $contact_json, "PUT", "application/json");
                }
            }
        }
    	return $result;
    }
}