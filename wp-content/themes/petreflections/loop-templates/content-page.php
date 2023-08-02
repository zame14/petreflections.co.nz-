<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/8/2022
 * Time: 2:28 PM
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php the_content(); ?>
</article>