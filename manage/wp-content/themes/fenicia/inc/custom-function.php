<?php

/***Fenicia Styles & Css***/

function fenicia_stylesheets() {

 

  wp_enqueue_style( 'bootstrap',  get_template_directory_uri() .'/assets/css/bootstrap.css', array(), null, 'all' );

  

  

  wp_enqueue_style( 'font-awesome','https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), null, 'all' );

  

  wp_enqueue_style( 'mCustomScrollbar',  get_template_directory_uri() .'/assets/css/jquery.mCustomScrollbar.css', array(), null, 'all' );

  

  wp_enqueue_style( 'simplelightbox',  get_template_directory_uri() .'/assets/css/simplelightbox.min.css', array(), null, 'all' );

  wp_enqueue_style( 'jquery-ui','https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', array(), null, 'all' );

  wp_enqueue_style( 'timepicker',get_template_directory_uri() .'/assets/css/bootstrap-datetimepicker.css', array(), null, 'all' );

  

  wp_enqueue_style( 'stellarnav.min',  get_template_directory_uri() .'/assets/css/stellarnav.min.css', array(), null, 'all' );

	wp_enqueue_style( 'animate',  get_template_directory_uri() .'/assets/css/animate.css', array(), null, 'all' );

	

  wp_enqueue_style( 'style',  get_template_directory_uri() .'/assets/css/style.css', array(), null, 'all' );

  wp_enqueue_style( 'responsive',  get_template_directory_uri() .'/assets/css/responsive.css', array(), null, 'all' );

  

}

add_action( 'wp_enqueue_scripts', 'fenicia_stylesheets' );

function fenicia_js() {

  wp_enqueue_script( 'jquery', get_stylesheet_directory_uri() . '/assets/js/jquery.js','',true );

  wp_enqueue_script( 'bootstrap.min', get_stylesheet_directory_uri() . '/assets/js/bootstrap.min.js', array( 'jquery' ),'',true );

	 wp_enqueue_script( 'wow.min', get_stylesheet_directory_uri() . '/assets/js/wow.min.js', array('jquery' ),'',true);

 wp_enqueue_script( 'simple-lightbox', get_stylesheet_directory_uri() . '/assets/js/simple-lightbox.js', array('jquery' ),'',true);

 

  

  wp_enqueue_script( 'stellarnav', get_stylesheet_directory_uri() . '/assets/js/stellarnav.js', array('jquery' ),'',true);

  wp_enqueue_script( 'mCustomScrollbar', get_stylesheet_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js','' ,true);

  wp_enqueue_script( 'jquery-ui','https://code.jquery.com/ui/1.10.3/jquery-ui.js','',true );

  wp_enqueue_script( 'moment.min',get_stylesheet_directory_uri() . '/assets/js/moment.min.js','',true );

  

  wp_enqueue_script( 'bootstrap-datetimepicker',get_stylesheet_directory_uri() . '/assets/js/bootstrap-datetimepicker.min.js','',true );

  

  

}

add_action( 'wp_enqueue_scripts', 'fenicia_js' );







show_admin_bar( false );

function my_login_logo() { 

  global $option_data; 

  $data = $option_data['custom_logo']; ?>

  <style type="text/css">

  #login h1 a, .login h1 a {

    background-image: url(<?php echo $data; ?>);

    width: 205px !important;

    height: 150px !important;

    background-size: 140px;

    background-color: #000;

  }

  .privacy-policy-page-link {display: none;}

</style>

<?php }

add_action( 'login_enqueue_scripts', 'my_login_logo' );

//admin logo url

function my_login_logo_url() {

  return home_url();

}

add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {

  return esc_attr( get_bloginfo( "name", "display" ) );

}

add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function add_my_favicon(){

  global $option_data;

  $getIconHtml = '<link rel="icon" type="image/ico" href="'.$option_data["custom_favicon"].'">';

  echo $getIconHtml;

}

add_action('wp_head', 'add_my_favicon');

add_action('admin_head', 'add_my_favicon');

add_action('login_head', 'add_my_favicon');



add_filter('gettext', 'change_howdy', 10, 3);

function change_howdy($translated, $text, $domain) {

  if (!is_admin() || 'default' != $domain)

    return $translated;

  if (false !== strpos($translated, 'Howdy'))

    return str_replace('Howdy', 'Welcome', $translated);

  return $translated;

}

add_action( 'admin_bar_menu', 'remove_wp_logo', 999 );

function remove_wp_logo( $wp_admin_bar ) {

  $wp_admin_bar->remove_node( 'wp-logo' );

}



function remove_footer_admin () 

{

  echo '<span id="footer-thankyou">Developed by <a href="https://www.fitser.com/" target="_blank">FITSER</a></span>';

}

add_filter('admin_footer_text', 'remove_footer_admin');



add_filter( 'login_errors', function( $error ) {

 global $errors;

 $err_codes = $errors->get_error_codes();



 // Invalid username.

 // Default: '<strong>ERROR</strong>: Invalid username. <a href="%s">Lost your password</a>?'

 if ( in_array( 'invalid_username', $err_codes ) ) {

  $error = '<strong>Invalid username or Password.</strong>';

 }



 // Incorrect password.

 // Default: '<strong>ERROR</strong>: The password you entered for the username <strong>%1$s</strong> is incorrect. <a href="%2$s">Lost your password</a>?'

 if ( in_array( 'incorrect_password', $err_codes ) ) {

  $error = '<strong>Invalid username or Password.</strong>';

 }



 return $error;

} );



function change_footer_version() {

  return ' ';

}

add_filter( 'update_footer', 'change_footer_version', 9999 );



function custom_admin_title( $admin_title ) {

  return str_replace( ' &#8212; WordPress', ' &#8212; Admin', $admin_title );

}

add_filter( 'admin_title', 'custom_admin_title' );





function remove_dashboard_widgets () {

  remove_meta_box('dashboard_quick_press','dashboard','side'); //Quick Press widget

  remove_meta_box('dashboard_recent_drafts','dashboard','side'); //Recent Drafts

  remove_meta_box('dashboard_primary','dashboard','side'); //WordPress.com Blog

  remove_meta_box('dashboard_secondary','dashboard','side'); //Other WordPress News

  remove_meta_box('dashboard_incoming_links','dashboard','normal'); //Incoming Links

  remove_meta_box('dashboard_plugins','dashboard','normal'); //Plugins

  remove_meta_box('dashboard_right_now','dashboard', 'normal'); //Right Now

  remove_meta_box('rg_forms_dashboard','dashboard','normal'); //Gravity Forms

  remove_meta_box('dashboard_recent_comments','dashboard','normal'); //Recent Comments

  remove_meta_box('icl_dashboard_widget','dashboard','normal'); //Multi Language Plugin

  remove_meta_box('dashboard_activity','dashboard', 'normal'); //Activity

  remove_action('welcome_panel','wp_welcome_panel');

}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');





add_action( 'wp_dashboard_setup', 'register_my_dashboard_widget' );

function register_my_dashboard_widget() {

  wp_add_dashboard_widget(

    'my_dashboard_widget',

    'My Dashboard Widget',

    'my_dashboard_widget_display'

  );

}

function my_dashboard_widget_display() {

  echo 'Hello, I am Mr. Widget';

}

add_filter( 'wpcf7_validate_email', 'custom_email_validation', 10, 2 );

function custom_email_validation( $result, $tag ) {

  $type = $tag['type'];

  $name = $tag['name'];

  if($type == 'email' && $_POST[$name] != '') {

    if(substr($_POST[$name], 0, 1) == '.' ||

      !preg_match('/^[A-Za-z0-9.]+@(?:[:[A-Za-z0-9-]+\.){1,2}[A-Za-z]{1,}+$/', $_POST[$name])) {  

      $result->invalidate( $name, wpcf7_get_message($name) );

  } 

}

if($type == 'text*' && $_POST[$name] != ''){ 

  if(!preg_match('/^[A-Za-z.]+$/', $_POST[$name])){

    $result->invalidate( $name, wpcf7_get_message( $name ) );

  }

}

return $result;

}



/*add_filter( 'wpcf7_validate_text*', 'custom_name_validation', 10, 2 );

function custom_name_validation( $result, $tag ) {

  $type = $tag['type'];

  $name = $tag['name'];

  if($type == 'text*' && $_POST[$name] != ''){ 

    if(!preg_match('/^[A-Za-z. ]+$/', $_POST[$name])){

      $result->invalidate( $name, 'The name entered is invalid.' );

    }

  }

  return $result;

}*/



add_filter( 'wpcf7_is_tel', 'custom_filter_wpcf7_is_tel', 10, 2 );

function custom_filter_wpcf7_is_tel( $result, $tel ) { 

  $result = preg_match( '/^\(?\+?([0-9]{1,4})?\)?[-\. ]?(\d{10,15})$/', $tel );

  return $result; 

}



function iconic_bypass_logout_confirmation() {

  global $wp;

  if ( isset( $wp->query_vars['customer-logout'] ) ) {

    wp_redirect( str_replace( '&amp;', '&', wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ) );

    exit;

  }

}

add_action( 'template_redirect', 'iconic_bypass_logout_confirmation' );







function comment_validation_init() {

  if(is_single() && comments_open() ) { ?>        

    <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

    <script type="text/javascript">

      jQuery(document).ready(function($) {

        $('#commentform').validate({



          rules: {

            author: {

              required: true,

              minlength: 2

            },



            email: {

              required: true,

              email: true

            },



            comment: {

              required: true,

              minlength: 10

            }

          },



          messages: {

            author: "Please fill the required field",

            email: "Please enter a valid email address.",

            comment: "Please fill minimum 10 letter."

          },



          errorElement: "div",

          errorPlacement: function(error, element) {

            element.after(error);

          }



        });

      });

    </script>

    <?php

  }

}

add_action('wp_footer', 'comment_validation_init');



add_filter('style_loader_tag', 'codeless_remove_type_attr', 10, 2);

add_filter('script_loader_tag', 'codeless_remove_type_attr', 10, 2);

function codeless_remove_type_attr($tag, $handle) {

  return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );

}



add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);



function special_nav_class ($classes, $item) {

  if (in_array('current-menu-item', $classes) ){

    $classes[] = 'current-menu';

  }

  return $classes;

}

 



function my_admin_title($admin_title, $title)

{

  return get_bloginfo('name').' admin'.' &bull; '.$title;

}

add_filter('admin_title', 'my_admin_title', 10, 2);

add_filter('login_title', 'my_admin_title', 10, 2);



function hide_update_notice_to_all_but_admin_users() 

{

  if (!current_user_can('update_core')) {

    remove_action( 'admin_notices', 'update-nag', 3 );

  }

}

add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );

function remove_core_updates(){

  global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);

}

add_filter('pre_site_transient_update_core','remove_core_updates');

add_filter('pre_site_transient_update_plugins','remove_core_updates');

add_filter('pre_site_transient_update_themes','remove_core_updates');



function create_post_type_event() {

  register_post_type( 'event',

    array(

      'labels' => array(

        'name' => __( 'Event' ),

        'singular_name' => __( 'event' )

      ),

      'menu_icon' => 'dashicons-calendar-alt',

      'public' => true,

      'show_in_rest' => true,

      'has_archive' => true,

      'supports'           => array( 'title', 'editor','comments','thumbnail' )

      

    )

  );

}

add_action( 'init', 'create_post_type_event' );

function create_post_type_venue() {

  register_post_type( 'venue',

    array(

      'labels' => array(

        'name' => __( 'Venue' ),

        'singular_name' => __( 'venue' )

      ),



      'public' => true,

      'show_in_rest' => true,

      'has_archive' => true,

      'supports'           => array( 'title', 'editor','comments' )

      

    )

  );

}

add_action( 'init', 'create_post_type_venue' );

function get_breadcrumb() {

    echo '<ul><li><a href="'.home_url().'" rel="nofollow">Home</a></li>';

    if (is_category() || is_single()) {

        //echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";

        the_category(' &bull; ');

            if (is_single()) {

                echo ' <li class="selected"><a href="'.get_permalink().'">';

                the_title();

                echo '</a></li>';

            }

    } elseif (is_page()) {

        echo ' <li class = "selected"><a href="'.get_permalink().'">';

        echo the_title();

        echo '</a></li></ul>';

    } elseif (is_search()) {

        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";

        echo '"<em>';

        echo the_search_query();

        echo '</em>"';

    }

}



add_filter( 'wpcf7_validate_text*', 'custom_text_validation_filter', 10, 2 );



function custom_text_validation_filter( $result, $tag ) {

    if ( 'fname' == $tag->name ) {

        // matches any utf words with the first not starting with a number

        $re = '/^[^\p{N}][\p{L}]*/i';

        if (!preg_match($re, $_POST['fname'], $matches)) {

            $result->invalidate($tag, "This is not a valid First name!" );

        }

    }



    return $result;

}

add_filter( 'wpcf7_validate_text*', 'custom_text_validation_filter_event', 10, 2 );



function custom_text_validation_filter_event( $result, $tag ) {

    if ( 'ename' == $tag->name ) {

        // matches any utf words with the first not starting with a number

        $re = '/^[^\p{N}][\p{L}]*/i';

        if (!preg_match($re, $_POST['ename'], $matches)) {

            $result->invalidate($tag, "This is not a valid name!" );

        }

    }



    return $result;

}

add_filter( 'wpcf7_validate_text*', 'custom_text_validation_filter1', 10, 2 );



function custom_text_validation_filter1( $result, $tag ) {

    if ( 'lname' == $tag->name ) {

        // matches any utf words with the first not starting with a number

        $re = '/^[^\p{N}][\p{L}]*/i';

        if (!preg_match($re, $_POST['lname'], $matches)) {

            $result->invalidate($tag, "This is not a valid Last name!" );

        }

    }



    return $result;

}



add_filter( 'wpcf7_validate_text*', 'custom_text_validation_filter_evnt', 10, 2 );



function custom_text_validation_filter_evnt( $result, $tag ) {

    if ( 'eventtype' == $tag->name ) {

        // matches any utf words with the first not starting with a number

        $re = '/^[^\p{N}][\p{L}]*/i';

        if (!preg_match($re, $_POST['eventtype'], $matches)) {

            $result->invalidate($tag, "Invalid Eventtype!" );

        }

    }



    return $result;

}

add_filter( 'wpcf7_validate_text*', 'custom_text_validation_filter_evntg', 10, 2 );



function custom_text_validation_filter_evntg( $result, $tag ) {

    if ( 'guestquantity' == $tag->name ) {

        // matches any utf words with the first not starting with a number

        $re = '/^\(?\+?([0-9]{1,4})?\)?[-\. ]?(\d{1})$/';

        if (!preg_match($re, $_POST['guestquantity'], $matches)) {

            $result->invalidate($tag, "Invalid Number Of Guests!" );

        }

    }



    return $result;

}

function wpb_sender_name( $original_email_from ) {

    return get_option('blogname');

}



//add_filter('wp_mail_from_name', 'remove_from_wordpress');

add_filter( 'wp_mail_from_name', 'wpb_sender_name' );



add_action( 'login_footer',  'login_validation_msg');

function login_validation_msg(){

 ?>

 <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

 <script type="text/javascript">

  jQuery(document).ready(function($) {

   $('#loginform').validate({

    rules: {

     log: {

      required: true

     },

     pwd: {

      required: true

     }

    },

    messages: {

     log: "<span style='color:red;'>Please enter a valid email address.</span>",

     pwd: "<span style='color:red;'>Please fill the required field.</span>"

    },

   });

  });

 </script>

 <?php

}

 function contactform7_before_send_mail( $form_to_DB ) {

    //set your db details

    $mydb = new wpdb('FeniciaL','4JAcsv3YR5mH9ROJ','FeniciaL','localhost');



    $form_to_DB = WPCF7_Submission::get_instance();

    if ( $form_to_DB ) 

        $formData = $form_to_DB->get_posted_data();



    $email = $formData['remail'];

    $subscriber = $formData['newsletter'];

    if (in_array("Subscribe to my newsletter", $subscriber)){

      $check_email = $mydb->query("SELECT * FROM wp_newsletter WHERE email='.$email.'");

      if ($check_email) {

          // found

      }

      else{

          $mydb->insert( 'wp_newsletter', array( 'email' =>$email ), array( '%s' ) );

          }

        }

}

remove_all_filters ('wpcf7_before_send_mail');

add_action( 'wpcf7_before_send_mail', 'contactform7_before_send_mail' ); 



