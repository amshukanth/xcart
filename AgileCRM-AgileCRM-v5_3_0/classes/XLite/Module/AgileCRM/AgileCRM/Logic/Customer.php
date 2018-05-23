<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\AgileCRM\AgileCRM\Logic;


class Customer
{
    
    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return array
     */
    public static function getData(\XLite\Model\Profile $profile)
    {
        $data = [
            'id'                => strval($profile->getProfileId()),
            'email_address'     => $profile->getLogin()
        ];

        $profileAddress = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        if ($profileAddress) {
           $data['address'] = static::getAddress($profileAddress);
        }
        
        return $data;
    }

     /**
     * @param \XLite\Model\Address $address
     *
     * @return array
     */
    public static function getAddress(\XLite\Model\Address $address)
    {  
        
        $address_data = array(
                            'firstname'     => $address->getFirstname(),
                            'lastname'      => $address->getLastname(),
                            'address1'      => $address->getStreet(),
                            'city'          => $address->getCity(),
                            'phone'         => $address->getPhone(),
                            'postal_code'   => $address->getZipcode(),
                             );


        if ($address->getCountry()) {

            $address_data['country']        = $address->getCountry()->getCountry();
            $address_data['country_code']   = $address->getCountry()->getCode();

            if ($address->getState()) {
                $address_data['province']       = $address->getState()->getState();

                if ($address->getCountry()->hasStates()) {
                    $address_data['province_code']  = $address->getState()->getCode();
                }
            }
        }

        return $address_data;
    }

}
