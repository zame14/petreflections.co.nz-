<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/6/2022
 * Time: 9:39 AM
 */
class Story extends prBase
{
    public function getFeatureImage($size = 'full')
    {
        return get_the_post_thumbnail($this->id(), $size);
    }
    function previous() {
        global $wpdb;
        $sql = '
        SELECT p.ID
        FROM ' . $wpdb->prefix . 'posts p
        WHERE p.ID < ' . $this->Post->ID . '
        AND post_status="publish" 
        AND post_type="story" 
        ORDER BY p.ID DESC
        LIMIT 1';
        $result = $wpdb->get_results($sql);

        $previd = $result[0]->ID;
        if($previd == "") {
            $sql1 = '
            SELECT p.ID 
            FROM ' . $wpdb->prefix . 'posts p
            WHERE post_status="publish" 
            AND post_type="story"
            ORDER BY p.ID DESC
            LIMIT 1';
            $result1 = $wpdb->get_results($sql1);

            $previd = $result1[0]->ID;

        }

        return new Story($previd);
    }
    public function next()
    {
        global $wpdb;
        $sql = '
        SELECT p.ID 
        FROM ' . $wpdb->prefix . 'posts p
        WHERE p.ID > ' . $this->Post->ID . '
        AND post_status="publish" 
        AND post_type="story" 
        ORDER BY p.ID ASC
        LIMIT 1';
        $result = $wpdb->get_results($sql);

        $nextid = $result[0]->ID;
        if($nextid == "") {
            $sql1 = '
            SELECT p.ID 
            FROM ' . $wpdb->prefix . 'posts p
            WHERE post_status="publish" 
            AND post_type="story"
            ORDER BY p.ID ASC
            LIMIT 1';
            $result1 = $wpdb->get_results($sql1);

            $nextid = $result1[0]->ID;

        }
        return new Story($nextid);
    }
}