<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/14/2022
 * Time: 2:06 PM
 */
class Pet extends prBase
{
    public function getCustomField($field)
    {
        return $this->getPostMeta($field);
    }
    public function getPetType()
    {
        $pet_type = $this->getPostMeta('pet-type');
        if($this->getPostMeta('pet-type') == "other") {
            $pet_type = $this->getPostMeta('pet-type-other');
        }
        return ucfirst($pet_type);
    }
    public function getOwner()
    {
        $customer_id = toolset_get_related_post( $this->id(), 'pet-owner', 'parent');
        return new Customer($customer_id);
    }
    public function getClinic()
    {
        $vet_id = toolset_get_related_post( $this->id(), 'pet-clinic', 'parent');
        return new Clinic($vet_id);
    }
    public function getFeatureImage()
    {
        if (has_post_thumbnail($this->id())) {
            return get_the_post_thumbnail($this->id(), 'pet');
        } else {
            $upload_dir = wp_upload_dir();
            return '<img src="' . $upload_dir['baseurl'] . '/2022/09/blank.jpg" alt="' . $this->getTitle() . '" />';
        }
    }
    public function getReturnedIn()
    {
        if($this->getPostMeta('pet-ashes-returned-in') == "urn") {
            $html = 'Wooden Urn - ' . $this->getPostMeta('pet-wooden-urn-type');
            return $html;
        } else {
            return $this->getPostMeta('pet-ashes-returned-in');
        }
    }
    public function getJewellery($label = true)
    {
        if($this->getPostMeta('pet-memorial-jewellery') == "yes") {
            if($label) {
                $html = 'Yes - ' . $this->getPostMeta('memorial-jewellery-type');
            } else {
                $html = 'Yes';
            }
            return $html;
        } else {
            return 'No';
        }
    }
    public function getReturnTo()
    {
        if($this->getPostMeta('pet-delivery-options') == "clinic") {
            $clinic = $this->getClinic();
            return $clinic->getTitle();
        } else {
            $owner = $this->getOwner();
            return $owner->getCustomField('customer-address');
        }
    }
    public function deliverTo()
    {
        $html = '';
        if($this->getPostMeta('pet-ashes-returned') == "yes") {
            if($this->getPostMeta('pet-delivery-options') == "clinic") {
                $html = 'Clinic';
            } else {
                $html = 'Owner';
            }
        } else {
            $html = '-';
        }
        return $html;
    }
    public function getWoodenUrn()
    {
        if($this->getPostMeta('pet-ashes-returned-in') == "urn") {
            $html = 'Yes';
            return $html;
        } else {
            return 'No';
        }
    }
    public function notificationEmail($i)
    {
        $owner = $this->getOwner();
        $vet = $this->getClinic();
        $phone = get_field('phone',14);
        $meta = 'notification-stage-' . $i;
        $subject_meta = 'notification-subject-' . $i;
        if($i == 4) {
            $subject_meta = 'notification-subject-3';
        }
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $to = $owner->getPostMeta('customer-email');
        $subject = get_field($subject_meta, 5);
        $subject = str_replace('{pet_name}', $this->getCustomField('pet-name'), $subject);
        $message = '<html>';
        $message .= get_field($meta, 5);
        $message .= '</html>';
        $message = str_replace('{pet_name}', $this->getCustomField('pet-name'), $message);
        $message = str_replace('{clinic}', $vet->getTitle(), $message);
        $message = str_replace('{phone}', $phone, $message);
        wp_mail($to, $subject, $message, $headers);
    }
    public function reminderEmail()
    {
        $owner = $this->getOwner();
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $to = $owner->getPostMeta('customer-email');
        //$to = 'aaron.zame@gmail.com';
        $subject = 'Honoring the memory of ' . $this->getCustomField('pet-name');
        $message = '<html>';
        $message .= get_field('one_week_reminder',5);
        $message .= '</html>';
        $message = str_replace('{pet_name}', $this->getCustomField('pet-name'), $message);
        wp_mail($to, $subject, $message, $headers);
    }
    public function updateDeliveryDate()
    {
        // get todays date
        date_default_timezone_set('Pacific/Auckland');
        $date = date("d/m/Y");
        update_post_meta($this->id(), 'wpcf-pet-date-delivered', $date);
    }
    public function updateServiceCompletedDate()
    {
        // get todays date
        date_default_timezone_set('Pacific/Auckland');
        $date = date("d/m/Y");
        update_post_meta($this->id(), 'wpcf-pet-date-service-completed', $date);
    }
}