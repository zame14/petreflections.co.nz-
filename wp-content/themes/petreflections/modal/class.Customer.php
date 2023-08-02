<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/14/2022
 * Time: 2:32 PM
 */
class Customer extends prBase
{
    public function getCustomField($field)
    {
        return $this->getPostMeta($field);
    }
    public function getNotifyBy()
    {
        if($this->getPostMeta('customer-notified') == "no") {
            return 'Do not notify';
        } else {
            return $this->getPostMeta('customer-notified');
        }
    }
    public function getNotificationMethod()
    {
        $html = '';
        if($this->getPostMeta('customer-notified') == "email") {
            $html = '<span class="fa fa-envelope"></span>';
        } elseif($this->getPostMeta('customer-notified') == "phone") {
            $html = '<span class="fa fa-phone"></span>';
        } elseif($this->getPostMeta('customer-notified') == "sms") {
            $html = '<span class="fa fa-mobile-phone"></span>';
        } else {
            $html = '-';
        }
        return $html;
    }
}