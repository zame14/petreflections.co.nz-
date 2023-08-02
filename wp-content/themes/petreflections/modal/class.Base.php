<?php

class prBase
{
    public $Post = null;

    function __construct($post)
    {
        // If an ID is passed instead then change for a post array
        if(intval($post)) $post = get_post($post);
        $this->Post = $post;
    }

    public function id() {
        return $this->Post->ID;
    }

    function getPostMeta($meta, $prefix = true) {
        if($prefix) $meta = 'wpcf-' . $meta;
        return get_post_meta($this->Post->ID, $meta, true);
    }


    public function getTitle()
    {
        $title = get_the_title($this->Post);
        return $title;
    }

    public function getContent($vc = false)
    {
        $content = wpautop($this->Post->post_content);
        if($vc)
        {
            $content = $this->Post->post_content;
            $content = apply_filters( 'the_content', $content );
        }
        return $content;
    }

    public function link()
    {
        return esc_url(get_the_permalink($this->Post));
    }
    /*
        public function getImage($size = 'large')
        {
            return get_the_post_thumbnail($this->Post, $size);
        }
    */
    public function getPostStatus()
    {
        return $this->Post->post_status;
    }
    public function getPostDate()
    {
        return get_the_date('d/m/Y', $this->Post->ID);
    }
    public function getMenuPosition()
    {
        return $this->Post->menu_order;
    }
}