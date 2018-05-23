<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\AgileCRM\AgileCRM\Controller\Admin;

use XLite\Module\AgileCRM\AgileCRM\Logic\AgileCurl;
use XLite\Module\AgileCRM\AgileCRM\Logic\Customer;
use XLite\Module\AgileCRM\AgileCRM\Controller\Customer\Profile;

/**
 * Module settings
 */
class Module extends \XLite\Controller\Admin\Module implements \XLite\Base\IDecorator
{   

    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {   
        $moduleId = \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->findOneBy(array('name' => 'AgileCRM'))
            ->getModuleID();

        if ($moduleId == $this->getModuleId()) {

            $agile_domain = \XLite\Core\Request::getInstance()->agile_domain;
            $agile_email = \XLite\Core\Request::getInstance()->agile_email;
            $agile_rest_api_key = \XLite\Core\Request::getInstance()->agile_rest_api_key;
            $agile_old = \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->agile_domain;
            $import_customers = \XLite\Core\Request::getInstance()->import_customers;

            $result = static::checkAgileApi("api-key","application/json", $agile_domain, $agile_email, $agile_rest_api_key);
            $arr = json_decode($result, TRUE);
            extract($arr);
            $rest_api = $api_key;
            file_put_contents("profiles.txt", print_r($profiles,true));
            if ($rest_api){
                
                parent::doActionUpdate();
                if($import_customers){
                    foreach (\XLite\Core\Database::getRepo('XLite\Model\Profile')->findAllCustomerAccounts() as $profile) {
                        $profile_data = Customer::getData($profile);
                        Profile::createAgileContact($profile_data);
                    }
                }
            }
            else{
              \XLite\Core\TopMessage::addError("Invalid Domain or Email or API Key");
            }
        }


    }

    public static function checkAgileApi($entity, $content_type, $agile_domain, $agile_email, $agile_rest_api_key) {

        if ($content_type == NULL) {
            $content_type = "application/json";
        }
        
        $agile_url = "https://" . $agile_domain . ".agilecrm.com/dev/api/" . $entity;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true);
        curl_setopt($ch, CURLOPT_URL, $agile_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
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