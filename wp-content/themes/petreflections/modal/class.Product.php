<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/14/2022
 * Time: 9:22 AM
 */
class Product extends prBase
{
    public function getProductImage($size = 'full')
    {
        return get_the_post_thumbnail($this->Post, $size);
    }
}