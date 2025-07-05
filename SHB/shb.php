<?php
/**
 * Plugin Name: SHB – Security Headers Boss (by Arun Ammisetty)
 * Description: One‑click HTTP security headers with beginner‑friendly presets and an onboarding wizard.
 * Plugin URI:  https://github.com/arunammisetty/SHB
 * Version:     1.0.0
 * Author:      Arun Ammisetty
 * Author URI:  https://aa.surge.sh
 * License:     GPL‑2.0‑or‑later
 * Text Domain: shb
 */

defined( 'ABSPATH' ) || exit;

// Autoload core class.
require_once __DIR__ . '/includes/class-shb-core.php';
SHB_Core::instance();
