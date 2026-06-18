<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe pour gérer les métaboxes des bannières
 */
class MBA_Banners_Meta_Pro {

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_boxes' ] );
		add_action( 'save_post_mbabanners', [ $this, 'save' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	/* --------- 1. Métabox --------- */
	public function add_boxes() {
		if ( ! current_user_can( MBA_BANNERS_PRO_CAPABILITY ) ) {
			return;
		}

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
		$type      = get_post_meta( $post->ID, '_mba_type',      true ) ?: 'image';
		$image_id  = get_post_meta( $post->ID, '_mba_image_id',  true );
		$html_code = get_post_meta( $post->ID, '_mba_html',      true );
		$positions = $this->normalize_positions( get_post_meta( $post->ID, '_mba_positions', true ) );
		$device    = get_post_meta( $post->ID, '_mba_device',    true ) ?: 'both';
		$dimensions = get_post_meta( $post->ID, '_mba_dimensions', true ) ?: '';
		$status    = get_post_meta( $post->ID, '_mba_status',    true ) ?: 'active';
		$image_link = get_post_meta( $post->ID, '_mba_image_link', true );
		$warnings = $this->get_configuration_warnings( $post->ID );

		wp_nonce_field( 'mba_save_banner', 'mba_banner_nonce' );
		?>
		<?php if ( $warnings ) : ?>
			<div class="notice notice-warning inline mba-config-warning">
				<p><strong><?php esc_html_e( 'Configuration à compléter', 'mba-banner-manager' ); ?></strong></p>
				<ul>
					<?php foreach ( $warnings as $warning ) : ?>
						<li><?php echo esc_html( $warning ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="mba-admin-layout">
			<div class="mba-admin-main">
				<section class="mba-admin-section">
					<h3><?php esc_html_e( 'Status', 'mba-banner-manager' ); ?></h3>
					<div class="mba-field-grid">
						<label><input type="radio" name="mba_status" value="active" <?php checked( $status, 'active' ); ?>/> <?php esc_html_e( 'Actif', 'mba-banner-manager' ); ?></label>
						<label><input type="radio" name="mba_status" value="inactive" <?php checked( $status, 'inactive' ); ?>/> <?php esc_html_e( 'Inactif', 'mba-banner-manager' ); ?></label>
					</div>
				</section>

				<section class="mba-admin-section">
					<h3><?php esc_html_e( 'Creative', 'mba-banner-manager' ); ?></h3>
					<div class="mba-field-grid">
						<label><input type="radio" name="mba_type" value="image" <?php checked( $type, 'image' ); ?>/> <?php esc_html_e( 'Image', 'mba-banner-manager' ); ?></label>
						<label><input type="radio" name="mba_type" value="html" <?php checked( $type, 'html' ); ?>/> <?php esc_html_e( 'HTML/JS', 'mba-banner-manager' ); ?></label>
					</div>

					<div id="mba-image-fields" style="<?php echo ( $type === 'image' ) ? '' : 'display:none'; ?>">
						<input type="hidden" name="mba_image_id" id="mba_image_id" value="<?php echo esc_attr( $image_id ); ?>">
						<p>
							<button type="button" class="button" id="mba_pick_image"><?php esc_html_e( 'Choisir une image', 'mba-banner-manager' ); ?></button>
							<span id="mba_image_preview">
								<?php if ( $image_id ) echo wp_get_attachment_image( $image_id, [ 150, 150 ] ); ?>
							</span>
						</p>
						<p class="description mba-empty-state" id="mba-image-empty" <?php echo $image_id ? 'style="display:none;"' : ''; ?>><?php esc_html_e( 'Aucune image sélectionnée.', 'mba-banner-manager' ); ?></p>
						<p><label><?php esc_html_e( 'Lien cible', 'mba-banner-manager' ); ?><br>
							<input type="url" name="mba_image_link" id="mba_image_link" style="width:100%" value="<?php echo esc_attr( $image_link ); ?>">
						</label></p>
						<p class="description mba-empty-state" id="mba-link-empty" <?php echo $image_link ? 'style="display:none;"' : ''; ?>><?php esc_html_e( 'Aucun lien cible configuré.', 'mba-banner-manager' ); ?></p>
					</div>

					<div id="mba-html-fields" style="<?php echo ( $type === 'html' ) ? '' : 'display:none'; ?>">
						<p><label><?php esc_html_e( 'Code HTML / JavaScript à insérer', 'mba-banner-manager' ); ?><br>
							<textarea name="mba_html" id="mba_html" rows="10" style="width:100%; font-family: monospace; font-size: 12px;" placeholder="<?php esc_attr_e( 'Insérez votre code HTML, JavaScript, ou bannière publicitaire ici...', 'mba-banner-manager' ); ?>"><?php echo esc_textarea( $html_code ); ?></textarea>
						</label></p>
						<p class="description"><?php esc_html_e( 'Le code HTML/JS doit être réservé aux administrateurs de confiance.', 'mba-banner-manager' ); ?></p>
					</div>
				</section>

				<section class="mba-admin-section" id="mba-dimensions-section" style="<?php echo ( $type === 'html' ) ? 'display:none;' : ''; ?>">
					<h3><?php esc_html_e( 'Format', 'mba-banner-manager' ); ?></h3>
					<p><select name="mba_dimensions" id="mba_dimensions_select" style="width:100%;">
						<?php $this->render_dimension_options( $dimensions ); ?>
					</select></p>
					<div id="mba-custom-dimensions" style="<?php echo ( $dimensions === 'custom' ) ? '' : 'display:none'; ?>">
						<label><?php esc_html_e( 'Largeur (px)', 'mba-banner-manager' ); ?> <input type="number" name="mba_custom_width" value="<?php echo esc_attr( get_post_meta( $post->ID, '_mba_custom_width', true ) ); ?>" min="1" max="2000" style="width:100px;"></label>
						<label style="margin-left:20px;"><?php esc_html_e( 'Hauteur (px)', 'mba-banner-manager' ); ?> <input type="number" name="mba_custom_height" value="<?php echo esc_attr( get_post_meta( $post->ID, '_mba_custom_height', true ) ); ?>" min="1" max="2000" style="width:100px;"></label>
					</div>
					<div id="mba-suggested-positions">
						<strong><?php esc_html_e( 'Suggestions d’emplacements', 'mba-banner-manager' ); ?></strong>
						<div id="mba-position-suggestions"><?php echo $this->get_position_suggestions( $dimensions ); ?></div>
					</div>
				</section>

				<section class="mba-admin-section">
					<h3><?php esc_html_e( 'Placement', 'mba-banner-manager' ); ?></h3>
					<div class="mba-field-grid mba-position-grid">
						<?php foreach ( $this->get_position_options() as $slug => $label ) : ?>
							<label><input type="checkbox" name="mba_positions[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( $slug, $positions, true ) ); ?>/> <?php echo esc_html( $label ); ?></label>
						<?php endforeach; ?>
					</div>
					<p class="description mba-empty-state" id="mba-positions-empty" <?php echo ! empty( $positions ) ? 'style="display:none;"' : ''; ?>><?php esc_html_e( 'Aucun emplacement sélectionné.', 'mba-banner-manager' ); ?></p>
				</section>

				<section class="mba-admin-section">
					<h3><?php esc_html_e( 'Targeting', 'mba-banner-manager' ); ?></h3>
					<div class="mba-field-grid">
						<label><input type="radio" name="mba_device" value="desktop" <?php checked( $device, 'desktop' ); ?>/> <?php esc_html_e( 'Desktop', 'mba-banner-manager' ); ?></label>
						<label><input type="radio" name="mba_device" value="mobile" <?php checked( $device, 'mobile' ); ?>/> <?php esc_html_e( 'Mobile', 'mba-banner-manager' ); ?></label>
						<label><input type="radio" name="mba_device" value="both" <?php checked( $device, 'both' ); ?>/> <?php esc_html_e( 'Les 2', 'mba-banner-manager' ); ?></label>
					</div>
				</section>
			</div>

			<aside class="mba-admin-preview">
				<div class="mba-preview-toolbar">
					<strong><?php esc_html_e( 'Preview', 'mba-banner-manager' ); ?></strong>
					<button type="button" class="button button-small mba-preview-mode is-active" data-mode="desktop"><?php esc_html_e( 'Desktop', 'mba-banner-manager' ); ?></button>
					<button type="button" class="button button-small mba-preview-mode" data-mode="mobile"><?php esc_html_e( 'Mobile', 'mba-banner-manager' ); ?></button>
				</div>
				<div class="mba-preview-frame mba-preview-desktop" id="mba-live-preview">
					<?php echo $this->render_preview_html( $type, $image_id, $html_code, $image_link ); ?>
				</div>
			</aside>
			</div>
			<?php
		}

		private function get_position_options() {
			return [
				'header'     => __( 'En-tête', 'mba-banner-manager' ),
				'footer'     => __( 'Pied de page', 'mba-banner-manager' ),
				'sidebar1'   => __( 'Sidebar 1', 'mba-banner-manager' ),
				'sidebar2'   => __( 'Sidebar 2', 'mba-banner-manager' ),
				'in_article' => __( 'Dans les articles', 'mba-banner-manager' ),
				'in_listing' => __( 'Entre les articles', 'mba-banner-manager' ),
			];
		}

		private function get_dimension_options() {
			return [
				'' => [
					'' => __( 'Sélectionner un format', 'mba-banner-manager' ),
				],
				__( 'Leaderboard', 'mba-banner-manager' ) => [
					'728x90'  => '728 x 90',
					'970x90'  => '970 x 90',
					'970x250' => '970 x 250',
					'468x60'  => '468 x 60',
					'320x50'  => '320 x 50',
				],
				__( 'Sidebar', 'mba-banner-manager' ) => [
					'300x250' => '300 x 250',
					'300x600' => '300 x 600',
					'160x600' => '160 x 600',
					'120x600' => '120 x 600',
					'250x250' => '250 x 250',
					'200x200' => '200 x 200',
				],
				__( 'Contenu', 'mba-banner-manager' ) => [
					'336x280' => '336 x 280',
					'580x400' => '580 x 400',
					'180x150' => '180 x 150',
				],
				__( 'Réseaux sociaux', 'mba-banner-manager' ) => [
					'1200x630' => '1200 x 630',
					'1080x1080' => '1080 x 1080',
					'600x200'  => '600 x 200',
				],
				__( 'Avancé', 'mba-banner-manager' ) => [
					'custom' => __( 'Format personnalisé', 'mba-banner-manager' ),
				],
			];
		}

		private function render_dimension_options( $selected ) {
			foreach ( $this->get_dimension_options() as $group_label => $options ) {
				if ( '' === $group_label ) {
					foreach ( $options as $value => $label ) {
						echo '<option value="' . esc_attr( $value ) . '" ' . selected( $selected, $value, false ) . '>' . esc_html( $label ) . '</option>';
					}
					continue;
				}

				echo '<optgroup label="' . esc_attr( $group_label ) . '">';
				foreach ( $options as $value => $label ) {
					echo '<option value="' . esc_attr( $value ) . '" ' . selected( $selected, $value, false ) . '>' . esc_html( $label ) . '</option>';
				}
				echo '</optgroup>';
			}
		}

		private function render_preview_html( $type, $image_id, $html_code, $image_link = '' ) {
			if ( 'html' === $type ) {
				if ( empty( trim( (string) $html_code ) ) ) {
					return '<div class="mba-preview-empty">' . esc_html__( 'Aucun code HTML à prévisualiser.', 'mba-banner-manager' ) . '</div>';
				}

				$preview = current_user_can( 'unfiltered_html' ) ? $html_code : wp_kses_post( $html_code );
				return '<iframe class="mba-preview-iframe" sandbox="allow-scripts allow-popups allow-forms" srcdoc="' . esc_attr( $preview ) . '"></iframe>';
			}

			if ( ! $image_id ) {
				return '<div class="mba-preview-empty">' . esc_html__( 'Aucune image sélectionnée.', 'mba-banner-manager' ) . '</div>';
			}

			$image = wp_get_attachment_image( $image_id, 'medium', false, [ 'class' => 'mba-preview-image' ] );
			if ( ! $image ) {
				return '<div class="mba-preview-empty">' . esc_html__( 'Image introuvable.', 'mba-banner-manager' ) . '</div>';
			}

			if ( $image_link ) {
				return '<a href="' . esc_url( $image_link ) . '" target="_blank" rel="noopener noreferrer">' . $image . '</a>';
			}

			return $image;
		}

		private function get_configuration_warnings( $post_id ) {
			$status     = get_post_meta( $post_id, '_mba_status', true ) ?: 'active';
			$type       = get_post_meta( $post_id, '_mba_type', true ) ?: 'image';
			$image_id   = absint( get_post_meta( $post_id, '_mba_image_id', true ) );
			$image_link = get_post_meta( $post_id, '_mba_image_link', true );
			$html_code  = get_post_meta( $post_id, '_mba_html', true );
			$positions  = $this->normalize_positions( get_post_meta( $post_id, '_mba_positions', true ) );
			$warnings   = [];

			if ( 'active' !== $status ) {
				return [];
			}

			if ( 'image' === $type ) {
				if ( ! $image_id ) {
					$warnings[] = __( 'Une bannière image active doit avoir une image.', 'mba-banner-manager' );
				}
				if ( ! $image_link ) {
					$warnings[] = __( 'Une bannière image active doit avoir un lien cible.', 'mba-banner-manager' );
				}
			} elseif ( empty( trim( (string) $html_code ) ) ) {
				$warnings[] = __( 'Une bannière HTML active doit avoir du code HTML/JS.', 'mba-banner-manager' );
			}

			if ( empty( $positions ) ) {
				$warnings[] = __( 'Une bannière active doit avoir au moins un emplacement.', 'mba-banner-manager' );
			}

			return $warnings;
		}

		public function admin_notices() {
			$screen = get_current_screen();
			if ( ! $screen || 'mbabanners' !== $screen->post_type || 'post' !== $screen->base ) {
				return;
			}

			if ( isset( $_GET['mba_duplicated'] ) && '1' === sanitize_key( wp_unslash( $_GET['mba_duplicated'] ) ) ) {
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Bannière dupliquée en brouillon.', 'mba-banner-manager' ) . '</p></div>';
			}

			$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
			if ( ! $post_id ) {
				return;
			}

			$warnings = $this->get_configuration_warnings( $post_id );
			if ( ! $warnings ) {
				return;
			}

			echo '<div class="notice notice-warning is-dismissible"><p><strong>' . esc_html__( 'Configuration de bannière incomplète.', 'mba-banner-manager' ) . '</strong></p><ul>';
			foreach ( $warnings as $warning ) {
				echo '<li>' . esc_html( $warning ) . '</li>';
			}
			echo '</ul></div>';
		}

		/* --------- 2. Sauvegarde --------- */
	public function save( $post_id ) {
		$nonce = isset( $_POST['mba_banner_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['mba_banner_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'mba_save_banner' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! current_user_can( MBA_BANNERS_PRO_CAPABILITY ) || ! current_user_can( 'edit_post', $post_id ) ) return;

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

		$positions = isset( $post_data['mba_positions'] ) ? $this->normalize_positions( $post_data['mba_positions'] ) : [];
		update_post_meta( $post_id, '_mba_positions', $positions );

		$device = isset( $post_data['mba_device'] ) && in_array( $post_data['mba_device'], [ 'desktop', 'mobile', 'both' ], true ) ? $post_data['mba_device'] : 'both';
		update_post_meta( $post_id, '_mba_device', $device );
	}

	/* --------- 3. Assets admin --------- */
	public function assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== 'mbabanners' ) {
			return;
		}

		if ( $screen->base === 'post' ) {
			wp_enqueue_media();
			wp_enqueue_script( 'jquery' );

			$script_path = MBA_BANNERS_PRO_PATH . 'admin/js/mba-banners-admin.js';
			$script_url  = MBA_BANNERS_PRO_URL . 'admin/js/mba-banners-admin.js';

			wp_enqueue_script(
				'mba-banners-admin',
				$script_url,
				[ 'jquery', 'media-views' ],
				file_exists( $script_path ) ? (string) filemtime( $script_path ) : MBA_BANNERS_PRO_VERSION,
				true
			);
		}

		wp_register_style( 'mba-banners-admin', false, [], MBA_BANNERS_PRO_VERSION );
		wp_enqueue_style( 'mba-banners-admin' );
		wp_add_inline_style( 'mba-banners-admin', '
			.mba-admin-layout {
				display: grid;
				grid-template-columns: minmax(0, 1fr) 320px;
				gap: 20px;
				align-items: start;
			}
			.mba-admin-main {
				min-width: 0;
			}
			.mba-admin-section {
				border: 1px solid #dcdcde;
				background: #fff;
				border-radius: 4px;
				margin: 0 0 14px;
				padding: 16px;
			}
			.mba-admin-section h3 {
				margin: 0 0 12px;
				font-size: 14px;
				line-height: 1.4;
			}
			.mba-field-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
				gap: 8px 14px;
			}
			.mba-position-grid {
				grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
			}
			.mba-empty-state {
				color: #b32d2e;
				margin: 6px 0 0;
			}
			.mba-config-warning ul,
			.notice ul {
				list-style: disc;
				margin-left: 20px;
			}
			.mba-admin-preview {
				position: sticky;
				top: 42px;
				border: 1px solid #dcdcde;
				background: #fff;
				border-radius: 4px;
				padding: 12px;
			}
			.mba-preview-toolbar {
				display: flex;
				align-items: center;
				gap: 8px;
				margin-bottom: 12px;
			}
			.mba-preview-toolbar strong {
				margin-right: auto;
			}
			.mba-preview-mode.is-active {
				border-color: #2271b1;
				color: #0a4b78;
			}
			.mba-preview-frame {
				display: flex;
				align-items: center;
				justify-content: center;
				min-height: 180px;
				overflow: auto;
				padding: 16px;
				background: #f6f7f7;
				border: 1px dashed #c3c4c7;
				border-radius: 4px;
			}
			.mba-preview-mobile {
				max-width: 180px;
				min-height: 260px;
				margin: 0 auto;
			}
			.mba-preview-image,
			#mba-live-preview img {
				display: block;
				max-width: 100%;
				height: auto;
			}
			.mba-preview-empty {
				color: #646970;
				text-align: center;
			}
			.mba-preview-html {
				max-width: 100%;
			}
			.mba-preview-iframe {
				width: 100%;
				min-height: 220px;
				border: 0;
				background: #fff;
			}
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
			@media (max-width: 960px) {
				.mba-admin-layout {
					grid-template-columns: 1fr;
				}
				.mba-admin-preview {
					position: static;
				}
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

	private function normalize_positions( $positions ) {
		$allowed_positions = array_keys( $this->get_position_options() );
		$positions         = array_map( 'sanitize_key', (array) $positions );

		return array_values( array_intersect( array_filter( $positions ), $allowed_positions ) );
	}
}
