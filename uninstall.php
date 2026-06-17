<?php
// Sécurité
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$posts = get_posts( [
	'post_type'   => 'mbabanners',
	'numberposts' => -1,
	'post_status' => 'any',
] );

foreach ( $posts as $post ) {
	wp_delete_post( $post->ID, true );
}
