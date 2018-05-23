<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\AgileCRM\AgileCRM\View\Header;

use XLite\Module\AgileCRM\AgileCRM;

/**
 * Header declaration (Agile Universal)
 *
 * @ListChild (list="head", zone="customer")
 */
class Agile_universal extends \XLite\Module\AgileCRM\AgileCRM\View\Header\AHeader
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/AgileCRM/AgileCRM/header/agile_universal.twig';
    }

    protected function getAgileDomain(){

        return AgileCRM\Main::agile_domain();
    }

    protected function getAgileEmail(){

        return AgileCRM\Main::agile_email();
    }

    protected function getAgileRestApiKey(){

        return AgileCRM\Main::agile_rest_api_key();
    }

    protected function isSyncContacts(){

        return AgileCRM\Main::isSyncContacts();
    }

    protected function isSyncOrders(){

        return AgileCRM\Main::isSyncOrders();
    }

    protected function isWebrules(){

        return AgileCRM\Main::isWebrules();
    }

    protected function isWebstats(){

        return AgileCRM\Main::isWebstats();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisibleForCustomer()
    {
        return true;
    }

    /**
     * Defines the default value for email
     *
     * @return string
     */
    protected function getEmail()
    {
        return AgileCRM\Main::getCurrentUserEmail();
    }
}
