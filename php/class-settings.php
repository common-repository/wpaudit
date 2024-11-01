<?php
/**
 * Class Settings
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * The main plugin settings page
 */
class Settings {

	private $ajax;
	private $background_processing;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( AJAX $ajax, Background_Processing $background_processing ) {
		$this->ajax                  = $ajax;
		$this->background_processing = $background_processing;
	}


	/**
	 * Run all of the plugin functions.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'plugin_action_links_' . plugin_basename( DIGITAL_ELITE_WP_AUDIT_ROOT ), array( $this, 'add_setings_link' ) );
	}

	/**
	 * Add the settings page.
	 *
	 * @since 1.0.0
	 */
	public function add_settings_page() {
		add_submenu_page(
			'options-general.php',
			esc_html__( 'WP Audit', 'wp-audit' ),
			esc_html__( 'WP Audit', 'wp-audit' ),
			'manage_options',
			'wp-audit',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'WP Audit', 'wp-audit' ); ?></h2>
			<?php settings_errors(); ?>

			<?php $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'non-published-posts'; ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=wp-audit&tab=shortcode-finder" class="nav-tab"><?php esc_html_e( 'Shortcode Finder', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=non-published-posts" class="nav-tab"><?php esc_html_e( 'Non-Published Posts', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=content-revisions" class="nav-tab"><?php esc_html_e( 'Content Revisions', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=short-content" class="nav-tab"><?php esc_html_e( 'Short Content', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=non-https-links" class="nav-tab"><?php esc_html_e( 'Non-HTTPS Links', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=plugins" class="nav-tab"><?php esc_html_e( 'Plugins', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=themes" class="nav-tab"><?php esc_html_e( 'Themes', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=duplicate-slugs" class="nav-tab"><?php esc_html_e( 'Duplicate Slugs', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=database" class="nav-tab"><?php esc_html_e( 'Database', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=general" class="nav-tab"><?php esc_html_e( 'General', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=multilingualpress" class="nav-tab"><?php esc_html_e( 'MultilingualPress', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=unattached-images" class="nav-tab"><?php esc_html_e( 'Unattached Images', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=test-content" class="nav-tab"><?php esc_html_e( 'Test Content', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=security" class="nav-tab"><?php esc_html_e( 'Security', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=url-consistency" class="nav-tab"><?php esc_html_e( 'URL Consistency', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=scripts" class="nav-tab"><?php esc_html_e( 'Scripts', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=comments" class="nav-tab"><?php esc_html_e( 'Comments', 'wp-audit' ); ?></a>
				<a href="?page=wp-audit&tab=wpml" class="nav-tab"><?php esc_html_e( 'WPML', 'wp-audit' ); ?></a>
			</h2>

			<?php
			if ( 'shortcode-finder' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_shortcode_finder_settings' );
			} elseif ( 'non-published-posts' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_non_published_posts_settings' );
			} elseif ( 'content-revisions' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_content_revisions_settings' );
			} elseif ( 'short-content' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_short_content_settings' );
			} elseif ( 'non-https-links' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_non_https_links_settings' );
			} elseif ( 'plugins' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_plugins_settings' );
			} elseif ( 'themes' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_themes_settings' );
			} elseif ( 'duplicate-slugs' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_duplicate_slugs_settings' );
			} elseif ( 'database' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_database_settings' );
			} elseif ( 'general' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_general_settings' );
			} elseif ( 'multilingualpress' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_multilingualpress_settings' );
			} elseif ( 'unattached-images' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_unattached_images_settings' );
			} elseif ( 'test-content' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_test_content_settings' );
			} elseif ( 'security' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_security_settings' );
			} elseif ( 'url-consistency' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_url_consistency_settings' );
			} elseif ( 'scripts' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_scripts_settings' );
			} elseif ( 'comments' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_comments_settings' );
			}  elseif ( 'wpml' === $active_tab ) {
				do_settings_sections( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_wpml_settings' );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Add 'Settings' action on installed plugin list.
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @since 1.0.0
	 */
	function add_setings_link( $links ) {
		array_unshift( $links, '<a href="options-general.php?page=' . esc_attr( 'wp_audit' ) . '">' . esc_html__( 'Settings', 'wp-audit' ) . '</a>' );
		return $links;
	}
}
