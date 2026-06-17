<?php
/**
 * Plugin Name: MBA Banner Manager Pro
 * Description: Gestion professionnelle des bannières image, HTML et popup avec ciblage par emplacement et appareil.
 * Version:     1.0.1
 * Author:      Abderrahim KHALID
 * Text Domain: mba-banner-manager
 * Network:     true
 * Requires at least: 5.0
 * Tested up to: 7.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MBA_BANNERS_PRO_VERSION', '1.0.1' );
define( 'MBA_BANNERS_PRO_PATH', plugin_dir_path( __FILE__ ) );
define( 'MBA_BANNERS_PRO_URL',  plugin_dir_url( __FILE__ ) );

/* ---------- 1. Charger les classes admin ---------- */
if ( is_admin() ) {
	$cpt_file = MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-cpt.php';
	$meta_file = MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-meta.php';
	
	if ( file_exists( $cpt_file ) && file_exists( $meta_file ) ) {
		require_once $cpt_file;
		require_once $meta_file;

		if ( class_exists( 'MBA_Banners_CPT_Pro' ) && class_exists( 'MBA_Banners_Meta_Pro' ) ) {
			new MBA_Banners_CPT_Pro();
			new MBA_Banners_Meta_Pro();
		}
	}
}

/* ---------- 2. Charger la classe frontend ---------- */
$frontend_file = MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-frontend.php';
$popup_file = MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-popup.php';

if ( file_exists( $frontend_file ) ) {
	require_once $frontend_file;
	
	if ( class_exists( 'MBA_Banners_Frontend_Pro' ) ) {
		new MBA_Banners_Frontend_Pro();
	}
}
if ( file_exists( $popup_file ) ) {
	require_once $popup_file;
	if ( class_exists( 'MBA_Banners_Popup_Pro' ) ) {
		new MBA_Banners_Popup_Pro();
	}
}
