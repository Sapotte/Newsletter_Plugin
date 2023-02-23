<?php

if(!defined('WP_UNINSTALL_PLUGIN')){
    die;
}

global $wpdb;

$table = $wpdb->prefix.'newsletter';
$wpdb->query( "DROP TABLE IF EXISTS `$table`");

$unsubscribe_page = get_page_by_title( 'Se désabonner de la Newsletter', 'page');

if($unsubscribe_page) {
    wp_delete_post( $unsubscribe_page-> ID, true );
}