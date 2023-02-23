<?php 

/*
Plugin Name: Newsletter
Description: Inscription et envoi newsletter
Version: 1.0
Author: Sarah Schneider
*/

class Newsletter_Iltordello {
    function __construct() {
        
        include_once plugin_dir_path( __FILE__ ).'/Newsletter_Iltordello_Widget.php';
        add_action( 'widgets_init', function() {
            register_widget('Newsletter_Iltordello_Widget');
        });
        
        include_once plugin_dir_path( __FILE__ ).'/Newsletter_Iltordello_Plugin.php';
        if(class_exists('Newsletter_Iltordello_Plugin')) {
            $inst_newsletter = new Newsletter_Iltordello_Plugin();
        }
        
        if(isset($inst_newsletter)){
            register_activation_hook( __FILE__, array($inst_newsletter, 'newsletter_install') );
            add_action( 'admin_menu', array( $inst_newsletter, 'newsletter_iltordello_menu') );
            add_action( 'admin_init', array($inst_newsletter, 'delete_auto'));
            add_action( "wp_head", array($inst_newsletter, 'newsletter_front_head'));  
            
            if(isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'newsletter':
                        add_action( 'wp_ajax_nopriv_newsletter', array($inst_newsletter, 'newsletter_iltordello_front_ajax'));
                        add_action('wp_ajax_newsletter', array($inst_newsletter, 'newsletter_iltordello_front_ajax'));
                        break;
                    case 'newsletter_unsubscribe':
                        add_action( 'wp_ajax_nopriv_newsletter_unsubscribe', array($inst_newsletter, 'newsletter_unsubscribe_front_ajax'));
                        add_action('wp_ajax_newsletter_unsubscribe', array($inst_newsletter, 'newsletter_unsubscribe_front_ajax'));
                        break;
                }   
            }
        
            add_action( 'wpcf7_before_send_mail', array($inst_newsletter, 'create_contact_wpcf7_form'), 10, 3 );
        }
        
    }
}
new Newsletter_Iltordello();