<?php
/**
 * Class Settings Database
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Database {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_database',
			esc_html__( 'Database', 'wp-audit' ),
			function () {
				global $wpdb;
				$query_size = 'SELECT table_schema,
					ROUND( SUM( data_length + index_length ) / 1024 / 1024, 2 ) "size" 
					FROM information_schema.tables 
					WHERE table_schema = "' . DB_NAME . '"';
					$size = $wpdb->get_results( $query_size );

					$query_table_names = 'SELECT table_name 
					FROM information_schema.tables 
					WHERE table_schema = "' . DB_NAME . '"';
					$table_names = $wpdb->get_results( $query_table_names );
					
					?>
					<p>Identify issues where plugins are aggressively logging data. If you use the likes of wp-stream (<a href="https://en-gb.wordpress.org/plugins/stream/" target="_blank">https://en-gb.wordpress.org/plugins/stream/</a>) or redirection plugin (<a href="https://en-gb.wordpress.org/plugins/stream/" target="_blank">http://wordpress.org/plugins/redirection/</a>) you may notice this. In addition to logging many plugins create custom database tables and fail to remove them on uninstall.</p>
					<p>Current database size: <?php echo round( $size[0]->size * (1/1024), 2 );?> GB.</p>

					<table style="width: 100%">
					<tr>
						<th>Table Name</th>
						<th>Records</th>
						<th>Data Size (MB)</th>
						<th>Index Size (MB)</th>
					</tr>
					<?php
					foreach ( $table_names as $table_name ) {

						$query_records = 'SELECT COUNT(*) as "count"
						FROM ' . $table_name->table_name;
						$records = $wpdb->get_results( $query_records );

						$query_size = 'SELECT table_schema,
						ROUND( SUM( data_length + index_length ) / 1024 / 1024, 2 ) "size",
						ROUND( index_length / ( 1024 *1024 ) , 2 ) AS "index"
						FROM information_schema.TABLES WHERE table_schema = "' . DB_NAME . '"
						AND table_name = "' . $table_name->table_name . '"';
						$size = $wpdb->get_results( $query_size );

						?>
						<tr>
						<td><?php echo esc_html( $table_name->table_name );?></td>
						<td><?php echo esc_html( $records[0]->count ); ?></td>
						<td><?php echo round( $size[0]->size, 2 );?></td>
						<td><?php echo round( $size[0]->index, 2 );?></td>
						</tr>
						<?php
					}
					?>
					</table>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_database_settings'
		);
	}
}
