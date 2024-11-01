<?php
/**
 * WP Audit by Digital Elite
 *
 * @link              https://github.com/digitalelite/wp-audit
 * @package           digital-elite\wp-audit
 *
 * Plugin Name:       WordPress Audit
 * Plugin URI:        https://github.com/digitalelite/wp-audit
 * Description:       The WordPress Audit plugin helps website managers improve the security, speed and search engine visibility of their WordPress websites.
 * Version:           1.0.4
 * Author:            Digital Elite <stuart@digitalelite.co.uk>
 * Author URI:        https://www.digitalelite.co.uk/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wp-audit
 * Domain Path:       /languages
 */

/**
 * Copyright (C) 2018  Digital Elite  stuart@digitalelite.co.uk
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace digital_elite\wp_audit;

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Constants.
define( 'DIGITAL_ELITE_WP_AUDIT_ROOT', __FILE__ );
define( 'DIGITAL_ELITE_WP_AUDIT_PREFIX', 'digital_elite_wp_audit' );

/**
 * The main loader for this plugin
 */
class Main {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Run all of the plugin functions.
	 *
	 * @since 1.0.0
	 */
	public function run() {

		/**
		 * Load Text Domain
		 */
		load_plugin_textdomain( 'wp-audit', false, DIGITAL_ELITE_WP_AUDIT_ROOT . '\languages' );

		/**
		 * Actions and Hooks
		 */

		// Load Assets
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) ); // Load Admin Assets
	}

	/**
	 * Enqueue Admin Styles.
	 */
	public function admin_assets() {

		$styles  = '/assets/css/plugin-admin.css';
		$scripts = '/assets/js/plugin-admin.js';

		// Enqueue Styles.
		wp_enqueue_style(
			'wp-audit-admin-css',
			plugins_url( $styles, __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . $styles )
		);

		// Enqueue Scripts.
		wp_enqueue_script(
			'wp-audit-plugin-admin-js',
			plugins_url( $scripts, __FILE__ ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( __FILE__ ) . $scripts ),
			true
		);

		$vars = array(
			'ajaxurl'   => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajaxnonce' => wp_create_nonce( 'ajax_nonce' ),
		);
		wp_localize_script( 'wp-audit-plugin-admin-js', 'ajax_object', $vars );
	}
}

// Load Classes
require_once 'vendor/wp-background-processing/wp-background-processing.php';
require_once 'php/class-ajax.php';
require_once 'php/class-background-processing.php';
require_once 'php/class-settings-comments.php';
require_once 'php/class-settings-content-revisions.php';
require_once 'php/class-settings-database.php';
require_once 'php/class-settings-duplicate-slugs.php';
require_once 'php/class-settings-general.php';
require_once 'php/class-settings-multilingualpress.php';
require_once 'php/class-settings-non-https-links.php';
require_once 'php/class-settings-non-published-posts.php';
require_once 'php/class-settings-plugins.php';
require_once 'php/class-settings-scripts.php';
require_once 'php/class-settings-security.php';
require_once 'php/class-settings-short-content.php';
require_once 'php/class-settings-shortcode-finder.php';
require_once 'php/class-settings-test-content.php';
require_once 'php/class-settings-themes.php';
require_once 'php/class-settings-unattached-images.php';
require_once 'php/class-settings-url-consistency.php';
require_once 'php/class-settings-wpml.php';
require_once 'php/class-settings.php';

$main                         = new Main();
$ajax                         = new AJAX();
$background_processing        = new Background_Processing();
$settings_comments            = new Settings_Comments( $ajax, $background_processing );
$settings_content_revisions   = new Settings_Content_Revisions( $ajax, $background_processing );
$settings_database            = new Settings_Database( $ajax, $background_processing );
$settings_duplicate_slugs     = new Settings_Duplicate_Slugs( $ajax, $background_processing );
$settings_general             = new Settings_General( $ajax, $background_processing );
$settings_multilingualpress   = new Settings_MultilingualPress( $ajax, $background_processing );
$settings_non_https_links     = new Settings_Non_HTTPS_Links( $ajax, $background_processing );
$settings_non_published_posts = new Settings_Non_Published_Posts( $ajax, $background_processing );
$settings_plugins             = new Settings_Plugins( $ajax, $background_processing );
$settings_scripts             = new Settings_Scripts( $ajax, $background_processing );
$settings_security            = new Settings_Security( $ajax, $background_processing );
$settings_short_content       = new Settings_Short_Content( $ajax, $background_processing );
$settings_shortcode_finder    = new Settings_Shortcode_Finder( $ajax, $background_processing );
$settings_test_content        = new Settings_Test_Content( $ajax, $background_processing );
$settings_themes              = new Settings_Themes( $ajax, $background_processing );
$settings_unattached_images   = new Settings_Unattached_Images( $ajax, $background_processing );
$settings_url_consistency     = new Settings_URL_Consistency( $ajax, $background_processing );
$settings_wpml                = new Settings_WPML( $ajax, $background_processing );
$settings                     = new Settings( $ajax, $background_processing );

$ajax->run();
$settings->run();
$settings_comments->run();
$settings_content_revisions->run();
$settings_database->run();
$settings_duplicate_slugs->run();
$settings_general->run();
$settings_multilingualpress->run();
$settings_non_https_links->run();
$settings_non_published_posts->run();
$settings_plugins->run();
$settings_scripts->run();
$settings_security->run();
$settings_short_content->run();
$settings_shortcode_finder->run();
$settings_test_content->run();
$settings_themes->run();
$settings_unattached_images->run();
$settings_url_consistency->run();
$settings_wpml->run();
$main->run();
