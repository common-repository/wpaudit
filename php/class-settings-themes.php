<?php
/**
 * Class Settings Themes
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Themes {

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
		add_action( 'admin_init', array( $this, 'init_settings_page' ) );
	}

	public function init_settings_page() {
		add_settings_section(
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_themes',
			esc_html__( 'Themes', 'wp-audit' ),
			function () {
				$themes = wp_get_themes();
				?>
				<p><?php esc_html_e( 'WordPress ships with a number of default Themes, such as Twenty Eleven, Twenty Twelve, Twenty Thirteen. You will likely only use one of these, and may not use any if you are using your own custom theme.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'We suggest deleting any unrequired themes.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'Currently Installed themes are listed here:', 'wp-audit' ); ?></p>
				<ul>
				<?php 
				foreach ( $themes as $theme ) {
					?>
					<li><?php echo esc_html( $theme->Name ); ?></li>
					<?php
				}
				?>
				</ul>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_themes_settings'
		);
	}
}
