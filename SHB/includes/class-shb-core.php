<?php
/**
 * Core singleton for SHB – Security Headers Boss.
 *
 * @package  SHB
 * @author   Arun Ammisetty
 * @license  GPL‑2.0‑or‑later
 */

defined( 'ABSPATH' ) || exit;

final class SHB_Core {

	/** @var string Plugin version */
	const VERSION = '1.0.0';

	/** @var string Option key used in wp_options */
	private $option_key = 'shb_settings';

	/** @var self */
	private static $instance = null;

	/** Singleton accessor */
	public static function instance(): self {
		return self::$instance ?: ( self::$instance = new self() );
	}

	/** Constructor — register hooks. */
	private function __construct() {
		add_action( 'admin_menu',           [ $this, 'add_menu' ] );
		add_action( 'admin_init',           [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts',[ $this, 'enqueue_admin_assets' ] );
		add_action( 'send_headers',         [ $this, 'maybe_send_headers' ], 11 );

		register_activation_hook( dirname( __DIR__ ) . '/shb.php', [ $this, 'on_activate' ] );
	}

	private function __clone() {}
	private function __wakeup() {}

	/*--------------------------------------------------------------
	 Admin
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
		$modes          = [ 'safe', 'strict', 'paranoid' ];
		$input['mode']  = in_array( $input['mode'], $modes, true ) ? $input['mode'] : 'safe';
		return $input;
	}

	public function render_settings_page(): void {
		require_once dirname( __DIR__ ) . '/admin/view-settings-page.php';
	}

	public function render_field_mode(): void {
		$curr  = $this->get_setting( 'mode', 'safe' );
		$modes = [
			'safe'     => __( 'Standard – Safe & Compatible', 'shb' ),
			'strict'   => __( 'Strict – Blocks inline scripts', 'shb' ),
			'paranoid' => __( 'Paranoid – Max isolation', 'shb' ),
		];
		foreach ( $modes as $value => $label ) : ?>
			<label style="display:block;margin:6px 0;">
				<input type="radio"
				       name="<?php echo esc_attr( $this->option_key ); ?>[mode]"
				       value="<?php echo esc_attr( $value ); ?>"
				       <?php checked( $curr, $value ); ?>>
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endforeach;
	}

	public function enqueue_admin_assets( string $hook ): void {
		if ( 'settings_page_shb' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'shb-admin', plugins_url( '../assets/css/admin.css', __FILE__ ), [], self::VERSION );
		wp_enqueue_script( 'shb-wizard', plugins_url( '../admin/wizard.js', __FILE__ ), [ 'jquery' ], self::VERSION, true );
		wp_localize_script(
			'shb-wizard',
			'SHB_DATA',
			[
				'status'  => $this->get_status(),
				'missing' => $this->count_missing_headers(),
				'mode'    => $this->get_setting( 'mode', 'safe' ),
				'nonce'   => wp_create_nonce( 'shb_wizard' ),
			]
		);
	}

	/*--------------------------------------------------------------
	 Front‑end
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
		$all = get_option( $this->option_key, [ 'mode' => 'safe' ] );
		return '' === $key ? $all : ( $all[ $key ] ?? $default );
	}

	private function get_status(): string {
		$missing = $this->count_missing_headers();
		if ( 0 === $missing )  return 'good';
		return ( $missing <= 2 ) ? 'warning' : 'critical';
	}

	private function count_missing_headers(): int {
		$recommended = [ 'strict-transport-security', 'referrer-policy' ];
		$mode        = $this->get_setting( 'mode', 'safe' );

		if ( in_array( $mode, [ 'strict', 'paranoid' ], true ) )
			$recommended[] = 'content-security-policy';
		if ( 'paranoid' === $mode )
			array_push( $recommended, 'cross-origin-opener-policy', 'cross-origin-embedder-policy' );

		$sent = array_map( 'strtolower', headers_list() );

		$missing = 0;
		foreach ( $recommended as $h ) {
			$found = false;
			foreach ( $sent as $line ) {
				if ( 0 === strpos( $line, $h ) ) { $found = true; break; }
			}
			if ( ! $found ) $missing ++;
		}
		return $missing;
	}

	/*--------------------------------------------------------------
	 Activation
	--------------------------------------------------------------*/

	public function on_activate(): void {
		add_option( 'shb_do_activation_redirect', true );
	}
}
/* END class */
