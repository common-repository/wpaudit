<?php
/**
 * Class Settings Non HTTPS Links
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Non_HTTPS_Links {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_non_https_links',
			esc_html__( 'Non HTTPS Links', 'wp-audit' ),
			function () {
				$https_posts = get_transient( 'wp-audit-https-content' );

				if ( isset( $_POST['run_https_check_nonce'] ) && wp_verify_nonce( $_POST['run_https_check_nonce'], 'run_https_check_action' ) ) {
					$https_posts = false;
				}

				if ( false === $https_posts ) {

					$query = new \WP_Query(
						array(
							'post_type'      => get_post_types(),
							'posts_per_page' => -1,
						)
					);

					$https_posts = array();

					if ( $query->have_posts() ) {
						foreach ( $query->posts as $query_post ) {
							if ( strpos( $query_post->post_content, 'http://' . $_SERVER['SERVER_NAME'] ) !== false ) {
								$query_post->type = 'Post Content';
								$https_posts[]    = $query_post;
							}
							$meta = get_post_meta( $query_post->ID );
							$meta = serialize( $meta );

							if ( strpos( $meta, 'http://' . $_SERVER['SERVER_NAME'] ) !== false ) {
								$query_post->type = 'Post Meta';
								$https_posts[]    = $query_post;
							}
						}
					}

					usort( $https_posts, function( $a, $b ) {
						if ( $a->post_title === $b->post_title ) {
							return 0;
						}

						return $a->post_title < $b->post_title ? -1 : 1;
					} );

					usort( $https_posts, function( $a, $b ) {
						if ( $a->post_type === $b->post_type ) {
							return 0;
						}

						return $a->post_type < $b->post_type ? -1 : 1;
					} );

					set_transient( 'wp-audit-https-content', $https_posts, 12 * HOUR_IN_SECONDS );
				}
				?>

				<p><?php esc_html_e( 'Google Webmaster Tools treats HTTP and HTTPS as separate sites hence it is important that all internal URLs on your site use the HTTPS protocol. If you have moved from HTTP to HTTPS using an automated tool, chances are some internal links may have slipped through the cracks. Enforcing SSL across your entire site will ensure any HTTP links are redirecting however it is far better to fix these links in your database directly rather than rely on the redirect.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'Note: it is always good practise to use Relative URLs (e.g. /images/stuart.png) or Protocol Relative URLs (e.g //www.digitalelite.co.uk/images/stuart.png) for internal linking, rather than using Absolute URLs (e.g http://www.digitalelite.co.uk/images/stuart.png).', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'Click the button below to find all internal links ( tags) that are using the HTTP protocol. This will download a .csv file with all pages that contain an http:// link to an internal page.', 'wp-audit' ); ?></p>

				<?php
				if ( count( $https_posts ) > 0 ) {
					?>
					<table class="form-table">
						<tr>
							<th><?php esc_html_e( 'Post Title', 'wp-audit' ); ?></th>
							<th><?php esc_html_e( 'Post Type', 'wp-audit' ); ?></th>
							<th><?php esc_html_e( 'Location', 'wp-audit' ); ?></th>
						</tr>
						<?php
						foreach ( $https_posts as $https_post ) {
							?>
							<tr>
								<td>
									<a href="<?php echo esc_url( get_edit_post_link( $https_post->ID ) ); ?>">
										<?php
										if ( ! empty( $https_post->post_title ) ) {
											echo esc_html( $https_post->post_title );
										} else {
											?>
											<em>
											<?php esc_html_e( '<Untitled>', 'wp-audit' ); ?>
											</em>
											<?php
										}
										?>
									</a>
								</td>
								<td><?php echo esc_html( $https_post->post_type ); ?></td>
								<td><?php echo esc_html( $https_post->type );?></td>
							</tr>
							<?php
						}
						?>
					</table>
					<?php
				}
				?>
				<?php submit_button( 'Find Non-HTTPS Links' ); ?>
				<?php wp_nonce_field( 'run_https_check_action', 'run_https_check_nonce' ); ?>

				<p><?php esc_html_e( 'Use Better Search Replace', 'wp-audit' ); ?> <a href="https://en-gb.wordpress.org/plugins/better-search-replace/" target="_blank">https://en-gb.wordpress.org/plugins/better-search-replace/</a><?php esc_html_e( ', or Really Simple SSL', 'wp-audit' ); ?> <a href="https://wordpress.org/plugins/really-simple-ssl/" target="_blank">https://wordpress.org/plugins/really-simple-ssl/</a> <?php esc_html_e( 'to fix these issues.', 'wp-audit' ); ?></p>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_non_https_links_settings'
		);
	}
}
