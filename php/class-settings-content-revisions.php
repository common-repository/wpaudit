<?php
/**
 * Class Settings Content REvisions
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Content_Revisions {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_content_revisions',
			esc_html__( 'Content Revisions', 'wp-audit' ),
			function () {
				?>
				<p><?php esc_html_e( 'By default WordPress includes a revision control system (', 'wp-audit' ); ?><a href="https://codex.wordpress.org/Revisions" target="_blank">https://codex.wordpress.org/Revisions</a><?php esc_html_e( '). Whilst this can be useful for writers and publishers, every revision is stored as a separate database entry which can bloat your database and effect performance. Some hosting provides disable revisions entirely for exactly this reason.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'WordPress revision control is managed via the WP_POST_REVISIONS parameter, this is currently:', 'wp-audit' ); ?></p>

				<p><code><?php esc_html_e( 'define( \'WP_POST_REVISIONS\', true );', 'wp-audit' ); ?></code></p>

				<p><?php esc_html_e( 'To disable revisions you can add the following code to your wp-config.php file (note, this still saves one autosave per post). The code below needs to be inserted above ‘ABSPATH’ otherwise it won’t work.', 'wp-audit' ); ?></p>

				<p><code><?php esc_html_e( 'define( \'WP_POST_REVISIONS\', false );', 'wp-audit' ); ?></code></p>

				<p><?php esc_html_e( 'Alternatively you can limit post revisions as follows:', 'wp-audit' ); ?></p>

				<p><code><?php esc_html_e( 'define( \'WP_POST_REVISIONS\', 3 );', 'wp-audit' ); ?></code></p>

				<p><?php esc_html_e( 'To remove existing revisions we recommend WP Optimise', 'wp-audit' ); ?> <a href="https://en-gb.wordpress.org/plugins/wp-optimize/" target="_blank">https://en-gb.wordpress.org/plugins/wp-optimize/</a><?php esc_html_e( ', Perfmatters', 'wp-audit' ); ?> <a href="https://perfmatters.io/features/" target="_blank">https://perfmatters.io/features/</a> <?php esc_html_e( 'or WP Sweep', 'wp-audit' ); ?> <a href="https://en-gb.wordpress.org/plugins/wp-sweep/" target="_blank">https://en-gb.wordpress.org/plugins/wp-sweep/</a>.</p>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_content_revisions_settings'
		);
	}
}
