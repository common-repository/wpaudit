<?php
/**
 * Class Settings Short Content
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Short_Content {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_short_content',
			esc_html__( 'Short Content', 'wp-audit' ),
			function () {
				$small_posts = get_transient( 'wp-audit-short-content' );

				if ( isset( $_POST['run_short_check_nonce'] ) && wp_verify_nonce( $_POST['run_short_check_nonce'], 'run_short_check_action' ) ) {
					$small_posts = false;
				}

				if ( false === $small_posts ) {

					$query = new \WP_Query(
						array(
							'post_type'      => get_post_types(),
							'posts_per_page' => -1,
						)
					);

					$small_posts = array();

					if ( $query->have_posts() ) {
						foreach ( $query->posts as $query_post ) {
							$query_post->length = strlen( $query_post->post_content );
							if ( $query_post->length < 251 ) {
								$small_posts[] = $query_post;
							}
						}
					}

					usort( $small_posts, function( $a, $b ) {
						if ( $a->post_title === $b->post_title ) {
							return 0;
						}

						return $a->post_title < $b->post_title ? -1 : 1;
					} );

					usort( $small_posts, function( $a, $b ) {
						if ( $a->post_type === $b->post_type ) {
							return 0;
						}

						return $a->post_type < $b->post_type ? -1 : 1;
					} );

					set_transient( 'wp-audit-short-content', $small_posts, 12 * HOUR_IN_SECONDS );
				}

				?>
				<p><?php esc_html_e( 'Whilst there may be good reasons for short content to exist on your site, it may also be an indication of potential issues. Whilst we know Google doesn\'t use content length as a ranking factor for SEO per se, we do know that longer content tends to attract more backlinks, offers more value to the reader, leads to more conversions and is likely to rank for additional related keywords and phrases. Short content can often get published by mistake, or be an indication of issues elsewhere on your website.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'To understand if short content is an issue, use the button below to export all posts with a content length that is less than 250 characters (this is approximately one paragraph). The CSV file will include post information such as: date, name, post ID, slug, URL, word count, character count and more.', 'wp-audit' ); ?></p>

				<?php

				if ( count( $small_posts ) > 0 ) {
					?>
					<table class="form-table">
						<tr>
							<th><?php esc_html_e( 'Post Title', 'wp-audit' ); ?></th>
							<th><?php esc_html_e( 'Post Type', 'wp-audit' ); ?></th>
							<th><?php esc_html_e( 'Content Size', 'wp-audit' ); ?></th>
						</tr>
						<?php
						foreach ( $small_posts as $small_post ) {
							?>
							<tr>
								<td>
									<a href="<?php echo esc_url( get_edit_post_link( $small_post->ID ) ); ?>">
										<?php
										if ( ! empty( $small_post->post_title ) ) {
											echo esc_html( $small_post->post_title );
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
								<td><?php echo esc_html( $small_post->post_type ); ?></td>
								<td><?php echo esc_html( $small_post->length );?></td>
							</tr>
							<?php
						}
						?>
					</table>
					<?php
				}
				submit_button( 'Run Short Content Check' );
				wp_nonce_field( 'run_short_check_action', 'run_short_check_nonce' );
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_short_content_settings'
		);
	}
}
