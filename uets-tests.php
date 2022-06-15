<?php
	
/**
 * Plugin Name: Uets Tests
 * Description: Плагин для сnворення тестов на сайте uets.net
 * Plugin URI:  uets.net
 * Author URI:  https://globalpartnersoft.com/
 * Author:      GlobalPartnerSoft
 *
 * Text Domain: uets-tests
 * Domain Path: Путь до MO файла (относительно папки плагина)
 *
 * Requires PHP: 5.4
 * Requires at least: 2.5
 * 
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Version:     1.0
 */

 if(!defined('ABSPATH')) {
    die;
 }

 define('UETSTESTS_PATH', plugin_dir_path(__FILE__));

 if (!class_exists('Gamajo_Template_Loader')) {
    require UETSTESTS_PATH . 'inc/class-gamajo-template-loader.php';
 }

 require UETSTESTS_PATH . 'inc/class-uets-template-loader.php';
 require_once UETSTESTS_PATH . 'inc/uets-send-mail.php';

//  add_action( 'wp_enqueue_scripts', 'ajax_form_scripts' );

//  function ajax_form_scripts() {
//   wp_localize_script( 'ajax-form', 'ajax_form_object', array(
//  'url'   => admin_url( 'admin-ajax.php' ),
//  'nonce' => wp_create_nonce( 'ajax-form-nonce' ),
//  ) );
 
//  }


 class uetsTests {

    public function register() {
        add_action('init', [$this, 'custom_post_type']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function enqueue_styles() {
        wp_enqueue_style('uets-test-style', plugins_url('assets/css/style.css' ,__FILE__));
        wp_enqueue_script('uets-scripts', plugins_url('assets/js/script.js' ,__FILE__), array('jquery'), 1.0, true);
        wp_enqueue_style( 'load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
        wp_localize_script( 'uets-scripts', 'ajax_form_object', array(
            'url'   => admin_url( 'admin-ajax.php' ) . '?action=send_mail',
            'nonce' => wp_create_nonce( 'ajax-form-nonce' ),
            ) );
    }

    public function custom_post_type() {
        register_post_type('test', array(
            'public' => true,
            'rewrite' => array('slug' => 'tests'),
            'label' => 'Тести',
            'supports' => array('title', 'editor')
        ));

        $labels = array(
            "name" => "Mail",
            "singular_name" => "Mail",
            "menu_name" => "Mail",
            "all_items" => "All mail",
            "add_new" => "Add New",
            "add_new_item" => "Add New",
            "edit" => "Edit",
            "edit_item" => "Edit",
            "new_item" => "New item",
            "view" => "View",
            "view_item" => "View item",
            "search_items" => "Search item",
            "not_found" => "No found",
            "not_found_in_trash" => "No found",
        );
    
        $args = array(
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "show_ui" => true,
            "has_archive" => false,
            "show_in_menu" => true,
            "exclude_from_search" => true,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => true,
            "rewrite" => false,
            "query_var" => true,
            "menu_position" => 25,
            "menu_icon" => "dashicons-email-alt",
            "supports" => array( "title", "editor" ),
        );
    
        register_post_type( "mail", $args );

    }

    static function activation() {

        flush_rewrite_rules();
     }

    static function deactivation() {

        flush_rewrite_rules();
     }

    
 }

 $uetsTests = new uetsTests();
 $uetsTests->register();

if (class_exists('uetsTests')) {
    register_activation_hook(__FILE__, array($uetsTests, 'activation'));
    register_deactivation_hook(__FILE__, array($uetsTests, 'activation'));
}
 