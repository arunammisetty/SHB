<?php
/**
 * Core singleton for SHB – Security Headers Boss.
 *
 * @package SHB
 */

defined( 'ABSPATH' ) || exit;

final class SHB_Core {

	/** Plugin version */
	const VERSION = '1.1.0';

	/** Option key */
	private $option_key = 'shb_settings';

	/** Singleton store */
	private static $instance = null;

	/** Accessor */
	public static function instance(): self {
		return self::$instance ?: ( self::$instance = new self() );
	}

	/** Boot */
	private function __construct() {
		// i18n
		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );

		// Admin
		add_action( 'admin_menu',            [ $this, 'add_menu' ] );
		add_action( 'admin_init',            [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_notices',         [ $this, 'maybe_activation_notice' ] );

		// AJAX wizard
		add_action( 'wp_ajax_shb_apply_recommended', [ $this, 'ajax_apply_recommended' ] );

		// Front‑end headers
		add_action( 'send_headers', [ $this, 'maybe_send_headers' ], 11 );

		// Activation
		register_activation_hook( SHB_FILE, [ $this, 'on_activate' ] );
	}

	/** No clone / wakeup */
	private function __clone() {}
	private function __wakeup() {}

	/*--------------------------------------------------------------
	 i18n
	--------------------------------------------------------------*/
	public function load_textdomain(): void {
		load_plugin_textdomain( 'shb', false, dirname( plugin_basename( SHB_FILE ) ) . '/languages' );
	}

	/*--------------------------------------------------------------
	 Admin UI
	--------------------------------------------------------------*/
	public function add_menu(): void {
		add_options_page(
			__( 'Security Headers', 'shb' ),
			__( 'Security Headers', 'shb' ),
			'manage_options',
			'shb',
			[ $this, 'render_settings_page' ]
		);
	}

	public function register_settings(): void {
		register_setting(
			'shb',
			$this->option_key,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_settings' ],
				'default'           => [ 'mode' => 'safe' ],
			]
		);

		add_settings_section(
			'shb_section',
			__( 'Choose Your Protection Mode', 'shb' ),
			'__return_false',
			'shb'
		);

		add_settings_field(
			'shb_mode',
			__( 'Mode', 'shb' ),
			[ $this, 'render_field_mode' ],
			'shb',
			'shb_section'
		);
	}

	public function sanitize_settings( array $input ): array {
		$modes         = [ 'safe', 'strict', 'paranoid' ];
		$input['mode'] = in_array( $input['mode'], $modes, true ) ? $input['mode'] : 'safe';
		return $input;
	}

	public function render_settings_page(): void {
		require SHB_DIR . 'admin/view-settings-page.php';
	}

	public function render_field_mode(): void {
		$current = $this->get_setting( 'mode', 'safe' );
		$modes   = [
			'safe'     => __( 'Standard – Safe & Compatible', 'shb' ),
			'strict'   => __( 'Strict – Blocks inline scripts', 'shb' ),
			'paranoid' => __( 'Paranoid – Max isolation', 'shb' ),
		];
		foreach ( $modes as $value => $label ) : ?>
			<label style="display:block;margin:6px 0;">
				<input type="radio"
				       name="<?php echo esc_attr( $this->option_key ); ?>[mode]"
				       value="<?php echo esc_attr( $value ); ?>"
				       <?php checked( $current, $value ); ?>>
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endforeach;
	}

	public function enqueue_admin_assets( string $hook ): void {
		if ( 'settings_page_shb' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'shb-admin', SHB_URL . 'assets/css/admin.css', [], self::VERSION );
		wp_enqueue_style( 'shb-wizard', SHB_URL . 'admin/wizard.css', [], self::VERSION );
		wp_enqueue_script( 'shb-wizard', SHB_URL . 'admin/wizard.js', [ 'jquery' ], self::VERSION, true );
		wp_localize_script(
			'shb-wizard',
			'SHB_DATA',
			[
				'nonce' => wp_create_nonce( 'shb_wizard' ),
			]
		);
	}

	/*--------------------------------------------------------------
	 AJAX – Wizard
	--------------------------------------------------------------*/
	public function ajax_apply_recommended(): void {
		check_ajax_referer( 'shb_wizard', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'shb' ), 403 );
		}
		update_option( $this->option_key, [ 'mode' => 'strict' ] ); // “Strict” is our recommended default
		wp_send_json_success();
	}

	/*--------------------------------------------------------------
	 Front‑end headers
	--------------------------------------------------------------*/
	public function maybe_send_headers(): void {
		$mode = $this->get_setting( 'mode', 'safe' );

		header( 'Permissions-Policy: interest-cohort=()' );

		switch ( $mode ) {
			case 'paranoid':
				header( 'Cross-Origin-Opener-Policy: same-origin' );
				header( 'Cross-Origin-Embedder-Policy: require-corp' );
				// no break
			case 'strict':
				header( "Content-Security-Policy: default-src 'self'; frame-ancestors 'none'" );
				// no break
			case 'safe':
			default:
				header( 'Strict-Transport-Security: max-age=63072000; includeSubDomains; preload' );
				header( 'Referrer-Policy: strict-origin-when-cross-origin' );
				break;
		}
	}

	/*--------------------------------------------------------------
	 Helpers
	--------------------------------------------------------------*/
	private function get_setting( string $key = '', $default = null ) {
		$options = get_option( $this->option_key, [ 'mode' => 'safe' ] );
		return '' === $key ? $options : ( $options[ $key ] ?? $default );
	}

	/*--------------------------------------------------------------
	 Activation redirect
	--------------------------------------------------------------*/
	public function on_activate(): void {
		add_option( 'shb_activation_redirect', true );
	}

	public function maybe_activation_notice(): void {
		if ( get_option( 'shb_activation_redirect', false ) ) {
			delete_option( 'shb_activation_redirect' );
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				wp_kses(
					sprintf(
						/* translators: %s = settings URL */
						__( 'SHB activated! Visit <a href="%s">Settings → Security Headers</a> to run the 60‑second wizard.', 'shb' ),
						esc_url( admin_url( 'options-general.php?page=shb' ) )
					),
					[ 'a' => [ 'href' => [] ] ]
				)
			);
		}
	}
}
