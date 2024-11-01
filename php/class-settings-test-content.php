<?php
/**
 * Class Settings Test Content
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Test_Content {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_test_content',
			esc_html__( 'Test Content', 'wp-audit' ),
			function () {
				$test_posts = get_transient( 'wp-audit-test-content' );

				if ( isset( $_POST['run_test_content_check_nonce'] ) && wp_verify_nonce( $_POST['run_test_content_check_nonce'], 'run_test_content_check_action' ) ) {
					$test_posts = false;
				}

				if ( false === $test_posts ) {

					$query = new \WP_Query(
						array(
							'post_type'      => get_post_types(),
							'posts_per_page' => -1,
						)
					);

					$test_posts = array();

					if ( $query->have_posts() ) {
						foreach ( $query->posts as $query_post ) {
							if ( isset( $_POST['title'] ) ) {
								if ( isset( $_POST['test'] ) && strpos( strtolower( $query_post->post_title ), 'test' ) !== false ) {
									$query_post->type = 'Post Title';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['temp'] ) && strpos( strtolower( $query_post->post_title ), 'temp' ) !== false ) {
									$query_post->type = 'Post Title';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['draft'] ) && strpos( strtolower( $query_post->post_title ), 'draft' ) !== false ) {
									$query_post->type = 'Post Title';
									$test_posts[]    = $query_post;
								}
							}
							if ( isset( $_POST['url'] ) ) {
								if ( isset( $_POST['test'] ) && strpos( strtolower( $query_post->post_name ), 'test' ) !== false ) {
									$query_post->type = 'URL';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['temp'] ) && strpos( strtolower( $query_post->post_name ), 'temp' ) !== false ) {
									$query_post->type = 'URL';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['draft'] ) && strpos( strtolower( $query_post->post_name ), 'draft' ) !== false ) {
									$query_post->type = 'URL';
									$test_posts[]    = $query_post;
								}
							}
							if ( isset( $_POST['content'] ) ) {
								if ( isset( $_POST['test'] ) && strpos( strtolower( $query_post->post_content ), 'test' ) !== false ) {
									$query_post->type = 'Post Content';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['temp'] ) && strpos( strtolower( $query_post->post_content ), 'temp' ) !== false ) {
									$query_post->type = 'Post Content';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['draft'] ) && strpos( strtolower( $query_post->post_content ), 'draft' ) !== false ) {
									$query_post->type = 'Post Content';
									$test_posts[]    = $query_post;
								}
							}
							if ( isset( $_POST['meta'] ) ) {
								$meta = get_post_meta( $query_post->ID );
								$meta = serialize( $meta );

								if ( isset( $_POST['test'] ) && strpos( strtolower( $meta ), 'test' ) !== false ) {
									$query_post->type = 'Post Meta';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['temp'] ) && strpos( strtolower( $meta ), 'temp' ) !== false ) {
									$query_post->type = 'Post Meta';
									$test_posts[]    = $query_post;
								}
								if ( isset( $_POST['draft'] ) && strpos( strtolower( $meta ), 'draft' ) !== false ) {
									$query_post->type = 'Post Meta';
									$test_posts[]    = $query_post;
								}
							}
						}
					}

					usort( $test_posts, function( $a, $b ) {
						if ( $a->post_title === $b->post_title ) {
							return 0;
						}

						return $a->post_title < $b->post_title ? -1 : 1;
					} );

					usort( $test_posts, function( $a, $b ) {
						if ( $a->post_type === $b->post_type ) {
							return 0;
						}

						return $a->post_type < $b->post_type ? -1 : 1;
					} );

					set_transient( 'wp-audit-test-content', $test_posts, 12 * HOUR_IN_SECONDS );
				}
				?>
				<form method="POST">
					<p><?php esc_html_e( "Use the button below to find any posts, pages or custom post types that contains the words 'test', 'temp' or 'draft'. This will check the title, the url, the meta and the page content.", 'wp-audit' ); ?></p>
					
					<h2><?php esc_html_e( 'Search For:', 'wp-audit' ); ?></h2>
					<ul>
						<li>
							<label>
								<input type="checkbox" value="true" name="test" <?php echo isset( $_POST['run_test_content_check_nonce'] ) ? ( isset( $_POST['test'] ) ? 'checked' : '' ) : 'checked'; ?>/> Test
							</label>
						</li>
						<li>
							<label>
								<input type="checkbox" value="true" name="temp" <?php echo isset( $_POST['run_test_content_check_nonce'] ) ? ( isset( $_POST['temp'] ) ? 'checked' : '' ) : 'checked'; ?>/> Temp
							</label>
						</li>
						<li>
							<label>
								<input type="checkbox" value="true" name="draft" <?php echo isset( $_POST['run_test_content_check_nonce'] ) ? ( isset( $_POST['draft'] ) ? 'checked' : '' ) : 'checked'; ?>/> Draft
							</label>
						</li>
					</ul>

					<h2><?php esc_html_e( 'Search In:', 'wp-audit' ); ?></h2>
					<ul>
						<li>
							<label>
								<input type="checkbox" value="true" name="title" <?php echo isset( $_POST['run_test_content_check_nonce'] ) ? ( isset( $_POST['title'] ) ? 'checked' : '' ) : ''; ?>/> Title
							</label>
						</li>
						<li>
							<label>
								<input type="checkbox" value="true" name="url" <?php echo isset( $_POST['run_test_content_check_nonce'] ) ? ( isset( $_POST['url'] ) ? 'checked' : '' ) : 'checked'; ?>/> URL
							</label>
						</li>
						<li>
							<label>
								<input type="checkbox" value="true" name="content" <?php echo isset( $_POST['run_test_content_check_nonce'] ) ? ( isset( $_POST['content'] ) ? 'checked' : '' ) : 'checked'; ?>/> Content
							</label>
						</li>
						<li>
							<label>
								<input type="checkbox" value="true" name="meta" <?php echo isset( $_POST['run_test_content_check_nonce'] ) ? ( isset( $_POST['meta'] ) ? 'checked' : '' ) : ''; ?>/> Meta
							</label>
						</li>
					</ul>

					<?php submit_button( 'Find Test Content' ); ?>
					<?php wp_nonce_field( 'run_test_content_check_action', 'run_test_content_check_nonce' ); ?>

					<?php
					if ( count( $test_posts ) > 0 ) {
						?>
						<table class="form-table">
							<tr>
								<th><?php esc_html_e( 'Post Title', 'wp-audit' ); ?></th>
								<th><?php esc_html_e( 'Post Type', 'wp-audit' ); ?></th>
								<th><?php esc_html_e( 'Location', 'wp-audit' ); ?></th>
							</tr>
							<?php
							foreach ( $test_posts as $https_post ) {
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
					<?php submit_button( 'Find Test Content' ); ?>
					<?php wp_nonce_field( 'run_test_content_check_action', 'run_test_content_check_nonce' ); ?>

					<p><?php esc_html_e( 'Use Better Search Replace', 'wp-audit' ); ?> <a href="https://en-gb.wordpress.org/plugins/better-search-replace/" target="_blank">https://en-gb.wordpress.org/plugins/better-search-replace/</a><?php esc_html_e( ', or Really Simple SSL', 'wp-audit' ); ?> <a href="https://wordpress.org/plugins/really-simple-ssl/" target="_blank">https://wordpress.org/plugins/really-simple-ssl/</a> <?php esc_html_e( 'to fix these issues.', 'wp-audit' ); ?></p>
				</form>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_test_content_settings'
		);
	}
}
