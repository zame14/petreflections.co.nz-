<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/6/2022
 * Time: 10:32 AM
 */
global $post;
$story = new Story($post->ID);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="row">
        <div class="col-12">
            <div class="page-title">
                <h1>A Place to Share</h1>
            </div>
        </div>
    </div>
    <div class="row align-items-center">
        <div class="col-12 col-md-6 left-col">
            <?=$story->getFeatureImage()?>
        </div>
        <div class="col-12 col-md-6 right-col">
            <h2><?=$story->getTitle()?></h2>
            <div class="description">
                <?=$story->getContent()?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="navigation-wrapper">
            <?php
            $previous = $story->previous();
            if($previous->id() <> "") {
                echo '<a href="' . $previous->link() . '" class="prev">
                    Previous <span class="title">' . $previous->getTitle() . '</span>
                </a>';
            }
            echo '<a href="' . get_page_link(15) . '" class="listing"><span class="fa fa-th"></span></a>';
            $next = $story->next();
            if($next->id() <> "") {
                echo '<a href="' . $next->link() . '" class="next">
                    Next <span class="title">' . $next->getTitle() . '</span>
                </a>';
            }
            ?>
            </div>
        </div>
    </div>
</article>
