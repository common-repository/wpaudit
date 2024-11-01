<?php
/**
 * Class Settings URL Consistency
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_URL_Consistency {

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
	}

	public function init_settings_page() {
		add_settings_section(
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_url_consistency',
			esc_html__( 'URL Consistency', 'wp-audit' ),
			function () {
				?>
					<div class="url-consistency">
						<p><?php esc_html_e( 'Your sites primary domain is:', 'wp-audit' ); ?> <?php echo esc_html( get_site_url() ); ?></p>

						<p><?php esc_html_e( "If you have previously moved your site to a new domain, it's possible that some URLs were not updated. Enter the URL of your old site below and click 'scan site' to find any occurrences of your old URL.", 'wp-audit' ); ?></p>

						<form method="POST" class="inline-controls">
							<label for="url"><strong><?php esc_html_e( 'URL:', 'wp-audit' ); ?></strong>
								<input type="text" value="" name="url" placeholder="live-themer-test.pantheonsite.io/" class="regular-text" />
							</label>
							<?php submit_button( 'Scan For Old URL' ); ?>
							<?php wp_nonce_field( 'run_url_consistency_check_action', 'run_url_consistency_check_nonce' ); ?>
						</form>

						<p><?php esc_html_e( 'Additionally you should ensure all links to your domain in your database are set to HTTPS. Use the search below to search for any instances of a HTTP link on your site.', 'wp-audit' ); ?></p>

						<form method="POST" class="inline-controls">
							<label for="url"><strong><?php esc_html_e( 'URL:', 'wp-audit' ); ?></strong>
								<input type="text" value="<?php echo str_replace( 'https:', 'https', get_site_url() );?>" name="url" placeholder="<?php echo str_replace( 'https:', 'https', get_site_url() );?>" class="regular-text"/>
							</label>
							<?php submit_button( 'Scan For HTTP' ); ?>
							<?php wp_nonce_field( 'run_url_consistency_check_action', 'run_url_consistency_check_nonce' ); ?>
						</form>

						<p><?php esc_html_e( 'We recommend using Better Search Replace (', 'wp-audit' ); ?><a href="https://en-gb.wordpress.org/plugins/better-search-replace/" target="_blank">https://en-gb.wordpress.org/plugins/better-search-replace/</a><?php esc_html_e( ') should you need to update links in your database.', 'wp-audit' ); ?></p>
					</div>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_url_consistency_settings'
		);
	}

	function download() {
		if ( isset( $_POST['url'] ) && ! empty( $_POST['url'] ) && isset( $_POST['run_url_consistency_check_nonce'] ) && wp_verify_nonce( $_POST['run_url_consistency_check_nonce'], 'run_url_consistency_check_action' ) ) {

			header( "Content-type: text/csv" );
			header( "Content-Disposition: attachment; filename=url-consistency.csv" );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );

			$string  = '';
			$string .= '"Post ID",';
			$string .= '"Post Title",';
			$string .= '"Post Type",';
			$string .= '"Location",';
			$string .= '"Edit Link",';
			$string .= "\n";

			echo $string;

			$query = new \WP_Query(
				array(
					'post_type'      => get_post_types(),
					'posts_per_page' => -1,
				)
			);

			$found_posts = array();

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $query_post ) {
					if ( strpos( strtolower( $query_post->post_content ), sanitize_text_field( $_POST['url'] ) ) !== false ) {
						$query_post->type = 'Post Content';
						$found_posts[]    = $query_post;
					}
					$meta = get_post_meta( $query_post->ID );
					$meta = serialize( $meta );

					if ( strpos( strtolower( $meta ), sanitize_text_field( $_POST['url'] ) ) !== false ) {
						$query_post->type = 'Post Meta';
						$found_posts[]    = $query_post;
					}
				}
			}

			usort( $found_posts, function( $a, $b ) {
				if ( $a->post_title === $b->post_title ) {
					return 0;
				}

				return $a->post_title < $b->post_title ? -1 : 1;
			} );

			usort( $found_posts, function( $a, $b ) {
				if ( $a->post_type === $b->post_type ) {
					return 0;
				}

				return $a->post_type < $b->post_type ? -1 : 1;
			} );
			
			if ( count( $found_posts ) > 0 ) {
				foreach ( $found_posts as $post ) {
					$string  = '';
					$string .= '"' . $post->ID . '",';
					$string .= '"' . $post->post_title . '",';
					$string .= '"' . $post->post_type . '",';
					$string .= '"' . $post->type . '",';
					$string .= '"' . get_edit_post_link( $post->ID ) . '",';
					$string .= "\n";
					echo $string;
				}
			}
			
			exit;
		}
	}
}
