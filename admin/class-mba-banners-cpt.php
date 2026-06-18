<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MBA_Banners_CPT_Pro {

	const POST_TYPE = 'mbabanners';

	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_filter( 'manage_mbabanners_posts_columns', [ $this, 'add_custom_columns' ] );
		add_action( 'manage_mbabanners_posts_custom_column', [ $this, 'fill_custom_columns' ], 10, 2 );
		add_filter( 'manage_edit-mbabanners_sortable_columns', [ $this, 'make_columns_sortable' ] );
	}

	public static function capabilities() {
		return [
			'edit_post'              => MBA_BANNERS_PRO_CAPABILITY,
			'read_post'              => MBA_BANNERS_PRO_CAPABILITY,
			'delete_post'            => MBA_BANNERS_PRO_CAPABILITY,
			'edit_posts'             => MBA_BANNERS_PRO_CAPABILITY,
			'edit_others_posts'      => MBA_BANNERS_PRO_CAPABILITY,
			'publish_posts'          => MBA_BANNERS_PRO_CAPABILITY,
			'read_private_posts'     => MBA_BANNERS_PRO_CAPABILITY,
			'delete_posts'           => MBA_BANNERS_PRO_CAPABILITY,
			'delete_private_posts'   => MBA_BANNERS_PRO_CAPABILITY,
			'delete_published_posts' => MBA_BANNERS_PRO_CAPABILITY,
			'delete_others_posts'    => MBA_BANNERS_PRO_CAPABILITY,
			'edit_private_posts'     => MBA_BANNERS_PRO_CAPABILITY,
			'edit_published_posts'   => MBA_BANNERS_PRO_CAPABILITY,
			'create_posts'           => MBA_BANNERS_PRO_CAPABILITY,
		];
	}

	public static function register() {
		$instance = new self();
		$instance->register_post_type();
	}

	/* --------- 1. Enregistrement du CPT --------- */
	public function register_post_type() {

		$labels = [
			'name'               => 'Bannières',
			'singular_name'      => 'Bannière',
			'menu_name'          => 'Bannières',
			'add_new'            => 'Ajouter une bannière',
			'add_new_item'       => 'Ajouter une nouvelle bannière',
			'edit_item'          => 'Modifier la bannière',
			'new_item'           => 'Nouvelle bannière',
			'view_item'          => 'Voir la bannière',
			'search_items'       => 'Rechercher des bannières',
			'not_found'          => 'Aucune bannière trouvée',
			'not_found_in_trash' => 'Aucune bannière trouvée dans la corbeille',
		];

		$args = [
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-format-image',
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => [ 'mba_banner', 'mba_banners' ],
			'capabilities'        => self::capabilities(),
			'map_meta_cap'        => false,
			'supports'            => [ 'title' ],
			'rewrite'             => false,
		];

		register_post_type( self::POST_TYPE, $args );
	}

	/* --------- 3. Colonnes personnalisées --------- */
	public function add_custom_columns( $columns ) {
		$new_columns = [];
		
		// Garder la colonne titre en premier
		$new_columns['cb'] = $columns['cb'];
		$new_columns['title'] = $columns['title'];
		
		// Ajouter nos colonnes personnalisées
		$new_columns['status'] = 'Statut';
		$new_columns['type'] = 'Type';
		$new_columns['positions'] = 'Emplacements';
		$new_columns['author'] = 'Auteur';
		$new_columns['date'] = $columns['date'];
		
		return $new_columns;
	}

	public function fill_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'status':
				$status = get_post_meta( $post_id, '_mba_status', true ) ?: 'active';
				if ( $status === 'active' ) {
					echo '<span style="color: #46b450; font-weight: bold;">✓ Actif</span>';
				} else {
					echo '<span style="color: #dc3232; font-weight: bold;">✗ Inactif</span>';
				}
				break;
				
			case 'type':
				$type = get_post_meta( $post_id, '_mba_type', true ) ?: 'image';
				if ( $type === 'image' ) {
					$image_id = get_post_meta( $post_id, '_mba_image_id', true );
					if ( $image_id ) {
						echo '<span style="color: #0073aa;">🖼️ Image</span>';
						echo '<br><small>' . wp_get_attachment_image( $image_id, [ 50, 50 ] ) . '</small>';
					} else {
						echo '<span style="color: #0073aa;">🖼️ Image</span>';
						echo '<br><small style="color: #dc3232;">Aucune image</small>';
					}
				} else {
					echo '<span style="color: #0073aa;">📄 HTML/JS</span>';
					$html = get_post_meta( $post_id, '_mba_html', true );
					if ( $html ) {
						echo '<br><small style="color: #46b450;">Code présent</small>';
					} else {
						echo '<br><small style="color: #dc3232;">Aucun code</small>';
					}
				}
				break;
				
			case 'positions':
				$positions = (array) get_post_meta( $post_id, '_mba_positions', true );
				if ( ! empty( $positions ) ) {
					$position_labels = [
						'header' => 'En-tête',
						'footer' => 'Pied de page',
						'sidebar1' => 'Sidebar 1',
						'sidebar2' => 'Sidebar 2',
						'in_article' => 'Dans articles',
						'in_listing' => 'Entre articles'
					];
					$display_positions = [];
					foreach ( $positions as $position ) {
						if ( isset( $position_labels[ $position ] ) ) {
							$display_positions[] = $position_labels[ $position ];
						}
					}
					echo '<small>' . esc_html( implode( ', ', $display_positions ) ) . '</small>';
				} else {
					echo '<small style="color: #dc3232;">Aucun emplacement</small>';
				}
				break;
				
			case 'author':
				$author_id = get_post_field( 'post_author', $post_id );
				$author = get_userdata( $author_id );
				if ( $author ) {
					echo esc_html( $author->display_name );
				}
				break;
		}
	}

	public function make_columns_sortable( $columns ) {
		$columns['status'] = 'status';
		$columns['type'] = 'type';
		$columns['positions'] = 'positions';
		$columns['author'] = 'author';
		return $columns;
	}
}
