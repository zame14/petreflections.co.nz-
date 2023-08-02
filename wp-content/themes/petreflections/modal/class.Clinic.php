<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/14/2022
 * Time: 11:08 AM
 */
class Clinic extends prBase
{
    function getPets()
    {
        $parent_id = $this->id();
        $relationship_slug = 'pet-clinic';
        $arr = array();
        $posts_array = toolset_get_related_posts(
            $parent_id,
            $relationship_slug,
            array(
                'query_by_role' => 'parent', // origin post role
                'role_to_return' => 'child', // role of posts to return
                'return' => 'post_id', // return array of IDs (post_id) or post objects (post_object)
                'limit' => 999, // max number of results
                'offset' => 0, // starting from
                'orderby' => 'ID',
                'order' => 'DESC',
                'need_found_rows' => false, // also return count of results
                'args' => null // for adding meta queries etc.
            )
        );
        foreach ($posts_array as $post)
        {
            $pet = new Pet($post);
            $arr[] = $pet;
        }
        return $arr;
    }
    public function getCustomField($field)
    {
        return $this->getPostMeta($field);
    }
    public function report_num_pets()
    {
        global $wpdb;
        $sql = 'SELECT count(ID) as pets
        FROM ' . $wpdb->prefix . 'posts
        WHERE post_status = "publish"
        AND post_type = "pet"
        AND post_author = ' . $this->getCustomField('wordpress-user-id') . '
        Group by post_author';
        $results = $wpdb->get_results($sql);

        ($results[0]->pets <> "") ? $result = $results[0]->pets : $result = 0;

        return $result;
    }
    public function report_num_of($meta, $meta_value)
    {
        global $wpdb;
        $sql = 'SELECT count(p.ID) as urns
        FROM ' . $wpdb->prefix . 'posts p
        INNER JOIN ' . $wpdb->prefix . 'postmeta pm
        ON p.ID = pm.post_id
        WHERE post_status = "publish"
        AND post_type = "pet"
        AND post_author = ' . $this->getCustomField('wordpress-user-id') . '
        AND pm.meta_key = "' . $meta . '"
        AND pm.meta_value = "' . $meta_value . '"        
        Group by post_author';
        $results = $wpdb->get_results($sql);

        ($results[0]->urns <> "") ? $result = $results[0]->urns : $result = 0;

        return $result;
    }
    public function report_num_pets_by_date($date1, $date2)
    {
        global $wpdb;
        $sql = 'SELECT count(ID) as pets
        FROM ' . $wpdb->prefix . 'posts
        WHERE post_status = "publish"
        AND post_type = "pet"
        AND post_author = ' . $this->getCustomField('wordpress-user-id') . '
        AND (post_date BETWEEN "' . $date1 . '" AND "' . $date2 . '")
        Group by post_author';
        $results = $wpdb->get_results($sql);

        ($results[0]->pets <> "") ? $result = $results[0]->pets : $result = 0;

        return $result;
    }
    public function report_num_of_by_date($meta, $meta_value, $date1, $date2)
    {
        global $wpdb;
        $sql = 'SELECT count(p.ID) as urns
        FROM ' . $wpdb->prefix . 'posts p
        INNER JOIN ' . $wpdb->prefix . 'postmeta pm
        ON p.ID = pm.post_id
        WHERE post_status = "publish"
        AND post_type = "pet"
        AND post_author = ' . $this->getCustomField('wordpress-user-id') . '
        AND pm.meta_key = "' . $meta . '"
        AND pm.meta_value = "' . $meta_value . '" 
        AND (p.post_date BETWEEN "' . $date1 . '" AND "' . $date2 . '")
        Group by post_author';
        $results = $wpdb->get_results($sql);

        ($results[0]->urns <> "") ? $result = $results[0]->urns : $result = 0;

        return $result;
    }
}