<?php
/**
 * Class Settings Scripts
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial scripts page
 */
class Settings_Scripts {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_scripts',
			esc_html__( 'Scripts', 'wp-audit' ),
			function () {
				global $wp_scripts;
				?>
					<p><?php esc_html_e( 'External scripts can significantly slow down your website, negatively affecting both user experience and your search engine results.', 'wp-audit' ); ?></p>

					<p><?php esc_html_e( 'Without getting into too much technical detail, the biggest thing that slows down websites is almost always HTTP requests that aren’t needed.', 'wp-audit' ); ?></p>

					<p><?php esc_html_e( 'Review the scripts below to ensure they are required for the operation of your site. For more detailed analysis, we recommend ', 'wp-audit' ); ?><a href="https://developers.google.com/speed/pagespeed/insights/"><?php esc_html_e( 'Google PageSpeed Insights', 'wp-audit' ); ?></a><?php esc_html_e( '.', 'wp-audit' ); ?></p>

					<ul>
					<?php
					foreach ( $wp_scripts->queue as $script ) {
						?>
						<li><?php echo $wp_scripts->registered[ $script ]->src; ?></li>
						<?php
					}
					?>
					</ul>
					<p><?php esc_html_e( 'Other excellent plugins include ', 'wp-audit' ); ?><a href="https://perfmatters.io"><?php esc_html_e( 'Perfmatters', 'wp-audit' ); ?></a><?php esc_html_e( ' and ', 'wp-audit' ); ?><a href="https://wordpress.org/plugins/autoptimize/"><?php esc_html_e( 'Autoptimzie ', 'wp-audit' ); ?></a></p>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_scripts_settings'
		);
	}
}
