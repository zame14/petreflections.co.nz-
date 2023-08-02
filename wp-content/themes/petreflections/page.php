<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
get_header();
global $post;
$container_class = 'container';
$page_title = get_the_title();
switch ($post->ID) {
    case 16:
        $page_title = 'Vet Registration';
        break;
    case 81:
        $page_title = 'Log a New Pet';
        break;
    case 29:
        $user = wp_get_current_user();
        if($user->id <> 0) {
            if(current_user_can('administrator')) {
                $page_title = 'Pet Reflections Admin';
            } else {
                $clinic = getVetByUserID($user->id);
                $page_title = $clinic[0]->getTitle();
            }
        } else {
            $page_title = get_the_title();
        }
        $container_class = 'container-fluid';
        break;
    case 331:
        $container_class = 'container-fluid';
        break;
    default:
        $page_title = get_the_title();
}
?>
<div class="wrapper" id="page-wrapper">
    <?php
    if(is_front_page()) { ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 no-padding">
                    <div class="home-banner-wrapper">
                        <?= get_the_post_thumbnail($post->ID, 'full') ?>
                        <div class="banner-content-wrapper ani-in">
                            <div class="inner-wrapper">
                                <?=get_field('banner_content', 5)?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } else {
        if (has_post_thumbnail($post->ID)) { ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 no-padding">
                        <div class="inside-banner-wrapper">
                            <?= get_the_post_thumbnail($post->ID, 'full') ?>
                            <div class="page-title ani-in">
                                <h1><?= $page_title; ?></h1>
                                <div class="inner-wrapper">
                                    <?=get_field('banner_quote', $post->ID)?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else { ?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h1><?= $page_title; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
    }
    ?>
    <div id="content" class="<?=$container_class?>">
        <div class="row">
            <div class="col-12">
                <main class="site-main" id="main">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        switch ($post->ID) {
                            case 326:
                                get_template_part('loop-templates/content', 'report');
                                break;
                            default:
                                get_template_part('loop-templates/content', 'page');
                        }
                        ?>
                    <?php endwhile; // end of the loop. ?>
                </main>
            </div>
        </div>
    </div>
</div><!-- #page-wrapper -->
<?php
get_footer();