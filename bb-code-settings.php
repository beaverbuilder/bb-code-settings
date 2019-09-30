<?php
/**
 * Plugin Name: Beaver Builder Code Settings
 * Plugin URI: https://www.wpbeaverbuilder.com
 * Description: Adds CSS and JS code settings to rows, columns, and modules in Beaver Builder.
 * Version: 0.1
 * Author: Justin Busa
 * Author URI: https://www.wpbeaverbuilder.com
 * Copyright: (c) 2019 Beaver Builder
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bb-code-settings
 */

define( 'BB_CODE_SETTINGS_VERSION', '0.1' );
define( 'BB_CODE_SETTINGS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BB_CODE_SETTINGS_URL', plugins_url( '/', __FILE__ ) );

require_once 'classes/class-bb-code-settings.php';

BB_Code_Settings::init();
