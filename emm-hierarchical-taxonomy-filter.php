<?php
/**
 * Plugin Name: EMM Hierarchical Taxonomy Filter
 * Description: Filtro hierarquico de taxonomia para Loop Grid do Elementor Pro.
 * Version:     1.2.1
 * Author:      EMM
 * Text Domain: emm-htf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'EMM_HTF_VERSION', '1.2.1' );
define( 'EMM_HTF_PATH', plugin_dir_path( __FILE__ ) );
define( 'EMM_HTF_URL',  plugin_dir_url( __FILE__ ) );

function emm_htf_register_widget( $widgets_manager ) {
    require_once EMM_HTF_PATH . 'includes/class-widget.php';
    $widgets_manager->register( new \EMM_HTF\Widget() );
}
add_action( 'elementor/widgets/register', 'emm_htf_register_widget' );

function emm_htf_enqueue_assets() {
    wp_enqueue_style(
        'emm-htf-filter',
        EMM_HTF_URL . 'assets/css/filter.css',
        array(),
        EMM_HTF_VERSION
    );

    wp_enqueue_script(
        'emm-htf-filter',
        EMM_HTF_URL . 'assets/js/filter.js',
        array( 'jquery' ),
        EMM_HTF_VERSION,
        true
    );

    wp_localize_script( 'emm-htf-filter', 'emmHtfConfig', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'emm_htf_nonce' ),
        'postId'  => get_the_ID(),
    ) );
}
add_action( 'wp_enqueue_scripts', 'emm_htf_enqueue_assets' );