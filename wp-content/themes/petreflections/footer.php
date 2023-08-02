<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<section id="footer">
    <div class="container">
        <div class="row">
            <div class="col-12 title">
                Pet Reflections
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="inner-wrapper">
                    <?php wp_nav_menu(
                        array(
                            'theme_location'  => 'footer-menu',
                            'container_class' => 'footer-menu-wrapper',
                            'container_id'    => '',
                            'menu_class'      => '',
                            'fallback_cb'     => '',
                            'menu_id'         => 'footer-menu',
                        )
                    ); ?>
                    <?=myAccountMenu()?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 contact-details">
                <ul>
                    <li><a href="mailto:<?=get_field('email',14)?>"><?=get_field('email',14)?></a></li>
                    <li><a href="tel:<?=formatPhoneNumber(get_field('phone',14))?>"><?=get_field('phone',14)?></a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="copyright">
                    <ul>
                        <li>&copy; Copyright <?=date('Y')?> <?=get_bloginfo('name')?></li>
                        <li class="site-by">Custom Website by <a href="https://www.azwebsolutions.co.nz/" target="_blank">A-Z Web Solutions<span class="az"></span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
    if(is_active_sidebar('footerwidget')){
        dynamic_sidebar('footerwidget');
    }
    ?>
</section>
</div><!-- #page we need this extra closing tag here -->
<?php wp_footer(); ?>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="<?=get_stylesheet_directory_uri()?>/js/theme.js" type="text/javascript"></script>
</body>
</html>