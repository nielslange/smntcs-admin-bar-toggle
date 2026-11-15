<?php
/**
 * Plugin Name:         SMNTCS Admin Bar Toggle
 * Plugin URI:          https://github.com/nielslange/smntcs-admin-bar-toggle
 * Description:         Show or hide the admin bar on the frontend with a keyboard shortcut (Cmd+B / Ctrl+B by default), configurable per user, without leaving a gap.
 * Version:             1.0
 * Requires at least:   5.7
 * Requires PHP:        7.4
 * Author:              Niels Lange
 * Author URI:          https://nielslange.de
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         smntcs-admin-bar-toggle
 *
 * @package SMNTCS_Admin_Bar_Toggle
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define plugin file.
define( 'SMNTCS_ADMIN_BAR_TOGGLE_PLUGIN_FILE', __FILE__ );

// Include the main class file.
require_once __DIR__ . '/includes/class-smntcs-admin-bar-toggle.php';

new SMNTCS_Admin_Bar_Toggle();

register_uninstall_hook( SMNTCS_ADMIN_BAR_TOGGLE_PLUGIN_FILE, [ 'SMNTCS_Admin_Bar_Toggle', 'uninstall' ] );
