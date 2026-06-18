<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MBA_Banners_Popup_Pro {

	const OPTION_KEY = 'mba_banner_popup_options';

	public function __construct() {
		// Admin menu
		add_action('admin_menu', [ $this, 'add_admin_menu' ]);
		add_action('admin_init', [ $this, 'register_settings' ]);
		add_action('admin_enqueue_scripts', [ $this, 'admin_assets' ]);
		// Frontend
		add_action('wp_footer', [ $this, 'display_popup' ]);
		add_action('wp_enqueue_scripts', [ $this, 'frontend_assets' ]);
	}

	public function add_admin_menu() {
		add_submenu_page(
				'edit.php?post_type=mbabanners',
				'Popup Bannière',
				'Popup Bannière',
				MBA_BANNERS_PRO_CAPABILITY,
				'mba-banner-popup',
				[ $this, 'render_admin_page' ]
			);
	}

	public function register_settings() {
		register_setting(
			'mba_banner_popup_group',
			self::OPTION_KEY,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_options' ],
				'default'           => $this->default_options(),
			]
		);
	}

	public function admin_assets($hook) {
		$page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
		if ('mba-banner-popup' === $page) {
			wp_enqueue_media();
			$js_path = MBA_BANNERS_PRO_PATH . 'admin/js/mba-popup-admin.js';
			$js_url = MBA_BANNERS_PRO_URL . 'admin/js/mba-popup-admin.js';
			$js_ver = file_exists($js_path) ? filemtime($js_path) : time();
			wp_enqueue_script('mba-popup-admin', $js_url, [ 'jquery' ], $js_ver, true);
		}
	}

	private function default_options() {
		return [
			'enabled' => 0,
			'image_id' => '',
			'link' => '',
			'alt' => '',
			'delay' => 2
		];
	}

	public function sanitize_options($input) {
		$input = is_array($input) ? $input : [];
		return [
			'enabled' => empty($input['enabled']) ? 0 : 1,
			'image_id' => absint($input['image_id'] ?? 0),
			'link' => esc_url_raw($input['link'] ?? ''),
			'alt' => sanitize_text_field($input['alt'] ?? ''),
			'delay' => min(60, max(0, absint($input['delay'] ?? 2)))
		];
	}

	public function render_admin_page() {
		if ( ! current_user_can( MBA_BANNERS_PRO_CAPABILITY ) ) {
			wp_die( esc_html__( 'Vous n’avez pas les permissions nécessaires.', 'mba-banner-manager' ) );
		}

		$options = $this->get_options();
		?>
		<div class="wrap">
			<h1>Popup Bannière</h1>
			<form method="post" action="options.php">
				<?php settings_fields('mba_banner_popup_group'); ?>
				<table class="form-table">
					<tr>
						<th scope="row">Activer le popup</th>
						<td><input type="checkbox" name="mba_banner_popup_options[enabled]" value="1" <?php checked($options['enabled'], 1); ?>></td>
					</tr>
					<tr>
						<th scope="row">Image du popup</th>
						<td>
							<input type="hidden" id="mba_popup_image_id" name="mba_banner_popup_options[image_id]" value="<?php echo esc_attr($options['image_id']); ?>">
							<button type="button" class="button" id="mba_popup_pick_image">Choisir une image</button>
							<span id="mba_popup_image_preview">
								<?php if ($options['image_id']) echo wp_get_attachment_image($options['image_id'], [150,150]); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th scope="row">Lien cible</th>
						<td><input type="url" name="mba_banner_popup_options[link]" value="<?php echo esc_attr($options['link']); ?>" style="width: 350px;"></td>
					</tr>
					<tr>
						<th scope="row">Texte alternatif</th>
						<td><input type="text" name="mba_banner_popup_options[alt]" value="<?php echo esc_attr($options['alt']); ?>" style="width: 350px;"></td>
					</tr>
					<tr>
						<th scope="row">Délai d'apparition (secondes)</th>
						<td><input type="number" name="mba_banner_popup_options[delay]" value="<?php echo esc_attr($options['delay']); ?>" min="0" max="60" style="width: 80px;"></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function display_popup() {
		$options = $this->get_options();
		if (empty($options['enabled']) || empty($options['image_id'])) return;
		$image_html = wp_get_attachment_image($options['image_id'], 'full', false, [
			'alt' => esc_attr($options['alt'] ?? ''),
			'class' => 'mba-popup-img',
		]);
		if ( ! $image_html ) {
			return;
		}

		$link = !empty($options['link']) ? esc_url($options['link']) : '';
		?>
		<div id="mba-popup-banner" class="mba-popup-banner" role="dialog" aria-modal="true" aria-hidden="true" style="display:none;">
			<div class="mba-popup-inner">
				<button class="mba-popup-close" aria-label="Fermer">&times;</button>
				<?php if ($link) : ?>
					<a href="<?php echo $link; ?>" target="_blank" rel="nofollow sponsored noopener">
						<?php echo $image_html; ?>
					</a>
				<?php else : ?>
					<?php echo $image_html; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function frontend_assets() {
		$options = $this->get_options();
		if ( empty( $options['enabled'] ) || empty( $options['image_id'] ) ) {
			return;
		}

		$css_path = MBA_BANNERS_PRO_PATH . 'admin/css/mba-popup.css';
		$css_url = MBA_BANNERS_PRO_URL . 'admin/css/mba-popup.css';
		$css_ver = file_exists($css_path) ? filemtime($css_path) : time();
		wp_enqueue_style('mba-popup-style', $css_url, [], $css_ver);

		$js_path = MBA_BANNERS_PRO_PATH . 'admin/js/mba-popup.js';
		$js_url = MBA_BANNERS_PRO_URL . 'admin/js/mba-popup.js';
		$js_ver = file_exists($js_path) ? filemtime($js_path) : time();
		wp_enqueue_script('mba-popup-script', $js_url, [ 'jquery' ], $js_ver, true);
		wp_add_inline_script(
			'mba-popup-script',
			'window.mbaPopupDelay = ' . wp_json_encode( absint( $options['delay'] ) ) . ';',
			'before'
		);
	}

	private function get_options() {
		return wp_parse_args( get_option( self::OPTION_KEY, [] ), $this->default_options() );
	}
}
