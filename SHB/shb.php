<?php
/**
 * Plugin Name:  SHB – Security Headers Boss (by Arun Ammisetty)
 * Description:  One‑click HTTP security headers with beginner‑friendly presets and a 60‑second onboarding wizard.
 * Plugin URI:   https://github.com/arunammisetty/SHB
 * Version:      1.1.0
 * Author:       Arun Ammisetty
 * Author URI:   https://aa.surge.sh
 * License:      GPL‑2.0‑or‑later
 * Text Domain:  shb
 * Domain Path:  /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'SHB_FILE', __FILE__ );
define( 'SHB_DIR',  plugin_dir_path( __FILE__ ) );
define( 'SHB_URL',  plugin_dir_url( __FILE__ ) );

// Core
require_once SHB_DIR . 'includes/class-shb-core.php';
SHB_Core::instance();
