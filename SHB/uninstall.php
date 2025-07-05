<?php
/**
 * Cleanup on plugin deletion.
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'shb_settings' );
