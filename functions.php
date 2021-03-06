<?php
function karma_enqueue_styles() { 
    $parent_style = 'parent-style'; 
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'karma_enqueue_styles' );
function register_my_menu() {
  register_nav_menu('new-menu',__( 'Mobile Menu' ));
}
add_action( 'init', 'register_my_menu' );
add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2); 
function add_login_logout_link($items, $args) {
ob_start();
wp_loginout('index.php');
$loginoutlink = ob_get_contents();
ob_end_clean();
$items .= '<li id="medLogout" class="menu-item menu-item-type-post_type menu-item-object-page"><strong>'. $loginoutlink .'</strong></li>';
return $items; }
add_filter('gettext', 'change_howdy', 10, 3);
function change_howdy($translated, $text, $domain) {
    if (!is_admin() || 'default' != $domain)
        return $translated;
    if (false !== strpos($translated, 'Howdy'))
        return str_replace('Howdy', 'Welcome', $translated);
    return $translated;
}
// Creates Custom Post Type
function patient_pages_init() {
    $args = array(
      'label' => 'Patient Pages',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'patient-pages'),
        'query_var' => true,
        'menu_icon' => 'dashicons-video-alt',
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'trackbacks',
            'custom-fields',
            'comments',
            'revisions',
            'thumbnail',
            'author',
            'page-attributes',)
        );
    register_post_type( 'patient-pages', $args );
}
add_action( 'init', 'patient_pages_init' );
add_action('admin_head', 'wpse_52099_script_enqueuer');
function wpse_52099_script_enqueuer(){
    if(!current_user_can('administrator')) {
		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' ); 
		add_filter( 'admin_footer_text', '__return_empty_string', 11 );
		add_filter( 'update_footer',     '__return_empty_string', 11 );
		get_header();
        echo <<<HTML
        <style type="text/css">
		
		div#wpwrap {
    margin-left: 0px;
    max-width: 100vw;
}
		
		
input[type=text], input[type=search], input[type=radio], input[type=tel], input[type=time], input[type=url], input[type=week], input[type=password], input[type=checkbox], input[type=color], input[type=date], input[type=datetime], input[type=datetime-local], input[type=email], input[type=month], input[type=number], select, textarea {  border: 0px solid #ddd; -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.07); box-shadow: inset 0 1px 2px rgba(0,0,0,0); background-color: #e2e8ec; color: #32373c; outline: 0.1; -webkit-transition: 50ms border-color ease-in-out;  transition: 50ms border-color ease-in-out; }
input { border-radius: 10px; padding: 10px; border-width: 2px; border-color: rgba(0,0,0,0); background-color: #c5dbe5; }
input[type=text]:focus, input[type=search]:focus, input[type=radio]:focus, input[type=tel]:focus, input[type=time]:focus, input[type=url]:focus, input[type=week]:focus, input[type=password]:focus, input[type=checkbox]:focus, input[type=color]:focus, input[type=date]:focus, input[type=datetime]:focus, input[type=datetime-local]:focus, input[type=email]:focus, input[type=month]:focus, input[type=number]:focus, select:focus, textarea:focus { border-color: #5b9dd9; -webkit-box-shadow: 0 0 2px rgba(30,140,190,.8); box-shadow: 0 0 2px rgba(30,140,190,.8); border: width:3px; border-width: 2px; }
		
        #wpcontent, #footer { margin-left: 0px; }
		html { padding-top:0px !important; margin-top:0px !important; }
		#header { padding-top:0px !important; margin-top:0px !important; }
		.header-overlay { border-top: 1px solid #4B8CA8; background-color: #1e73be; }
		#adminmenuback, #adminmenuwrap, #wpadminbar, .update-nag, .user-admin-bar-front-wrap, #contextual-help-link-wrap { display:none; }
		.header-area { padding:0 !important; width: 88% !important; }
		nav { padding-top: 30px; }
		#wpwrap { display:block; width:60%; margin-left:auto; margin-right:auto; }
        </style>
        <script type="text/javascript">
        jQuery(document).ready( function($) {
            $('#adminmenuback, #adminmenuwrap, #wpadminbar, .update-nag, #contextual-help-link-wrap').remove();
			var yp = $("#your-profile");
			yp.find("p:first-of-type").remove();
			yp.find(".form-table:first-of-type").remove();
			yp.find("h2:first-of-type").remove();
			yp.find("h2:first-of-type").remove();
			yp.find("h2:first-of-type").remove();
        });     
        </script>
		</header>
HTML;
    }
}
add_action( 'woocommerce_before_checkout_billing_form', 'my_custom_checkout_field' );
function my_custom_checkout_field( $checkout ) {
    echo '
	<style>
	.new_details > input { border: 1px solid #D2D2D2; box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1) inset;  padding: 8px 10px; width:100% !important; }
	.woocommerce-billing-fields > h3:first-of-type { display:none; }
	.woocommerce-shipping-fields { display:none; }
	.woocommerce .col2-set .col-1, .woocommerce-page .col2-set .col-1 { width:100% !important; }
	</style>
	<div id="my_custom_checkout_field">
	<h3>Account Details</h3>
	<div style="float:left; width:48%;">';
    woocommerce_form_field( 'username', array(
        'type'          => 'text',
        'class'         => array('new_details form-row-wide'),
        'label'         => __('Username'),
        'placeholder'   => __(''),
		'required'		=> 'true'
        ), $checkout->get_value( 'username' ));
	woocommerce_form_field( 'password', array(
        'type'          => 'password',
        'class'         => array('new_details form-row-wide'),
        'label'         => __('Password'),
        'placeholder'   => __(''),
		'required'		=> 'true'
        ), $checkout->get_value( 'password' ));
	echo "</div><div style='float:right; width:48%;'>";
	woocommerce_form_field( 'email', array(
        'type'          => 'email',
        'class'         => array('new_details form-row-wide'),
        'label'         => __('Email'),
        'placeholder'   => __(''),
		'required'		=> 'true'
        ), $checkout->get_value( 'email' ));
		
	woocommerce_form_field( 'cpassword', array(
        'type'          => 'password',
        'class'         => array('new_details form-row-wide'),
        'label'         => __('Confirm Password'),
        'placeholder'   => __(''),
		'required'		=> 'true'
        ), $checkout->get_value( 'cpassword' ));
    echo '</div></div>
	<h3 style="margin-top:1em; float:left; clear:both; width:100%;">Billing Details</h3>';
}
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
    unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_email']);
    unset($fields['order']['order_comments']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_phone']);
    return $fields;
}
add_action('woocommerce_after_checkout_validation', 'add_submission');
function add_submission() {
global $wpdb;
	$userName =  $_POST['checkUsers'];
	$uEmail = $_POST['checkEmail'];
	$uPass = $_POST['checkPass'];
	$uFirst = $_POST['checkFirst'];
	$uLast = $_POST['checkLast'];
	$allName = $uFirst.$uLast;
	$dataString = $allName."-".$uEmail."-".$userName;
	$allName = str_replace(' ', '', $allName);
	$wpdb->insert( 'wp_tmpData', array('customerKey' => $allName, 'username' => $userName, 'email' => $uEmail, 'pw' => $uPass) ); }
add_action( 'woocommerce_order_status_completed','add_medweb_account' );
function add_medweb_account($order_id) {
global $wpdb;
$order = new WC_Order( $order_id );
$firstName = $order->billing_first_name;
$lastName = $order->billing_last_name;
$bCountry = $order->billing_country;
$bAddress1 = $order->billing_address_1;
$bCity = $order->billing_city;
$bState = $order->billing_state;
$bZip = $order->billing_postcode;
$customerKey2 = $firstName.$lastName;
$customerKey2 = str_replace(' ', '', $customerKey2);
$customerQuery = "SELECT * FROM wp_tmpData WHERE customerKey = '".$customerKey2."'";
$retRow = $wpdb->get_row($customerQuery, ARRAY_A);
$takeUser = $retRow['username'];
$takeEmail = $retRow['email'];
$takePass = $retRow['pw'];
$userdata = array('user_login' => $takeUser, 'user_pass' => $takePass, 'user_email' => $takeEmail, 'first_name' => $firstName, 'last_name' => $lastName, 'role' => 'subscriber', 'show_admin_bar_front' => 'false');
$user = wp_insert_user( $userdata ); 
if ($user !== NULL && $user !== 0 && $user !== "0") { add_user_meta( $user, 'create_group', 0); add_user_meta( $user, 'delegate_count', 0); add_user_meta( $user, 'group_id', 0); update_user_meta( $user, 'wp_user_level', '1'); add_user_meta( $user, 'forum_count', 0); add_user_meta( $user, 'enable_donate', 0); add_user_meta( $user, 'enable_pic', 0); add_user_meta( $user, 'enable_graphic', 0); add_user_meta( $user, 'paypal_url', 0); update_post_meta( $order_id, '_customer_user', $user ); $findSub = get_post_meta( $order_id, 'subscriptions', false ); $subID = $findSub[0][0]; update_post_meta( $subID, 'user_id', $user ); 
$hasKey = get_user_meta($user,"billing_first_name",true);
if ($hasKey === "") { add_user_meta( $user, 'billing_first_name', $firstName); add_user_meta( $user, 'billing_last_name', $lastName); add_user_meta( $user, 'billing_company', ""); add_user_meta( $user, 'billing_address_1', $bAddress1); add_user_meta( $user, 'billing_address_2', ""); add_user_meta( $user, 'billing_city', $bCity); add_user_meta( $user, 'billing_postcode', $bZip); add_user_meta( $user, 'billing_country', $bCountry); add_user_meta( $user, 'billing_state', $bState); add_user_meta( $user, 'billing_phone', ""); add_user_meta( $user, 'billing_email', ""); add_user_meta( $user, 'shipping_first_name', ""); add_user_meta( $user, 'shipping_last_name', ""); add_user_meta( $user, 'shipping_company', ""); add_user_meta( $user, 'shipping_address_1', ""); add_user_meta( $user, 'shipping_address_2', ""); add_user_meta( $user, 'shipping_city', ""); add_user_meta( $user, 'shipping_postcode', ""); add_user_meta( $user, 'shipping_country', ""); add_user_meta( $user, 'shipping_state', ""); add_user_meta( $user, 'shipping_phone', ""); add_user_meta( $user, 'shipping_email', ""); }
else { update_user_meta( $user, 'billing_first_name', $firstName); update_user_meta( $user, 'billing_last_name', $lastName); update_user_meta( $user, 'billing_address_1', $bAddress1); update_user_meta( $user, 'billing_city', $bCity); update_user_meta( $user, 'billing_postcode', $bZip); update_user_meta( $user, 'billing_country', $bCountry); update_user_meta( $user, 'billing_state', $bState); }
$wpdb->delete( 'wp_tmpData', array( 'customerKey' => $customerKey2 ) ); }
	}	
?>
