<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/25/2022
 * Time: 10:47 AM
 */
class Log extends prBase
{
    public function getCustomField($field)
    {
        return $this->getPostMeta($field);
    }
    public function getLogDate()
    {
        $timestamp = get_post_meta($this->id(), 'wpcf-cremation-date-time', false);
        return date('F j Y',$timestamp[0]);
    }
    public function getLogTime()
    {
        $timestamp = get_post_meta($this->id(), 'wpcf-cremation-date-time', false);
        return date('g:i a',$timestamp[0]);
    }
}