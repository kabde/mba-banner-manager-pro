<?php
// Sécurité
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function mba_banners_pro_uninstall_blog() {
	$posts = get_posts( [
		'post_type'   => 'mbabanners',
		'numberposts' => -1,
		'post_status' => 'any',
	] );

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}

	delete_option( 'mba_banner_popup_options' );

	$role = get_role( 'administrator' );
	if ( $role ) {
		$role->remove_cap( 'manage_mba_banners' );
	}
}

if ( is_multisite() ) {
	$site_ids = get_sites( [ 'fields' => 'ids', 'number' => 0 ] );
	foreach ( $site_ids as $site_id ) {
		switch_to_blog( $site_id );
		mba_banners_pro_uninstall_blog();
		restore_current_blog();
	}
} else {
	mba_banners_pro_uninstall_blog();
}
