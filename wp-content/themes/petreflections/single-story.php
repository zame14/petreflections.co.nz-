<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/6/2022
 * Time: 10:31 AM
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
    <div class="wrapper" id="page-wrapper">
        <div id="content" class="container">
            <div class="row">
                <div class="col-12">
                    <main class="site-main" id="main">
                        <?=get_template_part('loop-templates/content', 'story')?>
                    </main>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();