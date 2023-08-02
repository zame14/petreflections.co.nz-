<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/15/2022
 * Time: 11:24 AM
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
    <div class="wrapper" id="page-wrapper">
        <div id="content" class="container">
            <div class="row">
                <div class="col-12">
                    <main class="site-main" id="main">
                        <?=get_template_part('loop-templates/content', 'pet')?>
                    </main>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();