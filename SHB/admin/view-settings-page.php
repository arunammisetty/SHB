<?php
/** @var SHB_Core $this */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1 class="shb-title"><?php esc_html_e( 'Security Headers', 'shb' ); ?></h1>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'shb' );
		do_settings_sections( 'shb' );
		submit_button();
		?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Quick Status', 'shb' ); ?></h2>
	<?php
	$status_class = $this->get_status();
	$missing      = intval( ( new ReflectionMethod( $this, 'count_missing_headers' ) )->invoke( $this ) );
	?>
	<div id="shb-dashboard-tile" class="status-<?php echo esc_attr( $status_class ); ?>">
		<p class="score"><?php printf( esc_html__( '%d improvements available', 'shb' ), $missing ); ?></p>
		<a class="button button-primary" href="#shb-wizard"><?php esc_html_e( 'Run 60‑second Wizard', 'shb' ); ?></a>
	</div>

	<!-- Wizard modal -->
	<div id="shb-wizard" class="shb-modal" style="display:none;">
		<div class="shb-modal-content">
			<span class="shb-close" aria-label="<?php esc_attr_e( 'Close', 'shb' ); ?>">&times;</span>
			<h2><?php esc_html_e( 'Security Headers Wizard', 'shb' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'We scanned your site and found missing headers.', 'shb' ); ?></li>
				<li><?php esc_html_e( 'Click the button below to apply the recommended preset.', 'shb' ); ?></li>
				<li><?php esc_html_e( 'Reload your homepage to verify everything works.', 'shb' ); ?></li>
			</ol>
			<button id="shb-apply-suggested" class="button button-primary button-hero">
				<?php esc_html_e( 'Apply Recommended Settings', 'shb' ); ?>
			</button>
		</div>
	</div>
</div>
