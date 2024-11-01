<?php
/**
 * Class Settings Comments
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Comments {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_comments',
			esc_html__( 'Comments', 'wp-audit' ),
			function () {
				$post_count      = 0;
				$post_type_count = array();
				$post_types      = get_post_types();
				foreach ( $post_types as $post_type ) {
					$post_type_count[ $post_type ] = 0;
					if ( post_type_supports( $post_type, 'comments' ) ) {
						$posts = get_posts(
							array(
								'posts_per_page' => -1,
								'post_type'      => $post_type,
							)
						);
						if ( ! empty( $posts ) ) {
							foreach ( $posts as $post ) {
								if ( comments_open( $post->ID ) ) {
									$post_count++;
									$post_type_count[ $post_type ]++;
								}
							}
						}
					}
				}
				?>
				<p><?php esc_html_e( 'Comments can enhance a blog in many ways, they can promote discussion and build a community. However content that has comments enabled, but no actual comments, feels very incomplete. Additionally disabling comments can improve your load times and help to improve the security of your website.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'Your site currently has commenting:', 'wp-audit' ); ?> <?php echo 'open' === get_option( 'default_comment_status' ) ? 'Enabled' : 'Disabled'; ?></p>

				<p><?php esc_html_e( 'The Number of pages with comments allowed is: ', 'wp-audit' ); ?><?php echo esc_html( $post_count ); ?></p>

				<?php

				if ( $post_count > 0 ) {
					?>
					<ul>
					<?php
					foreach ( $post_type_count as $key => $value ) {
						if ( $value > 0 ) {
							?>
							<li><strong><?php echo esc_html( $key ); ?>:</strong> <?php echo esc_html( $value ); ?><li>
							<?php
						}
					}
					?>
					</ul>
					<?php
				}

				?>

				<p><?php esc_html_e( 'If you do not use comments you should disable them, either overall or for selected post types (e.g. allow them on posts, but not pages).', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'You can enable/disable comments on future posts by going into your ', 'wp-audit' ); ?><a href="<?php echo get_admin_url(); ?>options-discussion.php"><?php esc_html_e( 'Discussion Settings', 'wp-audit' ); ?></a><?php esc_html_e( ' however this doesn\'t affect older articles.', 'wp-audit' ); ?></p>

				<p><?php esc_html_e( 'To disable comments globally such that they cannot be enabled anywhere, use the ', 'wp-audit' ); ?><a href="https://wordpress.org/plugins/disable-comments/">https://wordpress.org/plugins/disable-comments/</a><?php esc_html_e( '. This plugin enables you to disable comments on Pages, Post, Media or Everywhere.', 'wp-audit' ); ?></p>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_comments_settings'
		);
	}
}
