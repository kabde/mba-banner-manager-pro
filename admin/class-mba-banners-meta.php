<?php
/**
 * Classe pour gérer les métaboxes des bannières
 */
class MBA_Banners_Meta_Pro {

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_boxes' ] );
		add_action( 'save_post_mbabanners', [ $this, 'save' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
	}

	/* --------- 1. Métabox --------- */
	public function add_boxes() {
		add_meta_box(
			'mba_banner_details',
			'Détails de la bannière',
			[ $this, 'render_box' ],
			'mbabanners',
			'normal',
			'high'
		);
	}

	public function render_box( $post ) {
		// valeurs existantes
		$type      = get_post_meta( $post->ID, '_mba_type',      true ) ?: 'image';
		$image_id  = get_post_meta( $post->ID, '_mba_image_id',  true );
		$html_code = get_post_meta( $post->ID, '_mba_html',      true );
		$positions = (array) ( get_post_meta( $post->ID, '_mba_positions', true ) );
		$device    = get_post_meta( $post->ID, '_mba_device',    true ) ?: 'both';
		$dimensions = get_post_meta( $post->ID, '_mba_dimensions', true ) ?: '';
		$status    = get_post_meta( $post->ID, '_mba_status',    true ) ?: 'active';

		wp_nonce_field( 'mba_save_banner', 'mba_banner_nonce' );
		?>
		<p>
			<strong>Statut</strong><br>
			<label><input type="radio" name="mba_status" value="active" <?php checked( $status, 'active' ); ?>/> Actif</label>
			<label><input type="radio" name="mba_status" value="inactive" <?php checked( $status, 'inactive' ); ?>/> Inactif</label>
		</p>

		<p>
			<strong>Type de bannière</strong><br>
			<label><input type="radio" name="mba_type" value="image" <?php checked( $type, 'image' ); ?>/> Image</label>
			<label><input type="radio" name="mba_type" value="html"  <?php checked( $type, 'html'  ); ?>/> HTML/JS</label>
		</p>

		<div id="mba-image-fields" style="<?php echo ( $type === 'image' ) ? '' : 'display:none'; ?>">
			<p>
				<input type="hidden" name="mba_image_id" id="mba_image_id" value="<?php echo esc_attr( $image_id ); ?>">
				<button type="button" class="button" id="mba_pick_image">Choisir une image</button>
				<span id="mba_image_preview">
					<?php if ( $image_id ) echo wp_get_attachment_image( $image_id, [ 150, 150 ] ); ?>
				</span>
			</p>
			<p><label>Lien cible :<br>
				<input type="url" name="mba_image_link" style="width:100%" value="<?php echo esc_attr( get_post_meta( $post->ID, '_mba_image_link', true ) ); ?>">
			</label></p>
		</div>

		<div id="mba-html-fields" style="<?php echo ( $type === 'html' ) ? '' : 'display:none'; ?>">
			<p><label>Code HTML / JavaScript à insérer :<br>
				<textarea name="mba_html" rows="10" style="width:100%; font-family: 'Courier New', monospace; font-size: 12px;" placeholder="Insérez votre code HTML, JavaScript, ou bannière publicitaire ici..."><?php echo esc_textarea( $html_code ); ?></textarea>
			</label></p>
			<p style="color: #666; font-style: italic;">
				💡 <strong>Conseils :</strong> Vous pouvez insérer du code HTML, JavaScript, ou des bannières publicitaires (Google AdSense, etc.). 
				Le code sera inséré tel quel dans votre site.
			</p>
		</div>

		<div id="mba-dimensions-section" style="<?php echo ( $type === 'html' ) ? 'display:none;' : ''; ?>">
			<hr>

			<p><strong>Format de la bannière</strong><br>
				<select name="mba_dimensions" id="mba_dimensions_select" style="width:100%;">
					<option value="">Sélectionner un format</option>
					<optgroup label="Bannières horizontales (Header/Footer/Listing)">
						<option value="728x90" <?php selected( $dimensions, '728x90' ); ?>>Bannière horizontale standard (728×90)</option>
						<option value="970x90" <?php selected( $dimensions, '970x90' ); ?>>Bannière horizontale large (970×90)</option>
						<option value="970x250" <?php selected( $dimensions, '970x250' ); ?>>Bannière horizontale grande (970×250)</option>
						<option value="468x60" <?php selected( $dimensions, '468x60' ); ?>>Bannière horizontale petite (468×60)</option>
						<option value="320x50" <?php selected( $dimensions, '320x50' ); ?>>Bannière mobile horizontale (320×50)</option>
					</optgroup>
					<optgroup label="Bannières verticales (Sidebar)">
						<option value="300x250" <?php selected( $dimensions, '300x250' ); ?>>Rectangle sidebar (300×250)</option>
						<option value="300x600" <?php selected( $dimensions, '300x600' ); ?>>Grand rectangle sidebar (300×600)</option>
						<option value="160x600" <?php selected( $dimensions, '160x600' ); ?>>Skyscraper sidebar (160×600)</option>
						<option value="120x600" <?php selected( $dimensions, '120x600' ); ?>>Skyscraper étroit (120×600)</option>
						<option value="250x250" <?php selected( $dimensions, '250x250' ); ?>>Carré sidebar (250×250)</option>
						<option value="200x200" <?php selected( $dimensions, '200x200' ); ?>>Petit carré sidebar (200×200)</option>
					</optgroup>
					<optgroup label="Bannières intégrées (Dans les articles)">
						<option value="336x280" <?php selected( $dimensions, '336x280' ); ?>>Rectangle intégré (336×280)</option>
						<option value="300x250" <?php selected( $dimensions, '300x250' ); ?>>Rectangle article (300×250)</option>
						<option value="580x400" <?php selected( $dimensions, '580x400' ); ?>>Bannière large intégrée (580×400)</option>
						<option value="180x150" <?php selected( $dimensions, '180x150' ); ?>>Petit rectangle intégré (180×150)</option>
					</optgroup>
					<optgroup label="Bannières spéciales">
						<option value="1200x630" <?php selected( $dimensions, '1200x630' ); ?>>Bannière réseaux sociaux (1200×630)</option>
						<option value="1080x1080" <?php selected( $dimensions, '1080x1080' ); ?>>Carré Instagram (1080×1080)</option>
						<option value="600x200" <?php selected( $dimensions, '600x200' ); ?>>Bannière email (600×200)</option>
					</optgroup>
					<optgroup label="Format personnalisé">
						<option value="custom" <?php selected( $dimensions, 'custom' ); ?>>Dimensions personnalisées</option>
					</optgroup>
				</select>
			</p>

			<div id="mba-custom-dimensions" style="<?php echo ( $dimensions === 'custom' ) ? '' : 'display:none'; ?>">
				<p>
					<label>Largeur (px) : <input type="number" name="mba_custom_width" value="<?php echo esc_attr( get_post_meta( $post->ID, '_mba_custom_width', true ) ); ?>" min="1" max="2000" style="width:100px;"></label>
					<label style="margin-left:20px;">Hauteur (px) : <input type="number" name="mba_custom_height" value="<?php echo esc_attr( get_post_meta( $post->ID, '_mba_custom_height', true ) ); ?>" min="1" max="2000" style="width:100px;"></label>
				</p>
			</div>

			<div id="mba-suggested-positions" style="margin-top: 10px; padding: 10px; background: #f9f9f9; border-left: 4px solid #0073aa;">
				<strong>💡 Suggestions d'emplacements :</strong>
				<div id="mba-position-suggestions">
					<?php echo $this->get_position_suggestions( $dimensions ); ?>
				</div>
			</div>
		</div>

		<hr>

		<p><strong>Emplacements</strong></p>
		<?php
		$emplacements = [ 
			'header' => 'En-tête du site', 
			'footer' => 'Pied de page', 
			'sidebar1' => 'Barre latérale principale', 
			'sidebar2' => 'Barre latérale secondaire', 
			'in_article' => 'Dans les articles', 
			'in_listing' => 'Entre les articles (pages catégories)' 
		];
		foreach ( $emplacements as $slug => $label ) {
			printf(
				'<label style="margin-right:15px;"><input type="checkbox" name="mba_positions[]" value="%1$s" %2$s/> %3$s</label>',
				esc_attr( $slug ),
				checked( in_array( $slug, $positions ), true, false ),
				esc_html( $label )
			);
		}
		?>

		<hr>

		<p><strong>Appareil ciblé</strong><br>
			<label><input type="radio" name="mba_device" value="desktop" <?php checked( $device, 'desktop' ); ?>/> Desktop</label>
			<label><input type="radio" name="mba_device" value="mobile"  <?php checked( $device, 'mobile'  ); ?>/> Mobile</label>
			<label><input type="radio" name="mba_device" value="both"    <?php checked( $device, 'both'    ); ?>/> Les 2</label>
		</p>
		<?php
	}

	/* --------- 2. Sauvegarde --------- */
	public function save( $post_id ) {
		$nonce = isset( $_POST['mba_banner_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['mba_banner_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'mba_save_banner' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		$post_data = wp_unslash( $_POST );

		$type = isset( $post_data['mba_type'] ) && in_array( $post_data['mba_type'], [ 'image', 'html' ], true ) ? $post_data['mba_type'] : 'image';
		update_post_meta( $post_id, '_mba_type', $type );

		$status = isset( $post_data['mba_status'] ) && in_array( $post_data['mba_status'], [ 'active', 'inactive' ], true ) ? $post_data['mba_status'] : 'active';
		update_post_meta( $post_id, '_mba_status', $status );

		if ( 'image' === $type ) {
			update_post_meta( $post_id, '_mba_image_id',  absint( $post_data['mba_image_id'] ?? 0 ) );
			update_post_meta( $post_id, '_mba_image_link', esc_url_raw( $post_data['mba_image_link'] ?? '' ) );
			delete_post_meta( $post_id, '_mba_html' );
		} else {
			$html = $post_data['mba_html'] ?? '';
			$html = current_user_can( 'unfiltered_html' ) ? $html : wp_kses_post( $html );
			update_post_meta( $post_id, '_mba_html', $html );
			delete_post_meta( $post_id, '_mba_image_id' );
			delete_post_meta( $post_id, '_mba_image_link' );
		}

		// Sauvegarder les dimensions
		$allowed_dimensions = [
			'', '728x90', '970x90', '970x250', '468x60', '320x50',
			'300x250', '300x600', '160x600', '120x600', '250x250', '200x200',
			'336x280', '580x400', '180x150', '1200x630', '1080x1080', '600x200',
			'custom',
		];
		$dimensions = sanitize_text_field( $post_data['mba_dimensions'] ?? '' );
		$dimensions = in_array( $dimensions, $allowed_dimensions, true ) ? $dimensions : '';
		update_post_meta( $post_id, '_mba_dimensions', $dimensions );

		if ( 'custom' === $dimensions ) {
			update_post_meta( $post_id, '_mba_custom_width', min( 2000, max( 1, absint( $post_data['mba_custom_width'] ?? 0 ) ) ) );
			update_post_meta( $post_id, '_mba_custom_height', min( 2000, max( 1, absint( $post_data['mba_custom_height'] ?? 0 ) ) ) );
		} else {
			delete_post_meta( $post_id, '_mba_custom_width' );
			delete_post_meta( $post_id, '_mba_custom_height' );
		}

		$allowed_positions = [ 'header', 'footer', 'sidebar1', 'sidebar2', 'in_article', 'in_listing' ];
		$positions = isset( $post_data['mba_positions'] ) ? array_map( 'sanitize_text_field', (array) $post_data['mba_positions'] ) : [];
		$positions = array_values( array_intersect( $positions, $allowed_positions ) );
		update_post_meta( $post_id, '_mba_positions', $positions );

		$device = isset( $post_data['mba_device'] ) && in_array( $post_data['mba_device'], [ 'desktop', 'mobile', 'both' ], true ) ? $post_data['mba_device'] : 'both';
		update_post_meta( $post_id, '_mba_device', $device );
	}

	/* --------- 3. Assets admin --------- */
	public function assets( $hook ) {
		// Nous ne sommes intéressés que par les pages de bannières
		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== 'mbabanners' ) {
			return;
		}

		// Charger les scripts seulement sur la page d'édition
		if ( $screen->base === 'post' ) {

		/* 1. scripts/feuilles de style cœur ----------------------------------- */
		wp_enqueue_media();                 // charge tous les JS/CSS nécessaires
		wp_enqueue_script( 'jquery' );

		/* 2. votre script ----------------------------------------------------- */
		$script_path = MBA_BANNERS_PRO_PATH . 'admin/js/mba-banners-admin.js';
		$script_url = MBA_BANNERS_PRO_URL . 'admin/js/mba-banners-admin.js';
		
		// Cache buster intelligent basé sur la date de modification du fichier
		if (file_exists($script_path)) {
			$file_time = filemtime($script_path);
			$cache_buster = '?v=' . $file_time;
		} else {
			$cache_buster = '?v=' . time(); // Fallback si le fichier n'existe pas
		}
		
		$script_url .= $cache_buster;
		
		wp_enqueue_script(
			'mba-banners-admin',
			$script_url,
			[ 'jquery', 'media-views' ],    // 'media-views' suffit, pas besoin de 'media-grid'
			'1.0',
			true
		);
		}

		// Ajouter des styles CSS personnalisés (pour toutes les pages de bannières)
		wp_add_inline_style( 'wp-admin', '
			#mba-html-fields textarea {
				background-color: #f8f9fa;
				border: 1px solid #ddd;
				border-radius: 4px;
				padding: 10px;
			}
			#mba-html-fields textarea:focus {
				border-color: #0073aa;
				box-shadow: 0 0 0 1px #0073aa;
			}
			#mba-suggested-positions {
				margin-top: 15px;
				padding: 12px;
				background: #f8f9fa;
				border-left: 4px solid #0073aa;
				border-radius: 4px;
			}
			#mba-position-suggestions span {
				display: inline-block;
				margin: 2px 5px 2px 0;
				padding: 4px 8px;
				background-color: #e0f2f7;
				border-radius: 3px;
				font-size: 0.85em;
				color: #0073aa;
			}
			
			/* Styles pour les colonnes du listing */
			.wp-list-table .column-status {
				width: 80px;
			}
			.wp-list-table .column-type {
				width: 120px;
			}
			.wp-list-table .column-positions {
				width: 150px;
			}
			.wp-list-table .column-author {
				width: 120px;
			}
			.wp-list-table .column-type img {
				border-radius: 3px;
				border: 1px solid #ddd;
			}
			.wp-list-table .column-status span {
				padding: 2px 6px;
				border-radius: 3px;
				font-size: 0.85em;
			}
			.wp-list-table .column-type span {
				font-weight: 500;
			}
		' );
	}

	/* --------- 4. Suggestions d'emplacements --------- */
	private function get_position_suggestions( $dimensions ) {
		if ( empty( $dimensions ) ) {
			return '<em>Sélectionnez un format pour voir les suggestions</em>';
		}

		$suggestions = [];
		$dimensions_data = [
			// Bannières horizontales
			'728x90' => [ 'header', 'footer', 'in_listing' ],
			'970x90' => [ 'header', 'footer', 'in_listing' ],
			'970x250' => [ 'header', 'footer', 'in_listing' ],
			'468x60' => [ 'header', 'footer', 'in_listing' ],
			'320x50' => [ 'header', 'footer', 'in_listing' ],
			
			// Bannières verticales
			'300x250' => [ 'sidebar1', 'sidebar2' ],
			'300x600' => [ 'sidebar1', 'sidebar2' ],
			'160x600' => [ 'sidebar1', 'sidebar2' ],
			'120x600' => [ 'sidebar1', 'sidebar2' ],
			'250x250' => [ 'sidebar1', 'sidebar2' ],
			'200x200' => [ 'sidebar1', 'sidebar2' ],
			
			// Bannières intégrées
			'336x280' => [ 'in_article' ],
			'580x400' => [ 'in_article' ],
			'180x150' => [ 'in_article' ],
			
			// Bannières spéciales
			'1200x630' => [ 'in_article', 'in_listing' ],
			'1080x1080' => [ 'in_article', 'in_listing' ],
			'600x200' => [ 'in_article', 'in_listing' ],
		];

		if ( isset( $dimensions_data[ $dimensions ] ) ) {
			$suggested_positions = $dimensions_data[ $dimensions ];
			$position_labels = [
				'header' => 'En-tête',
				'footer' => 'Pied de page', 
				'sidebar1' => 'Barre latérale',
				'sidebar2' => 'Barre latérale',
				'in_article' => 'Dans les articles',
				'in_listing' => 'Entre les articles'
			];
			
			foreach ( $suggested_positions as $position ) {
				$label = isset( $position_labels[ $position ] ) ? $position_labels[ $position ] : $position;
				$suggestions[] = '<span style="display:inline-block; margin-right:5px; padding:2px 8px; background-color:#e0f2f7; border-radius:3px; font-size:0.8em; color:#0073aa;">' . esc_html( $label ) . '</span>';
			}
		}

		return empty( $suggestions ) ? '<em>Aucune suggestion pour ce format</em>' : implode( '', $suggestions );
	}
}
