<?php
/**
 * Class Settings Non Published Posts
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Non_Published_Posts {

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
		$post_types = get_post_types();
		sort( $post_types );

		foreach ( $post_types as $post_type ) {

			$post_type_object = get_post_type_object( $post_type );
			$post_statuses    = get_post_statuses();

			asort( $post_statuses );
			unset( $post_statuses['publish'] );

			// Add section.
			add_settings_section(
				DIGITAL_ELITE_WP_AUDIT_PREFIX . '_audit',
				esc_html__( 'Non-Published Posts', 'wp-audit' ),
				function () use ( $post_type_object ) {
					?>
					<p>
						<?php esc_html_e( 'There are a total of 8 different WordPress post statuses (', 'wp-audit' ); ?><a href="https://codex.wordpress.org/Post_Status" target="_blank">https://codex.wordpress.org/Post_Status)</a>
						<?php
							esc_html_e( ') that identify where a post is in the WordPress editing process and
							who can see it. A number of these are for posts that
							are not public, the most common of which are Private,
							Trash, Drafts and Auto-Draft. Sites that are not
							maintained well can quickly build up old posts in
							these areas that can start to cause problems elsewhere
							in the WordPress database.', 'wp-audit' );
						?>
					</p>

					<p>
						<?php
							esc_html_e( 'A summary of your non-published post statuses can be seen below:', 'wp-audit' );
						?>
					</p>
					<?php
				},
				DIGITAL_ELITE_WP_AUDIT_PREFIX . '_non_published_posts_settings'
			);

			$query = new \WP_Query(
				array(
					'post_type'      => $post_type,
					'post_status'    => $post_statuses,
					'posts_per_page' => 1,
				)
			);

			if ( $query->have_posts() ) {
				// Add fields to a section.
				add_settings_field(
					DIGITAL_ELITE_WP_AUDIT_PREFIX . '_audit_' . esc_attr( $post_type_object->name ),
					esc_html( $post_type_object->label ),
					function () use ( $post_type_object, $post_statuses ) {
						?>
						<ul>
							<?php
							foreach ( $post_statuses as $key => $post_status ) {

								$count_query = new \WP_Query(
									array(
										'post_type'      => $post_type_object->name,
										'post_status'    => $key,
										'posts_per_page' => -1,
									)
								);

								if ( $count_query->post_count > 0 ) {
									?>
									<li><?php echo esc_html( $post_status ); ?> <a href="<?php echo esc_url( get_admin_url() . 'edit.php' . '?post_type=' . $post_type_object->name . '&post_status=' . $key ); ?>"><?php echo esc_html( $count_query->post_count ); ?></a></li>
									<?php
								}
							}
							?>
						</ul>
						<?php
					},
					DIGITAL_ELITE_WP_AUDIT_PREFIX . '_non_published_posts_settings',
					DIGITAL_ELITE_WP_AUDIT_PREFIX . '_audit'
				);
			}
		}
	}
}
