<?php
/**
 * Class Settings Security
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Security {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_security',
			esc_html__( 'Security', 'wp-audit' ),
			function () {
				$themes = wp_get_themes();
				?>
				<p><?php esc_html_e( 'A number of different security plugins exist to improve the security of your site. Unfortunately a number of these add considerable overhead, are not simple to use, and can slow down your site. The trick is balancing security with ease of use. Requiring 2Factor authentication may not be viable for a large enterprise site with many authors.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'One of the simplest things you can do to increase security is to change the default /wp-admin/ login page in order to prevent bruteforce login attempts.', 'wp-audit' ); ?></p>

			<p><?php esc_html_e( 'Your current login URL is:', 'wp-audit' ); ?> <?php echo esc_html( get_admin_url() /*wp_login_url()*/ ); ?></p>

				<p><?php esc_html_e( 'We recommend the following 2 plugins:', 'wp-audit' ); ?></p>

				<ul>
					<li><a href="https://wordpress.org/plugins/wps-limit-login/" target="_blank">https://wordpress.org/plugins/wps-limit-login/</a> <?php esc_html_e( '- limits login attempts to wp-admin and locks out accounts', 'wp-audit' ); ?></li>
					<li><a href="https://wordpress.org/plugins/wps-hide-login/" target="_blank">https://wordpress.org/plugins/wps-hide-login/</a> <?php esc_html_e( '- hides/changes the default wp-admin login page', 'wp-audit' ); ?></li>
				</ul>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_security_settings'
		);
	}
}
