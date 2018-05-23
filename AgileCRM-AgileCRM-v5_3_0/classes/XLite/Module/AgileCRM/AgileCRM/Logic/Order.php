<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\AgileCRM\AgileCRM\Logic;

use XLite\Module\AgileCRM\AgileCRM\Logic\Customer;

class Order
{
    
    /**
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    public static function getData(\XLite\Model\Order $order)
    {
        $orders['profile_id'] = $order->profile->getProfileId();
    	  $orders['order_id'] = $order->orderNumber;
        $orders['email'] = $order->profile->getLogin(); 
        $orders['total'] = $order->getTotal();
        $profile_data = Customer::getData($order->profile);
        
        foreach($order->items as $item){
            $orders['items'][] = array(
                                    'item_id' => $item->item_id,
                                    'item_name' => $item->name,
                                    'item_price' => $item->price,
                                    'item_net_price' => $item->itemNetPrice,                                      
                                      );
        }

        $orders['billing_address'] = $profile_data['address'];

    	  return $orders;
    }

}