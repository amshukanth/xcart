<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\AgileCRM\AgileCRM\Controller\Customer;

use XLite\Module\AgileCRM\AgileCRM\Logic\Order;
use XLite\Module\AgileCRM\AgileCRM\Logic\AgileCurl;
use XLite\Module\AgileCRM\AgileCRM\Controller\Customer\Profile;

/**
 * Class Profile
 */
abstract class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess implements \XLite\Base\IDecorator 
{
	
    public function handleRequest()
    {   
        parent::handleRequest();

    	$order = $this->getOrder();

    	if(\XLite\Module\AgileCRM\AgileCRM\Main::isSyncOrders())
    	{	
    		if($order)
    		{ 
                $order_details = Order::getData($order);

                $result = AgileCurl::curl_wrap("contacts/search/email/".$order_details['email'], null, "GET", "application/json");
                $result = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);

                if(count($result)==0){
                    
                    if(\XLite\Module\AgileCRM\AgileCRM\Main::isSyncContacts()){
                        $profile_data = array(
                                            'id' => $order_details['profile_id'],
                                            'email_address' => $order_details['email']
                                             );
                        Profile::createAgileContact($profile_data);
                        $result = AgileCurl::curl_wrap("contacts/search/email/".$order_details['email'], null, "GET", "application/json");
                        $result = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
                    }        
                }
                
                if(count($result)>0)
                {
                    $contact_id = $result->id;
                    $productname = array();
                    
                    $street = $order_details['billing_address']['address1'];
                    $city = $order_details['billing_address']['city'];
                    $state = $order_details['billing_address']['province'];
                    $country = $order_details['billing_address']['country'];   

                    foreach ($order_details['items'] as $product_item) {
                        $productname[] = $this->fn_js_escape($product_item['item_name']);
                    }
                    $noteproductname = implode(',',$productname);
                    $productname = implode('","',$productname);
                    $Str = $productname;
                    $Str = preg_replace('/[^a-zA-Z0-9_.]/', '_', $Str);
                    $contact_json = array(
                        "id" => $contact_id, 
                       "tags" => array($Str)
                    );

                   $contact_json = stripslashes(json_encode($contact_json));
                   $curltags = AgileCurl::curl_wrap("contacts/edit/tags", $contact_json, "PUT", "application/json");
                   $billingaddress = $street.",".$city.",".$state.",".$country;
                   $grandtotal = $order_details['total'];
                   $orderid = $order_details['order_id'];         
                
                    $note_json = array(
                      "subject"=> "Order# ". $orderid ,
                      "description"=>"Order status: Success\nTotal amount:".$grandtotal."\nItems(id-qty):".$noteproductname."\nBilling:".$billingaddress,
                      "contact_ids"=>array($contact_id)
                    );

                    $note_json = json_encode($note_json);
                    $curls = AgileCurl::curl_wrap("notes", $note_json, "POST", "application/json");

                    if(\XLite\Module\AgileCRM\AgileCRM\Main::isSyncContacts()){
                        $billing_address_sync = array(
                          "address"=>$street,
                          "city"=>$city,
                          "state"=>$state,
                          "country"=>$country
                        );

                        $contact_json_update = array(
                          "id"=>$contact_id, //It is mandatory field. Id of contact
                          "tags"=>array("XCart"),
                          "properties"=>array(
                            array(
                              "name"=>"first_name",
                              "value"=>$order_details['billing_address']['firstname'],
                              "type"=>"SYSTEM"
                            ),
                            array(
                              "name"=>"last_name",
                              "value"=>$order_details['billing_address']['lastname'],
                              "type"=>"SYSTEM"
                            ),
                            array(
                              "name"=>"email",
                              "value"=>$order_details['email'],
                              "type"=>"SYSTEM"
                            ),  
                            array(
                                "name"=>"address",
                                "value"=>json_encode($billing_address_sync),
                                "type"=>"SYSTEM"
                            ),
                            array(
                                "name"=>"phone",
                                "value"=>$order_details['billing_address']['phone'],
                                "type"=>"SYSTEM"
                            )
                          )
                        );
                        $contact_json_update = json_encode($contact_json_update);
                        $curlupdate = AgileCurl::curl_wrap("contacts/edit-properties", $contact_json_update, "PUT", "application/json");
                    }
                }          
                
    		}
    	}
    }

    public function fn_js_escape($str)
    {
        return strtr($str, array('\\' => '\\\\',  "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', "\t" => '\\t', '</' => '<\/', "/" => '\\/'));
    }
}