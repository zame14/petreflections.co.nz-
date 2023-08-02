<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
require_once('modal/class.Base.php');
require_once('modal/class.Product.php');
require_once('modal/class.Clinic.php');
require_once('modal/class.Pet.php');
require_once('modal/class.Customer.php');
require_once('modal/class.Story.php');
require_once('modal/class.Log.php');
require_once('modal/class.WPAjax.php');
add_action( 'wp_enqueue_scripts', 'p_enqueue_styles');
function p_enqueue_styles() {
    //wp_enqueue_style( 'bootstrap-css', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style( 'font-awesome', get_stylesheet_directory_uri() . '/css/font-awesome.min.css');
    //wp_enqueue_style( 'slick', get_stylesheet_directory_uri() . '/slick-carousel/slick/slick.css');
    //wp_enqueue_style( 'slick-theme', get_stylesheet_directory_uri() . '/slick-carousel/slick/slick-theme.css');
    wp_enqueue_style( 'understrap-theme', get_stylesheet_directory_uri() . '/style.css');
}
//add_image_size( 'grid', 600, 400, true);
function understrap_remove_scripts() {
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );

    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );
function dg_remove_page_templates( $templates ) {
    unset( $templates['page-templates/blank.php'] );
    unset( $templates['page-templates/right-sidebarpage.php'] );
    unset( $templates['page-templates/both-sidebarspage.php'] );
    unset( $templates['page-templates/empty.php'] );
    unset( $templates['page-templates/fullwidthpage.php'] );
    unset( $templates['page-templates/left-sidebarpage.php'] );
    unset( $templates['page-templates/right-sidebarpage.php'] );

    return $templates;
}
add_filter( 'theme_page_templates', 'dg_remove_page_templates' );
add_image_size( 'pet', 400, 400, true);
add_action('cred_save_data', 'insert_new_custom_post',10,2);
function insert_new_custom_post($post_id, $form_data)
{
    if ($form_data['id'] == 27) {
        // vet has registered
        $post_name = $_POST['wpcf-vet-clinic-name'];
        $my_post = array(
            'post_title' => wp_strip_all_tags($post_name),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'clinic',
            'post_author' => 1
        );
        $new_post_id = wp_insert_post($my_post);
        update_post_meta($new_post_id, 'wpcf-vet-firstname', $_POST['first_name']);
        update_post_meta($new_post_id, 'wpcf-vet-lastname', $_POST['last_name']);
        update_post_meta($new_post_id, 'wpcf-vet-email', $_POST['user_email']);
        update_post_meta($new_post_id, 'wpcf-vet-phone', $_POST['user_phone']);
        update_post_meta($new_post_id, 'wpcf-wordpress-user-id', $post_id);
    }
    if ($form_data['id'] == 75) {
        //print_r($_POST);
        //exit;
        //vet has submitted a new pet
        // create new customer record
        $post_name = $_POST['customer-firstname'] . ' ' . $_POST['customer-lastname'];
        $my_post = array(
            'post_title' => wp_strip_all_tags($post_name),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'customer',
            'post_author' => 1
        );
        $new_post_id = wp_insert_post($my_post);
        update_post_meta($new_post_id, 'wpcf-customer-phone', $_POST['customer-phone']);
        update_post_meta($new_post_id, 'wpcf-customer-email', $_POST['customer-email']);
        update_post_meta($new_post_id, 'wpcf-customer-address', $_POST['customer-address']);
        update_post_meta($new_post_id, 'wpcf-customer-notified', $_POST['customer-notified']);

        // update pet record with custom title
        $post_name = strtolower($_POST['wpcf-pet-name']) . '-' . strtolower($_POST['wpcf-pet-tag-id']);
        $args = array(
            'ID' => $post_id,
            'post_title' => wp_strip_all_tags($post_name),
            'post_name' => wp_strip_all_tags($post_name)
        );
        //remove_action( 'cred_save_data', 'insert_new_custom_post' );
        wp_update_post($args);
        //add_action( 'cred_save_data', 'insert_new_custom_post' );

        //set status of the pet
        update_post_meta($post_id, 'wpcf-pet-status', get_field('status-1',5));
        // if returning pet in urn, add urn type
        if($_POST['wpcf-pet-ashes-returned-in'] == "urn") {
            update_post_meta($post_id, 'wpcf-pet-wooden-urn-type', $_POST['wooden-urn']);
            update_post_meta($post_id, 'wpcf-pet-plaque', $_POST['plaque']);
        }
        // check if select jewellery
        if($_POST['wpcf-pet-memorial-jewellery'] == "yes") {
            update_post_meta($post_id, 'wpcf-memorial-jewellery-type', $_POST['jewellery']);
        }
        // link up customer with pet
        toolset_connect_posts( 'pet-owner', $new_post_id, $post_id );

        //link up vet with pet
        $clinic = getVetByUserID($_POST['vet-id']);
        toolset_connect_posts( 'pet-clinic', $clinic[0]->id(), $post_id );

        //lastly if customer wants to be notified via email, send out notification to customer that their pet is ready to be pick up from vets.
        if($_POST['customer-notified'] == "email") {
            //send email to customer
            $pet = new Pet($post_id);
            $pet->notificationEmail(1);
        }
    }
    if ($form_data['id'] == 415) {
        $date = str_replace(",","",$_REQUEST['wpcf-cremation-date-time']['display-only']);
        // update pet record with custom title
        $post_name = 'Log-' . $date;
        $args = array(
            'ID' => $post_id,
            'post_title' => wp_strip_all_tags($post_name),
            'post_name' => wp_strip_all_tags($post_name)
        );
        wp_update_post($args);
    }
}
function newRegistrationMgs_shortcode()
{
    $html = '';
    if(isset($_REQUEST['cred_referrer_form_id']) && $_REQUEST['cred_referrer_form_id'] <> "") {
        $html = '<p>Thank you for registering. Please log in below with your username and password that you registered with.</p>';
    } else {
        $html = '<p>Please log in below with your vet account.</p>';
    }
    return $html;
}
add_shortcode('registration_msg','newRegistrationMgs_shortcode');
function isVetLoggedIn()
{
    $user = wp_get_current_user();
    if($user->id <> "") {
        if ( in_array( 'subscriber', (array) $user->roles ) ) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function myAccountMenu()
{
    $html = '<ul>';
    if(is_user_logged_in()) {
        $html .= '<li><a href="' . get_page_link(29) . '">Vet Link</a></li>';
        $html .= '<li><a href="' . wp_logout_url('/') . '">Log Out</a></li>';
    } else {
        $html .= '<li><a href="' . get_page_link(29) . '">Vet Login</a></li>';
        //$html .= '<li><a href="' . get_page_link(16) . '">Vet Registration</a></li>';
    }
    $html .= '
    </ul>';
    return $html;
}
function myAccount_shortcode()
{
    $html = '';
    $user = wp_get_current_user();

    if($user->id <> 0) {
        $html = '
        <div class="my-account-wrapper">
            <div class="container">
                <div class="intro-wrapper">Click on the pet tag ID to view full pet details</div>';
                if(isset($_REQUEST['cred_referrer_form_id']) && $_REQUEST['cred_referrer_form_id'] <> "27") {
                    $html .= '<div class="notification">
                        <span class="fa fa-times" onclick="hideNotification()"></span>
                        ' . get_field('vet_logs_a_new_pet',5) . '
                    </div>';
                }
                $html .= '
                <div class="log-a-pet-wrapper">
                    <a href="' . get_page_link(81) . '" class="btn btn-primary">Log a new pet</a>
                </div>
            </div>';
        $html .= '
            <div class="table-responsive">';
                $html .= pet_table();
            $html .= '    
            </div>    
        </div>';
        echo $html;
    }
}
add_shortcode('my_account_module', 'myAccount_shortcode');
function getProductsByCategory($cat_id, $orderby = 'ID', $order = 'asc')
{
    $products = Array();
    $posts_array = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => $orderby,
        'order' => $order,
        'tax_query' => array(
            array(
                'taxonomy' => 'product-category',
                'field' => 'term_id',
                'terms' => $cat_id,
                'operator' => 'IN'
            )
        )
    ]);
    foreach ($posts_array as $post) {
        $product = new Product($post);
        $products[] = $product;
    }
    return $products;
}
function products_shortcode($atts)
{
    ($atts['term_id'] == 6) ? $name = 'wooden-urn' : $name = 'jewellery';
    $html = '<div class="products-wrapper">';
    foreach (getProductsByCategory($atts['term_id']) as $product) {
        $html .= '<div class="inner-wrapper">
            <div class="image-wrapper">
                ' . $product->getProductImage() . '
            </div>
            <div class="content-wrapper">
                <h3>' . $product->getTitle() . '</h3>
                <input type="radio" name="' . $name . '" value="' . $product->getTitle() . '" class="form-check-input wpt-form-radio form-radio radio" />                
            </div>
        </div>';
    }
    $html .= '
    </div>';

    return $html;
}
add_shortcode('products','products_shortcode');
function plaques_shortcode()
{
    $html = '<select id="plaque" name="plaque" class="form-control wpt-form-select form-select select">
        <option value="" selected="selected" class="wpt-form-option form-option option">--- not set ---</option>';
        foreach (getProductsByCategory(12) as $product) {
            $html .= '<option value="' . $product->getTitle() . '" class="wpt-form-option form-option option">' . $product->getTitle() . '</option>';
        }
    $html .= '
    </select>';
    return $html;
}
add_shortcode('plaques','plaques_shortcode');
function vet_id_field_shortcode()
{
    $user = wp_get_current_user();
    $html = '<input type="hidden" name="vet-id" class="wpt-form-hidden form-hidden" value="' . $user->id . '" />';
    return $html;
}
add_shortcode('vet_id_field','vet_id_field_shortcode');
function vet_name_field_shortcode()
{
    $user = wp_get_current_user();
    $clinic = getVetByUserID($user->id);
    $html = '<input type="hidden" name="vet-name" class="wpt-form-hidden form-hidden" value="' . $clinic[0]->getTitle() . '" />';
    return $html;
}
add_shortcode('vet_name_field','vet_name_field_shortcode');
function getVetByUserID($user_id)
{
    $arr = Array();
    $posts_array = get_posts([
        'post_type' => 'clinic',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'ID',
        'order' => 'ASC',
        'meta_query' => [
            [
                'key' => 'wpcf-wordpress-user-id',
                'value' => $user_id
            ]
        ],
    ]);
    foreach ($posts_array as $post) {
        $clinic = new Clinic($post);
        $arr[] = $clinic;
    }
    return $arr;
}
function getPets()
{
    $arr = Array();
    $posts_array = get_posts([
        'post_type' => 'pet',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'ID',
        'order' => 'DESC'
    ]);
    foreach ($posts_array as $post) {
        $pet = new Pet($post);
        $arr[] = $pet;
    }
    return $arr;
}
function getPetsByStatus($status)
{
    $arr = Array();
    $posts_array = get_posts([
        'post_type' => 'pet',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'ID',
        'order' => 'DESC',
        'meta_query' => [
            [
                'key' => 'wpcf-pet-status',
                'value' => $status
            ]
        ],
    ]);
    foreach ($posts_array as $post) {
        $pet = new Pet($post);
        $arr[] = $pet;
    }
    return $arr;
}
function getPetsForReminder()
{
    $arr = Array();
    $posts_array = get_posts([
        'post_type' => 'pet',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'ID',
        'order' => 'DESC',
        'meta_query' => [
            [
                'key' => 'wpcf-pet-reminder-sent',
                'value' => 0
            ]
        ],
    ]);
    foreach ($posts_array as $post) {
        $pet = new Pet($post);
        $arr[] = $pet;
    }
    return $arr;
}
function getLogs()
{
    $arr = Array();
    $posts_array = get_posts([
        'post_type' => 'cremation',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'ID',
        'order' => 'DESC'
    ]);
    foreach ($posts_array as $post) {
        $log = new Log($post);
        $arr[] = $log;
    }
    return $arr;
}
function selectStatus($pet_id)
{
    $pet = new Pet($pet_id);
    $current_status = $pet->getCustomField('pet-status');
    $selected = '';
    $html = '<select class="status" data-id="' . $pet->id() . '">';
        for($i=1; $i <= 8; $i++) {
            $selected = '';
            $meta = 'status-' . $i;
            if(get_field($meta, 5) == $current_status) {
                $selected = 'selected';
            }
            $html .= '<option value="' . get_field($meta, 5) . '" ' . $selected . '>' . get_field($meta, 5) . '</option>';
        }
    $html .= '</select>';
    return $html;
}
/**************** Ajax **************************/
add_action('wp_head', function() {
    echo '<script type="text/javascript">
       var ajaxurl = "' . admin_url('admin-ajax.php') . '";
     </script>';
});
add_action('wp_ajax_ajax', function() {
    $WPAjax = new WPAjax($_GET['call']);
});
add_action('wp_ajax_nopriv_ajax', function() {
    $WPAjax = new WPAjax($_GET['call']);
});
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
    global $wp_meta_boxes;

    wp_add_dashboard_widget('my_account', 'My Account' , 'custom_dashboard_calendar');
}
function custom_dashboard_calendar() {
    echo '<div><a href="https://www.petreflections.co.nz/my-account/" target="_blank">View Pets</a></div>';
    echo '<div><a href="https://www.petreflections.co.nz/reports/" target="_blank">Reports</a></div>';
    echo '<div><a href="https://www.petreflections.co.nz/logs/" target="_blank">Cremation Logs</a></div>';
    echo '<div><a href="https://www.petreflections.co.nz/register/" target="_blank">Register a new vet clinic</a></div>';
}
function vetRegistersNotification_shortcode()
{
    $html = '<div class="my-account-wrapper">';
    if(isset($_REQUEST['cred_referrer_form_id'])) {
        $html .= '<div class="notification">
                    ' . get_field('vet_registers',5) . '
                </div>';
    }
    $html .= '</div>';
    return $html;
}
add_shortcode('vet_registers_notification','vetRegistersNotification_shortcode');

function pet_table()
{
    $user = wp_get_current_user();
    (current_user_can('administrator')) ? $role = 'admin' : $role = 'vet';
    $vet = '';
    if($role == 'admin') {
        $pets = getPets();
        $col_span = 9;
    } else {
        $clinic = getVetByUserID($user->id);
        $arr_data = $clinic[0]->getPets();
        $pets = array_reverse($arr_data);
        $col_span = 6;
    }
    $count = count($pets);
    $html = '<div class="filter-wrapper container">
        <input type="text" class="search_tag form-control" placeholder="search by pet tag id" />
        <input type="text" class="search_owner form-control" placeholder="search by owner" />
    </div>';
    if($role == 'admin') {
        $html .= '<div class="toggle-view">
            <a href="javascript:;" onclick="toggleTable(1)">
                <span class="fa fa-toggle-off"></span><strong>turn on view by status</strong>
            </a>
        </div>';
    }
    $html .= '
    <table class="table table-striped results">
        <thead>
            <tr>
                <th>Pet Tag ID</th>
                <th>Pet Name</th>
                <th>Pet Type</th>
                <th>Owner</th>';
                if($role=="admin") {
                    $html .= '<th>Vet Clinic</th>
                    <th class="center-me">Return Ashes</th>
                    <th class="center-me">Wooden Urn</th>
                    <th class="center-me">Jewellery</th>
                    <th class="center-me">Notify Via</th>
                    <th>Deliver to</th>';
                } else {
                    $html .= '<th>Phone</th>';
                }
                $html .= '
                <th>Status</th>
            </tr>
            <tr class="warning no-result">
                <td class="center-me" colspan="' . $col_span . '"><i class="fa fa-warning"></i>&nbsp;No records found.&nbsp;&nbsp;<a href="' . get_page_link(29) . '">Try again</a></td>
            </tr>
        </thead>
        <tbody>';
        if ($count > 0) {
            foreach ($pets as $pet) {
                if($role == 'admin') {
                    $vet = $pet->getClinic();
                }
                $owner = $pet->getOwner();
                $html .= '<tr>
                    <td><a href="' . $pet->link() . '">' . $pet->getCustomField('pet-tag-id') . '</a></td>
                    <td>' . $pet->getCustomField('pet-name') . '</td>
                    <td>' . $pet->getPetType() . '</td>
                    <td>' . $owner->getTitle() . '</td>';
                    if($role == 'admin') {
                        $html .= '<td>' . $vet->getTitle() . '</td>
                        <td class="center-me">' . ucfirst($pet->getCustomField('pet-ashes-returned')) . '</td>
                        <td class="center-me">' . $pet->getWoodenUrn() . '</td>
                        <td class="center-me">' . $pet->getJewellery(false) . '</td>
                        <td class="center-me">' . $owner->getNotificationMethod() . '</td>
                        <td>' . $pet->deliverTo() . '</td>
                        <td>' . selectStatus($pet->id()) . '</td>';
                    } else {
                        $html .= '<td>' . $owner->getCustomField('customer-phone') . '</td>
                        <td>' . $pet->getCustomField('pet-status') . '</td>';
                    }
                    $html .= '
                </tr>';
            }
        } else {
                $html .= '
                <tr>
                    <td class="center-me" colspan="' . $col_span . '">There are currently no pets logged</td>
                </tr>';
        }
        $html .= '
        </tbody>
    </table>';
    return $html;
}
function pet_table_status_view()
{
    $html = '
    <div class="toggle-view">
        <a href="javascript:;" onclick="toggleTable(2)">
            <span class="fa fa-toggle-on"></span><strong>turn off view by status</strong>
        </a>   
    </div>';
    // loop through each status
    for($i=1; $i <= 8; $i++) {
        $meta = 'status-' . $i;
        $pets = getPetsByStatus(get_field($meta, 5));
        $count = count($pets);
        $html .= '<div class="status-section status-' . $i . '">
            <h3>' . get_field($meta, 5) . '</h3>
            <table class="table table-striped results">
                <thead>
                    <tr>
                        <th>Pet Tag ID</th>
                        <th>Pet Name</th>
                        <th>Pet Type</th>
                        <th>Owner</th>
                        <th>Vet Clinic</th>
                        <th class="center-me">Return Ashes</th>
                        <th class="center-me">Wooden Urn</th>
                        <th class="center-me">Jewellery</th>
                    </tr>
                </thead>
                <tbody>';
                if ($count > 0) {
                    foreach ($pets as $pet) {
                        $vet = $pet->getClinic();
                        $owner = $pet->getOwner();
                        $html .= '
                        <tr>
                            <td><a href="' . $pet->link() . '">' . $pet->getCustomField('pet-tag-id') . '</a></td>
                            <td>' . $pet->getCustomField('pet-name') . '</td>
                            <td>' . $pet->getPetType() . '</td>
                            <td>' . $owner->getTitle() . '</td>
                            <td>' . $vet->getTitle() . '</td>
                            <td class="center-me">' . ucfirst($pet->getCustomField('pet-ashes-returned')) . '</td>
                            <td class="center-me">' . $pet->getWoodenUrn() . '</td>
                            <td class="center-me">' . $pet->getJewellery(false) . '</td>
                        </tr>';
                    }
                } else {
                    $html .= '
                    <tr>
                        <td class="center-me" colspan="8">There are currently no pets in this status</td>
                    </tr>';
                }
                $html .= '
                </tbody>
            </table>                                                                                       
        </div>';
    }
    return $html;
}
add_action('init', 'pr_register_menus');
function pr_register_menus() {
    register_nav_menus(
        Array(
            'footer-menu' => __('Footer Menu'),
        )
    );
}
function footer_widget_init()
{
    register_sidebar( array(
        'name'          => __( 'Footer Widget', 'understrap' ),
        'id'            => 'footerwidget',
        'description'   => 'Widget area in the footer',
        'before_widget'  => '<div class="footer-widget-wrapper">',
        'after_widget'   => '</div><!-- .footer-widget -->',
        'before_title'   => '<h3 class="widget-title">',
        'after_title'    => '</h3>',
    ) );
}
add_action( 'widgets_init', 'footer_widget_init' );
function formatPhoneNumber($ph) {
    $ph = str_replace('(', '', $ph);
    $ph = str_replace(')', '', $ph);
    $ph = str_replace(' ', '', $ph);
    $ph = str_replace('+64', '0', $ph);

    return $ph;
}
function benefits_shortcode()
{
    $meta = '';
    $html = '<div class="benefits-wrapper">';
    for($i = 1; $i <= 4; $i++) {
        $meta = 'benefit_';
        $meta .= $i;
        $html .= '<div class="benefit"><span>' . get_field($meta,13) . '</span></div>';
    }
    $html .= '
    </div>';
    return $html;
}
add_shortcode('benefits','benefits_shortcode');

function theProcess_shortcode()
{
    $meta = '';
    $img_meta = '';
    $class = '';
    $html = '<div class="the-process-wrapper">';
    for($i = 1; $i <= 4; $i++) {
        $meta = 'process_';
        $meta .= $i;
        $img_meta = 'process_';
        $img_meta .= $i;
        $img_meta .= '_icon';
        $class = 'process-';
        $class .= $i;
        $html .= '<div class="process">
            <div class="inner-wrapper ' . $class . '">
                <div class="icon-wrapper"><img src="' . get_field($img_meta,13) . '" alt="" /></div>
                <div class="text-wrapper"><span>' . get_field($meta,13) . '</span></div>
            </div>
        </div>';
    }
    $html .='
    </div>';
    return $html;
}
add_shortcode('the_process','theProcess_shortcode');
function getPetStories()
{
    $arr = Array();
    $posts_array = get_posts([
        'post_type' => 'story',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'ID',
        'order' => 'DESC',
    ]);
    foreach ($posts_array as $post) {
        $story = new Story($post);
        $arr[] = $story;
    }
    return $arr;
}
function storiesModule_shortcode()
{
    $html = '<div class="row stories-wrapper justify-content-center">';
    foreach (getPetStories() as $story) {
        $html .= '<div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <a href="' . $story->link() . '">
                <div class="image-wrapper">
                    ' . $story->getFeatureImage('pet') . '
                </div>
                <h3>' . $story->getTitle() . '</h3>
            </a>
        </div>';
    }
    $html .= '
    </div>';
    return $html;
}
add_shortcode('stories_module', 'storiesModule_shortcode');

function contactDetails_shortcode()
{
    $html = '<ul class="contact">
        <li><a href="mailto:' . get_field('email',14) . '"><span class="fa fa-envelope"></span>' . get_field('email',14) . '</a></li>
        <li><a href="tel:' . formatPhoneNumber(get_field('phone',14)) . '"><span class="fa fa-phone"></span>' . get_field('phone',14) . '</a></li>
    </ul>';
    return $html;
}
add_shortcode('contact_details', 'contactDetails_shortcode');

function officeHours_shortcode()
{
    $html = '<strong>Office Hours</strong>';
    $html .= get_field('office_hours',14);
    return $html;
}
add_shortcode('office_hours', 'officeHours_shortcode');

function reports_shortcode()
{
    global $post;
    $i = 0;
    $exclude = array(1);
    $args = array(
        'exclude' => $exclude,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'all'
    );
    $users = get_users($args);
    $html = '
    <div class="date-picker-wrapper">
        <form method="get" action="' . get_page_link($post->ID) . '" id="pet-report">
            <div class="inner-wrapper">
                <div>
                    <label>Start date:</label>
                    <input class="datepicker1" data-provide="datepicker1" name="start_date" />
                </div>                
                <div>
                    <label>End date:</label>
                    <input class="datepicker2" data-provide="datepicker2" name="end_date" />
                </div>      
                <div>
                    <a href="javascript:;" class="btn btn-primary">View report</a>
                </div>    
            </div>
        </form>            
    </div>
    <div class="reports-wrapper">
        <table class="table report">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Clinic name</th>
                    <th class="center-me">No. of pets</th>
                    <th class="center-me">Urns</th>
                    <th class="center-me">Jewellery</th>
                    <th class="center-me">Returned to vets</th>
                    <th class="center-me">Returned home</th>
                </tr>
            </thead>
            <tbody>';
            foreach($users as $user) {
                $vet = getVetByUserID($user->ID);
                $html .= '<tr>
                    <td class="center-me"><span class="fa fa-plus table-btn" onclick="showTable(' . $i . ')"></span></td>
                    <td>' . $vet[0]->getTitle() . '</td>
                    <td class="center-me num-of-pets">' . $vet[0]->report_num_pets() . '</td>
                    <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-ashes-returned-in','urn') . '</td>
                    <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-memorial-jewellery','yes') . '</td>
                    <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-delivery-options','clinic') . '</td>
                    <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-delivery-options','address') . '</td>
                </tr>
                <tr class="row-collapse row-collapse-' . $i . '">
                    <td colspan="7" class="td-no-padding">
                        <table class="table ntable">
                            <thead>
                                <tr>
                                    <th>Pet Tag ID</th>
                                    <th>Pet Type</th>
                                    <th class="center-me">Weight</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach($vet[0]->getPets() as $pet) {
                                $html .= '
                                <tr>
                                    <td>' . $pet->getCustomField('pet-tag-id') . '</td>
                                    <td>' . $pet->getPetType() . '</td>
                                    <td class="center-me">' . $pet->getCustomField('pet-weight') . 'kg</td>
                                </tr>';
                            }
                            $html .= '
                            </tbody>
                        </table>
                    </td>
                </tr>';
                $i++;
            }
            $html.= '</tbody>
        </table>
    </div>';
    return $html;
}
add_shortcode('reports', 'reports_shortcode');

function convertToRawDate($date)
{
    $date = DateTime::createFromFormat('d/m/Y', $date);
    $raw_date = $date->format("Y-m-d");
    return $raw_date;
}
function convertToTimeStamp($date)
{
    $new_date = str_replace('/','-',$date);
    $new_date = date('Y-m-d', strtotime($new_date));
    $timestamp = strtotime($new_date);
    return $timestamp;
}
function cremationLogs_shortcode()
{
    $html = '';
    $user = wp_get_current_user();
    if($user->id <> 0) {
        $logs = getLogs();
        $count = count($logs);
        $html = '
        <div class="my-account-wrapper">
            <div class="container">
                <div class="intro-wrapper">Click on the log date to view full details</div>';
        if (isset($_REQUEST['cred_referrer_form_id']) && $_REQUEST['cred_referrer_form_id'] <> "") {
            $html .= '<div class="notification">
                    <span class="fa fa-times" onclick="hideNotification()"></span>
                    ' . get_field('new_cremation_log', 5) . '
                </div>';
        }
        $html .= '
                <div class="log-a-pet-wrapper">
                    <a href="' . get_page_link(419) . '" class="btn btn-primary">Add a new cremation log</a>
                </div>
            </div>';
        $html .= '
            <div class="table-responsive">
                <table class="table table-striped results">            
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Operator</th>
                            <th class="center-me">Animals in machine</th>
                            <th class="center-me">Total weight</th>
                            <th class="center-me">Alkali added</th>
                        </tr>
                    </thead>
                    <tbody>';
                    if($count > 0) {
                        foreach ($logs as $log) {
                            $html .= '<tr>
                                <td><a href="' . get_page_link(435) . '?log_id=' . $log->id() . '">' . $log->getLogDate() . '</a></td>
                                <td>' . $log->getLogTime() . '</td>
                                <td>' . $log->getCustomField('cremation-operator') . '</td>
                                <td class="center-me">' . $log->getCustomField('cremation-animals-in-machine') . '</td>
                                <td class="center-me">' . $log->getCustomField('cremation-total-weight') . '</td>
                                <td class="center-me">' . $log->getCustomField('cremation-alkali-added') . '</td>
                            </tr>';
                        }
                    }
                    $html .= '
                    </tbody>
                </table>    
            </div>
        </div>';
    }
    return $html;
}
add_shortcode('cremation_logs', 'cremationLogs_shortcode');
function editLog_shortcode()
{
    $post_id = $_REQUEST['log_id'];
    echo do_shortcode('[cred_form form=437 post=' . $post_id . ']');
}
add_shortcode('edit_log', 'editLog_shortcode');
function reminderEmail()
{
    // loop through
    foreach(getPetsForReminder() as $pet)
    {
        //check status
        if($pet->getCustomField('pet-status') == "Delivered to owner" || $pet->getCustomField('pet-status') == "Delivered to vets" || $pet->getCustomField('pet-status') == "Service complete")  {
            // check if it has been 7 days since status was updated.
            // get todays date
            date_default_timezone_set('Pacific/Auckland');
            $now = date("Y-m-d");
            $today = convertToTimeStamp($now);
            //get delivered date + 7 days
            $raw_date = convertToRawDate($pet->getCustomField('pet-date-delivered'));
            $date_to_check = strtotime(date("Y-m-d", strtotime($raw_date)) . " +1 week");

            if($today == $date_to_check) {
                // send one week reminder

                $pet->reminderEmail();
                // update reminder send field
                update_post_meta($pet->id(), 'wpcf-pet-reminder-sent', 1);
            }
        }
    }
}
add_action('petreflections_cron_hook', 'reminderEmail');
if(isset($_REQUEST['action']) && $_REQUEST['action']=="reminder") {

}