<?php
/**
 * Class Settings Plugins
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Plugins {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_plugins',
			esc_html__( 'Plugins', 'wp-audit' ),
			function () {
				$plugins          = get_plugins();
				$active_plugins   = array();
				$inactive_plugins = array();

				foreach ( $plugins as $key => $plugin ) {
					if ( is_plugin_active( $key ) ) {
						$active_plugins[] = $key;
					}

					if ( is_plugin_inactive( $key ) ) {
						$inactive_plugins[] = $key;
					}
				}
				?>

				<p>The WordPress plugin repository is both a blessing and a curse. Whilst the ability to access 50,000+ plugins provides huge benefits, a single poor plugin choice can slow your whole website to a crawl. As we know, site speed is one of the biggest ranking factors for search engines.</p>

				<p>
					<strong>Active Plugins:</strong> <?php echo count( $active_plugins ); ?>
					<br/>
					<strong>Inctive Plugins:</strong> <?php echo count( $inactive_plugins ); ?>
				</p>

				<p>A simple way to determining a potential plugin issue or conflict is to disable all plugins then enable them one by one, checking site response time each time to see which one causes the slow down.</p>

				<p>Alternatively, install Query Monitor (<a href="https://en-gb.wordpress.org/plugins/query-monitor/" target="_blank">https://en-gb.wordpress.org/plugins/query-monitor/</a>) and check the 'Queries by Component' report to view aggregate database queries and the time taken to execute them, grouped by each of your plugins.</p>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_plugins_settings'
		);
	}
}
