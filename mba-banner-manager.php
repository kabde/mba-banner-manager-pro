<?php
/**
 * Plugin Name: MBA Banner Manager Pro
 * Description: Gestion professionnelle des bannières image, HTML et popup avec ciblage par emplacement et appareil.
 * Version:     1.1.0
 * Author:      Abderrahim KHALID
 * Text Domain: mba-banner-manager
 * Network:     true
 * Requires at least: 5.0
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * Update URI:  https://github.com/kabde/mba-banner-manager-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MBA_BANNERS_PRO_VERSION', '1.1.0' );
define( 'MBA_BANNERS_PRO_FILE', __FILE__ );
define( 'MBA_BANNERS_PRO_BASENAME', plugin_basename( __FILE__ ) );
define( 'MBA_BANNERS_PRO_PATH', plugin_dir_path( __FILE__ ) );
define( 'MBA_BANNERS_PRO_URL',  plugin_dir_url( __FILE__ ) );
define( 'MBA_BANNERS_PRO_CAPABILITY', 'manage_mba_banners' );
define( 'MBA_BANNERS_PRO_GITHUB_REPO', 'kabde/mba-banner-manager-pro' );
define( 'MBA_BANNERS_PRO_RELEASE_ASSET', 'mba-banner-manager-pro.zip' );

require_once MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-cpt.php';
require_once MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-frontend.php';
require_once MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-popup.php';

if ( is_admin() ) {
	require_once MBA_BANNERS_PRO_PATH . 'admin/class-mba-banners-meta.php';
}

new MBA_Banners_CPT_Pro();
new MBA_Banners_Frontend_Pro();
new MBA_Banners_Popup_Pro();

if ( is_admin() ) {
	new MBA_Banners_Meta_Pro();
}

function mba_banners_pro_add_caps_for_blog() {
	$role = get_role( 'administrator' );
	if ( ! $role ) {
		return;
	}

	$role->add_cap( MBA_BANNERS_PRO_CAPABILITY );
}

function mba_banners_pro_activate( $network_wide = false ) {
	if ( is_multisite() && $network_wide ) {
		$site_ids = get_sites( array( 'fields' => 'ids', 'number' => 0 ) );
		foreach ( $site_ids as $site_id ) {
			switch_to_blog( $site_id );
			mba_banners_pro_add_caps_for_blog();
			restore_current_blog();
		}
	} else {
		mba_banners_pro_add_caps_for_blog();
	}

	MBA_Banners_CPT_Pro::register();
	mba_banners_pro_clear_update_cache();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mba_banners_pro_activate' );

function mba_banners_pro_deactivate() {
	mba_banners_pro_clear_update_cache();
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mba_banners_pro_deactivate' );

function mba_banners_pro_add_caps_on_new_blog( $blog_id ) {
	if ( ! is_multisite() ) {
		return;
	}

	switch_to_blog( $blog_id );
	mba_banners_pro_add_caps_for_blog();
	restore_current_blog();
}
add_action( 'wpmu_new_blog', 'mba_banners_pro_add_caps_on_new_blog' );

function mba_banners_pro_maybe_add_caps() {
	$role = get_role( 'administrator' );
	if ( $role && ! $role->has_cap( MBA_BANNERS_PRO_CAPABILITY ) ) {
		$role->add_cap( MBA_BANNERS_PRO_CAPABILITY );
	}
}
add_action( 'admin_init', 'mba_banners_pro_maybe_add_caps' );

function mba_banners_pro_get_github_release() {
	$cache_key = 'mba_banners_pro_github_release';
	$release   = get_site_transient( $cache_key );

	if ( false !== $release ) {
		return is_array( $release ) ? $release : [];
	}

	$response = wp_remote_get(
		'https://api.github.com/repos/' . MBA_BANNERS_PRO_GITHUB_REPO . '/releases/latest',
		[
			'timeout' => 10,
			'headers' => [
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'MBA-Banner-Manager-Pro/' . MBA_BANNERS_PRO_VERSION . '; ' . home_url(),
			],
		]
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_site_transient( $cache_key, [], HOUR_IN_SECONDS );
		return [];
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) || empty( $data['tag_name'] ) ) {
		set_site_transient( $cache_key, [], HOUR_IN_SECONDS );
		return [];
	}

	set_site_transient( $cache_key, $data, 6 * HOUR_IN_SECONDS );
	return $data;
}

function mba_banners_pro_get_release_asset_url( $release ) {
	if ( empty( $release['assets'] ) || ! is_array( $release['assets'] ) ) {
		return '';
	}

	foreach ( $release['assets'] as $asset ) {
		if ( ! empty( $asset['name'] ) && MBA_BANNERS_PRO_RELEASE_ASSET === $asset['name'] && ! empty( $asset['browser_download_url'] ) ) {
			return esc_url_raw( $asset['browser_download_url'] );
		}
	}

	return '';
}

function mba_banners_pro_add_update_info( $transient ) {
	if ( empty( $transient ) || ! is_object( $transient ) ) {
		return $transient;
	}

	$release = mba_banners_pro_get_github_release();
	$version = ! empty( $release['tag_name'] ) ? ltrim( $release['tag_name'], 'vV' ) : '';
	$package = mba_banners_pro_get_release_asset_url( $release );

	if ( ! $version || ! $package || ! version_compare( MBA_BANNERS_PRO_VERSION, $version, '<' ) ) {
		return $transient;
	}

	$transient->response[ MBA_BANNERS_PRO_BASENAME ] = (object) [
		'id'           => 'github.com/' . MBA_BANNERS_PRO_GITHUB_REPO,
		'slug'         => 'mba-banner-manager',
		'plugin'       => MBA_BANNERS_PRO_BASENAME,
		'new_version'  => $version,
		'url'          => 'https://github.com/' . MBA_BANNERS_PRO_GITHUB_REPO,
		'package'      => $package,
		'tested'       => '7.0',
		'requires'     => '5.0',
		'requires_php' => '7.4',
	];

	return $transient;
}
add_filter( 'pre_set_site_transient_update_plugins', 'mba_banners_pro_add_update_info' );

function mba_banners_pro_plugins_api( $result, $action, $args ) {
	if ( 'plugin_information' !== $action || empty( $args->slug ) || 'mba-banner-manager' !== $args->slug ) {
		return $result;
	}

	$release = mba_banners_pro_get_github_release();
	$version = ! empty( $release['tag_name'] ) ? ltrim( $release['tag_name'], 'vV' ) : MBA_BANNERS_PRO_VERSION;
	$package = mba_banners_pro_get_release_asset_url( $release );

	return (object) [
		'name'          => 'MBA Banner Manager Pro',
		'slug'          => 'mba-banner-manager',
		'version'       => $version,
		'author'        => '<a href="https://github.com/kabde">Abderrahim KHALID</a>',
		'homepage'      => 'https://github.com/' . MBA_BANNERS_PRO_GITHUB_REPO,
		'download_link' => $package,
		'requires'      => '5.0',
		'tested'        => '7.0',
		'requires_php'  => '7.4',
		'sections'      => [
			'description' => 'Manage image, HTML and popup banners with placement and device targeting.',
			'changelog'   => ! empty( $release['body'] ) ? wp_kses_post( wpautop( $release['body'] ) ) : 'See the GitHub release notes.',
		],
	];
}
add_filter( 'plugins_api', 'mba_banners_pro_plugins_api', 20, 3 );

function mba_banners_pro_clear_update_cache() {
	delete_site_transient( 'mba_banners_pro_github_release' );
}
add_action( 'upgrader_process_complete', 'mba_banners_pro_clear_update_cache' );
