<?php
/** @var SHB_Core $this */
defined( 'ABSPATH' ) || exit;
$option_key = 'shb_settings';
?>
<div class="wrap">
	<h1 class="shb-title"><?php esc_html_e( 'Security Headers', 'shb' ); ?></h1>

	<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'shb' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'shb' );
		do_settings_sections( 'shb' );
		submit_button();
		?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Quick Status', 'shb' ); ?></h2>
	<div id="shb-dashboard-tile" class="status-<?php echo esc_attr( $this->get_status() ); ?>">
		<p class="score">
			<?php
			printf(
				/* translators: %d = number of missing headers */
				esc_html__( '%d improvements available', 'shb' ),
				intval( $this->count_missing_headers() )
			);
			?>
		</p>
		<a class="button button-primary" href="#shb-wizard"><?php esc_html_e( 'Run 60‑second Wizard', 'shb' ); ?></a>
	</div>

	<!-- Onboarding wizard (hidden until JS opens it) -->
	<div id="shb-wizard" class="shb-modal" style="display:none;">
		<div class="shb-modal-content">
			<span class="shb-close">&times;</span>
			<h2><?php esc_html_e( 'Security Headers Wizard', 'shb' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'We scanned your site and found missing headers.', 'shb' ); ?></li>
				<li><?php esc_html_e( 'Choose the level of protection you want.', 'shb' ); ?></li>
				<li><?php esc_html_e( 'Click Apply & Test and you’re done!', 'shb' ); ?></li>
			</ol>
			<button id="shb-apply-suggested" class="button button-primary">
				<?php esc_html_e( 'Apply Recommended Settings', 'shb' ); ?>
			</button>
		</div>
	</div>
</div>
