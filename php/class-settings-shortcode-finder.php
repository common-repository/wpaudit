<?php
/**
 * Class Settings Shortcode Finder
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Shortcode_Finder {

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
		add_action( 'init', array( $this, 'download' ) );
		add_action( 'init', array( $this, 'generate' ), 9999 );
	}

	public function init_settings_page() {
		add_settings_section(
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_shortcode_finder',
			esc_html__( 'Find Shortcodes', 'wp-audit' ),
			function () {
				?>
				<div class="shortcode-list">
						<?php
						if ( isset( $_POST['shortcode_list__generate_nonce'] ) && wp_verify_nonce( $_POST[ 'shortcode_list__generate_nonce' ], 'shortcode_list__generate_action' ) && is_admin() ) {
							?>
							<p><?php esc_html_e( 'Indexing site in the background (you can navigate away from this page). This page will refresh automatically.', 'wp-audit' ); ?></p>
							<img src="/wp-includes/images/spinner-2x.gif"/>
							<?php
						} else {
							echo $this->ajax->build_table();
						}
						?>
					</div>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_shortcode_finder_settings'
		);
	}

	function generate() {

		if ( isset( $_POST['shortcode_list__generate_nonce'] ) && wp_verify_nonce( $_POST['shortcode_list__generate_nonce'], 'shortcode_list__generate_action' ) && is_admin() ) {

			update_option( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_shortcode_list', array() );

			$post_ids   = array();
			$query      = array(
				'posts_per_page' => -1,
				'post_type'      => get_post_types(),
				'post_status'    => 'publish',
			);
			$all_posts = new \WP_Query( $query );

			if ( $all_posts->have_posts() ) {
				$post_ids = wp_list_pluck( $all_posts->posts, 'ID' );
			}

			foreach ( $post_ids as $post_id ) {
				$this->background_processing->push_to_queue( $post_id );
			}

			$this->background_processing->save()->dispatch();
		}
	}

	function download() {

		if ( isset( $_POST['shortcode_list_download_nonce'] ) && wp_verify_nonce( $_POST['shortcode_list_download_nonce'], 'shortcode_list_download' ) && is_admin() ) {

			$shortcodes      = array();
			$shortcodes_keys = array();
			$shortcode_list  = get_option( DIGITAL_ELITE_WP_AUDIT_PREFIX . '_shortcode_list', array() );
			if ( is_array( $shortcode_list ) && ! empty( $shortcode_list ) ) {
				ksort( $shortcode_list );

				foreach ( $shortcode_list as $key => $shortcode ) {
					uasort( $shortcode, function( $a, $b ) {
						if ( isset( $a['title'] ) && isset( $b['title'] ) ) {
							return $a['title'] <=> $b['title'];
						}
						return false;
					} );

					if ( isset( $shortcode['keys'] ) ) {
						foreach ( $shortcode['keys'] as $key ) {
							$shortcodes_keys[] = $key;
						}
					}
				}

				header( 'Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				header( 'Content-Disposition: attachment; filename=shortcode-list.xlsx' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				ob_start();

				?>

				<table>
					<tr>
						<th><?php esc_html_e( 'name', 'wp-audit' ); ?></th>
						<th><?php esc_html_e( 'post id', 'wp-audit' ); ?></th>
						<th><?php esc_html_e( 'link', 'wp-audit' ); ?></th>
						<th><?php esc_html_e( 'post_type', 'wp-audit' ); ?></th>
						<th><?php esc_html_e( 'location', 'wp-audit' ); ?></th>
						<th><?php esc_html_e( 'shortcode', 'wp-audit' ); ?></th>
						<?php
						foreach ( $shortcodes_keys as $shortcode_key ) {
							?>
							<th><?php echo esc_html( $shortcode_key ); ?></th>
							<?php
						}
						?>
					</tr>

				<?php

				foreach ( $shortcode_list as $key => $shortcode ) {
					uasort( $shortcode, function( $a, $b ) {
						if ( isset( $a['title'] ) && isset( $b['title'] ) ) {
							return $a['title'] <=> $b['title'];
						}
						return false;
					} );

					foreach ( $shortcode as $k => $s ) {

						if ( 'keys' !== $k ) {

							$post = get_post( $s['post_id'] );
							?>

							<tr>
								<td><?php echo esc_html( $post->post_title ); ?></td>
								<td><?php echo esc_html( $s['post_id'] ); ?></td>
								<td><?php echo esc_url( get_edit_post_link( $s['post_id'] ) ); ?></td>
								<td><?php echo esc_html( $post->post_type ); ?></td>
								<td><?php echo esc_html( $s['location'] ); ?></td>
								<td><?php echo esc_html( $s['shortcode'] ); ?></td>
								<?php
								foreach ( $shortcodes_keys as $shortcode_key ) {
									if ( isset( $s['attributes'][ $shortcode_key ] ) ) {
										?>
										<td><?php echo esc_html( $s['attributes'][ $shortcode_key ] ); ?></td>
										<?php
									} else {
										?>
										<td></td>
										<?php
									}
								}
								?>
							</tr>
							<?php
						}
					}
				}

				?>
				</table>
				<?php
				$html .= ob_get_contents();
				ob_end_clean();

				echo $html;

				exit;
			}
		}
	}
}
