<?php

namespace XLite\Module\AgileCRM\AgileCRM;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'AgileCRM Team';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Agile CRM';
    }

    /**
     * Get module major version
     *
     * @return string
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return 0;
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Agile CRM is an all-in-one, affordable and next-gen Customer Relationship Management (CRM) software with marketing, sales and service automation, built with love for small businesses.';
    }

    /**
     * Determines if we need to show settings form link
     *
     * @return boolean
     */
    public static function showSettingsForm()
    {
        return true;
    }

    public function agile_domain(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->agile_domain;
    }

    public function agile_email(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->agile_email;
    }

    public function agile_rest_api_key(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->agile_rest_api_key;
    }

    public function isSyncContacts(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->sync_customers;
    }


    public function isSyncOrders(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->sync_orders;
    }

    public function isWebrules(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->web_rules;
    }

    public function isWebstats(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->web_stats;
    }

    public function isImportCustomers(){

        return \XLite\Core\Config::getInstance()->AgileCRM->AgileCRM->import_customers;
    }

    public function getCurrentUserEmail(){

        if(\XLite\Core\Auth::getInstance()->getProfile())
            return \XLite\Core\Auth::getInstance()->getProfile()->getLogin();
        else
            return "";

    }
    /**
     * Log always
     *
     * @param $message
     * @param $data
     */
    public static function logError($message, $data)
    {
        \XLite\Logger::logCustom('agilecrm_error', [
            'message'   => $message,
            'data'      => $data
        ]);
    }
}